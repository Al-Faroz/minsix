<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>

<header class="page-hero dark">
    <div class="site-container">
        <p class="section-eyebrow">KABAR MADRASAH</p>
        <h1>Cerita, kegiatan, dan pencapaian.</h1>
        <p>Berita, agenda, prestasi, dan galeri yang sedang aktif ditampilkan dalam satu halaman.</p>
    </div>
</header>

<div class="site-container page-stack">
<?php if ($news !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">BERITA</p><h2>Kabar Terbaru</h2></div>
    <div class="public-card-grid">
        <?php foreach ($news as $item): ?>
        <a class="public-card" href="<?= site_url('kabar/berita/' . $item['slug']) ?>">
            <div class="public-card-media"><?php if ($item['relative_path']): ?><img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"><?php else: ?><span>Berita</span><?php endif ?></div>
            <small><?= esc(date('d.m.Y', strtotime($item['published_at']))) ?></small>
            <h3><?= esc($item['title']) ?></h3>
            <?php if ($item['summary']): ?><p><?= esc($item['summary']) ?></p><?php endif ?>
        </a>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($events !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">AGENDA</p><h2>Agenda Madrasah</h2></div>
    <div class="event-list">
        <?php foreach ($events as $item): ?>
        <a class="event-row" href="<?= site_url('kabar/agenda/' . $item['slug']) ?>">
            <time><strong><?= esc(date('d', strtotime($item['start_at']))) ?></strong><span><?= esc(strtoupper(date('M Y', strtotime($item['start_at'])))) ?></span></time>
            <div><h3><?= esc($item['title']) ?></h3><p><?= esc($item['location'] ?: 'MIN 6 Jember') ?></p></div>
            <span>→</span>
        </a>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($achievements !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">PRESTASI</p><h2>Prestasi Peserta Didik</h2></div>
    <div class="public-card-grid">
        <?php foreach ($achievements as $item): ?>
        <a class="public-card" href="<?= site_url('kabar/prestasi/' . $item['slug']) ?>">
            <div class="public-card-media"><?php if ($item['relative_path']): ?><img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"><?php else: ?><span>Prestasi</span><?php endif ?></div>
            <small><?= esc(trim(($item['award'] ?: '') . ($item['level'] ? ' · ' . $item['level'] : ''))) ?></small>
            <h3><?= esc($item['title']) ?></h3>
            <?php if ($item['participant_name']): ?><p><?= esc($item['participant_name']) ?></p><?php endif ?>
        </a>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($galleries !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">GALERI</p><h2>Potret Madrasah</h2></div>
    <div class="gallery-public-grid">
        <?php foreach ($galleries as $item): ?>
        <a class="gallery-public-card" href="<?= site_url('kabar/galeri/' . $item['slug']) ?>">
            <div><?php if ($item['relative_path']): ?><img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"><?php else: ?><span>Galeri</span><?php endif ?></div>
            <h3><?= esc($item['title']) ?></h3>
        </a>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($news === [] && $events === [] && $achievements === [] && $galleries === []): ?>
    <div class="public-empty">Belum ada Kabar Madrasah yang dipublikasikan.</div>
<?php endif ?>
</div>

<?= $this->endSection() ?>
