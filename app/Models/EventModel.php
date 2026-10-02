<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'slug', 'title', 'summary', 'description', 'location', 'start_at', 'end_at',
        'primary_media_id', 'status', 'created_by', 'updated_by',
    ];
}
