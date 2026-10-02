<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class KabarController extends SiteController
{
    public function index(): string
    {
        $this->requireKabar();

        $context = $this->siteContext();
        $db = db_connect();

        $news = [];
        $events = [];
        $achievements = [];
        $galleries = [];

        if ($this->featureEnabled('news')) {
            $news = $db->table('news n')
                ->select('n.id, n.slug, n.title, n.summary, n.published_at, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = n.primary_media_id', 'left')
                ->where('n.status', 'PUBLISHED')
                ->where('n.published_at <=', date('Y-m-d H:i:s'))
                ->orderBy('n.published_at', 'DESC')
                ->limit(9)
                ->get()->getResultArray();
        }

        if ($this->featureEnabled('events')) {
            $events = $db->table('events')
                ->where('status', 'PUBLISHED')
                ->orderBy('start_at', 'DESC')
                ->limit(12)
                ->get()->getResultArray();
        }

        if ($this->featureEnabled('achievements')) {
            $achievements = $db->table('achievements a')
                ->select('a.*, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = a.primary_media_id', 'left')
                ->where('a.status', 'PUBLISHED')
                ->orderBy('a.achievement_date', 'DESC')
                ->orderBy('a.published_at', 'DESC')
                ->limit(12)
                ->get()->getResultArray();
        }

        if ($this->featureEnabled('gallery')) {
            $galleries = $db->table('galleries g')
                ->select('g.*, m.relative_path, m.alt_text')
                ->join('media m', 'm.id = g.cover_media_id', 'left')
                ->where('g.status', 'PUBLISHED')
                ->orderBy('g.gallery_date', 'DESC')
                ->orderBy('g.published_at', 'DESC')
                ->limit(12)
                ->get()->getResultArray();
        }

        return view('frontend/kabar/index', array_merge($context, [
            'title' => 'Kabar Madrasah | ' . ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => 'Kabar Madrasah',
            'metaDescription' => 'Berita, agenda, prestasi, dan galeri MIN 6 JEMBER',
            'news' => $news,
            'events' => $events,
            'achievements' => $achievements,
            'galleries' => $galleries,
            'currentNav' => 'kabar',
        ]));
    }

    public function newsDetail(string $slug): string
    {
        $this->requireSubFeature('news');

        $context = $this->siteContext();
        $item = db_connect()->table('news n')
            ->select('n.*, m.relative_path, m.alt_text, og.relative_path AS og_path')
            ->join('media m', 'm.id = n.primary_media_id', 'left')
            ->join('media og', 'og.id = n.og_media_id', 'left')
            ->where('n.slug', $slug)
            ->where('n.status', 'PUBLISHED')
            ->where('n.published_at <=', date('Y-m-d H:i:s'))
            ->limit(1)->get()->getRowArray();

        if (! $item) {
            throw PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        return view('frontend/kabar/news_detail', array_merge($context, [
            'title' => ($item['meta_title'] ?: $item['title']) . ' | ' . ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => $item['meta_title'] ?: $item['title'],
            'metaDescription' => $item['meta_description'] ?: ($item['summary'] ?: $item['title']),
            'ogImageUrl' => ! empty($item['og_path']) ? base_url($item['og_path']) : (! empty($item['relative_path']) ? base_url($item['relative_path']) : null),
            'ogType' => 'article',
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $item['title'],
                'datePublished' => $item['published_at'] ? date('c', strtotime($item['published_at'])) : null,
                'dateModified' => $item['updated_at'] ? date('c', strtotime($item['updated_at'])) : null,
                'mainEntityOfPage' => site_url('kabar/berita/' . $item['slug']),
            ],
            'item' => $item,
            'currentNav' => 'kabar',
        ]));
    }

    public function achievementDetail(string $slug): string
    {
        $this->requireSubFeature('achievements');

        $context = $this->siteContext();
        $item = db_connect()->table('achievements a')
            ->select('a.*, m.relative_path, m.alt_text, og.relative_path AS og_path')
            ->join('media m', 'm.id = a.primary_media_id', 'left')
            ->join('media og', 'og.id = a.og_media_id', 'left')
            ->where('a.slug', $slug)
            ->where('a.status', 'PUBLISHED')
            ->limit(1)->get()->getRowArray();

        if (! $item) {
            throw PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan.');
        }

        return view('frontend/kabar/achievement_detail', array_merge($context, [
            'title' => ($item['meta_title'] ?: $item['title']) . ' | ' . ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => $item['meta_title'] ?: $item['title'],
            'metaDescription' => $item['meta_description'] ?: ($item['summary'] ?: $item['title']),
            'ogImageUrl' => ! empty($item['og_path']) ? base_url($item['og_path']) : (! empty($item['relative_path']) ? base_url($item['relative_path']) : null),
            'ogType' => 'article',
            'item' => $item,
            'currentNav' => 'kabar',
        ]));
    }

    public function eventDetail(string $slug): string
    {
        $this->requireSubFeature('events');

        $context = $this->siteContext();
        $item = db_connect()->table('events e')
            ->select('e.*, m.relative_path, m.alt_text')
            ->join('media m', 'm.id = e.primary_media_id', 'left')
            ->where('e.slug', $slug)
            ->where('e.status', 'PUBLISHED')
            ->limit(1)->get()->getRowArray();

        if (! $item) {
            throw PageNotFoundException::forPageNotFound('Agenda tidak ditemukan.');
        }

        return view('frontend/kabar/event_detail', array_merge($context, [
            'title' => $item['title'] . ' | ' . ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => $item['title'],
            'metaDescription' => $item['summary'] ?: $item['title'],
            'ogImageUrl' => ! empty($item['relative_path']) ? base_url($item['relative_path']) : null,
            'item' => $item,
            'currentNav' => 'kabar',
        ]));
    }

    public function galleryDetail(string $slug): string
    {
        $this->requireSubFeature('gallery');

        $context = $this->siteContext();
        $db = db_connect();

        $gallery = $db->table('galleries g')
            ->select('g.*, m.relative_path AS cover_path, m.alt_text AS cover_alt')
            ->join('media m', 'm.id = g.cover_media_id', 'left')
            ->where('g.slug', $slug)
            ->where('g.status', 'PUBLISHED')
            ->limit(1)->get()->getRowArray();

        if (! $gallery) {
            throw PageNotFoundException::forPageNotFound('Galeri tidak ditemukan.');
        }

        $items = $db->table('gallery_items gi')
            ->select('gi.id, gi.caption, gi.display_order, m.relative_path, m.alt_text, m.original_name')
            ->join('media m', 'm.id = gi.media_id')
            ->where('gi.gallery_id', $gallery['id'])
            ->orderBy('gi.display_order', 'ASC')
            ->orderBy('gi.id', 'ASC')
            ->get()->getResultArray();

        return view('frontend/kabar/gallery_detail', array_merge($context, [
            'title' => $gallery['title'] . ' | ' . ($context['site']['site_name'] ?? 'MIN 6 JEMBER'),
            'seoTitle' => $gallery['title'],
            'metaDescription' => $gallery['description'] ?: $gallery['title'],
            'ogImageUrl' => ! empty($gallery['cover_path']) ? base_url($gallery['cover_path']) : null,
            'gallery' => $gallery,
            'items' => $items,
            'currentNav' => 'kabar',
        ]));
    }

    private function requireKabar(): void
    {
        if (! $this->featureEnabled('kabar')) {
            throw PageNotFoundException::forPageNotFound('Kabar Madrasah sedang tidak tersedia.');
        }
    }

    private function requireSubFeature(string $key): void
    {
        $this->requireKabar();

        if (! $this->featureEnabled($key)) {
            throw PageNotFoundException::forPageNotFound('Konten sedang tidak tersedia.');
        }
    }
}
