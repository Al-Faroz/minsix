<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('auth_logged_in')) {
            return redirect()
                ->to(site_url('manager/login'))
                ->with('error', 'Silakan masuk untuk mengakses CMS MIN 6 JEMBER.');
        }

        $userId = (int) session()->get('auth_user_id');
        $user = $userId > 0 ? (new UserModel())->find($userId) : null;

        if (
            $user === null
            || (int) $user['is_active'] !== 1
            || ! in_array($user['role'], ['ADMIN', 'OPERATOR'], true)
        ) {
            session()->destroy();

            return redirect()
                ->to(site_url('manager/login'))
                ->with('error', 'Sesi Anda tidak lagi aktif. Silakan masuk kembali.');
        }

        // Keep session identity synchronized with the authoritative user record.
        session()->set([
            'auth_username' => $user['username'],
            'auth_name' => $user['name'],
            'auth_role' => $user['role'],
        ]);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
