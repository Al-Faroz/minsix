<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<article class="article-page">
    <header class="article-header site-container">
        <p class="section-eyebrow">AGENDA</p>
        <h1><?= esc($item['title']) ?></h1>
        <div class="event-meta">
            <div><span>Mulai</span><strong><?= esc(date('d.m.Y H:i', strtotime($item['start_at']))) ?></strong></div>
            <?php if ($item['end_at']): ?><div><span>Selesai</span><strong><?= esc(date('d.m.Y H:i', strtotime($item['end_at']))) ?></strong></div><?php endif ?>
            <?php if ($item['location']): ?><div><span>Lokasi</span><strong><?= esc($item['location']) ?></strong></div><?php endif ?>
        </div>
    </header>
    <?php if ($item['relative_path']): ?><figure class="article-cover site-container"><img src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"></figure><?php endif ?>
    <?php if ($item['description'] || $item['summary']): ?><div class="article-body site-container prose"><?= nl2br(esc($item['description'] ?: $item['summary'])) ?></div><?php endif ?>
</article>
<?= $this->endSection() ?>
