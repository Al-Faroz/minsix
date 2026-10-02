<?php
$siteName = $site['site_name'] ?? 'MIN 6 JEMBER';
$tagline = $site['site_tagline'] ?? 'Berakhlaqul Karimah dan Berprestasi';
$kabarVisible = isset($features['kabar']) && (int) $features['kabar']['is_enabled'] === 1 && (int) $features['kabar']['show_in_nav'] === 1;
$spmbVisible = isset($features['spmb']) && (int) $features['spmb']['is_enabled'] === 1 && (int) $features['spmb']['show_in_nav'] === 1;

$rawSeoTitle = trim((string) ($seoTitle ?? ''));
if ($rawSeoTitle === '') {
    $rawSeoTitle = trim((string) ($title ?? ''));
}
if ($rawSeoTitle === '') {
    $rawSeoTitle = trim((string) ($site['seo_default_title'] ?? ''));
}
if ($rawSeoTitle === '') {
    $rawSeoTitle = $siteName;
}

$documentTitle = str_contains(mb_strtolower($rawSeoTitle), mb_strtolower($siteName))
    ? $rawSeoTitle
    : $rawSeoTitle . ' | ' . $siteName;

$description = trim((string) ($metaDescription ?? ''));
if ($description === '') {
    $description = trim((string) ($site['seo_default_description'] ?? ''));
}
if ($description === '') {
    $description = $tagline;
}

$canonicalBase = rtrim(trim((string) ($site['seo_canonical_base_url'] ?? '')), '/');
$relativeUri = trim(uri_string(), '/');
$resolvedCanonical = $canonicalUrl ?? ($canonicalBase !== ''
    ? $canonicalBase . ($relativeUri !== '' ? '/' . $relativeUri : '/')
    : current_url());

$resolvedOgImage = $ogImageUrl ?? ($seoDefaultOgUrl ?? null);
$resolvedOgType = $ogType ?? 'website';

$organizationData = [
    '@context' => 'https://schema.org',
    '@type' => 'EducationalOrganization',
    'name' => $siteName,
    'url' => $canonicalBase !== '' ? $canonicalBase . '/' : site_url('/'),
    'logo' => base_url('assets/brand/min6-logo.png'),
];

if (! empty($site['address'])) $organizationData['address'] = $site['address'];
if (! empty($site['phone'])) $organizationData['telephone'] = $site['phone'];
if (! empty($site['email'])) $organizationData['email'] = $site['email'];
if (! empty($site['instagram_url'])) $organizationData['sameAs'] = [$site['instagram_url']];

$structuredItems = [$organizationData];
if (! empty($structuredData) && is_array($structuredData)) {
    $structuredItems[] = $structuredData;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/brand/favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/brand/min6-logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Raleway:wght@400;500;600;700;800&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title><?= esc($documentTitle) ?></title>
    <meta name="description" content="<?= esc($description) ?>">
    <link rel="canonical" href="<?= esc($resolvedCanonical) ?>">

    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="<?= esc($siteName) ?>">
    <meta property="og:type" content="<?= esc($resolvedOgType) ?>">
    <meta property="og:title" content="<?= esc($documentTitle) ?>">
    <meta property="og:description" content="<?= esc($description) ?>">
    <meta property="og:url" content="<?= esc($resolvedCanonical) ?>">
    <?php if ($resolvedOgImage): ?><meta property="og:image" content="<?= esc($resolvedOgImage) ?>"><?php endif ?>

    <meta name="twitter:card" content="<?= $resolvedOgImage ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= esc($documentTitle) ?>">
    <meta name="twitter:description" content="<?= esc($description) ?>">
    <?php if ($resolvedOgImage): ?><meta name="twitter:image" content="<?= esc($resolvedOgImage) ?>"><?php endif ?>

    <?php foreach ($structuredItems as $structuredItem): ?>
        <script type="application/ld+json"><?= json_encode($structuredItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php endforeach ?>

    <link rel="stylesheet" href="<?= base_url('assets/site/css/site.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Lewati ke konten</a>

<header class="site-header" id="siteHeader">
    <div class="site-container nav-shell">
        <a class="site-brand" href="<?= site_url('/') ?>" aria-label="<?= esc($siteName) ?>">
            <img class="site-brand-logo" src="<?= base_url('assets/brand/min6-logo.png') ?>" alt="" width="42" height="42">
            <span class="brand-copy"><strong><?= esc($siteName) ?></strong><small><?= esc($tagline) ?></small></span>
        </a>

        <button class="nav-toggle" id="siteNavToggle" type="button" aria-expanded="false" aria-controls="siteNav" aria-label="Buka menu">
            <span></span><span></span>
        </button>

        <nav class="site-nav" id="siteNav" aria-label="Navigasi utama">
            <a class="<?= ($currentNav ?? '') === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
            <a class="<?= ($currentNav ?? '') === 'profile' ? 'active' : '' ?>" href="<?= site_url('profil') ?>">Profil</a>
            <a class="<?= ($currentNav ?? '') === 'program' ? 'active' : '' ?>" href="<?= site_url('program') ?>">Program</a>
            <a class="<?= ($currentNav ?? '') === 'gtk' ? 'active' : '' ?>" href="<?= site_url('gtk') ?>">GTK</a>
            <?php if ($kabarVisible): ?><a class="<?= ($currentNav ?? '') === 'kabar' ? 'active' : '' ?>" href="<?= site_url('kabar') ?>">Kabar Madrasah</a><?php endif ?>
            <?php if ($spmbVisible): ?><a class="nav-cta<?= ($currentNav ?? '') === 'spmb' ? ' active' : '' ?>" href="<?= site_url('spmb') ?>">SPMB</a><?php endif ?>
        </nav>
    </div>
</header>

<main id="main-content">
    <?= $this->renderSection('content') ?>
</main>

<footer class="site-footer">
    <div class="site-container footer-grid">
        <div class="footer-brand">
            <img class="footer-brand-logo" src="<?= base_url('assets/brand/min6-logo-white.png') ?>" alt="" width="56" height="56">
            <h2><?= esc($siteName) ?></h2>
            <p><?= esc($tagline) ?></p>
        </div>
        <div>
            <span class="footer-label">Navigasi</span>
            <a href="<?= site_url('profil') ?>">Profil</a>
            <a href="<?= site_url('program') ?>">Program</a>
            <a href="<?= site_url('gtk') ?>">GTK</a>
            <?php if ($kabarVisible): ?><a href="<?= site_url('kabar') ?>">Kabar Madrasah</a><?php endif ?>
        </div>
        <div>
            <span class="footer-label">Kontak</span>
            <?php if (! empty($site['address'])): ?><p><?= nl2br(esc($site['address'])) ?></p><?php endif ?>
            <?php if (! empty($site['phone'])): ?><a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $site['phone'])) ?>"><?= esc($site['phone']) ?></a><?php endif ?>
            <?php if (! empty($site['email'])): ?><a href="mailto:<?= esc($site['email']) ?>"><?= esc($site['email']) ?></a><?php endif ?>
            <?php if (! empty($site['instagram_url'])): ?><a href="<?= esc($site['instagram_url']) ?>" target="_blank" rel="noopener">@<?= esc($site['instagram_username'] ?? 'min6jember') ?></a><?php endif ?>
        </div>
    </div>
    <div class="site-container footer-bottom">
        <span>© <?= date('Y') ?> <?= esc($siteName) ?></span>
        <a class="footer-manager-link" href="<?= site_url('manager/login') ?>" rel="nofollow">
            <span aria-hidden="true">↗</span> Login CMS
        </a>
    </div>
</footer>

<script src="<?= base_url('assets/site/js/site.js') ?>" defer></script>
</body>
</html>
