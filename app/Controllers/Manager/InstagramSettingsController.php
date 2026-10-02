<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
use App\Services\InstagramConfigService;
use App\Services\InstagramSyncService;
use CodeIgniter\HTTP\RedirectResponse;

class InstagramSettingsController extends BaseController
{
    public function index(): string
    {
        $settings = (new SiteSettingModel())->valuesByKey();
        $latest = (new InstagramPostModel())->where('source', 'API')
            ->where('fetched_at IS NOT NULL', null, false)
            ->orderBy('fetched_at', 'DESC')
            ->first();
        $configService = new InstagramConfigService();

        return view('manager/instagram/settings', [
            'title' => 'Pengaturan Instagram | CMS MIN 6 JEMBER',
            'pageTitle' => 'Pengaturan Instagram',
            'settings' => $settings,
            'apiReady' => $configService->isReady(),
            'apiState' => $configService->state(),
            'latestFetchedAt' => $latest['fetched_at'] ?? null,
            'apiCount' => (new InstagramPostModel())->where('source', 'API')->countAllResults(),
        ]);
    }

    public function update(): RedirectResponse
    {
        if (! $this->validate([
            'instagram_source_mode' => 'required|in_list[HYBRID,MANUAL]',
            'instagram_display_count' => 'required|integer|greater_than_equal_to[6]|less_than_equal_to[8]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $values = [
            'instagram_source_mode' => (string) $this->request->getPost('instagram_source_mode'),
            'instagram_display_count' => (string) (int) $this->request->getPost('instagram_display_count'),
        ];

        $model = new SiteSettingModel();
        $db = db_connect();
        $db->transBegin();

        try {
            foreach ($values as $key => $value) {
                $existing = $model->where('setting_key', $key)->first();
                $data = [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'value_type' => 'string',
                    'is_public' => 0,
                    'updated_by' => (int) session()->get('auth_user_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $result = $existing ? $model->update((int) $existing['id'], $data) : $model->insert($data);
                if ($result === false) {
                    throw new \RuntimeException('Gagal menyimpan pengaturan Instagram.');
                }
            }
            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi pengaturan Instagram gagal.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Pengaturan Instagram gagal: {class}', ['class' => $e::class]);
            return redirect()->back()->withInput()->with('error', 'Pengaturan Instagram gagal disimpan.');
        }

        $this->auditAction('INSTAGRAM_CONFIG_UPDATED', 'Mode source dan jumlah carousel Instagram diperbarui.');
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Pengaturan Instagram berhasil disimpan.');
    }

    public function updateApi(): RedirectResponse
    {
        if (! $this->validate([
            'api_base_url' => 'required|max_length[255]',
            'api_version' => 'permit_empty|max_length[50]|regex_match[/^[A-Za-z0-9._-]+$/]',
            'instagram_user_id' => 'required|max_length[100]|regex_match[/^[A-Za-z0-9._-]+$/]',
            'instagram_access_token' => 'permit_empty|max_length[10000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            (new InstagramConfigService())->saveApiSettings(
                (string) $this->request->getPost('api_base_url'),
                (string) $this->request->getPost('api_version'),
                (string) $this->request->getPost('instagram_user_id'),
                trim((string) $this->request->getPost('instagram_access_token')),
                (int) session()->get('auth_user_id')
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            log_message('error', 'Credential Instagram gagal disimpan: {class}', ['class' => $e::class]);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $this->auditAction('INSTAGRAM_API_CONFIG_UPDATED', 'Konfigurasi API Instagram diperbarui. Nilai token tidak dicatat.');
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Konfigurasi API berhasil disimpan.');
    }

    public function testConnection(): RedirectResponse
    {
        $result = (new InstagramSyncService())->testConnection();
        $this->auditAction(
            ($result['ok'] ?? false) ? 'INSTAGRAM_CONNECTION_OK' : 'INSTAGRAM_CONNECTION_FAILED',
            (string) ($result['message'] ?? 'Tes koneksi Instagram dijalankan.')
        );
        $key = ($result['ok'] ?? false) ? 'success' : 'error';
        return redirect()->to(site_url('manager/instagram-settings'))->with($key, (string) $result['message']);
    }

    public function clearToken(): RedirectResponse
    {
        if (! (new InstagramConfigService())->clearDatabaseToken()) {
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', 'Token database gagal dihapus.');
        }
        $this->auditAction('INSTAGRAM_TOKEN_CLEARED', 'Access Token terenkripsi di database dihapus. Token ENV, bila ada, tidak berubah.');
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Token database berhasil dihapus.');
    }

    public function sync(): RedirectResponse
    {
        $result = (new InstagramSyncService())->sync();
        $this->auditAction(
            ($result['ok'] ?? false) ? 'INSTAGRAM_SYNC_RUN' : 'INSTAGRAM_SYNC_FAILED',
            (string) ($result['message'] ?? 'Sinkronisasi Instagram dijalankan.')
        );
        if (! ($result['ok'] ?? false)) {
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', (string) $result['message']);
        }
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', (string) $result['message']);
    }

    private function auditAction(string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'INSTAGRAM_CONFIG',
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
