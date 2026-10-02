<?= $this->extend('frontend/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$sectionDefaults = [
    'eyebrow' => '',
    'title' => '',
    'subtitle' => '',
    'body' => '',
    'content_data' => [],
    'primary_media_id' => null,
    'cta_label' => '',
    'cta_url' => '',
    'secondary_cta_label' => '',
    'secondary_cta_url' => '',
];

$section = static function (string $key) use ($sections, $sectionDefaults): array {
    return array_merge($sectionDefaults, $sections[$key] ?? []);
};

$link = static function (?string $value, string $fallback): string {
    $value = trim((string) $value);
    if ($value === '') {
        $value = $fallback;
    }

    if (str_starts_with($value, '#')) {
        return $value;
    }

    if (preg_match('~^(?:https?:)?//|^(?:mailto:|tel:)~i', $value)) {
        return $value;
    }

    return site_url(ltrim($value, '/'));
};

$mediaFor = static function (array $row) use ($sectionMedia): ?array {
    $id = (int) ($row['primary_media_id'] ?? 0);
    return $id > 0 ? ($sectionMedia[$id] ?? null) : null;
};
$hero = $section('hero');
$heroMedia = $mediaFor($hero);
$stats = $section('stats');
$statsData = $stats['content_data'] ?? [];
$about = $section('about');
$aboutMedia = $mediaFor($about);
$habits = $section('habits');
$habitMedia = $mediaFor($habits);
$programIntro = $section('program_intro');
$achievementIntro = $section('achievement_intro');
$storyIntro = $section('story_intro');
$newsIntro = $section('news_intro');
$instagramIntro = $section('instagram_intro');
$headmasterSection = $section('headmaster');
$headmasterMedia = $mediaFor($headmasterSection);
$spmbSection = $section('spmb_cta');
$contact = $section('contact');

$habitItems = $habits['content_data']['items'] ?? [
    'Sambut siswa setiap pagi',
    'Upacara bendera hari Senin',
    'Salat Dhuha',
    'Asmaul Husna',
    'Surat-surat pendek dan doa',
    'Salat Zuhur berjamaah',
    "Pembelajaran Al-Qur'an metode Yanbu'a",
];
?>

<section class="hero-section">
    <div class="site-container hero-grid">
        <div class="hero-copy">
            <p class="section-eyebrow"><?= esc($hero['eyebrow'] ?? 'MADRASAH IBTIDAIYAH NEGERI 6 JEMBER') ?></p>
            <h1><?= esc($hero['title'] ?? 'Berakhlakul Karimah. Tumbuh dalam Prestasi.') ?></h1>
            <p class="hero-lead"><?= esc($hero['subtitle'] ?? 'Lingkungan belajar untuk menumbuhkan ilmu, karakter, kreativitas, dan nilai-nilai keislaman sejak usia dasar.') ?></p>
            <?php if (! empty($hero['body'])): ?><p class="hero-body"><?= nl2br(esc($hero['body'])) ?></p><?php endif ?>
            <div class="hero-actions">
                <a class="button primary" href="<?= esc($link($hero['cta_url'], '#mengenal')) ?>"><?= esc($hero['cta_label'] ?: 'Jelajahi Madrasah') ?></a>
                <a class="button text" href="<?= esc($link($hero['secondary_cta_url'], 'profil')) ?>"><?= esc($hero['secondary_cta_label'] ?: 'Kenali MIN 6 JEMBER') ?> <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="hero-visual<?= $heroMedia ? '' : ' placeholder' ?>">
            <?php if ($heroMedia): ?>
                <img src="<?= base_url($heroMedia['relative_path']) ?>" alt="<?= esc($heroMedia['alt_text'] ?: 'Aktivitas MIN 6 JEMBER') ?>">
            <?php else: ?>
                <div class="visual-placeholder"><span>MIN 6</span><small>Tambahkan foto hero melalui CMS Beranda.</small></div>
            <?php endif ?>
        </div>
    </div>

    <?php if ($statsData !== []): ?>
    <div class="site-container stats-strip">
        <?php foreach ($statsData as $stat): ?>
            <?php if (($stat['value'] ?? '') !== ''): ?>
                <div class="stat-item"><strong><?= esc($stat['value']) ?></strong><span><?= esc($stat['label'] ?? '') ?></span></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</section>

<section class="section section-light" id="mengenal">
    <div class="site-container split-editorial">
        <div class="section-copy">
            <p class="section-eyebrow"><?= esc($about['eyebrow'] ?: 'MENGENAL MIN 6 JEMBER') ?></p>
            <h2><?= esc($about['title'] ?: 'Ruang belajar yang menumbuhkan ilmu dan karakter.') ?></h2>
        </div>
        <div class="section-body">
            <?php $aboutBody = trim((string) ($about['body'] ?? '')) ?: trim((string) ($profileAbout['body'] ?? '')); ?>
            <?php if ($aboutBody !== ''): ?><p><?= nl2br(esc($aboutBody)) ?></p><?php else: ?><p>MIN 6 JEMBER tumbuh sebagai ruang belajar yang memadukan pembelajaran, pembiasaan, nilai keislaman, kreativitas, dan prestasi.</p><?php endif ?>
            <a class="arrow-link" href="<?= esc($link($about['cta_url'], 'profil')) ?>"><?= esc($about['cta_label'] ?: 'Selengkapnya') ?> →</a>
        </div>
        <?php if ($aboutMedia): ?><figure class="wide-photo"><img loading="lazy" src="<?= base_url($aboutMedia['relative_path']) ?>" alt="<?= esc($aboutMedia['alt_text'] ?: 'MIN 6 JEMBER') ?>"></figure><?php endif ?>
    </div>
</section>

<section class="section section-dark">
    <div class="site-container">
        <div class="section-heading">
            <p class="section-eyebrow"><?= esc($habits['eyebrow'] ?: 'KESEHARIAN MADRASAH') ?></p>
            <h2><?= esc($habits['title'] ?: 'Keseharian yang Membentuk Karakter') ?></h2>
            <?php if (! empty($habits['body'])): ?><p><?= nl2br(esc($habits['body'])) ?></p><?php endif ?>
        </div>
        <div class="habit-layout">
            <div class="numbered-list">
                <?php foreach ($habitItems as $index => $item): ?>
                    <div class="numbered-row"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><strong><?= esc($item) ?></strong></div>
                <?php endforeach ?>
            </div>
            <div class="habit-photo<?= $habitMedia ? '' : ' placeholder' ?>">
                <?php if ($habitMedia): ?><img loading="lazy" src="<?= base_url($habitMedia['relative_path']) ?>" alt="<?= esc($habitMedia['alt_text'] ?: 'Keseharian MIN 6 JEMBER') ?>"><?php else: ?><div class="visual-placeholder"><span>01–07</span><small>Foto pembiasaan dapat dipilih dari CMS.</small></div><?php endif ?>
            </div>
        </div>
    </div>
</section>

<?php if ($programs !== []): ?>
<section class="section section-light">
    <div class="site-container">
        <div class="section-heading wide">
            <p class="section-eyebrow"><?= esc($programIntro['eyebrow'] ?: 'BELAJAR & BERKEMBANG') ?></p>
            <h2><?= esc($programIntro['title'] ?: 'Program yang mendampingi setiap proses tumbuh.') ?></h2>
            <?php if (! empty($programIntro['subtitle'])): ?><p><?= esc($programIntro['subtitle']) ?></p><?php endif ?>
        </div>
        <div class="program-list">
            <?php foreach ($programs as $index => $program): ?>
                <article class="program-row">
                    <span class="program-no"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div><small><?= esc($program['category'] ?: 'Program') ?></small><h3><?= esc($program['name']) ?></h3></div>
                    <p><?= esc($program['summary'] ?: '') ?></p>
                </article>
            <?php endforeach ?>
        </div>
        <a class="arrow-link" href="<?= esc($link($programIntro['cta_url'], 'program')) ?>"><?= esc($programIntro['cta_label'] ?: 'Lihat Program') ?> →</a>
    </div>
</section>
<?php endif ?>

<?php if ($achievements !== []): ?>
<section class="section section-accent">
    <div class="site-container">
        <div class="section-heading wide">
            <p class="section-eyebrow"><?= esc($achievementIntro['eyebrow'] ?: 'PRESTASI PESERTA DIDIK') ?></p>
            <h2><?= esc($achievementIntro['title'] ?: 'Tumbuh melalui proses, berkembang melalui pengalaman.') ?></h2>
        </div>
        <div class="achievement-grid">
            <?php foreach ($achievements as $achievement): ?>
                <a class="achievement-card" href="<?= site_url('kabar/prestasi/' . $achievement['slug']) ?>">
                    <div class="card-media">
                        <?php if ($achievement['relative_path']): ?><img loading="lazy" src="<?= base_url($achievement['relative_path']) ?>" alt="<?= esc($achievement['alt_text'] ?: $achievement['title']) ?>"><?php else: ?><span>Prestasi</span><?php endif ?>
                    </div>
                    <div class="card-copy">
                        <small><?= esc(trim(($achievement['award'] ?: '') . ($achievement['level'] ? ' · ' . $achievement['level'] : ''))) ?></small>
                        <h3><?= esc($achievement['title']) ?></h3>
                        <?php if ($achievement['participant_name']): ?><p><?= esc($achievement['participant_name']) ?></p><?php endif ?>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<?php if ($galleries !== []): ?>
<section class="section section-dark story-section">
    <div class="site-container">
        <div class="section-heading wide">
            <p class="section-eyebrow"><?= esc($storyIntro['eyebrow'] ?: 'CERITA DARI MADRASAH') ?></p>
            <h2><?= esc($storyIntro['title'] ?: 'Potret keseharian MIN 6 JEMBER.') ?></h2>
        </div>
        <div class="story-grid">
            <?php foreach ($galleries as $index => $gallery): ?>
                <a class="story-card story-<?= ($index % 3) + 1 ?>" href="<?= site_url('kabar/galeri/' . $gallery['slug']) ?>">
                    <div class="story-image">
                        <?php if ($gallery['relative_path']): ?><img loading="lazy" src="<?= base_url($gallery['relative_path']) ?>" alt="<?= esc($gallery['alt_text'] ?: $gallery['title']) ?>"><?php else: ?><span>Galeri</span><?php endif ?>
                    </div>
                    <div><small><?= $gallery['gallery_date'] ? esc(date('d.m.Y', strtotime($gallery['gallery_date']))) : 'GALERI' ?></small><h3><?= esc($gallery['title']) ?></h3></div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<?php if ($news !== [] || $events !== []): ?>
<section class="section section-light">
    <div class="site-container">
        <div class="section-heading wide">
            <p class="section-eyebrow"><?= esc($newsIntro['eyebrow'] ?: 'KABAR TERBARU') ?></p>
            <h2><?= esc($newsIntro['title'] ?: 'Informasi terbaru dari madrasah.') ?></h2>
        </div>
        <div class="kabar-layout">
            <div class="news-grid">
                <?php foreach ($news as $item): ?>
                    <a class="news-card" href="<?= site_url('kabar/berita/' . $item['slug']) ?>">
                        <div class="news-image"><?php if ($item['relative_path']): ?><img loading="lazy" src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['title']) ?>"><?php else: ?><span>Kabar</span><?php endif ?></div>
                        <small><?= esc(date('d.m.Y', strtotime($item['published_at']))) ?></small>
                        <h3><?= esc($item['title']) ?></h3>
                        <?php if ($item['summary']): ?><p><?= esc($item['summary']) ?></p><?php endif ?>
                    </a>
                <?php endforeach ?>
            </div>
            <?php if ($events !== []): ?>
            <aside class="agenda-panel">
                <p class="section-eyebrow">AGENDA MENDATANG</p>
                <?php foreach ($events as $event): ?>
                    <a href="<?= site_url('kabar/agenda/' . $event['slug']) ?>" class="agenda-item">
                        <time datetime="<?= esc(date('Y-m-d', strtotime($event['start_at']))) ?>"><strong><?= esc(date('d', strtotime($event['start_at']))) ?></strong><span><?= esc(strtoupper(date('M', strtotime($event['start_at'])))) ?></span></time>
                        <div><h3><?= esc($event['title']) ?></h3><small><?= esc($event['location'] ?: 'MIN 6 JEMBER') ?></small></div>
                    </a>
                <?php endforeach ?>
            </aside>
            <?php endif ?>
        </div>
        <a class="arrow-link" href="<?= esc($link($newsIntro['cta_url'], 'kabar')) ?>"><?= esc($newsIntro['cta_label'] ?: 'Lihat Semua Kabar') ?> →</a>
    </div>
</section>
<?php endif ?>

<?php if ($showInstagram): ?>
<section class="section instagram-section">
    <div class="site-container">
        <div class="instagram-heading">
            <div>
                <p class="section-eyebrow"><?= esc($instagramIntro['eyebrow'] ?: 'INSTAGRAM') ?></p>
                <h2><?= esc($instagramIntro['title'] ?: 'Ikuti keseharian @min6jember') ?></h2>
            </div>
            <div class="instagram-heading-actions">
                <a class="button outline" target="_blank" rel="noopener" href="<?= esc($site['instagram_url'] ?? 'https://www.instagram.com/min6jember') ?>">@<?= esc($site['instagram_username'] ?? 'min6jember') ?> →</a>
                <?php if ($instagramPosts !== []): ?>
                    <div class="carousel-controls" aria-label="Kontrol carousel Instagram">
                        <button type="button" data-instagram-prev aria-label="Geser ke kiri">←</button>
                        <button type="button" data-instagram-next aria-label="Geser ke kanan">→</button>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <?php if ($instagramPosts !== []): ?>
            <div class="instagram-carousel" data-instagram-carousel tabindex="0" aria-label="Posting Instagram MIN 6 JEMBER">
                <?php foreach ($instagramPosts as $post): ?>
                    <?php
                    $imageSrc = $post['relative_path'] ?: ($post['thumbnail_url'] ?: $post['media_url']);
                    $imageUrl = preg_match('~^https?://~i', (string) $imageSrc) ? $imageSrc : base_url($imageSrc);
                    $targetUrl = $post['permalink'] ?: ($site['instagram_url'] ?? 'https://www.instagram.com/min6jember');
                    ?>
                    <a class="instagram-card" href="<?= esc($targetUrl) ?>" target="_blank" rel="noopener">
                        <div class="instagram-card-media">
                            <img loading="lazy" src="<?= esc($imageUrl) ?>" alt="<?= esc($post['alt_text'] ?: 'Posting Instagram MIN 6 JEMBER') ?>">
                            <span class="instagram-mark" aria-hidden="true">IG</span>
                        </div>
                        <?php if ($post['caption']): ?><p><?= esc($post['caption']) ?></p><?php endif ?>
                    </a>
                <?php endforeach ?>
            </div>
            <p class="instagram-cache-note">Ditampilkan dari <?= esc($instagramSourceUsed === 'API' ? 'cache lokal Instagram' : 'fallback manual') ?>.</p>
        <?php else: ?>
            <div class="instagram-empty">
                <p>Belum ada cache atau fallback Instagram yang dapat ditampilkan.</p>
                <a class="arrow-link" target="_blank" rel="noopener" href="<?= esc($site['instagram_url'] ?? 'https://www.instagram.com/min6jember') ?>">Buka Instagram resmi →</a>
            </div>
        <?php endif ?>
    </div>
</section>
<?php endif ?>

<section class="section section-light">
    <div class="site-container headmaster-grid">
        <div class="headmaster-photo<?= ($headmaster && $headmaster['relative_path']) || $headmasterMedia ? '' : ' placeholder' ?>">
            <?php $hmPhoto = ($headmaster['relative_path'] ?? null) ?: ($headmasterMedia['relative_path'] ?? null); ?>
            <?php if ($hmPhoto): ?><img loading="lazy" src="<?= base_url($hmPhoto) ?>" alt="<?= esc($headmaster['alt_text'] ?? 'Kepala MIN 6 JEMBER') ?>"><?php else: ?><div class="visual-placeholder"><span>GTK</span><small>Tambahkan foto Kepala Madrasah.</small></div><?php endif ?>
        </div>
        <div>
            <p class="section-eyebrow"><?= esc($headmasterSection['eyebrow'] ?: 'SAMBUTAN KEPALA MADRASAH') ?></p>
            <h2><?= esc($headmasterSection['title'] ?: 'Menyambut setiap langkah tumbuh bersama MIN 6 JEMBER.') ?></h2>
            <?php $hmBody = trim((string) ($headmasterSection['body'] ?? '')) ?: trim((string) ($headmasterProfile['body'] ?? '')); ?>
            <?php if ($hmBody): ?><p><?= nl2br(esc($hmBody)) ?></p><?php endif ?>
            <?php if ($headmaster): ?><strong class="signature"><?= esc(trim(($headmaster['front_title'] ? $headmaster['front_title'] . ' ' : '') . $headmaster['name'] . ($headmaster['back_title'] ? ', ' . $headmaster['back_title'] : ''))) ?></strong><?php endif ?>
            <a class="arrow-link" href="<?= esc($link($headmasterSection['cta_url'], 'profil')) ?>"><?= esc($headmasterSection['cta_label'] ?: 'Mengenal Madrasah') ?> →</a>
        </div>
    </div>
</section>

<?php if ($spmb): ?>
<section class="spmb-band">
    <div class="site-container spmb-grid">
        <div><p class="section-eyebrow"><?= esc($spmbSection['eyebrow'] ?: 'SPMB') ?></p><h2><?= esc($spmbSection['title'] ?: 'Mari tumbuh bersama MIN 6 JEMBER.') ?></h2><p><?= esc($spmb['title']) ?> · Tahun Ajaran <?= esc($spmb['academic_year']) ?></p></div>
        <div class="spmb-actions"><a class="button lime" href="<?= site_url('spmb') ?>"><?= esc($spmbSection['cta_label'] ?: 'Informasi SPMB') ?></a><?php if ($spmb['registration_url']): ?><a class="button dark-outline" href="<?= esc($spmb['registration_url']) ?>" target="_blank" rel="noopener">Daftar →</a><?php endif ?></div>
    </div>
</section>
<?php endif ?>

<section class="section contact-section">
    <div class="site-container contact-grid">
        <div><p class="section-eyebrow"><?= esc($contact['eyebrow'] ?: 'KONTAK') ?></p><h2><?= esc($contact['title'] ?: 'Terhubung dengan MIN 6 JEMBER') ?></h2><?php if (! empty($contact['body'])): ?><p><?= nl2br(esc($contact['body'])) ?></p><?php endif ?></div>
        <div class="contact-list">
            <?php if (! empty($site['address'])): ?><div><span>Alamat</span><p><?= nl2br(esc($site['address'])) ?></p></div><?php endif ?>
            <?php if (! empty($site['phone'])): ?><div><span>Telepon</span><a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $site['phone'])) ?>"><?= esc($site['phone']) ?></a></div><?php endif ?>
            <?php if (! empty($site['whatsapp'])): ?><div><span>WhatsApp</span><p><?= esc($site['whatsapp']) ?></p></div><?php endif ?>
            <?php if (! empty($site['email'])): ?><div><span>Email</span><a href="mailto:<?= esc($site['email']) ?>"><?= esc($site['email']) ?></a></div><?php endif ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
