<?php

namespace App\Models;

use CodeIgniter\Model;

class InstagramPostModel extends Model
{
    protected $table = 'instagram_posts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'source', 'instagram_media_id', 'permalink', 'caption', 'media_type',
        'media_url', 'thumbnail_url', 'local_media_id', 'published_at',
        'is_fallback', 'is_visible', 'sort_order', 'fetched_at',
    ];
}
