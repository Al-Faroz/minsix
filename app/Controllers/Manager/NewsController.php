<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\NewsModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class NewsController extends BaseController
{
    public function index(): string
    {
        $model = new NewsModel();

        return view('manager/news/index', [
            'title' => 'Berita | CMS MIN 6 Jember',
            'pageTitle' => 'Berita',
            'news' => $model->orderBy('published_at', 'DESC')->orderBy('id', 'DESC')->paginate(20, 'news'),
            'pager' => $model->pager,
            'feature' => (new SiteFeatureModel())->where('feature_key', 'news')->first(),
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
        $model = new NewsModel();
        $id = $model->insert([
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title')),
            'title' => trim((string) $this->request->getPost('title')),
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => trim((string) $this->request->getPost('content')),
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? $this->dateTimeOrNow($this->request->getPost('published_at')) : null,
            'meta_title' => trim((string) $this->request->getPost('meta_title')),
            'meta_description' => trim((string) $this->request->getPost('meta_description')),
            'og_media_id' => $this->validImageId($this->request->getPost('og_media_id')),
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Berita gagal disimpan.');
        }

        $this->audit((int) $id, 'NEWS_CREATED', 'Berita dibuat.');
        return redirect()->to(site_url('manager/news'))->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $news = (new NewsModel())->find($id);
        if (! $news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        return $this->formView($news);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new NewsModel();
        $news = $model->find($id);

        if (! $news) {
            return redirect()->to(site_url('manager/news'))->with('error', 'Berita tidak ditemukan.');
        }

        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $publishedAt = null;
        if ($status === 'PUBLISHED') {
            $publishedAt = $this->dateTimeOrNow($this->request->getPost('published_at'), $news['published_at']);
        }

        $model->update($id, [
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title'), $id),
            'title' => trim((string) $this->request->getPost('title')),
            'summary' => trim((string) $this->request->getPost('summary')),
            'content' => trim((string) $this->request->getPost('content')),
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => $status,
            'published_at' => $publishedAt,
            'meta_title' => trim((string) $this->request->getPost('meta_title')),
            'meta_description' => trim((string) $this->request->getPost('meta_description')),
            'og_media_id' => $this->validImageId($this->request->getPost('og_media_id')),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        $this->audit($id, 'NEWS_UPDATED', 'Berita diperbarui.');
        return redirect()->to(site_url('manager/news'))->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new NewsModel();
        $news = $model->find($id);

        if (! $news) {
            return redirect()->to(site_url('manager/news'))->with('error', 'Berita tidak ditemukan.');
        }

        $model->delete($id);
        $this->audit($id, 'NEWS_DELETED', 'Berita dihapus: ' . $news['title']);
        return redirect()->to(site_url('manager/news'))->with('success', 'Berita berhasil dihapus.');
    }

    private function formView(?array $news): string
    {
        return view('manager/news/form', [
            'title' => ($news ? 'Edit' : 'Tambah') . ' Berita | CMS MIN 6 Jember',
            'pageTitle' => $news ? 'Edit Berita' : 'Tambah Berita',
            'newsItem' => $news,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateForm(): bool
    {
        return $this->validate([
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|max_length[200]|alpha_dash',
            'summary' => 'permit_empty|max_length[3000]',
            'content' => 'required|min_length[3]|max_length[60000]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
            'status' => 'required|in_list[DRAFT,PUBLISHED]',
            'published_at' => 'permit_empty|valid_date[Y-m-d\\TH:i]',
            'meta_title' => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty|max_length[320]',
            'og_media_id' => 'permit_empty|is_natural_no_zero',
        ]);
    }

    private function uniqueSlug(string $requested, string $title, ?int $ignoreId = null): string
    {
        $base = url_title(trim($requested) !== '' ? $requested : $title, '-', true) ?: 'berita';
        $slug = $base;
        $n = 2;
        $model = new NewsModel();

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
                'module' => 'NEWS',
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
