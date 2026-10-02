<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\SiteSettingModel;
use CodeIgniter\HTTP\RedirectResponse;

class SettingsController extends BaseController
{
    private const FIELDS = [
        'site_name' => 'string',
        'site_tagline' => 'string',
        'address' => 'text',
        'phone' => 'string',
        'whatsapp' => 'string',
        'email' => 'string',
        'instagram_username' => 'string',
        'instagram_url' => 'string',
        'google_maps_embed' => 'text',
    ];

    public function index(): string
    {
        return view('manager/settings/index', [
            'title' => 'Pengaturan Website | CMS MIN 6 JEMBER',
            'pageTitle' => 'Pengaturan Website',
            'settings' => (new SiteSettingModel())->valuesByKey(),
        ]);
    }

    public function update(): RedirectResponse
    {
        $rules = [
            'site_name' => 'required|min_length[3]|max_length[180]',
            'site_tagline' => 'permit_empty|max_length[255]',
            'address' => 'permit_empty|max_length[1000]',
            'phone' => 'permit_empty|max_length[80]',
            'whatsapp' => 'permit_empty|max_length[80]',
            'email' => 'permit_empty|valid_email|max_length[180]',
            'instagram_username' => 'permit_empty|max_length[120]',
            'instagram_url' => 'permit_empty|valid_url_strict|max_length[500]',
            'google_maps_embed' => 'permit_empty|max_length[3000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new SiteSettingModel();
        $db = db_connect();
        $db->transStart();

        foreach (self::FIELDS as $key => $type) {
            $value = trim((string) $this->request->getPost($key));
            $existing = $model->where('setting_key', $key)->first();
            $data = [
                'setting_key' => $key,
                'setting_value' => $value,
                'value_type' => $type,
                'is_public' => 1,
                'updated_by' => (int) session()->get('auth_user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $model->update((int) $existing['id'], $data);
            } else {
                $model->insert($data);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pengaturan gagal disimpan.');
        }

        $this->audit('SETTINGS_UPDATED', 'Pengaturan website diperbarui.');

        return redirect()->to(site_url('manager/settings'))->with('success', 'Pengaturan website berhasil disimpan.');
    }

    private function audit(string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'SETTINGS',
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
