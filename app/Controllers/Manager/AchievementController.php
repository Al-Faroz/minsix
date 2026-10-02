<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AchievementModel;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class AchievementController extends BaseController
{
    public function index(): string
    {
        $model = new AchievementModel();

        return view('manager/achievements/index', [
            'title' => 'Prestasi | CMS MIN 6 Jember',
            'pageTitle' => 'Prestasi',
            'achievements' => $model->orderBy('achievement_date', 'DESC')->orderBy('id', 'DESC')->paginate(20, 'achievements'),
            'pager' => $model->pager,
            'feature' => (new SiteFeatureModel())->where('feature_key', 'achievements')->first(),
            'kabarFeature' => (new SiteFeatureModel())->where('feature_key', 'kabar')->first(),
        ]);
    }

    public function new(): string
    {
        return $this->formView(null);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $model = new AchievementModel();
        $id = $model->insert([
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title')),
            'title' => trim((string) $this->request->getPost('title')),
            'participant_name' => trim((string) $this->request->getPost('participant_name')),
            'field_name' => trim((string) $this->request->getPost('field_name')),
            'award' => trim((string) $this->request->getPost('award')),
            'level' => trim((string) $this->request->getPost('level')),
            'organizer' => trim((string) $this->request->getPost('organizer')),
            'achievement_date' => $this->request->getPost('achievement_date') ?: null,
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => trim((string) $this->request->getPost('content')),
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? date('Y-m-d H:i:s') : null,
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Prestasi gagal disimpan.');
        }

        $this->audit((int) $id, 'ACHIEVEMENT_CREATED', 'Prestasi dibuat.');
        return redirect()->to(site_url('manager/achievements'))->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $achievement = (new AchievementModel())->find($id);
        if (! $achievement) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan.');
        }

        return $this->formView($achievement);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new AchievementModel();
        $achievement = $model->find($id);

        if (! $achievement) {
            return redirect()->to(site_url('manager/achievements'))->with('error', 'Prestasi tidak ditemukan.');
        }

        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $publishedAt = $status === 'PUBLISHED'
            ? ($achievement['published_at'] ?: date('Y-m-d H:i:s'))
            : null;

        $model->update($id, [
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title'), $id),
            'title' => trim((string) $this->request->getPost('title')),
            'participant_name' => trim((string) $this->request->getPost('participant_name')),
            'field_name' => trim((string) $this->request->getPost('field_name')),
            'award' => trim((string) $this->request->getPost('award')),
            'level' => trim((string) $this->request->getPost('level')),
            'organizer' => trim((string) $this->request->getPost('organizer')),
            'achievement_date' => $this->request->getPost('achievement_date') ?: null,
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => trim((string) $this->request->getPost('content')),
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => $status,
            'published_at' => $publishedAt,
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        $this->audit($id, 'ACHIEVEMENT_UPDATED', 'Prestasi diperbarui.');
        return redirect()->to(site_url('manager/achievements'))->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new AchievementModel();
        $achievement = $model->find($id);

        if (! $achievement) {
            return redirect()->to(site_url('manager/achievements'))->with('error', 'Prestasi tidak ditemukan.');
        }

        $model->delete($id);
        $this->audit($id, 'ACHIEVEMENT_DELETED', 'Prestasi dihapus: ' . $achievement['title']);
        return redirect()->to(site_url('manager/achievements'))->with('success', 'Prestasi berhasil dihapus.');
    }

    private function formView(?array $achievement): string
    {
        return view('manager/achievements/form', [
            'title' => ($achievement ? 'Edit' : 'Tambah') . ' Prestasi | CMS MIN 6 Jember',
            'pageTitle' => $achievement ? 'Edit Prestasi' : 'Tambah Prestasi',
            'achievement' => $achievement,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateForm(): bool
    {
        return $this->validate([
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|max_length[200]|alpha_dash',
            'participant_name' => 'permit_empty|max_length[255]',
            'field_name' => 'permit_empty|max_length[150]',
            'award' => 'permit_empty|max_length[150]',
            'level' => 'permit_empty|max_length[100]',
            'organizer' => 'permit_empty|max_length[255]',
            'achievement_date' => 'permit_empty|valid_date[Y-m-d]',
            'summary' => 'permit_empty|max_length[3000]',
            'content' => 'permit_empty|max_length[30000]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
            'status' => 'required|in_list[DRAFT,PUBLISHED]',
        ]);
    }

    private function uniqueSlug(string $requested, string $title, ?int $ignoreId = null): string
    {
        $base = url_title(trim($requested) !== '' ? $requested : $title, '-', true) ?: 'prestasi';
        $slug = $base;
        $n = 2;
        $model = new AchievementModel();

        while (true) {
            $query = $model->where('slug', $slug);
            if ($ignoreId !== null) {
                $query->where('id !=', $ignoreId);
            }
            if ($query->first() === null) {
                return $slug;
            }
            $slug = $base . '-' . $n++;
        }
    }

    private function validImageId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = (int) $value;
        return (new MediaModel())->where('id', $id)->where('media_type', 'IMAGE')->first() ? $id : null;
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'ACHIEVEMENT',
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
