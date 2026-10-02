<?php

namespace App\Models;

use CodeIgniter\Model;

class SpmbFaqModel extends Model
{
    protected $table = 'spmb_faq';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'spmb_period_id', 'question', 'answer', 'display_order',
    ];
}
