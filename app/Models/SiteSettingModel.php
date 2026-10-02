<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'setting_key', 'setting_value', 'value_type', 'is_public', 'updated_by', 'updated_at',
    ];

    public function valuesByKey(): array
    {
        $rows = $this->findAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }

        return $result;
    }
}
