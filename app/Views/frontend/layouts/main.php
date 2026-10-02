<?php
$siteName = $site['site_name'] ?? 'MIN 6 Jember';
$tagline = $site['site_tagline'] ?? 'Berakhlaqul Karimah dan Berprestasi';
$kabarVisible = isset($features['kabar']) && (int) $features['kabar']['is_enabled'] === 1 && (int) $features['kabar']['show_in_nav'] === 1;
$spmbVisible = isset($features['spmb']) && (int) $features['spmb']['is_enabled'] === 1 && (int) $features['spmb']['show_in_nav'] === 1;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? $siteName) ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? $tagline) ?>">
    <link rel="stylesheet" href="<?= base_url('assets/site/css/site.css') ?>">
</head>
<body>
<a class="skip-link" href="#main-content">Lewati ke konten</a>

<header class="site-header" id="siteHeader">
    <div class="site-container nav-shell">
        <a class="site-brand" href="<?= site_url('/') ?>" aria-label="<?= esc($siteName) ?>">
            <span class="brand-symbol">M6</span>
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
            <span class="brand-symbol">M6</span>
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
    <div class="site-container footer-bottom"><span>© <?= date('Y') ?> <?= esc($siteName) ?></span><a href="<?= site_url('manager') ?>">Manager</a></div>
</footer>

<script src="<?= base_url('assets/site/js/site.js') ?>" defer></script>
</body>
</html>
