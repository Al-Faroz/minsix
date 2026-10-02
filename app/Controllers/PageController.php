<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class PageController extends SiteController
{
    public function profile(): string
    {
        $context = $this->siteContext();
        $rows = db_connect()->table('profile_sections p')
            ->select('p.*, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = p.primary_media_id', 'left')
            ->orderBy('p.display_order', 'ASC')
            ->get()
            ->getResultArray();

        return view('frontend/profile/index', array_merge($context, [
            'title' => 'Profil — ' . ($context['site']['site_name'] ?? 'MIN 6 Jember'),
            'metaDescription' => 'Profil MIN 6 Jember',
            'sections' => $rows,
            'currentNav' => 'profile',
        ]));
    }

    public function program(): string
    {
        $context = $this->siteContext();
        $programs = db_connect()->table('programs p')
            ->select('p.*, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = p.primary_media_id', 'left')
            ->where('p.status', 'PUBLISHED')
            ->orderBy('p.display_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($programs as $program) {
            $grouped[$program['category'] ?: 'Program Lainnya'][] = $program;
        }

        return view('frontend/program/index', array_merge($context, [
            'title' => 'Program — ' . ($context['site']['site_name'] ?? 'MIN 6 Jember'),
            'metaDescription' => 'Program pembelajaran dan pengembangan MIN 6 Jember',
            'programGroups' => $grouped,
            'currentNav' => 'program',
        ]));
    }

    public function gtk(): string
    {
        $context = $this->siteContext();
        $rows = db_connect()->table('gtk g')
            ->select('g.id, g.name, g.front_title, g.back_title, g.short_bio, g.display_order, m.relative_path, m.alt_text, r.id AS role_id, r.role_name, r.category, r.display_order AS role_order')
            ->join('media m', 'm.id = g.photo_media_id', 'left')
            ->join('gtk_role_assignments a', 'a.gtk_id = g.id', 'left')
            ->join('gtk_roles r', 'r.id = a.gtk_role_id AND r.is_active = 1', 'left')
            ->where('g.is_active', 1)
            ->orderBy('g.display_order', 'ASC')
            ->orderBy('g.name', 'ASC')
            ->orderBy('r.display_order', 'ASC')
            ->get()
            ->getResultArray();

        $labels = [
            'LEADERSHIP' => 'Pimpinan',
            'CLASS_TEACHER' => 'Guru Kelas',
            'SUBJECT_TEACHER' => 'Guru Mata Pelajaran',
            'STAFF' => 'Tenaga Kependidikan',
        ];

        $groups = [];
        foreach ($labels as $key => $label) {
            $groups[$key] = ['label' => $label, 'people' => []];
        }

        foreach ($rows as $row) {
            if (empty($row['category']) || ! isset($groups[$row['category']])) {
                continue;
            }

            $category = $row['category'];
            $personId = (int) $row['id'];

            if (! isset($groups[$category]['people'][$personId])) {
                $groups[$category]['people'][$personId] = [
                    'id' => $personId,
                    'name' => $row['name'],
                    'front_title' => $row['front_title'],
                    'back_title' => $row['back_title'],
                    'short_bio' => $row['short_bio'],
                    'relative_path' => $row['relative_path'],
                    'alt_text' => $row['alt_text'],
                    'roles' => [],
                ];
            }

            if (! empty($row['role_name'])) {
                $groups[$category]['people'][$personId]['roles'][] = $row['role_name'];
            }
        }

        foreach ($groups as &$group) {
            $group['people'] = array_values($group['people']);
        }
        unset($group);

        return view('frontend/gtk/index', array_merge($context, [
            'title' => 'GTK — ' . ($context['site']['site_name'] ?? 'MIN 6 Jember'),
            'metaDescription' => 'Guru dan Tenaga Kependidikan MIN 6 Jember',
            'groups' => $groups,
            'currentNav' => 'gtk',
        ]));
    }

    public function spmb(): string
    {
        if (! $this->featureEnabled('spmb')) {
            throw PageNotFoundException::forPageNotFound('SPMB sedang tidak tersedia.');
        }

        $context = $this->siteContext();
        $db = db_connect();
        $period = $db->table('spmb_periods s')
            ->select('s.*, qr.relative_path AS qr_path, qr.alt_text AS qr_alt, b.relative_path AS brochure_path, b.original_name AS brochure_name')
            ->join('media qr', 'qr.id = s.qr_media_id', 'left')
            ->join('media b', 'b.id = s.brochure_media_id', 'left')
            ->where('s.is_current', 1)
            ->where('s.status', 'PUBLISHED')
            ->limit(1)
            ->get()
            ->getRowArray();

        $requirements = [];
        $faq = [];

        if ($period) {
            $requirements = $db->table('spmb_requirements')
                ->where('spmb_period_id', $period['id'])
                ->orderBy('display_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            $faq = $db->table('spmb_faq')
                ->where('spmb_period_id', $period['id'])
                ->orderBy('display_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('frontend/spmb/index', array_merge($context, [
            'title' => 'SPMB — ' . ($context['site']['site_name'] ?? 'MIN 6 Jember'),
            'metaDescription' => $period['summary'] ?? 'Informasi SPMB MIN 6 Jember',
            'period' => $period,
            'requirements' => $requirements,
            'faq' => $faq,
            'currentNav' => 'spmb',
        ]));
    }
}
