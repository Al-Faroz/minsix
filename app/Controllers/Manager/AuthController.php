<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('auth_logged_in')) {
            return redirect()->to(site_url('manager'));
        }

        return view('manager/auth/login', ['title' => 'Masuk CMS | MIN 6 Jember']);
    }

    public function attempt(): RedirectResponse
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'password' => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $ip = $this->request->getIPAddress();

        $key = 'manager-login-' . hash('sha256', $ip . '|' . mb_strtolower($username));
        if (! service('throttler')->check($key, 5, 60)) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan masuk. Tunggu sebentar lalu coba kembali.');
        }

        $users = new UserModel();
        $user = $users->where('username', $username)->first();

        if ($user === null || (int) $user['is_active'] !== 1 || ! password_verify($password, $user['password_hash'])) {
            $this->audit(null, 'LOGIN_FAILED', 'Percobaan masuk gagal untuk username: ' . $username);
            return redirect()->back()->withInput()
                ->with('error', 'Username atau password tidak sesuai.');
        }

        session()->regenerate(true);
        session()->set([
            'auth_logged_in' => true,
            'auth_user_id' => (int) $user['id'],
            'auth_username' => $user['username'],
            'auth_name' => $user['name'],
            'auth_role' => $user['role'],
        ]);

        $users->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->audit((int) $user['id'], 'LOGIN_SUCCESS', 'Login CMS berhasil.');

        return redirect()->to(site_url('manager'))
            ->with('success', 'Selamat datang, ' . $user['name'] . '.');
    }

    public function logout(): RedirectResponse
    {
        $userId = session()->get('auth_user_id');
        if ($userId !== null) {
            $this->audit((int) $userId, 'LOGOUT', 'Logout CMS.');
        }

        session()->destroy();

        return redirect()->to(site_url('manager/login'))
            ->with('success', 'Anda telah keluar dari CMS.');
    }

    private function audit(?int $userId, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => $userId,
                'action' => $action,
                'module' => 'AUTH',
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
