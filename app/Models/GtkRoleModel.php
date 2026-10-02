<?php

namespace App\Models;

use CodeIgniter\Model;

class GtkRoleModel extends Model
{
    protected $table = 'gtk_roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'role_key', 'role_name', 'category', 'display_order', 'is_active',
    ];
}
