<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Encryption\Encryption;

class GenerateEncryptionKey extends BaseCommand
{
    protected $group = 'MIN SIX';
    protected $name = 'minsix:key:generate';
    protected $description = 'Generate encryption.key untuk credential integrasi terenkripsi.';

    public function run(array $params)
    {
        $key = Encryption::createKey(32);
        if ($key === false) {
            CLI::error('Gagal membuat encryption key.');
            return;
        }
        CLI::write('Salin baris berikut ke file .env. Jangan commit key ini.', 'yellow');
        CLI::newLine();
        CLI::write('encryption.key = hex2bin:' . bin2hex($key), 'green');
    }
}
