<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\ProfileSectionModel;
use CodeIgniter\HTTP\RedirectResponse;

class ProfileController extends BaseController
{
    public function index(): string
    {
        return view('manager/profile/index', [
            'title' => 'Profil | CMS MIN 6 JEMBER',
            'pageTitle' => 'Profil',
            'sections' => (new ProfileSectionModel())->orderBy('display_order', 'ASC')->findAll(),
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new ProfileSectionModel();
        $section = $model->find($id);

        if (! $section) {
            return redirect()->to(site_url('manager/profile'))->with('error', 'Section Profil tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'body' => 'permit_empty|max_length[30000]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mediaId = $this->request->getPost('primary_media_id');
        $mediaId = $mediaId !== null && $mediaId !== '' ? (int) $mediaId : null;

        if ($mediaId && ! (new MediaModel())->where('id', $mediaId)->where('media_type', 'IMAGE')->first()) {
            return redirect()->back()->withInput()->with('error', 'Foto yang dipilih tidak valid.');
        }

        $model->update($id, [
            'title' => trim((string) $this->request->getPost('title')),
            'body' => trim((string) $this->request->getPost('body')),
            'primary_media_id' => $mediaId,
            'updated_by' => (int) session()->get('auth_user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit($id, 'PROFILE_UPDATED', 'Section Profil diperbarui: ' . $section['section_key']);

        return redirect()->to(site_url('manager/profile'))->with('success', 'Konten Profil berhasil diperbarui.');
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'PROFILE',
                'record_id' => $id,
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
