<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>

<header class="page-hero spmb-page-hero">
    <div class="site-container">
        <p class="section-eyebrow">SPMB</p>
        <?php if ($period): ?>
            <h1><?= esc($period['title']) ?></h1>
            <p>Tahun Ajaran <?= esc($period['academic_year']) ?><?php if ($period['start_date'] || $period['end_date']): ?> · <?= $period['start_date'] ? esc(date('d.m.Y', strtotime($period['start_date']))) : '—' ?> – <?= $period['end_date'] ? esc(date('d.m.Y', strtotime($period['end_date']))) : '—' ?><?php endif ?></p>
        <?php else: ?>
            <h1>Informasi SPMB</h1>
            <p>Belum ada periode SPMB yang dipublikasikan sebagai Current.</p>
        <?php endif ?>
    </div>
</header>

<?php if ($period): ?>
<div class="site-container page-stack">
<section class="spmb-public-intro">
    <div>
        <?php if ($period['summary']): ?><p class="lead-copy"><?= esc($period['summary']) ?></p><?php endif ?>
        <?php if ($period['content']): ?><div class="prose"><?= \App\Libraries\SafeHtml::renderStored($period['content']) ?></div><?php endif ?>
    </div>
    <aside class="spmb-contact-card">
        <span>Informasi Pendaftaran</span>
        <?php if ($period['contact_name']): ?><strong><?= esc($period['contact_name']) ?></strong><?php endif ?>
        <?php if ($period['contact_phone']): ?><p><?= esc($period['contact_phone']) ?></p><?php endif ?>
        <?php if ($period['registration_url']): ?><a class="button primary" href="<?= esc($period['registration_url']) ?>" target="_blank" rel="noopener">Buka Pendaftaran</a><?php endif ?>
        <?php if ($period['brochure_path']): ?><a class="arrow-link" href="<?= base_url($period['brochure_path']) ?>" target="_blank">Lihat Brosur PDF →</a><?php endif ?>
        <?php if ($period['qr_path']): ?><img class="spmb-qr" src="<?= base_url($period['qr_path']) ?>" alt="<?= esc($period['qr_alt'] ?: 'QR Pendaftaran SPMB') ?>"><?php endif ?>
    </aside>
</section>

<?php if ($steps !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">ALUR PENDAFTARAN</p><h2>Langkah Pendaftaran</h2></div>
    <div class="spmb-step-grid">
        <?php foreach ($steps as $index => $item): ?>
            <article class="spmb-step-card">
                <span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= esc($item['title']) ?></h3>
                <?php if ($item['description']): ?><p><?= nl2br(esc($item['description'])) ?></p><?php endif ?>
            </article>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($highlights !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">PROGRAM UNGGULAN</p><h2>Yang Tumbuh Bersama Anak</h2></div>
    <div class="spmb-highlight-grid">
        <?php foreach ($highlights as $item): ?>
            <article class="spmb-highlight-card">
                <h3><?= esc($item['title']) ?></h3>
                <?php if ($item['description']): ?><p><?= nl2br(esc($item['description'])) ?></p><?php endif ?>
            </article>
        <?php endforeach ?>
    </div>
</section>
<?php endif ?>

<?php if ($requirements !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">PERSYARATAN</p><h2>Yang perlu disiapkan</h2></div>
    <ol class="requirement-list">
        <?php foreach ($requirements as $item): ?><li><?= esc($item['requirement_text']) ?></li><?php endforeach ?>
    </ol>
</section>
<?php endif ?>

<?php if ($faq !== []): ?>
<section>
    <div class="section-heading"><p class="section-eyebrow">FAQ</p><h2>Pertanyaan yang Sering Diajukan</h2></div>
    <div class="faq-public">
        <?php foreach ($faq as $item): ?><details><summary><?= esc($item['question']) ?></summary><p><?= nl2br(esc($item['answer'])) ?></p></details><?php endforeach ?>
    </div>
</section>
<?php endif ?>
</div>
<?php endif ?>

<?= $this->endSection() ?>
