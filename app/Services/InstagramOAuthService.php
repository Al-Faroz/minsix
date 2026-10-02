<?php

namespace App\Services;

use Config\Services;

class InstagramOAuthService
{
    private const AUTHORIZE_URL = 'https://www.instagram.com/oauth/authorize';
    private const TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
    private const GRAPH_URL = 'https://graph.instagram.com';
    private const SCOPE = 'instagram_business_basic';
    private const STATE_TTL = 600;
    private const TOKEN_REFRESH_MIN_AGE = 86400;
    private const TOKEN_REFRESH_WINDOW = 604800;
    private const DEFAULT_LONG_LIVED_TTL = 5184000;

    public function authorizationUrl(): string
    {
        $configService = new InstagramConfigService();
        $config = $configService->resolved();

        if (! $configService->isAppReady()) {
            throw new \RuntimeException('Meta App ID dan App Secret belum lengkap. Simpan konfigurasi aplikasi terlebih dahulu.');
        }
        if (! $configService->encryptionReady()) {
            throw new \RuntimeException('Encryption key belum tersedia. Koneksi Instagram tidak dapat disimpan aman.');
        }

        $state = bin2hex(random_bytes(24));
        session()->set([
            'instagram_oauth_state' => $state,
            'instagram_oauth_started_at' => time(),
        ]);

        return self::AUTHORIZE_URL . '?' . http_build_query([
            'client_id' => $config['app_id'],
            'redirect_uri' => $this->callbackUrl(),
            'response_type' => 'code',
            'scope' => self::SCOPE,
            'state' => $state,
            'enable_fb_login' => '0',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function completeAuthorization(string $code, string $state, int $updatedBy): array
    {
        $this->validateState($state);
        $code = trim($code);
        if ($code === '') {
            throw new \InvalidArgumentException('Authorization code Instagram tidak diterima.');
        }

        $configService = new InstagramConfigService();
        $config = $configService->resolved();
        if (! $configService->isAppReady()) {
            throw new \RuntimeException('Konfigurasi Meta App belum lengkap.');
        }

        $short = $this->exchangeAuthorizationCode($code, $config['app_id'], $config['app_secret']);
        $long = $this->exchangeLongLivedToken($short['access_token'], $config['app_secret']);
        $username = $this->fetchUsername($short['user_id'], $long['access_token'], $config['api_version']);

        $configService->storeConnection(
            $short['user_id'],
            $username,
            $long['access_token'],
            $long['expires_in'],
            $updatedBy,
            self::SCOPE
        );

        return [
            'ok' => true,
            'user_id' => $short['user_id'],
            'username' => $username,
            'expires_in' => $long['expires_in'],
            'message' => $username !== ''
                ? 'Instagram @' . $username . ' berhasil dihubungkan.'
                : 'Akun Instagram berhasil dihubungkan.',
        ];
    }

    public function refreshToken(bool $force = false, int $updatedBy = 0): array
    {
        $configService = new InstagramConfigService();
        $config = $configService->resolved();
        $state = $configService->state();

        if (! $state['connection_present']) {
            return ['ok' => false, 'refreshed' => false, 'message' => 'Instagram belum terhubung.'];
        }
        if ($state['token_expired']) {
            $configService->markConnectionStatus('REAUTH_REQUIRED', $updatedBy);
            return ['ok' => false, 'refreshed' => false, 'message' => 'Token Instagram sudah kedaluwarsa. Hubungkan ulang akun Instagram.'];
        }

        $issuedAt = $config['token_issued_at'] ? strtotime($config['token_issued_at']) : false;
        $expiresAt = $config['token_expires_at'] ? strtotime($config['token_expires_at']) : false;
        $age = $issuedAt === false ? null : time() - $issuedAt;
        $remaining = $expiresAt === false ? null : $expiresAt - time();

        if ($age !== null && $age < self::TOKEN_REFRESH_MIN_AGE) {
            if ($force) {
                return [
                    'ok' => true,
                    'refreshed' => false,
                    'message' => 'Token belum berusia 24 jam sehingga belum perlu/dapat direfresh.',
                ];
            }
            return ['ok' => true, 'refreshed' => false, 'message' => 'Token masih baru.'];
        }

        if (! $force && $remaining !== null && $remaining > self::TOKEN_REFRESH_WINDOW) {
            return ['ok' => true, 'refreshed' => false, 'message' => 'Token masih sehat; refresh belum diperlukan.'];
        }

        try {
            $response = Services::curlrequest()->get(self::GRAPH_URL . '/refresh_access_token', [
                'query' => [
                    'grant_type' => 'ig_refresh_token',
                    'access_token' => $config['access_token'],
                ],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Refresh token Instagram gagal dijalankan: {class}', ['class' => $e::class]);
            return ['ok' => false, 'refreshed' => false, 'message' => 'Tidak dapat menghubungi endpoint refresh Instagram.'];
        }

        try {
            $payload = $this->decodeResponse($response->getStatusCode(), (string) $response->getBody(), 'Refresh token Instagram gagal.');
        } catch (\RuntimeException $e) {
            return ['ok' => false, 'refreshed' => false, 'message' => $e->getMessage()];
        }

        $token = trim((string) ($payload['access_token'] ?? ''));
        if ($token === '') {
            return ['ok' => false, 'refreshed' => false, 'message' => 'Respons refresh Instagram tidak berisi access token.'];
        }

        $expiresIn = max(1, (int) ($payload['expires_in'] ?? self::DEFAULT_LONG_LIVED_TTL));
        $configService->storeRefreshedToken($token, $expiresIn, $updatedBy);

        return [
            'ok' => true,
            'refreshed' => true,
            'expires_in' => $expiresIn,
            'message' => 'Token Instagram berhasil direfresh.',
        ];
    }

    public function ensureFreshToken(int $updatedBy = 0): array
    {
        return $this->refreshToken(false, $updatedBy);
    }

    public function callbackUrl(): string
    {
        return site_url('manager/instagram-settings/callback');
    }

    private function validateState(string $state): void
    {
        $expected = (string) session()->get('instagram_oauth_state');
        $startedAt = (int) session()->get('instagram_oauth_started_at');
        session()->remove(['instagram_oauth_state', 'instagram_oauth_started_at']);

        if ($expected === '' || $state === '' || ! hash_equals($expected, $state)) {
            throw new \RuntimeException('State OAuth Instagram tidak valid. Ulangi proses Hubungkan Instagram.');
        }
        if ($startedAt <= 0 || (time() - $startedAt) > self::STATE_TTL) {
            throw new \RuntimeException('Sesi OAuth Instagram sudah kedaluwarsa. Ulangi proses Hubungkan Instagram.');
        }
    }

    private function exchangeAuthorizationCode(string $code, string $appId, string $appSecret): array
    {
        try {
            $response = Services::curlrequest()->post(self::TOKEN_URL, [
                'form_params' => [
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $this->callbackUrl(),
                    'code' => $code,
                ],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Code exchange Instagram gagal: {class}', ['class' => $e::class]);
            throw new \RuntimeException('Tidak dapat menukar authorization code Instagram.');
        }

        $payload = $this->decodeResponse($response->getStatusCode(), (string) $response->getBody(), 'Authorization code Instagram ditolak.');
        if (isset($payload['data']) && is_array($payload['data'])) {
            if (count($payload['data']) !== 1 || ! is_array($payload['data'][0])) {
                throw new \RuntimeException('Respons token Instagram ambigu dan tidak dapat dipilih otomatis.');
            }
            $payload = $payload['data'][0];
        }

        $accessToken = trim((string) ($payload['access_token'] ?? ''));
        $userId = trim((string) ($payload['user_id'] ?? $payload['id'] ?? ''));
        if ($accessToken === '' || $userId === '') {
            throw new \RuntimeException('Respons login Instagram tidak berisi access token atau user ID.');
        }

        return ['access_token' => $accessToken, 'user_id' => $userId];
    }

    private function exchangeLongLivedToken(string $shortToken, string $appSecret): array
    {
        try {
            $response = Services::curlrequest()->get(self::GRAPH_URL . '/access_token', [
                'query' => [
                    'grant_type' => 'ig_exchange_token',
                    'client_secret' => $appSecret,
                    'access_token' => $shortToken,
                ],
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Long-lived token exchange Instagram gagal: {class}', ['class' => $e::class]);
            throw new \RuntimeException('Tidak dapat membuat long-lived token Instagram.');
        }

        $payload = $this->decodeResponse($response->getStatusCode(), (string) $response->getBody(), 'Long-lived token Instagram gagal dibuat.');
        $accessToken = trim((string) ($payload['access_token'] ?? ''));
        if ($accessToken === '') {
            throw new \RuntimeException('Respons long-lived token Instagram tidak valid.');
        }

        return [
            'access_token' => $accessToken,
            'expires_in' => max(1, (int) ($payload['expires_in'] ?? self::DEFAULT_LONG_LIVED_TTL)),
        ];
    }

    private function fetchUsername(string $userId, string $accessToken, string $apiVersion): string
    {
        $endpoint = rtrim(self::GRAPH_URL, '/');
        if (trim($apiVersion) !== '') {
            $endpoint .= '/' . rawurlencode(trim($apiVersion));
        }
        $endpoint .= '/' . rawurlencode($userId);

        try {
            $response = Services::curlrequest()->get($endpoint, [
                'query' => ['fields' => 'id,user_id,username'],
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'timeout' => 20,
                'http_errors' => false,
            ]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return '';
            }
            $payload = json_decode((string) $response->getBody(), true);
            return is_array($payload) ? trim((string) ($payload['username'] ?? '')) : '';
        } catch (\Throwable $e) {
            log_message('warning', 'Username Instagram tidak dapat dibaca setelah login: {class}', ['class' => $e::class]);
            return '';
        }
    }

    private function decodeResponse(int $status, string $body, string $fallbackMessage): array
    {
        $payload = json_decode($body, true);
        if ($status < 200 || $status >= 300) {
            $message = is_array($payload)
                ? trim((string) ($payload['error_message'] ?? $payload['error']['message'] ?? ''))
                : '';
            throw new \RuntimeException($message !== '' ? $fallbackMessage . ' ' . $message : $fallbackMessage . ' HTTP ' . $status . '.');
        }
        if (! is_array($payload)) {
            throw new \RuntimeException($fallbackMessage . ' Format respons tidak dikenali.');
        }
        return $payload;
    }
}
