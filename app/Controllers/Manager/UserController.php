<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    private const ROLES = ['ADMIN', 'OPERATOR'];

    public function index(): string
    {
        return view('manager/users/index', [
            'title' => 'Pengguna | CMS MIN 6 JEMBER',
            'pageTitle' => 'Pengguna',
            'users' => (new UserModel())
                ->orderBy('role', 'ASC')
                ->orderBy('is_active', 'DESC')
                ->orderBy('name', 'ASC')
                ->findAll(),
            'currentUserId' => (int) session()->get('auth_user_id'),
        ]);
    }

    public function new(): string
    {
        return view('manager/users/form', [
            'title' => 'Tambah Pengguna | CMS MIN 6 JEMBER',
            'pageTitle' => 'Tambah Pengguna',
            'user' => null,
            'roles' => self::ROLES,
            'currentUserId' => (int) session()->get('auth_user_id'),
        ]);
    }

    public function create(): RedirectResponse
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-z0-9._-]+$/]',
            'name' => 'required|max_length[150]',
            'role' => 'required|in_list[ADMIN,OPERATOR]',
            'password' => 'required|min_length[10]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $users = new UserModel();

        if ($users->where('username', $username)->first() !== null) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $id = $users->insert([
            'username' => $username,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'name' => trim((string) $this->request->getPost('name')),
            'role' => strtoupper(trim((string) $this->request->getPost('role'))),
            'is_active' => 1,
        ]);

        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Akun pengguna gagal dibuat.');
        }

        $this->audit(
            'USER_CREATED',
            (int) $id,
            'Akun dibuat: ' . $username . ' (' . strtoupper(trim((string) $this->request->getPost('role'))) . ').'
        );

        return redirect()->to(site_url('manager/users'))
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $user = (new UserModel())->find($id);

        if ($user === null) {
            return redirect()->to(site_url('manager/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        return view('manager/users/form', [
            'title' => 'Edit Pengguna | CMS MIN 6 JEMBER',
            'pageTitle' => 'Edit Pengguna',
            'user' => $user,
            'roles' => self::ROLES,
            'currentUserId' => (int) session()->get('auth_user_id'),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $users = new UserModel();
        $user = $users->find($id);

        if ($user === null) {
            return redirect()->to(site_url('manager/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-z0-9._-]+$/]',
            'name' => 'required|max_length[150]',
            'role' => 'required|in_list[ADMIN,OPERATOR]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $duplicate = $users->where('username', $username)->where('id !=', $id)->first();

        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $role = strtoupper(trim((string) $this->request->getPost('role')));
        $isActive = (string) $this->request->getPost('is_active') === '1' ? 1 : 0;
        $currentUserId = (int) session()->get('auth_user_id');

        if ($id === $currentUserId && ($role !== 'ADMIN' || $isActive !== 1)) {
            return redirect()->back()->withInput()
                ->with('error', 'Admin tidak dapat menurunkan role atau menonaktifkan akun yang sedang digunakan.');
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $role, $isActive)) {
            return redirect()->back()->withInput()
                ->with('error', 'Perubahan ditolak karena sistem harus memiliki minimal satu Admin aktif.');
        }

        $updated = $users->update($id, [
            'username' => $username,
            'name' => trim((string) $this->request->getPost('name')),
            'role' => $role,
            'is_active' => $isActive,
        ]);

        if ($updated === false) {
            return redirect()->back()->withInput()->with('error', 'Pengguna gagal diperbarui.');
        }

        $this->audit(
            'USER_UPDATED',
            $id,
            'Akun diperbarui: ' . $username . ' (' . $role . ', ' . ($isActive ? 'aktif' : 'nonaktif') . ').'
        );

        return redirect()->to(site_url('manager/users/' . $id . '/edit'))
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $users = new UserModel();
        $user = $users->find($id);

        if ($user === null) {
            return redirect()->to(site_url('manager/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $currentUserId = (int) session()->get('auth_user_id');
        if ($id === $currentUserId) {
            return redirect()->to(site_url('manager/users'))
                ->with('error', 'Akun yang sedang digunakan tidak dapat dinonaktifkan dari daftar pengguna.');
        }

        $nextActive = (int) $user['is_active'] === 1 ? 0 : 1;

        if ($this->wouldRemoveLastActiveAdmin($user, (string) $user['role'], $nextActive)) {
            return redirect()->to(site_url('manager/users'))
                ->with('error', 'Admin aktif terakhir tidak dapat dinonaktifkan.');
        }

        if ($users->update($id, ['is_active' => $nextActive]) === false) {
            return redirect()->to(site_url('manager/users'))->with('error', 'Status pengguna gagal diubah.');
        }

        $this->audit(
            $nextActive ? 'USER_ACTIVATED' : 'USER_DEACTIVATED',
            $id,
            'Status akun ' . $user['username'] . ' diubah menjadi ' . ($nextActive ? 'aktif' : 'nonaktif') . '.'
        );

        return redirect()->to(site_url('manager/users'))
            ->with('success', 'Status pengguna berhasil diubah.');
    }

    public function resetPassword(int $id): RedirectResponse
    {
        $users = new UserModel();
        $user = $users->find($id);

        if ($user === null) {
            return redirect()->to(site_url('manager/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'password' => 'required|min_length[10]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        if ($users->update($id, [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
        ]) === false) {
            return redirect()->back()->with('error', 'Password pengguna gagal direset.');
        }

        $this->audit(
            'USER_PASSWORD_RESET',
            $id,
            'Password akun ' . $user['username'] . ' direset oleh Admin.'
        );

        return redirect()->to(site_url('manager/users/' . $id . '/edit'))
            ->with('success', 'Password pengguna berhasil direset.');
    }

    private function wouldRemoveLastActiveAdmin(array $user, string $nextRole, int $nextActive): bool
    {
        $currentlyActiveAdmin = $user['role'] === 'ADMIN' && (int) $user['is_active'] === 1;
        $willRemainActiveAdmin = $nextRole === 'ADMIN' && $nextActive === 1;

        if (! $currentlyActiveAdmin || $willRemainActiveAdmin) {
            return false;
        }

        return (new UserModel())
            ->where('role', 'ADMIN')
            ->where('is_active', 1)
            ->countAllResults() <= 1;
    }

    private function audit(string $action, int $targetUserId, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'USERS',
                'description' => 'Target user #' . $targetUserId . '. ' . $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit pengguna gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
