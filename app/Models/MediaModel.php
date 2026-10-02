<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'original_name', 'stored_name', 'relative_path', 'mime_type', 'extension',
        'file_size', 'width', 'height', 'alt_text', 'caption', 'media_type',
        'created_by', 'created_at',
    ];
}
