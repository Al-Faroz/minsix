<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">PHASE 4</p>
        <h2>Kabar Madrasah siap dikelola.</h2>
        <p>Berita, Agenda, Prestasi, dan Galeri tetap dapat dikelola dari CMS walaupun fitur publiknya sedang OFF.</p>
    </div>
    <span class="phase-badge">PHASE 4</span>
</section>

<section class="metric-grid">
    <article class="metric-card"><span>BERITA</span><strong><?= (int) $stats['news'] ?></strong><small>Total konten</small></article>
    <article class="metric-card"><span>PRESTASI</span><strong><?= (int) $stats['achievements'] ?></strong><small>Total data</small></article>
    <article class="metric-card"><span>AGENDA</span><strong><?= (int) $stats['events'] ?></strong><small>Mendatang & published</small></article>
    <article class="metric-card"><span>GALERI</span><strong><?= (int) $stats['galleries'] ?></strong><small>Total album</small></article>
</section>

<section class="quick-grid">
    <a class="quick-card" href="<?= site_url('manager/news/new') ?>"><span>01</span><strong>Tambah Berita</strong><small>Publikasi informasi terbaru</small></a>
    <a class="quick-card" href="<?= site_url('manager/achievements/new') ?>"><span>02</span><strong>Tambah Prestasi</strong><small>Catat prestasi siswa/madrasah</small></a>
    <a class="quick-card" href="<?= site_url('manager/events/new') ?>"><span>03</span><strong>Tambah Agenda</strong><small>Jadwalkan kegiatan</small></a>
</section>

<section class="next-panel">
    <p class="eyebrow">TAHAP BERIKUTNYA</p>
    <h2>SPMB</h2>
    <p>PHASE 5 akan membangun periode SPMB, persyaratan, FAQ, brosur, QR, dan gateway pendaftaran.</p>
</section>

<?= $this->endSection() ?>
