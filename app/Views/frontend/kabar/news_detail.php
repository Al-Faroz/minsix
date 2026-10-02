<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<article class="article-page">
    <header class="article-header site-container">
        <p class="section-eyebrow">BERITA · <?= esc(date('d.m.Y', strtotime($item['published_at']))) ?></p>
        <h1><?= esc($item['title']) ?></h1>
        <?php if ($item['summary']): ?><p><?= esc($item['summary']) ?></p><?php endif ?>
    </header>
    <?php if ($item['relative_path']): ?><figure class="article-cover site-container"><img src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"></figure><?php endif ?>
    <div class="article-body site-container prose"><?= nl2br(esc($item['content'])) ?></div>
</article>
<?= $this->endSection() ?>
