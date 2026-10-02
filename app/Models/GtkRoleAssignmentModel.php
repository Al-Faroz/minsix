<?php

namespace App\Models;

use CodeIgniter\Model;

class GtkRoleAssignmentModel extends Model
{
    protected $table = 'gtk_role_assignments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'gtk_id', 'gtk_role_id', 'role_label_override', 'display_order', 'created_at',
    ];
}
