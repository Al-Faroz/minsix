<?php
$currentPath = trim(uri_string(), '/');
$isAdmin = session()->get('auth_role') === 'ADMIN';
$gtkNavActive = $currentPath === 'manager/gtk'
    || str_starts_with($currentPath, 'manager/gtk/')
    || $currentPath === 'manager/gtk-roles'
    || str_starts_with($currentPath, 'manager/gtk-roles/');
$navActive = static fn (string $prefix): string =>
    ($currentPath === $prefix || str_starts_with($currentPath, $prefix . '/')) ? ' active' : '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'CMS MIN 6 Jember') ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/brand/favicon-32x32.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/manager/css/manager.css') ?>">
</head>
<body>
<div class="manager-shell">
    <aside class="sidebar" id="managerSidebar">
        <div class="sidebar-brand">
            <img class="manager-brand-logo" src="<?= base_url('assets/brand/min6-logo.png') ?>" alt="" width="38" height="38">
            <div><strong>MIN 6 Jember</strong><span>Content Manager</span></div>
        </div>

        <nav>
            <p class="nav-label">UTAMA</p>
            <a class="nav-item<?= $currentPath === 'manager' ? ' active' : '' ?>" href="<?= site_url('manager') ?>">Dashboard</a>

            <p class="nav-label">KONTEN</p>
            <a class="nav-item<?= $navActive('manager/homepage') ?>" href="<?= site_url('manager/homepage') ?>">Beranda</a>
            <a class="nav-item<?= $navActive('manager/profile') ?>" href="<?= site_url('manager/profile') ?>">Profil</a>
            <a class="nav-item<?= $navActive('manager/programs') ?>" href="<?= site_url('manager/programs') ?>">Program</a>
            <a class="nav-item<?= $gtkNavActive ? ' active' : '' ?>" href="<?= site_url('manager/gtk') ?>">GTK</a>
            <a class="nav-item<?= $navActive('manager/news') ?>" href="<?= site_url('manager/news') ?>">Berita</a>
            <a class="nav-item<?= $navActive('manager/events') ?>" href="<?= site_url('manager/events') ?>">Agenda</a>
            <a class="nav-item<?= $navActive('manager/achievements') ?>" href="<?= site_url('manager/achievements') ?>">Prestasi</a>
            <a class="nav-item<?= $navActive('manager/galleries') ?>" href="<?= site_url('manager/galleries') ?>">Galeri</a>
            <a class="nav-item<?= $navActive('manager/spmb') ?>" href="<?= site_url('manager/spmb') ?>">SPMB</a>
            <a class="nav-item<?= $navActive('manager/media') ?>" href="<?= site_url('manager/media') ?>">Media</a>
            <p class="nav-label">INTEGRASI</p>
            <a class="nav-item<?= $navActive('manager/instagram') ?>" href="<?= site_url('manager/instagram') ?>">Instagram Content</a>

            <?php if ($isAdmin): ?>
                <p class="nav-label">PENGATURAN</p>
                <a class="nav-item<?= $navActive('manager/settings') ?>" href="<?= site_url('manager/settings') ?>">Website</a>
                <a class="nav-item<?= $navActive('manager/features') ?>" href="<?= site_url('manager/features') ?>">Fitur</a>
                <a class="nav-item<?= $navActive('manager/instagram-settings') ?>" href="<?= site_url('manager/instagram-settings') ?>">Instagram</a>
                <a class="nav-item<?= $navActive('manager/seo') ?>" href="<?= site_url('manager/seo') ?>">SEO</a>
                <a class="nav-item<?= $navActive('manager/users') ?>" href="<?= site_url('manager/users') ?>">Pengguna</a>
            <?php endif ?>
        </nav>

        <div class="sidebar-foot"><span>MIN SIX</span><strong>Website Content Manager</strong></div>
    </aside>

    <div class="content-wrap">
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="sidebarToggle" type="button" aria-label="Buka navigasi">☰</button>
                <div><p class="eyebrow">CMS MIN 6 JEMBER</p><h1><?= esc($pageTitle ?? 'Manager') ?></h1></div>
            </div>
            <div class="user-actions">
                <div class="user-copy"><strong><?= esc((string) session()->get('auth_name')) ?></strong><span><?= esc((string) session()->get('auth_role')) ?></span></div>
                <a class="btn ghost" href="<?= site_url('manager/account/password') ?>">Ubah Password</a>
                <form action="<?= site_url('manager/logout') ?>" method="post"><?= csrf_field() ?><button class="btn dark" type="submit">Keluar</button></form>
            </div>
        </header>

        <main class="main">
            <?php if (session()->getFlashdata('success')): ?><div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
            <?php if (session()->getFlashdata('error')): ?><div class="alert danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
            <?php $errors = session()->getFlashdata('errors') ?? []; ?>
            <?php if ($errors !== []): ?><div class="alert danger"><?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?></div><?php endif ?>
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="footer"><span>MIN 6 Jember</span><span>CMS berbasis CodeIgniter 4</span></footer>
    </div>
</div>

<div class="overlay" id="sidebarOverlay"></div>
<script src="<?= base_url('assets/manager/js/manager.js') ?>" defer></script>
</body>
</html>
