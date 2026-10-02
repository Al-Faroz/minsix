<?php

namespace App\Models;

use CodeIgniter\Model;

class SpmbPeriodModel extends Model
{
    protected $table = 'spmb_periods';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'academic_year', 'title', 'summary', 'content', 'start_date', 'end_date',
        'registration_url', 'qr_media_id', 'brochure_media_id', 'contact_name',
        'contact_phone', 'status', 'is_current', 'created_by', 'updated_by',
    ];
}
