<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (method_exists($response, 'removeHeader')) {
            $response->removeHeader('X-Powered-By');
        }

        if (ENVIRONMENT === 'production') {
            $response->setHeader(
                'Content-Security-Policy',
                "default-src 'self'; "
                . "base-uri 'self'; "
                . "form-action 'self'; "
                . "frame-ancestors 'self'; "
                . "object-src 'none'; "
                . "script-src 'self' 'unsafe-inline'; "
                . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
                . "font-src 'self' data: https://fonts.gstatic.com; "
                . "img-src 'self' data: https:; "
                . "frame-src https://www.google.com https://maps.google.com; "
                . "connect-src 'self'; "
                . "manifest-src 'self'; "
                . "upgrade-insecure-requests"
            );

            if (method_exists($request, 'isSecure') && $request->isSecure()) {
                $response->setHeader('Strict-Transport-Security', 'max-age=15552000');
            }
        }

        return $response;
    }
}
