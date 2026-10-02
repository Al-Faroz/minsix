<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AccountController extends BaseController
{
    public function password(): string
    {
        return view('manager/account/password', [
            'title' => 'Ubah Password | CMS MIN 6 Jember',
            'pageTitle' => 'Ubah Password',
        ]);
    }

    public function updatePassword(): RedirectResponse
    {
        $rules = [
            'current_password' => 'required|max_length[255]',
            'password' => 'required|min_length[10]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $id = (int) session()->get('auth_user_id');
        $users = new UserModel();
        $user = $users->find($id);

        if ($user === null) {
            session()->destroy();
            return redirect()->to(site_url('manager/login'))
                ->with('error', 'Sesi pengguna tidak valid.');
        }

        if (! password_verify((string) $this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $users->update($id, [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        try {
            (new AuditLogModel())->insert([
                'user_id' => $id,
                'action' => 'PASSWORD_CHANGED',
                'module' => 'ACCOUNT',
                'description' => 'Password akun CMS diubah.',
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }

        session()->regenerate(true);

        return redirect()->to(site_url('manager/account/password'))
            ->with('success', 'Password berhasil diperbarui.');
    }
}
