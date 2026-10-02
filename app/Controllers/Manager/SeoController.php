<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\SiteSettingModel;
use CodeIgniter\HTTP\RedirectResponse;

class SeoController extends BaseController
{
    private const KEYS = [
        'seo_default_title' => 'string',
        'seo_default_description' => 'text',
        'seo_default_og_media_id' => 'integer',
        'seo_canonical_base_url' => 'string',
    ];

    public function index(): string
    {
        return view('manager/seo/index', [
            'title' => 'SEO | CMS MIN 6 Jember',
            'pageTitle' => 'SEO',
            'settings' => (new SiteSettingModel())->valuesByKey(),
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function update(): RedirectResponse
    {
        if (! $this->validate([
            'seo_default_title' => 'permit_empty|max_length[255]',
            'seo_default_description' => 'permit_empty|max_length[320]',
            'seo_default_og_media_id' => 'permit_empty|is_natural_no_zero',
            'seo_canonical_base_url' => 'permit_empty|valid_url_strict|max_length[500]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ogId = trim((string) $this->request->getPost('seo_default_og_media_id'));
        if ($ogId !== '' && ! (new MediaModel())->where('id', (int) $ogId)->where('media_type', 'IMAGE')->first()) {
            return redirect()->back()->withInput()->with('error', 'Default OG image tidak valid.');
        }

        $values = [
            'seo_default_title' => trim((string) $this->request->getPost('seo_default_title')),
            'seo_default_description' => trim((string) $this->request->getPost('seo_default_description')),
            'seo_default_og_media_id' => $ogId,
            'seo_canonical_base_url' => rtrim(trim((string) $this->request->getPost('seo_canonical_base_url')), '/'),
        ];

        $model = new SiteSettingModel();
        $db = db_connect();
        $db->transBegin();

        try {
            foreach (self::KEYS as $key => $type) {
                $existing = $model->where('setting_key', $key)->first();
                $data = [
                    'setting_key' => $key,
                    'setting_value' => $values[$key],
                    'value_type' => $type,
                    'is_public' => 1,
                    'updated_by' => (int) session()->get('auth_user_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $result = $existing
                    ? $model->update((int) $existing['id'], $data)
                    : $model->insert($data);

                if ($result === false) {
                    throw new \RuntimeException('Gagal menyimpan SEO global.');
                }
            }

            if (! $db->transStatus()) {
                throw new \RuntimeException('Transaksi SEO global gagal.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'SEO global gagal disimpan: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'SEO global gagal disimpan.');
        }

        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => 'SEO_SETTINGS_UPDATED',
                'module' => 'SEO',
                'description' => 'Pengaturan SEO global diperbarui.',
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit SEO gagal: {message}', ['message' => $e->getMessage()]);
        }

        return redirect()->to(site_url('manager/seo'))->with('success', 'Pengaturan SEO berhasil disimpan.');
    }
}
