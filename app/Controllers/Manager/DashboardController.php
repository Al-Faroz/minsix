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

        $gtkActive = 0;
        if ($db->tableExists('gtk')) {
            $gtkActive = $db->table('gtk')->where('is_active', 1)->countAllResults();
        }

        $spmbCurrent = null;
        if ($db->tableExists('spmb_periods')) {
            $spmbCurrent = $db->table('spmb_periods')
                ->select('academic_year, status')
                ->where('is_current', 1)
                ->get()
                ->getRowArray();
        }

        $recentContent = [];
        $recentSources = [
            ['table' => 'news', 'type' => 'BERITA', 'title' => 'title', 'route' => 'manager/news/%d/edit'],
            ['table' => 'achievements', 'type' => 'PRESTASI', 'title' => 'title', 'route' => 'manager/achievements/%d/edit'],
            ['table' => 'events', 'type' => 'AGENDA', 'title' => 'title', 'route' => 'manager/events/%d/edit'],
            ['table' => 'galleries', 'type' => 'GALERI', 'title' => 'title', 'route' => 'manager/galleries/%d/edit'],
            ['table' => 'programs', 'type' => 'PROGRAM', 'title' => 'name', 'route' => 'manager/programs/%d/edit'],
            ['table' => 'spmb_periods', 'type' => 'SPMB', 'title' => 'title', 'route' => 'manager/spmb/%d/edit'],
        ];

        foreach ($recentSources as $source) {
            if (! $db->tableExists($source['table'])) {
                continue;
            }

            $rows = $db->table($source['table'])
                ->select('id, ' . $source['title'] . ' AS content_title, status, updated_at, created_at')
                ->orderBy('updated_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $recentContent[] = [
                    'type' => $source['type'],
                    'title' => $row['content_title'],
                    'status' => $row['status'] ?? '',
                    'changed_at' => $row['updated_at'] ?: $row['created_at'],
                    'url' => site_url(sprintf($source['route'], (int) $row['id'])),
                ];
            }
        }

        usort($recentContent, static function (array $a, array $b): int {
            return strtotime((string) ($b['changed_at'] ?? '')) <=> strtotime((string) ($a['changed_at'] ?? ''));
        });
        $recentContent = array_slice($recentContent, 0, 6);

        return view('manager/dashboard', [
            'title' => 'Dashboard | CMS MIN 6 JEMBER',
            'pageTitle' => 'Dashboard',
            'stats' => [
                'news' => $count('news'),
                'achievements' => $count('achievements'),
                'events' => $upcoming,
                'galleries' => $count('galleries'),
                'gtk_active' => $gtkActive,
            ],
            'spmbCurrent' => $spmbCurrent,
            'recentContent' => $recentContent,
        ]);
    }
}
