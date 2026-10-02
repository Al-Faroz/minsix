<?php

namespace App\Models;

use CodeIgniter\Model;

class HomepageSectionModel extends Model
{
    protected $table = 'homepage_sections';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'section_key', 'eyebrow', 'title', 'subtitle', 'body', 'content_json',
        'primary_media_id', 'cta_label', 'cta_url', 'secondary_cta_label',
        'secondary_cta_url', 'display_order', 'updated_by', 'updated_at',
    ];
}
