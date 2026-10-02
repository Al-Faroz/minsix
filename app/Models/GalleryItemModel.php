<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryItemModel extends Model
{
    protected $table = 'gallery_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'gallery_id', 'media_id', 'caption', 'display_order', 'created_at',
    ];
}
