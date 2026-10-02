<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\EventModel;
use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use CodeIgniter\HTTP\RedirectResponse;

class EventController extends BaseController
{
    public function index(): string
    {
        $model = new EventModel();

        return view('manager/events/index', [
            'title' => 'Agenda | CMS MIN 6 JEMBER',
            'pageTitle' => 'Agenda',
            'events' => $model->orderBy('start_at', 'DESC')->paginate(20, 'events'),
            'pager' => $model->pager,
            'feature' => (new SiteFeatureModel())->where('feature_key', 'events')->first(),
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

        $start = $this->dbDateTime((string) $this->request->getPost('start_at'));
        $end = $this->optionalDateTime($this->request->getPost('end_at'));

        if ($end !== null && strtotime($end) < strtotime($start)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai tidak boleh lebih awal dari waktu mulai.');
        }

        $model = new EventModel();
        $id = $model->insert([
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title')),
            'title' => trim((string) $this->request->getPost('title')),
            'summary' => trim((string) $this->request->getPost('summary')),
            'description' => trim((string) $this->request->getPost('description')),
            'location' => trim((string) $this->request->getPost('location')),
            'start_at' => $start,
            'end_at' => $end,
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => (string) $this->request->getPost('status'),
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Agenda gagal disimpan.');
        }

        $this->audit((int) $id, 'EVENT_CREATED', 'Agenda dibuat.');
        return redirect()->to(site_url('manager/events'))->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $event = (new EventModel())->find($id);
        if (! $event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Agenda tidak ditemukan.');
        }

        return $this->formView($event);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new EventModel();
        $event = $model->find($id);

        if (! $event) {
            return redirect()->to(site_url('manager/events'))->with('error', 'Agenda tidak ditemukan.');
        }

        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $start = $this->dbDateTime((string) $this->request->getPost('start_at'));
        $end = $this->optionalDateTime($this->request->getPost('end_at'));

        if ($end !== null && strtotime($end) < strtotime($start)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai tidak boleh lebih awal dari waktu mulai.');
        }

        $updated = $model->update($id, [
            'slug' => $this->uniqueSlug((string) $this->request->getPost('slug'), (string) $this->request->getPost('title'), $id),
            'title' => trim((string) $this->request->getPost('title')),
            'summary' => trim((string) $this->request->getPost('summary')),
            'description' => trim((string) $this->request->getPost('description')),
            'location' => trim((string) $this->request->getPost('location')),
            'start_at' => $start,
            'end_at' => $end,
            'primary_media_id' => $this->validImageId($this->request->getPost('primary_media_id')),
            'status' => (string) $this->request->getPost('status'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        if ($updated === false) {
            return redirect()->back()->withInput()->with('error', 'Agenda gagal diperbarui.');
        }

        $this->audit($id, 'EVENT_UPDATED', 'Agenda diperbarui.');
        return redirect()->to(site_url('manager/events'))->with('success', 'Agenda berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new EventModel();
        $event = $model->find($id);

        if (! $event) {
            return redirect()->to(site_url('manager/events'))->with('error', 'Agenda tidak ditemukan.');
        }

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/events'))->with('error', 'Agenda gagal dihapus.');
        }

        $this->audit($id, 'EVENT_DELETED', 'Agenda dihapus: ' . $event['title']);
        return redirect()->to(site_url('manager/events'))->with('success', 'Agenda berhasil dihapus.');
    }

    private function formView(?array $event): string
    {
        return view('manager/events/form', [
            'title' => ($event ? 'Edit' : 'Tambah') . ' Agenda | CMS MIN 6 JEMBER',
            'pageTitle' => $event ? 'Edit Agenda' : 'Tambah Agenda',
            'event' => $event,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateForm(): bool
    {
        return $this->validate([
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|max_length[200]|alpha_dash',
            'summary' => 'permit_empty|max_length[3000]',
            'description' => 'permit_empty|max_length[30000]',
            'location' => 'permit_empty|max_length[255]',
            'start_at' => 'required|valid_date[Y-m-d\\TH:i]',
            'end_at' => 'permit_empty|valid_date[Y-m-d\\TH:i]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
            'status' => 'required|in_list[DRAFT,PUBLISHED]',
        ]);
    }

    private function uniqueSlug(string $requested, string $title, ?int $ignoreId = null): string
    {
        $base = url_title(trim($requested) !== '' ? $requested : $title, '-', true) ?: 'agenda';
        $slug = $base;
        $n = 2;
        $model = new EventModel();

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

    private function dbDateTime(string $value): string
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    private function optionalDateTime($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $this->dbDateTime($value);
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'EVENT',
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
