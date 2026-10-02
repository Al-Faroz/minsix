<?php

namespace App\Models;

use CodeIgniter\Model;

class AchievementModel extends Model
{
    protected $table = 'achievements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'slug', 'title', 'participant_name', 'field_name', 'award', 'level',
        'organizer', 'achievement_date', 'summary', 'content', 'primary_media_id',
        'status', 'published_at', 'meta_title', 'meta_description', 'og_media_id',
        'created_by', 'updated_by',
    ];
}
