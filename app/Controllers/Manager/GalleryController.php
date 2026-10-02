<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\GalleryItemModel;
use App\Models\GalleryModel;
use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class GalleryController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $model = new GalleryModel();
        $galleries = $model->orderBy('gallery_date', 'DESC')->orderBy('id', 'DESC')->paginate(20, 'galleries');

        foreach ($galleries as &$gallery) {
            $gallery['item_count'] = $db->table('gallery_items')->where('gallery_id', $gallery['id'])->countAllResults();
        }
        unset($gallery);

        return view('manager/galleries/index', [
            'title' => 'Galeri | CMS MIN 6 JEMBER',
            'pageTitle' => 'Galeri',
            'galleries' => $galleries,
            'pager' => $model->pager,
            'feature' => (new SiteFeatureModel())->where('feature_key', 'gallery')->first(),
            'kabarFeature' => (new SiteFeatureModel())->where('feature_key', 'kabar')->first(),
        ]);
    }

    public function new(): string
    {
        return $this->formView(null, []);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateAlbum()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $model = new GalleryModel();
        $id = $model->insert([
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title')),
            'title' => trim((string) $this->request->getPost('title')),
            'gallery_date' => $this->request->getPost('gallery_date') ?: null,
            'description' => trim((string) $this->request->getPost('description')),
            'cover_media_id' => $this->validImageId($this->request->getPost('cover_media_id')),
            'status' => $status,
            'published_at' => $status === 'PUBLISHED' ? date('Y-m-d H:i:s') : null,
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Album galeri gagal disimpan.');
        }

        $this->audit((int) $id, 'GALLERY_CREATED', 'Album galeri dibuat.');
        return redirect()->to(site_url('manager/galleries/' . $id . '/edit'))
            ->with('success', 'Album dibuat. Sekarang tambahkan foto ke galeri.');
    }

    public function edit(int $id): string
    {
        $gallery = (new GalleryModel())->find($id);
        if (! $gallery) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Galeri tidak ditemukan.');
        }

        $db = db_connect();
        $items = $db->table('gallery_items gi')
            ->select('gi.*, m.original_name, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = gi.media_id')
            ->where('gi.gallery_id', $id)
            ->orderBy('gi.display_order', 'ASC')
            ->orderBy('gi.id', 'ASC')
            ->get()->getResultArray();

        return $this->formView($gallery, $items);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new GalleryModel();
        $gallery = $model->find($id);

        if (! $gallery) {
            return redirect()->to(site_url('manager/galleries'))->with('error', 'Galeri tidak ditemukan.');
        }

        if (! $this->validateAlbum()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = (string) $this->request->getPost('status');
        $publishedAt = $status === 'PUBLISHED'
            ? ($gallery['published_at'] ?: date('Y-m-d H:i:s'))
            : null;

        $updated = $model->update($id, [
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title'), $id),
            'title' => trim((string) $this->request->getPost('title')),
            'gallery_date' => $this->request->getPost('gallery_date') ?: null,
            'description' => trim((string) $this->request->getPost('description')),
            'cover_media_id' => $this->validImageId($this->request->getPost('cover_media_id')),
            'status' => $status,
            'published_at' => $publishedAt,
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($updated === false) {
            return redirect()->back()->withInput()->with('error', 'Galeri gagal diperbarui.');
        }

        $this->audit($id, 'GALLERY_UPDATED', 'Album galeri diperbarui.');
        return redirect()->to(site_url('manager/galleries/' . $id . '/edit'))->with('success', 'Galeri berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new GalleryModel();
        $gallery = $model->find($id);

        if (! $gallery) {
            return redirect()->to(site_url('manager/galleries'))->with('error', 'Galeri tidak ditemukan.');
        }

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/galleries'))->with('error', 'Galeri gagal dihapus.');
        }

        $this->audit($id, 'GALLERY_DELETED', 'Album galeri dihapus: ' . $gallery['title']);

        return redirect()->to(site_url('manager/galleries'))->with('success', 'Album galeri berhasil dihapus. File media tetap tersimpan.');
    }

    public function addItems(int $galleryId): RedirectResponse
    {
        if (! (new GalleryModel())->find($galleryId)) {
            return redirect()->to(site_url('manager/galleries'))->with('error', 'Galeri tidak ditemukan.');
        }

        $mediaIds = $this->request->getPost('media_ids') ?? [];
        if (! is_array($mediaIds) || $mediaIds === []) {
            return redirect()->back()->with('error', 'Pilih minimal satu foto.');
        }

        $media = new MediaModel();
        $items = new GalleryItemModel();
        $added = 0;
        $nextOrder = (int) ((new GalleryItemModel())
            ->selectMax('display_order', 'max_order')
            ->where('gallery_id', $galleryId)
            ->first()['max_order'] ?? 0);

        foreach (array_unique(array_map('intval', $mediaIds)) as $mediaId) {
            if ($mediaId <= 0 || ! $media->where('id', $mediaId)->where('media_type', 'IMAGE')->first()) {
                continue;
            }

            if ($items->where('gallery_id', $galleryId)->where('media_id', $mediaId)->first()) {
                continue;
            }

            $nextOrder += 10;
            $items->insert([
                'gallery_id' => $galleryId,
                'media_id' => $mediaId,
                'caption' => null,
                'display_order' => $nextOrder,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $added++;
        }

        if ($added === 0) {
            return redirect()->back()->with('error', 'Tidak ada foto baru yang ditambahkan. Foto mungkin sudah ada di album.');
        }

        $this->audit($galleryId, 'GALLERY_ITEMS_ADDED', $added . ' foto ditambahkan ke galeri.');
        return redirect()->to(site_url('manager/galleries/' . $galleryId . '/edit'))->with('success', $added . ' foto berhasil ditambahkan.');
    }

    public function updateItem(int $galleryId, int $itemId): RedirectResponse
    {
        $model = new GalleryItemModel();
        $item = $model->where('id', $itemId)->where('gallery_id', $galleryId)->first();

        if (! $item) {
            return redirect()->back()->with('error', 'Item galeri tidak ditemukan.');
        }

        if (! $this->validate([
            'caption' => 'permit_empty|max_length[255]',
            'display_order' => 'permit_empty|integer',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $updated = $model->update($itemId, [
            'caption' => trim((string) $this->request->getPost('caption')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
        ]);

        if ($updated === false) {
            return redirect()->back()->with('error', 'Item galeri gagal diperbarui.');
        }

        $this->audit($galleryId, 'GALLERY_ITEM_UPDATED', 'Item galeri diperbarui.');
        return redirect()->to(site_url('manager/galleries/' . $galleryId . '/edit'))->with('success', 'Item galeri diperbarui.');
    }

    public function deleteItem(int $galleryId, int $itemId): RedirectResponse
    {
        $model = new GalleryItemModel();
        $item = $model->where('id', $itemId)->where('gallery_id', $galleryId)->first();

        if (! $item) {
            return redirect()->back()->with('error', 'Item galeri tidak ditemukan.');
        }

        if ($model->delete($itemId) === false) {
            return redirect()->back()->with('error', 'Foto gagal dilepas dari album.');
        }

        $this->audit($galleryId, 'GALLERY_ITEM_REMOVED', 'Foto dilepas dari album. File media tidak dihapus.');

        return redirect()->to(site_url('manager/galleries/' . $galleryId . '/edit'))->with('success', 'Foto dilepas dari album.');
    }

    private function formView(?array $gallery, array $items): string
    {
        return view('manager/galleries/form', [
            'title' => ($gallery ? 'Edit' : 'Tambah') . ' Galeri | CMS MIN 6 JEMBER',
            'pageTitle' => $gallery ? 'Edit Galeri' : 'Tambah Galeri',
            'gallery' => $gallery,
            'items' => $items,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateAlbum(): bool
    {
        return $this->validate([
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|max_length[200]|alpha_dash',
            'gallery_date' => 'permit_empty|valid_date[Y-m-d]',
            'description' => 'permit_empty|max_length[10000]',
            'cover_media_id' => 'permit_empty|is_natural_no_zero',
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

    private function uniqueSlug(string $requested, string $title, ?int $ignoreId = null): string
    {
        $base = url_title(trim($requested) !== '' ? $requested : $title, '-', true) ?: 'galeri';
        $slug = $base;
        $n = 2;
        $model = new GalleryModel();

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
                'module' => 'GALLERY',
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
