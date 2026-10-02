<?php

namespace App\Models;

use CodeIgniter\Model;

class GtkModel extends Model
{
    protected $table = 'gtk';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'name', 'front_title', 'back_title', 'photo_media_id', 'short_bio',
        'display_order', 'is_active', 'created_by', 'updated_by',
    ];
}
