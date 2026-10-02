<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'slug', 'name', 'category', 'summary', 'content', 'primary_media_id',
        'display_order', 'status', 'published_at', 'created_by', 'updated_by',
    ];
}
