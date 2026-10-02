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

        $config = $this->config();
        $missing = array_keys(array_filter($config, static fn ($value): bool => trim((string) $value) === ''));

        if ($missing !== []) {
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Credential/API environment belum lengkap.',
            ];
        }

        $endpoint = rtrim($config['base_url'], '/')
            . '/' . rawurlencode($config['api_version'])
            . '/' . rawurlencode($config['user_id'])
            . '/media';

        try {
            $client = Services::curlrequest();
            $response = $client->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['access_token'],
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
                    'limit' => 25,
                ],
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Instagram API request gagal.');
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Tidak dapat menghubungi Instagram API.',
            ];
        }

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            log_message('warning', 'Instagram API mengembalikan HTTP {status}.', ['status' => $status]);
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Instagram API mengembalikan respons HTTP ' . $status . '. Cache lama dipertahankan.',
            ];
        }

        $payload = json_decode((string) $response->getBody(), true);
        if (! is_array($payload) || ! isset($payload['data']) || ! is_array($payload['data'])) {
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Format respons Instagram API tidak dikenali. Cache lama dipertahankan.',
            ];
        }

        $rows = $payload['data'];
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

    public function readiness(): array
    {
        $config = $this->config();

        return [
            'base_url' => trim($config['base_url']) !== '',
            'api_version' => trim($config['api_version']) !== '',
            'user_id' => trim($config['user_id']) !== '',
            'access_token' => trim($config['access_token']) !== '',
        ];
    }

    public function isReady(): bool
    {
        return ! in_array(false, $this->readiness(), true);
    }

    private function config(): array
    {
        return [
            'base_url' => trim((string) env('instagram.apiBaseUrl', '')),
            'api_version' => trim((string) env('instagram.apiVersion', '')),
            'user_id' => trim((string) env('instagram.userId', '')),
            'access_token' => trim((string) env('instagram.accessToken', '')),
        ];
    }
}
