<?php

namespace App\Services;

use App\Models\IntegrationSettingModel;

class InstagramConfigService
{
    private const PROVIDER = 'INSTAGRAM';
    private const CONNECTION_KEYS = [
        'user_id',
        'username',
        'access_token',
        'token_issued_at',
        'token_expires_at',
        'last_refreshed_at',
        'connection_status',
        'granted_scopes',
    ];

    public function resolved(): array
    {
        $rows = $this->storedRows();

        $appId = $this->resolvedPlain($rows, 'app_id', 'instagram.appId', '');
        $apiVersion = $this->resolvedPlain($rows, 'api_version', 'instagram.apiVersion', '');

        [$appSecret, $appSecretSource, $appSecretError] = $this->resolvedSecret(
            $rows,
            'app_secret',
            'instagram.appSecret'
        );
        [$accessToken, $tokenSource, $tokenError] = $this->resolvedSecret($rows, 'access_token', null);

        return [
            'app_id' => trim($appId),
            'app_secret' => $appSecret,
            'app_secret_source' => $appSecretSource,
            'app_secret_error' => $appSecretError,
            'api_version' => trim($apiVersion),
            'user_id' => trim((string) ($rows['user_id']['setting_value'] ?? '')),
            'username' => trim((string) ($rows['username']['setting_value'] ?? '')),
            'access_token' => $accessToken,
            'token_source' => $tokenSource,
            'token_error' => $tokenError,
            'token_issued_at' => $this->nullableValue($rows, 'token_issued_at'),
            'token_expires_at' => $this->nullableValue($rows, 'token_expires_at'),
            'last_refreshed_at' => $this->nullableValue($rows, 'last_refreshed_at'),
            'connection_status' => strtoupper(trim((string) ($rows['connection_status']['setting_value'] ?? ''))),
            'granted_scopes' => trim((string) ($rows['granted_scopes']['setting_value'] ?? '')),
            'has_database_app_secret' => isset($rows['app_secret']) && trim((string) $rows['app_secret']['setting_value']) !== '',
            'has_database_token' => isset($rows['access_token']) && trim((string) $rows['access_token']['setting_value']) !== '',
        ];
    }

    public function state(): array
    {
        $config = $this->resolved();
        $expiresAt = $this->timestamp($config['token_expires_at']);
        $issuedAt = $this->timestamp($config['token_issued_at']);
        $now = time();
        $expired = $expiresAt !== null && $expiresAt <= $now;
        $daysRemaining = $expiresAt === null ? null : max(0, (int) ceil(($expiresAt - $now) / 86400));
        $tokenAgeHours = $issuedAt === null ? null : max(0, (int) floor(($now - $issuedAt) / 3600));
        $connectionPresent = $config['user_id'] !== ''\n            && $config['access_token'] !== ''\n            && ! $config['token_error']\n            && $config['connection_status'] === 'CONNECTED'\n            && $config['token_issued_at'] !== null\n            && $config['token_expires_at'] !== null;
        $connected = $connectionPresent && ! $expired;
        $refreshDue = $connected && $expiresAt !== null && ($expiresAt - $now) <= (7 * 86400);

        return [
            'app_id' => $config['app_id'],
            'api_version' => $config['api_version'],
            'app_id_ready' => $config['app_id'] !== '',
            'app_secret_ready' => $config['app_secret'] !== '',
            'app_secret_source' => $config['app_secret_source'],
            'app_secret_error' => $config['app_secret_error'],
            'encryption_ready' => $this->encryptionReady(),
            'storage_ready' => $this->storageReady(),
            'user_id' => $config['user_id'],
            'username' => $config['username'],
            'access_token_ready' => $config['access_token'] !== '',
            'token_source' => $config['token_source'],
            'token_error' => $config['token_error'],
            'token_issued_at' => $config['token_issued_at'],
            'token_expires_at' => $config['token_expires_at'],
            'last_refreshed_at' => $config['last_refreshed_at'],
            'connection_status' => $config['connection_status'],
            'granted_scopes' => $config['granted_scopes'],
            'connection_present' => $connectionPresent,
            'connected' => $connected,
            'token_expired' => $expired,
            'token_days_remaining' => $daysRemaining,
            'token_age_hours' => $tokenAgeHours,
            'refresh_due' => $refreshDue,
            'has_database_app_secret' => $config['has_database_app_secret'],
            'has_database_token' => $config['has_database_token'],
        ];
    }

    public function isAppReady(): bool
    {
        $config = $this->resolved();
        return $this->storageReady()
            && $config['app_id'] !== ''
            && $config['app_secret'] !== ''
            && ! $config['app_secret_error'];
    }

    public function storageReady(): bool
    {
        try {
            return db_connect()->tableExists('integration_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    public function isConnected(): bool
    {
        return (bool) $this->state()['connected'];
    }

    public function isReady(): bool
    {
        return $this->isAppReady() && $this->isConnected();
    }

    public function saveAppSettings(string $appId, string $apiVersion, string $newAppSecret, int $updatedBy): void
    {
        $appId = trim($appId);
        $apiVersion = trim($apiVersion);
        $newAppSecret = trim($newAppSecret);

        if ($appId === '' || ! preg_match('/^[0-9]+$/', $appId)) {
            throw new \InvalidArgumentException('Meta App ID wajib berupa angka.');
        }
        if ($apiVersion !== '' && ! preg_match('/^v[0-9]+\.[0-9]+$/i', $apiVersion)) {
            throw new \InvalidArgumentException('API Version harus berformat seperti v25.0 atau dikosongkan.');
        }
        if ($newAppSecret !== '' && ! $this->encryptionReady()) {
            throw new \RuntimeException('Encryption key belum tersedia. Atur encryption.key di .env sebelum menyimpan App Secret.');
        }

        $db = db_connect();
        if (! $db->tableExists('integration_settings')) {
            throw new \RuntimeException('Tabel integration_settings belum tersedia. Jalankan SQL upgrade PHASE 10C terlebih dahulu.');
        }

        $rows = $this->storedRows();
        $oldAppId = trim((string) ($rows['app_id']['setting_value'] ?? ''));
        if ($oldAppId !== '' && $oldAppId !== $appId && $newAppSecret === '' && isset($rows['app_secret'])) {
            throw new \InvalidArgumentException('Jika Meta App ID diganti, App Secret baru wajib diisi.');
        }
        $model = new IntegrationSettingModel();
        $db->transBegin();

        try {
            $this->upsert($model, 'app_id', $appId, false, $updatedBy);
            $this->upsert($model, 'api_version', $apiVersion, false, $updatedBy);

            if ($newAppSecret !== '') {
                $this->upsert($model, 'app_secret', $this->encryptSecret($newAppSecret), true, $updatedBy);
            }

            if ($oldAppId !== '' && $oldAppId !== $appId) {
                $this->deleteKeys($model, self::CONNECTION_KEYS);
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

    public function storeConnection(
        string $userId,
        string $username,
        string $accessToken,
        int $expiresIn,
        int $updatedBy,
        string $grantedScopes = 'instagram_business_basic'
    ): void {
        if (! $this->encryptionReady()) {
            throw new \RuntimeException('Encryption key belum tersedia. Koneksi Instagram tidak dapat disimpan aman.');
        }

        $userId = trim($userId);
        $accessToken = trim($accessToken);
        if ($userId === '' || $accessToken === '') {
            throw new \InvalidArgumentException('Instagram User ID atau access token kosong.');
        }

        $now = time();
        $expiresIn = max(1, $expiresIn);
        $model = new IntegrationSettingModel();
        $db = db_connect();
        $db->transBegin();

        try {
            $this->upsert($model, 'user_id', $userId, false, $updatedBy);
            $this->upsert($model, 'username', trim($username), false, $updatedBy);
            $this->upsert($model, 'access_token', $this->encryptSecret($accessToken), true, $updatedBy);
            $this->upsert($model, 'token_issued_at', date('Y-m-d H:i:s', $now), false, $updatedBy);
            $this->upsert($model, 'token_expires_at', date('Y-m-d H:i:s', $now + $expiresIn), false, $updatedBy);
            $this->upsert($model, 'last_refreshed_at', '', false, $updatedBy);
            $this->upsert($model, 'connection_status', 'CONNECTED', false, $updatedBy);
            $this->upsert($model, 'granted_scopes', trim($grantedScopes), false, $updatedBy);

            if (! $db->transStatus()) {
                throw new \RuntimeException('Koneksi Instagram gagal disimpan.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function storeRefreshedToken(string $accessToken, int $expiresIn, int $updatedBy = 0): void
    {
        if (! $this->encryptionReady()) {
            throw new \RuntimeException('Encryption key belum tersedia. Token hasil refresh tidak dapat disimpan.');
        }

        $accessToken = trim($accessToken);
        if ($accessToken === '') {
            throw new \InvalidArgumentException('Token hasil refresh kosong.');
        }

        $now = time();
        $expiresIn = max(1, $expiresIn);
        $model = new IntegrationSettingModel();
        $db = db_connect();
        $db->transBegin();

        try {
            $this->upsert($model, 'access_token', $this->encryptSecret($accessToken), true, $updatedBy);
            $this->upsert($model, 'token_issued_at', date('Y-m-d H:i:s', $now), false, $updatedBy);
            $this->upsert($model, 'token_expires_at', date('Y-m-d H:i:s', $now + $expiresIn), false, $updatedBy);
            $this->upsert($model, 'last_refreshed_at', date('Y-m-d H:i:s', $now), false, $updatedBy);
            $this->upsert($model, 'connection_status', 'CONNECTED', false, $updatedBy);

            if (! $db->transStatus()) {
                throw new \RuntimeException('Token Instagram gagal diperbarui.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function markConnectionStatus(string $status, int $updatedBy = 0): void
    {
        $status = strtoupper(trim($status));
        if ($status === '') {
            return;
        }
        $this->upsert(new IntegrationSettingModel(), 'connection_status', $status, false, $updatedBy);
    }

    public function disconnect(): bool
    {
        $db = db_connect();
        if (! $db->tableExists('integration_settings')) {
            return true;
        }

        try {
            $model = new IntegrationSettingModel();
            $this->deleteKeys($model, self::CONNECTION_KEYS);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Koneksi Instagram gagal diputus: {class}', ['class' => $e::class]);
            return false;
        }
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

    private function resolvedSecret(array $rows, string $key, ?string $envKey): array
    {
        if (isset($rows[$key]) && trim((string) ($rows[$key]['setting_value'] ?? '')) !== '') {
            try {
                return [$this->decryptSecret((string) $rows[$key]['setting_value']), 'DATABASE', false];
            } catch (\Throwable $e) {
                log_message('error', 'Secret Instagram {key} tidak dapat didekripsi: {class}', [
                    'key' => $key,
                    'class' => $e::class,
                ]);
                return ['', 'DATABASE', true];
            }
        }

        if ($envKey !== null) {
            $envValue = trim((string) env($envKey, ''));
            if ($envValue !== '') {
                return [$envValue, 'ENV', false];
            }
        }

        return ['', 'NONE', false];
    }

    private function encryptSecret(string $value): string
    {
        return base64_encode(service('encrypter')->encrypt($value));
    }

    private function decryptSecret(string $value): string
    {
        $cipher = base64_decode($value, true);
        if ($cipher === false) {
            throw new \RuntimeException('Ciphertext secret tidak valid.');
        }
        return (string) service('encrypter')->decrypt($cipher);
    }

    private function nullableValue(array $rows, string $key): ?string
    {
        $value = trim((string) ($rows[$key]['setting_value'] ?? ''));
        return $value === '' ? null : $value;
    }

    private function timestamp(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $timestamp = strtotime($value);
        return $timestamp === false ? null : $timestamp;
    }

    private function upsert(IntegrationSettingModel $model, string $key, string $value, bool $isSecret, int $updatedBy): void
    {
        $existing = $model->where('provider', self::PROVIDER)->where('setting_key', $key)->first();
        $data = [
            'provider' => self::PROVIDER,
            'setting_key' => $key,
            'setting_value' => $value,
            'is_secret' => $isSecret ? 1 : 0,
            'updated_by' => $updatedBy > 0 ? $updatedBy : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $result = $existing ? $model->update((int) $existing['id'], $data) : $model->insert($data);
        if ($result === false) {
            throw new \RuntimeException('Konfigurasi integrasi gagal disimpan.');
        }
    }

    private function deleteKeys(IntegrationSettingModel $model, array $keys): void
    {
        $model->where('provider', self::PROVIDER)->whereIn('setting_key', $keys)->delete();
    }
}
