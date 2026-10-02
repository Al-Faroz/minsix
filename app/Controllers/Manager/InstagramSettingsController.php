<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\InstagramPostModel;
use App\Models\SiteSettingModel;
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
            'apiReady' => $this->apiReady(),
            'apiState' => $this->apiState(),
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

    private function apiReady(): bool
    {
        $state = $this->apiState();
        return ! in_array(false, $state, true);
    }

    private function apiState(): array
    {
        return [
            'base_url' => trim((string) env('instagram.apiBaseUrl', '')) !== '',
            'api_version' => trim((string) env('instagram.apiVersion', '')) !== '',
            'user_id' => trim((string) env('instagram.userId', '')) !== '',
            'access_token' => trim((string) env('instagram.accessToken', '')) !== '',
        ];
    }

    private function audit(): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => 'INSTAGRAM_CONFIG_UPDATED',
                'module' => 'INSTAGRAM_CONFIG',
                'description' => 'Mode source dan jumlah carousel Instagram diperbarui. Credential tidak disimpan di database.',
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
