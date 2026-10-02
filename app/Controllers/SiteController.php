<?php

namespace App\Controllers;

use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use App\Models\SiteSettingModel;

abstract class SiteController extends BaseController
{
    private ?array $siteContextCache = null;

    protected function siteContext(): array
    {
        if ($this->siteContextCache !== null) {
            return $this->siteContextCache;
        }

        $settings = (new SiteSettingModel())->valuesByKey();
        $features = [];
        $defaultOgMedia = null;

        $defaultOgId = (int) ($settings['seo_default_og_media_id'] ?? 0);
        if ($defaultOgId > 0) {
            $defaultOgMedia = (new MediaModel())->where('id', $defaultOgId)->where('media_type', 'IMAGE')->first();
        }

        foreach ((new SiteFeatureModel())->findAll() as $row) {
            $features[$row['feature_key']] = $row;
        }

        $this->siteContextCache = [
            'site' => $settings,
            'features' => $features,
            'seoDefaultOgUrl' => $defaultOgMedia ? base_url($defaultOgMedia['relative_path']) : null,
        ];

        return $this->siteContextCache;
    }

    protected function featureEnabled(string $key): bool
    {
        $context = $this->siteContext();
        return isset($context['features'][$key])
            && (int) $context['features'][$key]['is_enabled'] === 1;
    }

    protected function featureInNav(string $key): bool
    {
        $context = $this->siteContext();
        return $this->featureEnabled($key)
            && isset($context['features'][$key])
            && (int) $context['features'][$key]['show_in_nav'] === 1;
    }

    protected function featureOnHome(string $key): bool
    {
        $context = $this->siteContext();
        return $this->featureEnabled($key)
            && isset($context['features'][$key])
            && (int) $context['features'][$key]['show_on_home'] === 1;
    }
}
