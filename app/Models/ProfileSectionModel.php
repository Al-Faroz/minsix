<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileSectionModel extends Model
{
    protected $table = 'profile_sections';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'section_key', 'title', 'body', 'content_json', 'primary_media_id',
        'display_order', 'updated_by', 'updated_at',
    ];
}
