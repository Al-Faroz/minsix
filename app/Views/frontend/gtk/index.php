<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>

<header class="page-hero">
    <div class="site-container">
        <p class="section-eyebrow">GTK</p>
        <h1>Guru & Tenaga Kependidikan</h1>
        <p>Satu orang dapat menjalankan lebih dari satu peran. Data ditampilkan berdasarkan kelompok jabatan.</p>
    </div>
</header>

<div class="site-container page-stack">
<?php foreach ($groups as $key => $group): ?>
    <?php if ($group['people'] === []) continue; ?>
    <section class="gtk-section">
        <div class="section-heading"><p class="section-eyebrow"><?= esc($key) ?></p><h2><?= esc($group['label']) ?></h2></div>
        <div class="gtk-grid">
        <?php foreach ($group['people'] as $person): ?>
            <article class="gtk-card">
                <div class="gtk-photo">
                    <?php if ($person['relative_path']): ?><img loading="lazy" src="<?= base_url($person['relative_path']) ?>" alt="<?= esc($person['alt_text'] ?: $person['name']) ?>"><?php else: ?><span>GTK</span><?php endif ?>
                </div>
                <div class="gtk-copy">
                    <h3><?= esc(trim(($person['front_title'] ? $person['front_title'] . ' ' : '') . $person['name'] . ($person['back_title'] ? ', ' . $person['back_title'] : ''))) ?></h3>
                    <p class="gtk-roles"><?= esc(implode(' · ', array_unique($person['roles']))) ?></p>
                    <?php if ($person['short_bio']): ?><p><?= esc($person['short_bio']) ?></p><?php endif ?>
                </div>
            </article>
        <?php endforeach ?>
        </div>
    </section>
<?php endforeach ?>
</div>

<?= $this->endSection() ?>
