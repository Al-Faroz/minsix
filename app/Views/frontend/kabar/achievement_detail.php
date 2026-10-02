<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<article class="article-page">
    <header class="article-header site-container">
        <p class="section-eyebrow">PRESTASI<?= $item['achievement_date'] ? ' · ' . esc(date('d.m.Y', strtotime($item['achievement_date']))) : '' ?></p>
        <h1><?= esc($item['title']) ?></h1>
        <div class="achievement-meta">
            <?php if ($item['participant_name']): ?><span>Peserta<strong><?= esc($item['participant_name']) ?></strong></span><?php endif ?>
            <?php if ($item['award']): ?><span>Penghargaan<strong><?= esc($item['award']) ?></strong></span><?php endif ?>
            <?php if ($item['level']): ?><span>Tingkat<strong><?= esc($item['level']) ?></strong></span><?php endif ?>
            <?php if ($item['field_name']): ?><span>Bidang<strong><?= esc($item['field_name']) ?></strong></span><?php endif ?>
        </div>
    </header>
    <?php if ($item['relative_path']): ?><figure class="article-cover site-container"><img src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"></figure><?php endif ?>
    <?php if ($item['content'] || $item['summary']): ?><div class="article-body site-container prose"><?= nl2br(esc($item['content'] ?: $item['summary'])) ?></div><?php endif ?>
</article>
<?= $this->endSection() ?>
