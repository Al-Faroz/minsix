<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class FeatureController extends BaseController
{
    private const CAPABILITIES = [
        'kabar' => ['nav' => true, 'home' => false, 'description' => 'Induk Kabar Madrasah. Jika OFF, seluruh Kabar tidak tampil di publik.'],
        'news' => ['nav' => false, 'home' => true, 'description' => 'Berita madrasah. Data tetap tersimpan ketika OFF.'],
        'events' => ['nav' => false, 'home' => true, 'description' => 'Agenda kegiatan mendatang.'],
        'achievements' => ['nav' => false, 'home' => true, 'description' => 'Prestasi peserta didik dan madrasah.'],
        'gallery' => ['nav' => false, 'home' => true, 'description' => 'Galeri foto kegiatan.'],
        'instagram' => ['nav' => false, 'home' => true, 'description' => 'Carousel Instagram resmi @min6jember.'],
        'spmb' => ['nav' => true, 'home' => true, 'description' => 'Menu dan CTA SPMB periode aktif.'],
    ];

    public function index(): string
    {
        $features = (new SiteFeatureModel())->orderBy('id', 'ASC')->findAll();

        return view('manager/features/index', [
            'title' => 'Pengaturan Fitur | CMS MIN 6 Jember',
            'pageTitle' => 'Pengaturan Fitur',
            'features' => $features,
            'capabilities' => self::CAPABILITIES,
        ]);
    }

    public function update(): RedirectResponse
    {
        $model = new SiteFeatureModel();
        $features = $model->findAll();
        $posted = $this->request->getPost('features') ?? [];
        $userId = (int) session()->get('auth_user_id');

        $db = db_connect();
        $db->transStart();

        foreach ($features as $feature) {
            $key = $feature['feature_key'];
            $capability = self::CAPABILITIES[$key] ?? ['nav' => false, 'home' => false];
            $row = is_array($posted[$key] ?? null) ? $posted[$key] : [];

            $model->update((int) $feature['id'], [
                'is_enabled' => isset($row['is_enabled']) ? 1 : 0,
                'show_in_nav' => $capability['nav'] && isset($row['show_in_nav']) ? 1 : 0,
                'show_on_home' => $capability['home'] && isset($row['show_on_home']) ? 1 : 0,
                'updated_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Pengaturan fitur gagal disimpan.');
        }

        $this->audit();

        return redirect()->to(site_url('manager/features'))->with('success', 'Pengaturan fitur berhasil disimpan.');
    }

    private function audit(): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => 'FEATURES_UPDATED',
                'module' => 'FEATURES',
                'description' => 'Konfigurasi ON/OFF dan visibility fitur diperbarui.',
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
