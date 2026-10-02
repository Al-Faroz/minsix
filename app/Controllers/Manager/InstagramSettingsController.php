<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
use App\Services\InstagramConfigService;
use App\Services\InstagramOAuthService;
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
        $oauthService = new InstagramOAuthService();

        return view('manager/instagram/settings', [
            'title' => 'Pengaturan Instagram | CMS MIN 6 JEMBER',
            'pageTitle' => 'Pengaturan Instagram',
            'settings' => $settings,
            'apiReady' => $configService->isReady(),
            'apiState' => $configService->state(),
            'callbackUrl' => $oauthService->callbackUrl(),
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

    public function updateApp(): RedirectResponse
    {
        if (! $this->validate([
            'app_id' => 'required|max_length[100]|regex_match[/^[0-9]+$/]',
            'api_version' => 'permit_empty|max_length[50]|regex_match[/^v[0-9]+\.[0-9]+$/i]',
            'app_secret' => 'permit_empty|max_length[1000]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            (new InstagramConfigService())->saveAppSettings(
                (string) $this->request->getPost('app_id'),
                (string) $this->request->getPost('api_version'),
                trim((string) $this->request->getPost('app_secret')),
                (int) session()->get('auth_user_id')
            );
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            log_message('error', 'Konfigurasi Meta App gagal disimpan: {class}', ['class' => $e::class]);
            return redirect()->back()->withInput()->with('error', 'Konfigurasi Meta App gagal disimpan karena terjadi kesalahan internal.');
        }

        $this->auditAction('INSTAGRAM_APP_CONFIG_UPDATED', 'Meta App ID/API Version/App Secret Instagram diperbarui. Secret tidak dicatat.');
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Konfigurasi Meta App berhasil disimpan.');
    }

    public function connect(): RedirectResponse
    {
        try {
            $url = (new InstagramOAuthService())->authorizationUrl();
        } catch (\Throwable $e) {
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', $e->getMessage());
        }

        $this->auditAction('INSTAGRAM_LOGIN_STARTED', 'Proses Instagram Login dimulai.');
        return redirect()->to($url);
    }

    public function callback(): RedirectResponse
    {
        $oauthError = trim((string) $this->request->getGet('error'));
        if ($oauthError !== '') {
            $description = trim((string) $this->request->getGet('error_description'));
            $message = $description !== '' ? $description : 'Instagram Login dibatalkan atau ditolak.';
            $this->auditAction('INSTAGRAM_LOGIN_FAILED', $message);
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', $message);
        }

        $code = trim((string) $this->request->getGet('code'));
        $state = trim((string) $this->request->getGet('state'));
        if ($code === '' || $state === '') {
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', 'Callback Instagram tidak lengkap. Ulangi Hubungkan Instagram.');
        }

        try {
            $result = (new InstagramOAuthService())->completeAuthorization(
                $code,
                $state,
                (int) session()->get('auth_user_id')
            );
        } catch (\Throwable $e) {
            log_message('error', 'Instagram Login callback gagal: {class}', ['class' => $e::class]);
            $this->auditAction('INSTAGRAM_LOGIN_FAILED', $e->getMessage());
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', $e->getMessage());
        }

        $this->auditAction('INSTAGRAM_CONNECTED', (string) ($result['message'] ?? 'Akun Instagram berhasil dihubungkan.'));
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', (string) $result['message']);
    }

    public function refreshToken(): RedirectResponse
    {
        $result = (new InstagramOAuthService())->refreshToken(true, (int) session()->get('auth_user_id'));
        $this->auditAction(
            ($result['ok'] ?? false) ? 'INSTAGRAM_TOKEN_REFRESH' : 'INSTAGRAM_TOKEN_REFRESH_FAILED',
            (string) ($result['message'] ?? 'Refresh token Instagram dijalankan.')
        );

        $key = ($result['ok'] ?? false) ? 'success' : 'error';
        return redirect()->to(site_url('manager/instagram-settings'))->with($key, (string) $result['message']);
    }

    public function disconnect(): RedirectResponse
    {
        try {
            if (! (new InstagramConfigService())->disconnect()) {
                throw new \RuntimeException('Koneksi Instagram gagal diputus.');
            }
        } catch (\Throwable $e) {
            return redirect()->to(site_url('manager/instagram-settings'))->with('error', 'Koneksi Instagram gagal diputus.');
        }

        $this->auditAction('INSTAGRAM_DISCONNECTED', 'Token dan identitas akun Instagram dihapus dari konfigurasi lokal. Cache media tetap dipertahankan.');
        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Koneksi Instagram diputus. Cache media lama tetap tersedia.');
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
