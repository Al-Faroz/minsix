<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        $count = static function (string $table) use ($db): int {
            return $db->tableExists($table) ? $db->table($table)->countAllResults() : 0;
        };

        $upcoming = 0;
        if ($db->tableExists('events')) {
            $upcoming = $db->table('events')
                ->where('status', 'PUBLISHED')
                ->where('start_at >=', date('Y-m-d H:i:s'))
                ->countAllResults();
        }

        return view('manager/dashboard', [
            'title' => 'Dashboard | CMS MIN 6 Jember',
            'pageTitle' => 'Dashboard',
            'stats' => [
                'news' => $count('news'),
                'achievements' => $count('achievements'),
                'events' => $upcoming,
                'galleries' => $count('galleries'),
            ],
        ]);
    }
}
