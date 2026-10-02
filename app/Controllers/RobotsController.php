<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class RobotsController extends BaseController
{
    public function index(): ResponseInterface
    {
        $body = "User-agent: *\n"
            . "Disallow: /manager/\n"
            . "Sitemap: " . site_url('sitemap.xml') . "\n";

        return $this->response
            ->setContentType('text/plain', 'UTF-8')
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setBody($body);
    }
}
