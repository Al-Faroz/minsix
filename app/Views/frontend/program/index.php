<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>

<header class="page-hero">
    <div class="site-container">
        <p class="section-eyebrow">PROGRAM</p>
        <h1>Belajar & Berkembang</h1>
        <p>Program pembelajaran, pembiasaan, keagamaan, ekstrakurikuler, dan pengembangan prestasi dalam satu halaman.</p>
    </div>
</header>

<div class="site-container page-stack">
<?php if ($programGroups === []): ?>
    <div class="public-empty">Belum ada program yang dipublikasikan.</div>
<?php endif ?>

<?php $groupNo = 0; foreach ($programGroups as $category => $programs): $groupNo++; ?>
<section class="program-group">
    <div class="program-group-head"><span><?= str_pad((string) $groupNo, 2, '0', STR_PAD_LEFT) ?></span><h2><?= esc($category) ?></h2></div>
    <div class="program-public-list">
        <?php foreach ($programs as $program): ?>
            <article class="program-public-item<?= empty($program['relative_path']) ? ' no-image' : '' ?>">
                <?php if ($program['relative_path']): ?><div class="program-public-image"><img loading="lazy" src="<?= base_url($program['relative_path']) ?>" alt="<?= esc($program['alt_text'] ?: $program['name']) ?>"></div><?php endif ?>
                <div>
                    <h3><?= esc($program['name']) ?></h3>
                    <?php if ($program['summary']): ?><p><?= esc($program['summary']) ?></p><?php endif ?>
                    <?php if ($program['content']): ?><div class="prose compact"><?= \App\Libraries\SafeHtml::renderStored($program['content']) ?></div><?php endif ?>
                </div>
            </article>
        <?php endforeach ?>
    </div>
</section>
<?php endforeach ?>
</div>

<?= $this->endSection() ?>
