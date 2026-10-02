<?php

namespace App\Models;

use CodeIgniter\Model;

class SpmbHighlightModel extends Model
{
    protected $table = 'spmb_highlights';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spmb_period_id', 'title', 'description', 'display_order',
    ];
}
