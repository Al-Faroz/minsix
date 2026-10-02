<?php
$currentPath = trim(uri_string(), '/');
$isAdmin = session()->get('auth_role') === 'ADMIN';
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
    <link rel="stylesheet" href="<?= base_url('assets/manager/css/manager.css') ?>">
</head>
<body>
<div class="manager-shell">
    <aside class="sidebar" id="managerSidebar">
        <div class="sidebar-brand">
            <div class="brand-mark small">M6</div>
            <div><strong>MIN 6 Jember</strong><span>Content Manager</span></div>
        </div>

        <nav>
            <p class="nav-label">UTAMA</p>
            <a class="nav-item<?= $currentPath === 'manager' ? ' active' : '' ?>" href="<?= site_url('manager') ?>">Dashboard</a>

            <p class="nav-label">KONTEN</p>
            <button class="nav-item disabled" type="button" disabled><span>Beranda</span><small>Segera</small></button>
            <a class="nav-item<?= $navActive('manager/profile') ?>" href="<?= site_url('manager/profile') ?>">Profil</a>
            <a class="nav-item<?= $navActive('manager/programs') ?>" href="<?= site_url('manager/programs') ?>">Program</a>
            <a class="nav-item<?= $navActive('manager/gtk') . $navActive('manager/gtk-roles') ?>" href="<?= site_url('manager/gtk') ?>">GTK</a>
            <a class="nav-item<?= $navActive('manager/news') ?>" href="<?= site_url('manager/news') ?>">Berita</a>
            <a class="nav-item<?= $navActive('manager/events') ?>" href="<?= site_url('manager/events') ?>">Agenda</a>
            <a class="nav-item<?= $navActive('manager/achievements') ?>" href="<?= site_url('manager/achievements') ?>">Prestasi</a>
            <button class="nav-item disabled" type="button" disabled><span>Galeri</span><small>4B</small></button>
            <button class="nav-item disabled" type="button" disabled><span>SPMB</span><small>Segera</small></button>
            <a class="nav-item<?= $navActive('manager/media') ?>" href="<?= site_url('manager/media') ?>">Media</a>
            <button class="nav-item disabled" type="button" disabled><span>Instagram Content</span><small>Segera</small></button>

            <?php if ($isAdmin): ?>
                <p class="nav-label">PENGATURAN</p>
                <a class="nav-item<?= $navActive('manager/settings') ?>" href="<?= site_url('manager/settings') ?>">Website</a>
                <a class="nav-item<?= $navActive('manager/features') ?>" href="<?= site_url('manager/features') ?>">Fitur</a>
                <?php foreach (['Instagram','SEO','Pengguna'] as $item): ?>
                    <button class="nav-item disabled" type="button" disabled><span><?= esc($item) ?></span><small>Segera</small></button>
                <?php endforeach ?>
            <?php endif ?>
        </nav>

        <div class="sidebar-foot"><span>PHASE 4A</span><strong>Berita · Agenda · Prestasi</strong></div>
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
