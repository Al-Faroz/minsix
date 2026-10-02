<?php

namespace App\Commands;

use App\Services\InstagramSyncService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstagramSync extends BaseCommand
{
    protected $group = 'MIN SIX';
    protected $name = 'instagram:sync';
    protected $description = 'Periksa lifecycle token lalu sinkronkan cache Instagram dan carousel homepage.';

    public function run(array $params)
    {
        $result = (new InstagramSyncService())->sync();

        if (! ($result['ok'] ?? false)) {
            CLI::error((string) ($result['message'] ?? 'Sinkronisasi Instagram gagal.'));
            return;
        }

        if ($result['skipped'] ?? false) {
            CLI::write((string) $result['message'], 'yellow');
            return;
        }

        CLI::write((string) $result['message'], 'green');
    }
}
