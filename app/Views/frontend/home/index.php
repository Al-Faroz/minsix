<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$hero = $sections['hero'] ?? [];
$heroMedia = ! empty($hero['primary_media_id']) ? ($sectionMedia[(int) $hero['primary_media_id']] ?? null) : null;
?>
<section class="hero-section">
    <div class="site-container hero-grid">
        <div class="hero-copy">
            <p class="section-eyebrow"><?= esc($hero['eyebrow'] ?? 'MADRASAH IBTIDAIYAH NEGERI 6 JEMBER') ?></p>
            <h1><?= esc($hero['title'] ?? 'Berakhlakul Karimah. Tumbuh dalam Prestasi.') ?></h1>
            <p class="hero-lead"><?= esc($hero['subtitle'] ?? 'Lingkungan belajar untuk menumbuhkan ilmu, karakter, kreativitas, dan nilai-nilai keislaman sejak usia dasar.') ?></p>
            <?php if (! empty($hero['body'])): ?><p class="hero-body"><?= nl2br(esc($hero['body'])) ?></p><?php endif ?>
            <div class="hero-actions">
                <a class="button primary" href="<?= esc($hero['cta_url'] ?: '#mengenal') ?>"><?= esc($hero['cta_label'] ?: 'Jelajahi Madrasah') ?></a>
                <a class="button text" href="<?= esc($hero['secondary_cta_url'] ?: site_url('profil')) ?>"><?= esc($hero['secondary_cta_label'] ?: 'Kenali MIN 6 Jember') ?> <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="hero-visual<?= $heroMedia ? '' : ' placeholder' ?>">
            <?php if ($heroMedia): ?>
                <img src="<?= base_url($heroMedia['relative_path']) ?>" alt="<?= esc($heroMedia['alt_text'] ?: 'Aktivitas MIN 6 Jember') ?>">
            <?php else: ?>
                <div class="visual-placeholder"><span>MIN 6</span><small>Tambahkan foto hero melalui CMS Beranda.</small></div>
            <?php endif ?>
        </div>
    </div>
</section>

<section class="intro-preview" id="mengenal">
    <div class="site-container">
        <p class="section-eyebrow">FRONTEND FOUNDATION</p>
        <h2>Homepage sedang dibangun dari data CMS, bukan halaman statis.</h2>
        <p>Fondasi visual, navigasi responsif, footer, feature-aware menu, dan section Hero sudah aktif. Section homepage lengkap akan dilanjutkan pada batch 6B.</p>
    </div>
</section>
<?= $this->endSection() ?>
