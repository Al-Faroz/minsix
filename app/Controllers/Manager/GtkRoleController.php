<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\GtkRoleAssignmentModel;
use App\Models\GtkRoleModel;
use CodeIgniter\HTTP\RedirectResponse;

class GtkRoleController extends BaseController
{
    private const CATEGORIES = ['LEADERSHIP','CLASS_TEACHER','SUBJECT_TEACHER','STAFF'];

    public function index(): string
    {
        return view('manager/gtk/roles', [
            'title' => 'Jabatan GTK | CMS MIN 6 Jember',
            'pageTitle' => 'Jabatan GTK',
            'roles' => (new GtkRoleModel())->orderBy('category', 'ASC')->orderBy('display_order', 'ASC')->orderBy('role_name', 'ASC')->findAll(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateRole()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('role_name'));
        $model = new GtkRoleModel();

        if ($model->where('role_name', $name)->first()) {
            return redirect()->back()->withInput()->with('error', 'Nama jabatan sudah ada.');
        }

        $id = $model->insert([
            'role_key' => $this->uniqueKey($name),
            'role_name' => $name,
            'category' => (string) $this->request->getPost('category'),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'is_active' => 1,
        ]);

        $this->audit((int) $id, 'GTK_ROLE_CREATED', 'Jabatan GTK dibuat: ' . $name);

        return redirect()->to(site_url('manager/gtk-roles'))->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new GtkRoleModel();
        $role = $model->find($id);

        if (! $role) {
            return redirect()->to(site_url('manager/gtk-roles'))->with('error', 'Jabatan tidak ditemukan.');
        }

        if (! $this->validateRole()) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $name = trim((string) $this->request->getPost('role_name'));
        $duplicate = $model->where('role_name', $name)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->back()->with('error', 'Nama jabatan sudah digunakan.');
        }

        $model->update($id, [
            'role_name' => $name,
            'category' => (string) $this->request->getPost('category'),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->audit($id, 'GTK_ROLE_UPDATED', 'Jabatan GTK diperbarui: ' . $name);

        return redirect()->to(site_url('manager/gtk-roles'))->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new GtkRoleModel();
        $role = $model->find($id);

        if (! $role) {
            return redirect()->to(site_url('manager/gtk-roles'))->with('error', 'Jabatan tidak ditemukan.');
        }

        $used = (new GtkRoleAssignmentModel())->where('gtk_role_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->to(site_url('manager/gtk-roles'))->with('error', 'Jabatan masih digunakan oleh GTK dan tidak dapat dihapus.');
        }

        $model->delete($id);
        $this->audit($id, 'GTK_ROLE_DELETED', 'Jabatan GTK dihapus: ' . $role['role_name']);

        return redirect()->to(site_url('manager/gtk-roles'))->with('success', 'Jabatan berhasil dihapus.');
    }

    private function validateRole(): bool
    {
        return $this->validate([
            'role_name' => 'required|min_length[2]|max_length[150]',
            'category' => 'required|in_list[' . implode(',', self::CATEGORIES) . ']',
            'display_order' => 'permit_empty|integer',
        ]);
    }

    private function uniqueKey(string $name): string
    {
        $base = strtoupper(str_replace('-', '_', url_title($name, '-', true)));
        $base = preg_replace('/[^A-Z0-9_]/', '', $base) ?: 'ROLE';
        $key = $base;
        $n = 2;
        $model = new GtkRoleModel();

        while ($model->where('role_key', $key)->first() !== null) {
            $key = $base . '_' . $n++;
        }

        return $key;
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'GTK_ROLE',
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
