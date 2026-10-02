<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$mapUrl = null;
$mapRaw = trim((string) ($site['google_maps_embed'] ?? ''));

if ($mapRaw !== '') {
    $candidate = $mapRaw;

    if (preg_match('~src=["\']([^"\']+)["\']~i', $mapRaw, $matches)) {
        $candidate = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    $parts = parse_url($candidate);
    $host = strtolower((string) ($parts['host'] ?? ''));
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    $path = strtolower((string) ($parts['path'] ?? ''));

    if (
        $scheme === 'https'
        && ($host === 'google.com' || str_ends_with($host, '.google.com'))
        && str_contains($path, '/maps')
    ) {
        $mapUrl = $candidate;
    }
}
?>

<header class="page-hero">
    <div class="site-container">
        <p class="section-eyebrow">PROFIL</p>
        <h1>Mengenal MIN 6 Jember</h1>
        <p>Satu halaman untuk mengenal identitas, visi, perjalanan, sambutan, dan lokasi madrasah.</p>
    </div>
</header>

<div class="site-container page-stack">
<?php foreach ($sections as $index => $item): ?>
    <section class="profile-block<?= $index % 2 ? ' reverse' : '' ?><?= empty($item['relative_path']) ? ' no-image' : '' ?>" id="<?= esc($item['section_key']) ?>">
        <div class="profile-copy">
            <span class="page-index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h2><?= esc($item['title']) ?></h2>
            <?php if ($item['body']): ?><div class="prose"><?= nl2br(esc($item['body'])) ?></div><?php else: ?><p class="empty-copy">Konten section ini belum dilengkapi.</p><?php endif ?>
        </div>
        <?php if ($item['relative_path']): ?>
            <figure class="profile-image"><img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"></figure>
        <?php endif ?>
    </section>
    <?php if (($item['section_key'] ?? '') === 'location' && $mapUrl): ?>
        <div class="profile-map" aria-label="Peta lokasi MIN 6 Jember">
            <iframe
                src="<?= esc($mapUrl) ?>"
                title="Lokasi MIN 6 Jember"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    <?php endif ?>
<?php endforeach ?>
</div>

<?= $this->endSection() ?>
