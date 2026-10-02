<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteFeatureModel extends Model
{
    protected $table = 'site_features';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'feature_key', 'label', 'is_enabled', 'show_in_nav', 'show_on_home', 'updated_by', 'updated_at',
    ];
}
