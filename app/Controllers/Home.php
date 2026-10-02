<?php

namespace App\Controllers;

use App\Models\HomepageSectionModel;
use App\Models\MediaModel;
use App\Models\ProfileSectionModel;

class Home extends SiteController
{
    public function index(): string
    {
        $context = $this->siteContext();
        $sections = [];

        foreach ((new HomepageSectionModel())->orderBy('display_order', 'ASC')->findAll() as $row) {
            $decoded = [];
            if (! empty($row['content_json'])) {
                $value = json_decode((string) $row['content_json'], true);
                $decoded = is_array($value) ? $value : [];
            }

            $row['content_data'] = $decoded;
            $sections[$row['section_key']] = $row;
        }

        $sectionMedia = $this->sectionMedia($sections);
        $db = db_connect();

        $profileAbout = (new ProfileSectionModel())->where('section_key', 'about')->first();
        $headmasterProfile = (new ProfileSectionModel())->where('section_key', 'headmaster_message')->first();

        $programs = $db->table('programs p')
            ->select('p.id, p.slug, p.name, p.category, p.summary, p.primary_media_id, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = p.primary_media_id', 'left')
            ->where('p.status', 'PUBLISHED')
            ->orderBy('p.display_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $kabarEnabled = $this->featureEnabled('kabar');

        $achievements = [];
        if ($kabarEnabled && $this->featureOnHome('achievements')) {
            $achievements = $db->table('achievements a')
                ->select('a.id, a.slug, a.title, a.participant_name, a.field_name, a.award, a.level, a.achievement_date, a.primary_media_id, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = a.primary_media_id', 'left')
                ->where('a.status', 'PUBLISHED')
                ->orderBy('a.achievement_date', 'DESC')
                ->orderBy('a.published_at', 'DESC')
                ->limit(6)
                ->get()
                ->getResultArray();
        }

        $galleries = [];
        if ($kabarEnabled && $this->featureOnHome('gallery')) {
            $galleries = $db->table('galleries g')
                ->select('g.id, g.slug, g.title, g.gallery_date, g.description, g.cover_media_id, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = g.cover_media_id', 'left')
                ->where('g.status', 'PUBLISHED')
                ->orderBy('g.gallery_date', 'DESC')
                ->orderBy('g.published_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        $news = [];
        if ($kabarEnabled && $this->featureOnHome('news')) {
            $news = $db->table('news n')
                ->select('n.id, n.slug, n.title, n.summary, n.published_at, n.primary_media_id, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = n.primary_media_id', 'left')
                ->where('n.status', 'PUBLISHED')
                ->where('n.published_at <=', date('Y-m-d H:i:s'))
                ->orderBy('n.published_at', 'DESC')
                ->limit(3)
                ->get()
                ->getResultArray();
        }

        $events = [];
        if ($kabarEnabled && $this->featureOnHome('events')) {
            $events = $db->table('events')
                ->select('id, slug, title, summary, location, start_at, end_at')
                ->where('status', 'PUBLISHED')
                ->where('start_at >=', date('Y-m-d H:i:s'))
                ->orderBy('start_at', 'ASC')
                ->limit(3)
                ->get()
                ->getResultArray();
        }

        $headmaster = $db->table('gtk g')
            ->select('g.id, g.name, g.front_title, g.back_title, g.short_bio, m.relative_path, m.alt_text')
            ->join('gtk_role_assignments a', 'a.gtk_id = g.id')
            ->join('gtk_roles r', 'r.id = a.gtk_role_id')
            ->join('media m', 'm.id = g.photo_media_id', 'left')
            ->where('g.is_active', 1)
            ->where('r.role_key', 'HEADMASTER')
            ->orderBy('a.display_order', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $spmb = null;
        if ($this->featureOnHome('spmb')) {
            $spmb = $db->table('spmb_periods s')
                ->select('s.*, qr.relative_path AS qr_path, brochure.relative_path AS brochure_path')
                ->join('media qr', 'qr.id = s.qr_media_id', 'left')
                ->join('media brochure', 'brochure.id = s.brochure_media_id', 'left')
                ->where('s.is_current', 1)
                ->where('s.status', 'PUBLISHED')
                ->limit(1)
                ->get()
                ->getRowArray();
        }

        $showInstagram = $this->featureOnHome('instagram');
        $instagramPosts = [];
        $instagramSourceUsed = null;

        if ($showInstagram && $db->tableExists('instagram_posts')) {
            $mode = strtoupper(trim((string) ($context['site']['instagram_source_mode'] ?? 'HYBRID')));
            $displayCount = max(6, min(8, (int) ($context['site']['instagram_display_count'] ?? 8)));

            if ($mode !== 'MANUAL') {
                $instagramPosts = $this->instagramRows($db, 'API', $displayCount);
                if ($instagramPosts !== []) {
                    $instagramSourceUsed = 'API';
                }
            }

            if ($instagramPosts === []) {
                $instagramPosts = $this->instagramRows($db, 'MANUAL', $displayCount);
                if ($instagramPosts !== []) {
                    $instagramSourceUsed = 'MANUAL';
                }
            }
        }

        return view('frontend/home/index', array_merge($context, [
            'title' => ($context['site']['seo_default_title'] ?? '') ?: ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => ($context['site']['seo_default_title'] ?? '') ?: ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'metaDescription' => ($context['site']['seo_default_description'] ?? '') ?: ($context['site']['site_tagline'] ?? ''),
            'ogImageUrl' => ! empty($sections['hero']['primary_media_id']) && isset($sectionMedia[(int) $sections['hero']['primary_media_id']])
                ? base_url($sectionMedia[(int) $sections['hero']['primary_media_id']]['relative_path'])
                : null,
            'sections' => $sections,
            'sectionMedia' => $sectionMedia,
            'profileAbout' => $profileAbout,
            'programs' => $programs,
            'achievements' => $achievements,
            'galleries' => $galleries,
            'news' => $news,
            'events' => $events,
            'headmaster' => $headmaster,
            'headmasterProfile' => $headmasterProfile,
            'spmb' => $spmb,
            'showInstagram' => $showInstagram,
            'instagramPosts' => $instagramPosts,
            'instagramSourceUsed' => $instagramSourceUsed,
            'currentNav' => 'home',
        ]));
    }

    private function instagramRows($db, string $source, int $limit): array
    {
        $builder = $db->table('instagram_posts i')
            ->select('i.*, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = i.local_media_id', 'left')
            ->where('i.source', $source)
            ->where('i.is_visible', 1);

        if ($source === 'MANUAL') {
            $builder->orderBy('i.sort_order', 'ASC')->orderBy('i.published_at', 'DESC');
        } else {
            $builder->orderBy('i.published_at', 'DESC')->orderBy('i.id', 'DESC');
        }

        $rows = $builder->limit($limit)->get()->getResultArray();

        foreach ($rows as &$row) {
            $children = [];
            if (! empty($row['children_json'])) {
                $decoded = json_decode((string) $row['children_json'], true);
                $children = is_array($decoded) ? array_values(array_filter($decoded, 'is_array')) : [];
            }

            $row['carousel_children'] = $children;
            $row['carousel_count'] = count($children);
            $row['display_media_url'] = $row['relative_path']
                ?: ($row['thumbnail_url'] ?: $row['media_url']);

            if (empty($row['display_media_url']) && $children !== []) {
                foreach ($children as $child) {
                    $candidate = trim((string) (($child['thumbnail_url'] ?? '') ?: ($child['media_url'] ?? '')));
                    if ($candidate !== '') {
                        $row['display_media_url'] = $candidate;
                        break;
                    }
                }
            }
        }
        unset($row);

        return array_values(array_filter($rows, static fn (array $row): bool => ! empty($row['display_media_url'])));
    }

    private function sectionMedia(array $sections): array
    {
        $ids = [];

        foreach ($sections as $row) {
            $id = (int) ($row['primary_media_id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        $ids = array_values(array_unique($ids));
        if ($ids === []) {
            return [];
        }

        $media = [];
        foreach ((new MediaModel())->whereIn('id', $ids)->findAll() as $item) {
            $media[(int) $item['id']] = $item;
        }

        return $media;
    }
}
