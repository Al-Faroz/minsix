<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table = 'galleries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'slug', 'title', 'gallery_date', 'description', 'cover_media_id',
        'status', 'published_at', 'created_by', 'updated_by',
    ];
}
