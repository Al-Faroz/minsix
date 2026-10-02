<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * Base URL development. Nilai production/development sebenarnya dioverride
     * melalui app.baseURL pada .env.
     */
    public string $baseURL = 'http://localhost:8080/';

    /** @var list<string> */
    public array $allowedHostnames = [];

    /**
     * index.php tidak dipakai pada URL karena front controller sudah berada di
     * root dan .htaccess melakukan rewrite.
     */
    public string $indexPage = '';

    public string $uriProtocol = 'REQUEST_URI';

    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    /**
     * UI aplikasi menggunakan Bahasa Indonesia. Locale framework tetap en agar
     * fallback pesan bawaan CI4 tersedia tanpa dependency translation tambahan.
     */
    public string $defaultLocale = 'en';

    public bool $negotiateLocale = false;

    /** @var list<string> */
    public array $supportedLocales = ['en'];

    public string $appTimezone = 'Asia/Jakarta';

    public string $charset = 'UTF-8';

    /**
     * Development localhost tetap HTTP. Pada production gunakan baseURL HTTPS;
     * pengaturan force HTTPS dapat diaktifkan sesuai konfigurasi hosting/proxy.
     */
    public bool $forceGlobalSecureRequests = false;

    /** @var array<string, string> */
    public array $proxyIPs = [];

    public bool $CSPEnabled = false;
}
