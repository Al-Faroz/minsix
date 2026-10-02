<?php

namespace App\Models;

use CodeIgniter\Model;

class IntegrationSettingModel extends Model
{
    protected $table = 'integration_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'provider', 'setting_key', 'setting_value', 'is_secret', 'updated_by', 'updated_at',
    ];

    public function valuesForProvider(string $provider): array
    {
        $rows = $this->where('provider', strtoupper($provider))->findAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row;
        }

        return $result;
    }
}
