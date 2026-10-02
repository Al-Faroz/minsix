<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'app_users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'username', 'password_hash', 'name', 'role', 'is_active', 'last_login_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
