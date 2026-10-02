<?php

namespace App\Models;

use CodeIgniter\Model;

class SpmbRequirementModel extends Model
{
    protected $table = 'spmb_requirements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'spmb_period_id', 'requirement_text', 'display_order',
    ];
}
