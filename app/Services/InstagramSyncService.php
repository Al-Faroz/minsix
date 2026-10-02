<?php

namespace App\Services;

use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
use Config\Services;

class InstagramSyncService
{
    private const GRAPH_URL = 'https://graph.instagram.com';

    public function sync(): array
    {
        $settings = (new SiteSettingModel())->valuesByKey();
        $mode = strtoupper(trim((string) ($settings['instagram_source_mode'] ?? 'HYBRID')));
        $oauth = new InstagramOAuthService();
        $configService = new InstagramConfigService();

        if ($configService->state()['connection_present']) {
            $refresh = $oauth->ensureFreshToken();
            if (! ($refresh['ok'] ?? false) && $mode !== 'MANUAL') {
                return [
                    'ok' => false,
                    'skipped' => false,
                    'count' => 0,
                    'message' => (string) ($refresh['message'] ?? 'Token Instagram perlu dihubungkan ulang.'),
                ];
            }
        }

        if ($mode === 'MANUAL') {
            return [
                'ok' => true,
                'skipped' => true,
                'count' => 0,
                'message' => 'Mode MANUAL aktif; sinkronisasi media dilewati. Lifecycle token tetap diperiksa bila akun terhubung.',
            ];
        }

        if (! $configService->isReady()) {
            return [
                'ok' => false,
                'skipped' => false,
                'count' => 0,
                'message' => 'Instagram belum terhubung melalui Instagram Login. Buka Pengaturan Instagram lalu hubungkan akun.',
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
        $carouselCount = 0;
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

                $mediaType = strtoupper(trim((string) ($row['media_type'] ?? '')));
                $children = [];
                if ($mediaType === 'CAROUSEL_ALBUM') {
                    $children = $this->normalizeChildren($row['children']['data'] ?? []);
                    if ($children === []) {
                        $childrenResult = $this->fetchChildren($mediaId);
                        if ($childrenResult['ok']) {
                            $children = $childrenResult['rows'];
                        } else {
                            log_message('warning', 'Children carousel Instagram {id} tidak dapat dibaca: {message}', [
                                'id' => $mediaId,
                                'message' => (string) ($childrenResult['message'] ?? 'unknown'),
                            ]);
                        }
                    }
                    if ($children !== []) {
                        $carouselCount++;
                    }
                }

                $mediaUrl = trim((string) ($row['media_url'] ?? ''));
                $thumbnailUrl = trim((string) ($row['thumbnail_url'] ?? ''));
                if ($mediaType === 'CAROUSEL_ALBUM' && $mediaUrl === '' && $thumbnailUrl === '') {
                    $cover = $this->firstDisplayableChild($children);
                    $mediaUrl = $cover['media_url'] ?? '';
                    $thumbnailUrl = $cover['thumbnail_url'] ?? '';
                }

                $data = [
                    'source' => 'API',
                    'instagram_media_id' => $mediaId,
                    'permalink' => trim((string) ($row['permalink'] ?? '')),
                    'caption' => trim((string) ($row['caption'] ?? '')),
                    'media_type' => $mediaType,
                    'media_url' => $mediaUrl,
                    'thumbnail_url' => $thumbnailUrl,
                    'children_json' => $children !== [] ? json_encode($children, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
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
            'carousel_count' => $carouselCount,
            'message' => $saved . ' post Instagram berhasil disinkronkan' . ($carouselCount > 0 ? ' (' . $carouselCount . ' carousel album).' : '.'),
            'fetched_at' => $fetchedAt,
        ];
    }

    public function testConnection(): array
    {
        $configService = new InstagramConfigService();
        if (! $configService->isReady()) {
            return ['ok' => false, 'rows' => [], 'message' => 'Instagram belum terhubung melalui Instagram Login.'];
        }

        $refresh = (new InstagramOAuthService())->ensureFreshToken();
        if (! ($refresh['ok'] ?? false)) {
            return ['ok' => false, 'rows' => [], 'message' => (string) ($refresh['message'] ?? 'Token Instagram tidak valid.')];
        }

        $fetch = $this->fetchMedia(1);
        if (! $fetch['ok']) {
            return $fetch;
        }

        return [
            'ok' => true,
            'count' => count($fetch['rows']),
            'message' => 'Koneksi Instagram berhasil. Akun dan token dapat membaca media.',
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
        if ($config['user_id'] === '' || $config['access_token'] === '') {
            return [
                'ok' => false,
                'rows' => [],
                'message' => 'Instagram belum terhubung melalui Instagram Login.',
            ];
        }

        $endpoint = $this->graphEndpoint($config['api_version'], $config['user_id'] . '/media');
        $fields = 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,children{id,media_type,media_url,thumbnail_url}';

        $result = $this->getJson($endpoint, $config['access_token'], [
            'fields' => $fields,
            'limit' => max(1, min($limit, 25)),
        ]);

        if (! $result['ok']) {
            // Keep basic sync available if a Graph version rejects nested field expansion.
            // CAROUSEL_ALBUM children are then read from the dedicated /children edge.
            $result = $this->getJson($endpoint, $config['access_token'], [
                'fields' => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
                'limit' => max(1, min($limit, 25)),
            ]);
        }

        if (! $result['ok']) {
            return ['ok' => false, 'rows' => [], 'message' => $result['message']];
        }

        $payload = $result['payload'];
        if (! isset($payload['data']) || ! is_array($payload['data'])) {
            return ['ok' => false, 'rows' => [], 'message' => 'Format respons media Instagram tidak dikenali.'];
        }

        return ['ok' => true, 'rows' => $payload['data'], 'message' => 'OK'];
    }

    private function fetchChildren(string $mediaId): array
    {
        $config = (new InstagramConfigService())->resolved();
        $endpoint = $this->graphEndpoint($config['api_version'], $mediaId . '/children');
        $result = $this->getJson($endpoint, $config['access_token'], [
            'fields' => 'id,media_type,media_url,thumbnail_url',
            'limit' => 20,
        ]);

        if (! $result['ok']) {
            return ['ok' => false, 'rows' => [], 'message' => $result['message']];
        }

        $payload = $result['payload'];
        return [
            'ok' => true,
            'rows' => $this->normalizeChildren($payload['data'] ?? []),
            'message' => 'OK',
        ];
    }

    private function normalizeChildren(mixed $rows): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $normalized = [];
        foreach ($rows as $child) {
            if (! is_array($child)) {
                continue;
            }
            $id = trim((string) ($child['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            $normalized[] = [
                'id' => $id,
                'media_type' => strtoupper(trim((string) ($child['media_type'] ?? ''))),
                'media_url' => trim((string) ($child['media_url'] ?? '')),
                'thumbnail_url' => trim((string) ($child['thumbnail_url'] ?? '')),
            ];
        }

        return $normalized;
    }

    private function firstDisplayableChild(array $children): array
    {
        foreach ($children as $child) {
            $thumbnail = trim((string) ($child['thumbnail_url'] ?? ''));
            $media = trim((string) ($child['media_url'] ?? ''));
            if ($thumbnail !== '' || $media !== '') {
                return [
                    'thumbnail_url' => $thumbnail,
                    'media_url' => $media,
                ];
            }
        }
        return ['thumbnail_url' => '', 'media_url' => ''];
    }

    private function graphEndpoint(string $apiVersion, string $path): string
    {
        $endpoint = self::GRAPH_URL;
        $apiVersion = trim($apiVersion);
        if ($apiVersion !== '') {
            $endpoint .= '/' . rawurlencode($apiVersion);
        }
        return $endpoint . '/' . ltrim($path, '/');
    }

    private function getJson(string $endpoint, string $accessToken, array $query): array
    {
        try {
            $response = Services::curlrequest()->get($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'query' => $query,
                'timeout' => 20,
                'http_errors' => false,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Instagram API request gagal: {class}', ['class' => $e::class]);
            return ['ok' => false, 'payload' => [], 'message' => 'Tidak dapat menghubungi Instagram API.'];
        }

        $status = $response->getStatusCode();
        $payload = json_decode((string) $response->getBody(), true);

        if ($status < 200 || $status >= 300) {
            $apiMessage = is_array($payload)
                ? trim((string) ($payload['error']['message'] ?? $payload['error_message'] ?? ''))
                : '';
            log_message('warning', 'Instagram API HTTP {status}: {message}', ['status' => $status, 'message' => $apiMessage]);
            return [
                'ok' => false,
                'payload' => [],
                'message' => 'Instagram API mengembalikan HTTP ' . $status . ($apiMessage !== '' ? ': ' . $apiMessage : '.') . ' Jika token kedaluwarsa, hubungkan ulang Instagram.',
            ];
        }

        if (! is_array($payload)) {
            return ['ok' => false, 'payload' => [], 'message' => 'Format respons Instagram API tidak dikenali.'];
        }

        return ['ok' => true, 'payload' => $payload, 'message' => 'OK'];
    }
}
