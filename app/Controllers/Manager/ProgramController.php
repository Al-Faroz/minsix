<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Libraries\SafeHtml;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\ProgramModel;
use CodeIgniter\HTTP\RedirectResponse;

class ProgramController extends BaseController
{
    private const CATEGORIES = [
        'Pembelajaran',
        'Pembiasaan & Karakter',
        'Keagamaan',
        "Yanbu'a",
        'Ekstrakurikuler',
        'Pengembangan Prestasi',
    ];

    public function index(): string
    {
        return view('manager/programs/index', [
            'title' => 'Program | CMS MIN 6 Jember',
            'pageTitle' => 'Program',
            'programs' => (new ProgramModel())->orderBy('display_order', 'ASC')->orderBy('name', 'ASC')->findAll(),
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

        $model = new ProgramModel();
        $slug = $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('name'));
        $status = (string) $this->request->getPost('status');
        $mediaId = $this->validImageId($this->request->getPost('primary_media_id'));

        $id = $model->insert([
            'slug' => $slug,
            'name' => trim((string) $this->request->getPost('name')),
            'category' => (string) $this->request->getPost('category'),
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => SafeHtml::sanitize((string) $this->request->getPost('content')),
            'primary_media_id' => $mediaId,
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? date('Y-m-d H:i:s') : null,
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Program gagal disimpan.');
        }

        $this->audit((int) $id, 'PROGRAM_CREATED', 'Program dibuat.');

        return redirect()->to(site_url('manager/programs'))->with('success', 'Program berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $program = (new ProgramModel())->find($id);
        if (! $program) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        return $this->formView($program);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new ProgramModel();
        $program = $model->find($id);

        if (! $program) {
            return redirect()->to(site_url('manager/programs'))->with('error', 'Program tidak ditemukan.');
        }

        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $publishedAt = $program['published_at'];
        if ($status === 'PUBLISHED' && empty($publishedAt)) {
            $publishedAt = date('Y-m-d H:i:s');
        }
        if ($status === 'DRAFT') {
            $publishedAt = null;
        }

        $updated = $model->update($id, [
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('name'), $id),
            'name' => trim((string) $this->request->getPost('name')),
            'category' => (string) $this->request->getPost('category'),
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => SafeHtml::sanitize((string) $this->request->getPost('content')),
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'status' => $status,
            'published_at' => $publishedAt,
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($updated === false) {
            return redirect()->back()->withInput()->with('error', 'Program gagal diperbarui.');
        }

        $this->audit($id, 'PROGRAM_UPDATED', 'Program diperbarui.');

        return redirect()->to(site_url('manager/programs'))->with('success', 'Program berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new ProgramModel();
        $program = $model->find($id);

        if (! $program) {
            return redirect()->to(site_url('manager/programs'))->with('error', 'Program tidak ditemukan.');
        }

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/programs'))->with('error', 'Program gagal dihapus.');
        }

        $this->audit($id, 'PROGRAM_DELETED', 'Program dihapus: ' . $program['name']);

        return redirect()->to(site_url('manager/programs'))->with('success', 'Program berhasil dihapus.');
    }

    private function formView(?array $program): string
    {
        return view('manager/programs/form', [
            'title' => ($program ? 'Edit' : 'Tambah') . ' Program | CMS MIN 6 Jember',
            'pageTitle' => $program ? 'Edit Program' : 'Tambah Program',
            'program' => $program,
            'categories' => self::CATEGORIES,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateForm(): bool
    {
        return $this->validate([
            'name' => 'required|min_length[2]|max_length[200]',
            'slug' => 'permit_empty|max_length[180]|alpha_dash',
            'category' => 'required|in_list[' . implode(',', self::CATEGORIES) . ']',
            'summary' => 'permit_empty|max_length[3000]',
            'content' => 'permit_empty|max_length[30000]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
            'display_order' => 'permit_empty|integer',
            'status' => 'required|in_list[DRAFT,PUBLISHED]',
        ]);
    }

    private function validImageId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;
        return (new MediaModel())->where('id', $id)->where('media_type', 'IMAGE')->first() ? $id : null;
    }

    private function uniqueSlug(string $requested, string $name, ?int $ignoreId = null): string
    {
        $base = url_title(trim($requested) !== '' ? $requested : $name, '-', true);
        $base = $base !== '' ? $base : 'program';
        $slug = $base;
        $n = 2;
        $model = new ProgramModel();

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

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'PROGRAM',
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
