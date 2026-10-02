<?php

namespace App\Services;

use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
use Config\Services;

class InstagramSyncService
{
    public function sync(): array
    {
        $settings = (new SiteSettingModel())->valuesByKey();
        $mode = strtoupper(trim((string) ($settings['instagram_source_mode'] ?? 'HYBRID')));

        if ($mode === 'MANUAL') {
            return [
                'ok' => true,
                'skipped' => true,
                'count' => 0,
                'message' => 'Mode MANUAL aktif; sinkronisasi API dilewati.',
            ];
        }

        $fetch = $this->fetchMedia(25);
        if (! $fetch['ok']) {
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => $fetch['message'],
            ];
        }

        $rows = $fetch['rows'];
        if ($rows === []) {
            return [
                'ok' => true,
                'skipped' => false,
                'count' => 0,
                'message' => 'API berhasil diakses tetapi tidak mengembalikan media. Cache lama dipertahankan.',
            ];
        }

        $model = new InstagramPostModel();
        $db = db_connect();
        $fetchedAt = date('Y-m-d H:i:s');
        $saved = 0;
        $db->transBegin();

        try {
            foreach ($rows as $row) {
                $mediaId = trim((string) ($row['id'] ?? ''));
                if ($mediaId === '') {
                    continue;
                }

                $existing = $model->where('source', 'API')
                    ->where('instagram_media_id', $mediaId)
                    ->first();

                $timestamp = trim((string) ($row['timestamp'] ?? ''));
                $publishedAt = $timestamp !== '' && strtotime($timestamp) !== false
                    ? date('Y-m-d H:i:s', strtotime($timestamp))
                    : ($existing['published_at'] ?? null);

                $data = [
                    'source' => 'API',
                    'instagram_media_id' => $mediaId,
                    'permalink' => trim((string) ($row['permalink'] ?? '')),
                    'caption' => trim((string) ($row['caption'] ?? '')),
                    'media_type' => trim((string) ($row['media_type'] ?? '')),
                    'media_url' => trim((string) ($row['media_url'] ?? '')),
                    'thumbnail_url' => trim((string) ($row['thumbnail_url'] ?? '')),
                    'local_media_id' => $existing['local_media_id'] ?? null,
                    'published_at' => $publishedAt,
                    'is_fallback' => 0,
                    'is_visible' => $existing !== null ? (int) $existing['is_visible'] : 1,
                    'sort_order' => $existing !== null ? (int) $existing['sort_order'] : 0,
                    'fetched_at' => $fetchedAt,
                ];

                $result = $existing
                    ? $model->update((int) $existing['id'], $data)
                    : $model->insert($data);

                if ($result === false) {
                    throw new \RuntimeException('Gagal menyimpan cache Instagram.');
                }

                $saved++;
            }

            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi cache Instagram gagal.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Penyimpanan cache Instagram gagal: {class}', ['class' => $e::class]);
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Cache Instagram gagal disimpan. Cache lama dipertahankan.',
            ];
        }

        return [
            'ok' => true,
            'skipped' => false,
            'count' => $saved,
            'message' => $saved . ' post Instagram berhasil disinkronkan.',
            'fetched_at' => $fetchedAt,
        ];
    }

    public function testConnection(): array
    {
        $fetch = $this->fetchMedia(1);
        if (! $fetch['ok']) {
            return $fetch;
        }

        return [
            'ok' => true,
            'count' => count($fetch['rows']),
            'message' => 'Koneksi Instagram API berhasil. Credential diterima oleh endpoint.',
        ];
    }

    public function readiness(): array
    {
        return (new InstagramConfigService())->state();
    }

    public function isReady(): bool
    {
        return (new InstagramConfigService())->isReady();
    }

    private function fetchMedia(int $limit): array
    {
        $config = (new InstagramConfigService())->resolved();
        $required = [
            'base_url' => $config['base_url'],
            'user_id' => $config['user_id'],
            'access_token' => $config['access_token'],
        ];
        $missing = array_keys(array_filter($required, static fn ($value): bool => trim((string) $value) === ''));

        if ($missing !== []) {
            return [
                'ok' => false,
                'rows' => [],
                'message' => 'Konfigurasi API belum lengkap. Isi Base URL, User ID, dan Access Token.',
            ];
        }

        $endpoint = rtrim($config['base_url'], '/');
        if ($config['api_version'] !== '') {
            $endpoint .= '/' . rawurlencode($config['api_version']);
        }
        $endpoint .= '/' . rawurlencode($config['user_id']) . '/media';

        try {
            $response = Services::curlrequest()->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['access_token'],
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
                    'limit' => max(1, min($limit, 25)),
                ],
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Instagram API request gagal: {class}', ['class' => $e::class]);
            return ['ok' => false, 'rows' => [], 'message' => 'Tidak dapat menghubungi Instagram API.'];
        }

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            log_message('warning', 'Instagram API mengembalikan HTTP {status}.', ['status' => $status]);
            return [
                'ok' => false,
                'rows' => [],
                'message' => 'Instagram API mengembalikan HTTP ' . $status . '. Periksa User ID, token, Base URL, dan API Version.',
            ];
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (! is_array($payload) || ! isset($payload['data']) || ! is_array($payload['data'])) {
            return ['ok' => false, 'rows' => [], 'message' => 'Format respons Instagram API tidak dikenali.'];
        }

        return ['ok' => true, 'rows' => $payload['data'], 'message' => 'OK'];
    }
}
