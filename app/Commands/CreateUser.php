<?php

namespace App\Commands;

use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateUser extends BaseCommand
{
    protected $group = 'MIN SIX';
    protected $name = 'minsix:user:create';
    protected $description = 'Membuat akun CMS dengan password sementara acak.';
    protected $usage = 'minsix:user:create [username] [name] [role]';

    public function run(array $params)
    {
        $username = trim((string) ($params[0] ?? ''));
        $name = trim((string) ($params[1] ?? ''));
        $role = strtoupper(trim((string) ($params[2] ?? 'ADMIN')));

        if ($username === '') {
            $username = trim((string) CLI::prompt('Username'));
        }
        if ($name === '') {
            $name = trim((string) CLI::prompt('Nama pengguna'));
        }

        if (strlen($username) < 3 || strlen($username) > 100) {
            CLI::error('Username harus 3-100 karakter.');
            return;
        }
        if ($name === '' || strlen($name) > 150) {
            CLI::error('Nama wajib diisi dan maksimal 150 karakter.');
            return;
        }
        if (! in_array($role, ['ADMIN', 'OPERATOR'], true)) {
            CLI::error('Role hanya boleh ADMIN atau OPERATOR.');
            return;
        }

        $users = new UserModel();
        if ($users->where('username', $username)->first() !== null) {
            CLI::error('Username sudah digunakan.');
            return;
        }

        $password = $this->generatePassword(18);
        $id = $users->insert([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $name,
            'role' => $role,
            'is_active' => 1,
        ]);

        if ($id === false) {
            CLI::error('Akun gagal dibuat.');
            return;
        }

        CLI::newLine();
        CLI::write('Akun CMS berhasil dibuat.', 'green');
        CLI::write('Username           : ' . $username);
        CLI::write('Role               : ' . $role);
        CLI::write('Password sementara : ' . $password, 'yellow');
        CLI::write('Login lalu segera ubah password.', 'yellow');
    }

    private function generatePassword(int $length): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $max = strlen($chars) - 1;
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, $max)];
        }

        return $out;
    }
}
