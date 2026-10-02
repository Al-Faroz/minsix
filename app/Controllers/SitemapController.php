<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class SitemapController extends SiteController
{
    public function index(): ResponseInterface
    {
        $context = $this->siteContext();
        $site = $context['site'];
        $base = rtrim(trim((string) ($site['seo_canonical_base_url'] ?? '')), '/');

        $url = static function (string $path) use ($base): string {
            if ($base !== '') {
                return $base . ($path === '' ? '/' : '/' . ltrim($path, '/'));
            }

            return site_url($path === '' ? '/' : $path);
        };

        $urls = [
            ['loc' => $url(''), 'lastmod' => null],
            ['loc' => $url('profil'), 'lastmod' => null],
            ['loc' => $url('program'), 'lastmod' => null],
            ['loc' => $url('gtk'), 'lastmod' => null],
        ];

        $db = db_connect();

        if ($this->featureEnabled('kabar')) {
            $urls[] = ['loc' => $url('kabar'), 'lastmod' => null];

            if ($this->featureEnabled('news')) {
                foreach ($db->table('news')->select('slug, updated_at, published_at')
                    ->where('status', 'PUBLISHED')
                    ->where('published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('published_at', 'DESC')->get()->getResultArray() as $row) {
                    $urls[] = [
                        'loc' => $url('kabar/berita/' . $row['slug']),
                        'lastmod' => $row['updated_at'] ?: $row['published_at'],
                    ];
                }
            }

            if ($this->featureEnabled('achievements')) {
                foreach ($db->table('achievements')->select('slug, updated_at, published_at')
                    ->where('status', 'PUBLISHED')
                    ->orderBy('published_at', 'DESC')->get()->getResultArray() as $row) {
                    $urls[] = [
                        'loc' => $url('kabar/prestasi/' . $row['slug']),
                        'lastmod' => $row['updated_at'] ?: $row['published_at'],
                    ];
                }
            }

            if ($this->featureEnabled('events')) {
                foreach ($db->table('events')->select('slug, updated_at, start_at')
                    ->where('status', 'PUBLISHED')
                    ->orderBy('start_at', 'DESC')->get()->getResultArray() as $row) {
                    $urls[] = [
                        'loc' => $url('kabar/agenda/' . $row['slug']),
                        'lastmod' => $row['updated_at'] ?: $row['start_at'],
                    ];
                }
            }

            if ($this->featureEnabled('gallery')) {
                foreach ($db->table('galleries')->select('slug, updated_at, published_at')
                    ->where('status', 'PUBLISHED')
                    ->orderBy('published_at', 'DESC')->get()->getResultArray() as $row) {
                    $urls[] = [
                        'loc' => $url('kabar/galeri/' . $row['slug']),
                        'lastmod' => $row['updated_at'] ?: $row['published_at'],
                    ];
                }
            }
        }

        if ($this->featureEnabled('spmb')) {
            $urls[] = ['loc' => $url('spmb'), 'lastmod' => null];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $item) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($item['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";

            if (! empty($item['lastmod']) && strtotime((string) $item['lastmod']) !== false) {
                $xml .= '    <lastmod>' . date('c', strtotime((string) $item['lastmod'])) . "</lastmod>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $this->response
            ->setContentType('application/xml', 'UTF-8')
            ->setBody($xml);
    }
}
