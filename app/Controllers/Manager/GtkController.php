<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\GtkModel;
use App\Models\GtkRoleAssignmentModel;
use App\Models\GtkRoleModel;
use App\Models\MediaModel;
use CodeIgniter\HTTP\RedirectResponse;

class GtkController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $rows = $db->table('gtk g')
            ->select("g.*, m.relative_path AS photo_path, GROUP_CONCAT(r.role_name ORDER BY r.category, r.display_order, r.role_name SEPARATOR ' • ') AS roles", false)
            ->join('media m', 'm.id = g.photo_media_id', 'left')
            ->join('gtk_role_assignments a', 'a.gtk_id = g.id', 'left')
            ->join('gtk_roles r', 'r.id = a.gtk_role_id', 'left')
            ->groupBy('g.id')
            ->orderBy('g.display_order', 'ASC')
            ->orderBy('g.name', 'ASC')
            ->get()->getResultArray();

        return view('manager/gtk/index', [
            'title' => 'GTK | CMS MIN 6 Jember',
            'pageTitle' => 'GTK',
            'gtk' => $rows,
        ]);
    }

    public function new(): string
    {
        return $this->formView(null, []);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('name'));
        if ((new GtkModel())->where('name', $name)->first()) {
            return redirect()->back()->withInput()->with('error', 'Nama GTK yang sama sudah ada. Edit data yang sudah ada jika orangnya sama.');
        }

        $db = db_connect();
        $db->transStart();

        $model = new GtkModel();
        $id = $model->insert([
            'name' => $name,
            'front_title' => trim((string) $this->request->getPost('front_title')),
            'back_title' => trim((string) $this->request->getPost('back_title')),
            'photo_media_id' => $this->validImageId($this->request->getPost('photo_media_id')),
            'short_bio' => trim((string) $this->request->getPost('short_bio')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'created_by' => (int) session()->get('auth_user_id'),
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        $this->syncRoles((int) $id, $this->request->getPost('role_ids') ?? []);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Data GTK gagal disimpan.');
        }

        $this->audit((int) $id, 'GTK_CREATED', 'GTK ditambahkan: ' . $name);

        return redirect()->to(site_url('manager/gtk'))->with('success', 'GTK berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $gtk = (new GtkModel())->find($id);
        if (! $gtk) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('GTK tidak ditemukan.');
        }

        $selected = array_column(
            (new GtkRoleAssignmentModel())->where('gtk_id', $id)->findAll(),
            'gtk_role_id'
        );

        return $this->formView($gtk, array_map('intval', $selected));
    }

    public function update(int $id): RedirectResponse
    {
        $model = new GtkModel();
        $gtk = $model->find($id);

        if (! $gtk) {
            return redirect()->to(site_url('manager/gtk'))->with('error', 'GTK tidak ditemukan.');
        }

        if (! $this->validateForm()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('name'));
        if ($model->where('name', $name)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Nama GTK yang sama sudah ada.');
        }

        $db = db_connect();
        $db->transStart();

        $model->update($id, [
            'name' => $name,
            'front_title' => trim((string) $this->request->getPost('front_title')),
            'back_title' => trim((string) $this->request->getPost('back_title')),
            'photo_media_id' => $this->validImageId($this->request->getPost('photo_media_id')),
            'short_bio' => trim((string) $this->request->getPost('short_bio')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_by' => (int) session()->get('auth_user_id'),
        ]);

        $this->syncRoles($id, $this->request->getPost('role_ids') ?? []);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Data GTK gagal diperbarui.');
        }

        $this->audit($id, 'GTK_UPDATED', 'GTK diperbarui: ' . $name);

        return redirect()->to(site_url('manager/gtk'))->with('success', 'GTK berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new GtkModel();
        $gtk = $model->find($id);

        if (! $gtk) {
            return redirect()->to(site_url('manager/gtk'))->with('error', 'GTK tidak ditemukan.');
        }

        $model->delete($id);
        $this->audit($id, 'GTK_DELETED', 'GTK dihapus: ' . $gtk['name']);

        return redirect()->to(site_url('manager/gtk'))->with('success', 'GTK berhasil dihapus.');
    }

    private function formView(?array $gtk, array $selectedRoles): string
    {
        $roles = (new GtkRoleModel())->where('is_active', 1)->orderBy('category', 'ASC')->orderBy('display_order', 'ASC')->orderBy('role_name', 'ASC')->findAll();
        $grouped = [];
        foreach ($roles as $role) {
            $grouped[$role['category']][] = $role;
        }

        return view('manager/gtk/form', [
            'title' => ($gtk ? 'Edit' : 'Tambah') . ' GTK | CMS MIN 6 Jember',
            'pageTitle' => $gtk ? 'Edit GTK' : 'Tambah GTK',
            'gtk' => $gtk,
            'roleGroups' => $grouped,
            'selectedRoles' => $selectedRoles,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validateForm(): bool
    {
        return $this->validate([
            'name' => 'required|min_length[2]|max_length[180]',
            'front_title' => 'permit_empty|max_length[50]',
            'back_title' => 'permit_empty|max_length[120]',
            'photo_media_id' => 'permit_empty|is_natural_no_zero',
            'short_bio' => 'permit_empty|max_length[3000]',
            'display_order' => 'permit_empty|integer',
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

    private function syncRoles(int $gtkId, array $roleIds): void
    {
        $assignments = new GtkRoleAssignmentModel();
        $assignments->where('gtk_id', $gtkId)->delete();

        $validRoles = [];
        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            if ($roleId > 0 && (new GtkRoleModel())->find($roleId)) {
                $validRoles[] = $roleId;
            }
        }

        foreach ($validRoles as $order => $roleId) {
            $assignments->insert([
                'gtk_id' => $gtkId,
                'gtk_role_id' => $roleId,
                'display_order' => $order,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'GTK',
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
