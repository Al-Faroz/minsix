<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('auth_logged_in')) {
            return redirect()->to(site_url('manager/login'));
        }

        $userId = (int) session()->get('auth_user_id');
        $user = $userId > 0 ? (new UserModel())->find($userId) : null;

        if ($user === null || (int) $user['is_active'] !== 1) {
            session()->destroy();

            return redirect()
                ->to(site_url('manager/login'))
                ->with('error', 'Sesi Anda tidak lagi aktif. Silakan masuk kembali.');
        }

        session()->set([
            'auth_username' => $user['username'],
            'auth_name' => $user['name'],
            'auth_role' => $user['role'],
        ]);

        if ($user['role'] !== 'ADMIN') {
            return redirect()
                ->to(site_url('manager'))
                ->with('error', 'Menu tersebut hanya dapat diakses oleh Admin.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
