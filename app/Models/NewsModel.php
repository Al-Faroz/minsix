<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsModel extends Model
{
    protected $table = 'news';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'slug', 'title', 'summary', 'content', 'primary_media_id', 'status',
        'published_at', 'meta_title', 'meta_description', 'og_media_id',
        'created_by', 'updated_by',
    ];
}
