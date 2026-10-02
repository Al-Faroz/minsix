<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<header class="article-header site-container">
    <p class="section-eyebrow">GALERI<?= $gallery['gallery_date'] ? ' · ' . esc(date('d.m.Y', strtotime($gallery['gallery_date']))) : '' ?></p>
    <h1><?= esc($gallery['title']) ?></h1>
    <?php if ($gallery['description']): ?><p><?= esc($gallery['description']) ?></p><?php endif ?>
</header>

<div class="site-container gallery-detail-grid">
<?php foreach ($items as $item): ?>
    <button class="gallery-lightbox-item" type="button" data-lightbox-src="<?= base_url($item['relative_path']) ?>" data-lightbox-alt="<?= esc($item['alt_text'] ?: $item['original_name']) ?>">
        <img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['original_name']) ?>">
        <?php if ($item['caption']): ?><span><?= esc($item['caption']) ?></span><?php endif ?>
    </button>
<?php endforeach ?>
</div>

<div class="lightbox" id="siteLightbox" hidden>
    <button class="lightbox-close" type="button" aria-label="Tutup">×</button>
    <img src="" alt="">
</div>
<?= $this->endSection() ?>
