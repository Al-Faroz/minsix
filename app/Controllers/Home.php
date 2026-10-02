<?php

namespace App\Controllers;

use App\Models\HomepageSectionModel;
use App\Models\MediaModel;

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

        $media = [];
        $ids = array_values(array_unique(array_filter(array_map(
            static fn (array $row): int => (int) ($row['primary_media_id'] ?? 0),
            $sections
        ))));

        if ($ids !== []) {
            foreach ((new MediaModel())->whereIn('id', $ids)->findAll() as $item) {
                $media[(int) $item['id']] = $item;
            }
        }

        return view('frontend/home/index', array_merge($context, [
            'title' => ($context['site']['site_name'] ?? 'MIN 6 Jember') . ' — ' . ($context['site']['site_tagline'] ?? ''),
            'sections' => $sections,
            'sectionMedia' => $media,
            'currentNav' => 'home',
        ]));
    }
}
