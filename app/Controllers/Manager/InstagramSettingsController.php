<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
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

        return view('manager/instagram/settings', [
            'title' => 'Pengaturan Instagram | CMS MIN 6 Jember',
            'pageTitle' => 'Pengaturan Instagram',
            'settings' => $settings,
            'apiReady' => (new InstagramSyncService())->isReady(),
            'apiState' => (new InstagramSyncService())->readiness(),
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

                $result = $existing
                    ? $model->update((int) $existing['id'], $data)
                    : $model->insert($data);

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
            log_message('error', 'Pengaturan Instagram gagal: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Pengaturan Instagram gagal disimpan.');
        }

        $this->audit();

        return redirect()->to(site_url('manager/instagram-settings'))->with('success', 'Pengaturan Instagram berhasil disimpan.');
    }

    public function sync(): RedirectResponse
    {
        $result = (new InstagramSyncService())->sync();

        $this->auditAction(
            ($result['ok'] ?? false) ? 'INSTAGRAM_SYNC_RUN' : 'INSTAGRAM_SYNC_FAILED',
            (string) ($result['message'] ?? 'Sinkronisasi Instagram dijalankan.')
        );

        if (! ($result['ok'] ?? false)) {
            return redirect()->to(site_url('manager/instagram-settings'))
                ->with('error', (string) ($result['message'] ?? 'Sinkronisasi Instagram gagal.'));
        }

        return redirect()->to(site_url('manager/instagram-settings'))
            ->with('success', (string) ($result['message'] ?? 'Sinkronisasi Instagram selesai.'));
    }

    private function audit(): void
    {
        $this->auditAction(
            'INSTAGRAM_CONFIG_UPDATED',
            'Mode source dan jumlah carousel Instagram diperbarui. Credential tidak disimpan di database.'
        );
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
