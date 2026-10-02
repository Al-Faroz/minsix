<?php

namespace App\Filters;

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
                ->with('error', 'Silakan masuk untuk mengakses CMS MIN 6 Jember.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
