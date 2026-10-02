<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\InstagramPostModel;
use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class InstagramContentController extends BaseController
{
    public function index(): string
    {
        $model = new InstagramPostModel();

        $manual = $model->where('source', 'MANUAL')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('published_at', 'DESC')
            ->findAll();

        $api = (new InstagramPostModel())->where('source', 'API')
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(24);

        return view('manager/instagram/index', [
            'title' => 'Instagram Content | CMS MIN 6 Jember',
            'pageTitle' => 'Instagram Content',
            'manualPosts' => $manual,
            'apiPosts' => $api,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
            'feature' => (new SiteFeatureModel())->where('feature_key', 'instagram')->first(),
        ]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateManual()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mediaId = (int) $this->request->getPost('local_media_id');
        if (! $this->validImage($mediaId)) {
            return redirect()->back()->withInput()->with('error', 'Gambar fallback tidak valid.');
        }

        $model = new InstagramPostModel();
        $id = $model->insert([
            'source' => 'MANUAL',
            'instagram_media_id' => null,
            'permalink' => trim((string) $this->request->getPost('permalink')),
            'caption' => trim((string) $this->request->getPost('caption')),
            'media_type' => 'IMAGE',
            'media_url' => null,
            'thumbnail_url' => null,
            'local_media_id' => $mediaId,
            'published_at' => $this->dateTimeOrNow($this->request->getPost('published_at')),
            'is_fallback' => 1,
            'is_visible' => $this->request->getPost('is_visible') ? 1 : 0,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'fetched_at' => null,
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Fallback Instagram gagal ditambahkan.');
        }

        $this->audit((int) $id, 'INSTAGRAM_MANUAL_CREATED', 'Fallback Instagram manual ditambahkan.');
        return redirect()->to(site_url('manager/instagram'))->with('success', 'Fallback Instagram berhasil ditambahkan.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new InstagramPostModel();
        $post = $model->find($id);

        if (! $post || $post['source'] !== 'MANUAL') {
            return redirect()->to(site_url('manager/instagram'))->with('error', 'Hanya konten MANUAL yang dapat diedit.');
        }

        if (! $this->validateManual()) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $mediaId = (int) $this->request->getPost('local_media_id');
        if (! $this->validImage($mediaId)) {
            return redirect()->back()->with('error', 'Gambar fallback tidak valid.');
        }

        $updated = $model->update($id, [
            'permalink' => trim((string) $this->request->getPost('permalink')),
            'caption' => trim((string) $this->request->getPost('caption')),
            'local_media_id' => $mediaId,
            'published_at' => $this->dateTimeOrNow($this->request->getPost('published_at'), $post['published_at']),
            'is_visible' => $this->request->getPost('is_visible') ? 1 : 0,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
        ]);

        if ($updated === false) {
            return redirect()->back()->with('error', 'Fallback Instagram gagal diperbarui.');
        }

        $this->audit($id, 'INSTAGRAM_MANUAL_UPDATED', 'Fallback Instagram manual diperbarui.');
        return redirect()->to(site_url('manager/instagram'))->with('success', 'Fallback Instagram berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new InstagramPostModel();
        $post = $model->find($id);

        if (! $post || $post['source'] !== 'MANUAL') {
            return redirect()->to(site_url('manager/instagram'))->with('error', 'Hanya konten MANUAL yang dapat dihapus.');
        }

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/instagram'))->with('error', 'Fallback Instagram gagal dihapus.');
        }

        $this->audit($id, 'INSTAGRAM_MANUAL_DELETED', 'Fallback Instagram manual dihapus.');
        return redirect()->to(site_url('manager/instagram'))->with('success', 'Fallback Instagram berhasil dihapus.');
    }

    private function validateManual(): bool
    {
        return $this->validate([
            'local_media_id' => 'required|is_natural_no_zero',
            'permalink' => 'permit_empty|valid_url_strict|max_length[500]',
            'caption' => 'permit_empty|max_length[5000]',
            'published_at' => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'sort_order' => 'permit_empty|integer',
        ]);
    }

    private function validImage(int $id): bool
    {
        return $id > 0
            && (new MediaModel())->where('id', $id)->where('media_type', 'IMAGE')->first() !== null;
    }

    private function dateTimeOrNow($value, ?string $fallback = null): string
    {
        $value = trim((string) $value);
        if ($value !== '') {
            return date('Y-m-d H:i:s', strtotime($value));
        }

        return $fallback ?: date('Y-m-d H:i:s');
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'INSTAGRAM',
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
