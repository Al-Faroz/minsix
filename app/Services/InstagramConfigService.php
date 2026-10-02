<?php

namespace App\Services;

use App\Models\IntegrationSettingModel;

class InstagramConfigService
{
    private const PROVIDER = 'INSTAGRAM';
    private const DEFAULT_BASE_URL = 'https://graph.instagram.com';
    private const ALLOWED_API_HOSTS = ['graph.instagram.com', 'graph.facebook.com'];

    public function resolved(): array
    {
        $rows = $this->storedRows();

        $baseUrl = $this->resolvedPlain($rows, 'api_base_url', 'instagram.apiBaseUrl', self::DEFAULT_BASE_URL);
        $baseUrl = $this->normalizeBaseUrl($baseUrl) ?? '';
        $apiVersion = $this->resolvedPlain($rows, 'api_version', 'instagram.apiVersion', '');
        $userId = $this->resolvedPlain($rows, 'user_id', 'instagram.userId', '');

        $token = '';
        $tokenSource = 'NONE';
        $tokenError = false;
        $hasDatabaseToken = isset($rows['access_token']) && trim((string) $rows['access_token']['setting_value']) !== '';

        if ($hasDatabaseToken) {
            try {
                $cipher = base64_decode((string) $rows['access_token']['setting_value'], true);
                if ($cipher === false) {
                    throw new \RuntimeException('Ciphertext token tidak valid.');
                }
                $token = (string) service('encrypter')->decrypt($cipher);
                $tokenSource = 'DATABASE';
            } catch (\Throwable $e) {
                $tokenError = true;
                log_message('error', 'Token Instagram di database tidak dapat didekripsi: {class}', ['class' => $e::class]);
            }
        }

        if ($token === '') {
            $envToken = trim((string) env('instagram.accessToken', ''));
            if ($envToken !== '') {
                $token = $envToken;
                $tokenSource = 'ENV';
            }
        }

        return [
            'base_url' => $baseUrl,
            'api_version' => trim($apiVersion),
            'user_id' => trim($userId),
            'access_token' => $token,
            'token_source' => $tokenSource,
            'token_error' => $tokenError,
            'has_database_token' => $hasDatabaseToken,
        ];
    }

    public function state(): array
    {
        $config = $this->resolved();
        return [
            'base_url' => $config['base_url'],
            'api_version' => $config['api_version'],
            'user_id' => $config['user_id'],
            'base_url_ready' => $config['base_url'] !== '',
            'user_id_ready' => $config['user_id'] !== '',
            'access_token_ready' => $config['access_token'] !== '',
            'token_source' => $config['token_source'],
            'token_error' => $config['token_error'],
            'has_database_token' => $config['has_database_token'],
            'encryption_ready' => $this->encryptionReady(),
        ];
    }

    public function isReady(): bool
    {
        $state = $this->state();
        return $state['base_url_ready'] && $state['user_id_ready'] && $state['access_token_ready'];
    }

    public function saveApiSettings(string $baseUrl, string $apiVersion, string $userId, string $newAccessToken, int $updatedBy): void
    {
        $normalizedBaseUrl = $this->normalizeBaseUrl($baseUrl);
        if ($normalizedBaseUrl === null) {
            throw new \InvalidArgumentException('API Base URL harus HTTPS dan menggunakan graph.instagram.com atau graph.facebook.com.');
        }
        if ($newAccessToken !== '' && ! $this->encryptionReady()) {
            throw new \RuntimeException('Encryption key belum tersedia. Atur encryption.key di .env sebelum menyimpan Access Token.');
        }

        $db = db_connect();
        if (! $db->tableExists('integration_settings')) {
            throw new \RuntimeException('Tabel integration_settings belum tersedia. Jalankan SQL upgrade PHASE 10C terlebih dahulu.');
        }

        $model = new IntegrationSettingModel();
        $db->transBegin();
        try {
            $this->upsert($model, 'api_base_url', $normalizedBaseUrl, false, $updatedBy);
            $this->upsert($model, 'api_version', trim($apiVersion), false, $updatedBy);
            $this->upsert($model, 'user_id', trim($userId), false, $updatedBy);
            if ($newAccessToken !== '') {
                $cipher = service('encrypter')->encrypt($newAccessToken);
                $this->upsert($model, 'access_token', base64_encode($cipher), true, $updatedBy);
            }
            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi konfigurasi Instagram gagal.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function clearDatabaseToken(): bool
    {
        $db = db_connect();
        if (! $db->tableExists('integration_settings')) {
            return true;
        }

        $model = new IntegrationSettingModel();
        $row = $model->where('provider', self::PROVIDER)->where('setting_key', 'access_token')->first();
        if ($row === null) {
            return true;
        }
        return $model->delete((int) $row['id']) !== false;
    }

    public function encryptionReady(): bool
    {
        try {
            $config = config('Encryption');
            return trim((string) ($config->key ?? '')) !== '';
        } catch (\Throwable) {
            return false;
        }
    }

    public function normalizeBaseUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            $url = self::DEFAULT_BASE_URL;
        }
        $parts = parse_url($url);
        if (! is_array($parts)) {
            return null;
        }
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($scheme !== 'https' || ! in_array($host, self::ALLOWED_API_HOSTS, true)) {
            return null;
        }
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            return null;
        }
        $path = trim((string) ($parts['path'] ?? ''), '/');
        return 'https://' . $host . ($path !== '' ? '/' . $path : '');
    }

    private function storedRows(): array
    {
        try {
            $db = db_connect();
            if (! $db->tableExists('integration_settings')) {
                return [];
            }
            return (new IntegrationSettingModel())->valuesForProvider(self::PROVIDER);
        } catch (\Throwable $e) {
            log_message('warning', 'Integration settings belum dapat dibaca: {class}', ['class' => $e::class]);
            return [];
        }
    }

    private function resolvedPlain(array $rows, string $key, string $envKey, string $default): string
    {
        if (array_key_exists($key, $rows)) {
            return trim((string) ($rows[$key]['setting_value'] ?? ''));
        }
        $envValue = trim((string) env($envKey, ''));
        return $envValue !== '' ? $envValue : $default;
    }

    private function upsert(IntegrationSettingModel $model, string $key, string $value, bool $isSecret, int $updatedBy): void
    {
        $existing = $model->where('provider', self::PROVIDER)->where('setting_key', $key)->first();
        $data = [
            'provider' => self::PROVIDER,
            'setting_key' => $key,
            'setting_value' => $value,
            'is_secret' => $isSecret ? 1 : 0,
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $result = $existing ? $model->update((int) $existing['id'], $data) : $model->insert($data);
        if ($result === false) {
            throw new \RuntimeException('Konfigurasi integrasi gagal disimpan.');
        }
    }
}
