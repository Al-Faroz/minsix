<?php

namespace App\Models;

use CodeIgniter\Model;

class SpmbStepModel extends Model
{
    protected $table = 'spmb_steps';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spmb_period_id', 'title', 'description', 'display_order',
    ];
}
