<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">PHASE 5</p>
        <h2>Konten utama CMS semakin lengkap.</h2>
        <p>SPMB tahunan, persyaratan, FAQ, QR, brosur, dan Current period kini dikelola bersama konten madrasah lainnya.</p>
    </div>
    <span class="phase-badge">PHASE 5</span>
</section>

<section class="metric-grid">
    <article class="metric-card"><span>BERITA</span><strong><?= (int) $stats['news'] ?></strong><small>Total konten</small></article>
    <article class="metric-card"><span>PRESTASI</span><strong><?= (int) $stats['achievements'] ?></strong><small>Total data</small></article>
    <article class="metric-card"><span>AGENDA</span><strong><?= (int) $stats['events'] ?></strong><small>Mendatang & published</small></article>
    <article class="metric-card"><span>GTK</span><strong><?= (int) $stats['gtk_active'] ?></strong><small>GTK aktif</small></article>
    <article class="metric-card"><span>GALERI</span><strong><?= (int) $stats['galleries'] ?></strong><small>Total album</small></article>
    <article class="metric-card"><span>SPMB CURRENT</span><strong><?= esc($spmbCurrent['academic_year'] ?? '—') ?></strong><small><?= esc($spmbCurrent['status'] ?? 'Belum ditetapkan') ?></small></article>
</section>

<section class="quick-grid">
    <a class="quick-card" href="<?= site_url('manager/news/new') ?>"><span>01</span><strong>Tambah Berita</strong><small>Publikasi informasi terbaru</small></a>
    <a class="quick-card" href="<?= site_url('manager/achievements/new') ?>"><span>02</span><strong>Tambah Prestasi</strong><small>Catat prestasi siswa/madrasah</small></a>
    <a class="quick-card" href="<?= site_url('manager/events/new') ?>"><span>03</span><strong>Tambah Agenda</strong><small>Jadwalkan kegiatan</small></a>
    <a class="quick-card" href="<?= site_url('manager/spmb') ?>"><span>04</span><strong>Update SPMB</strong><small>Kelola periode penerimaan murid baru</small></a>
</section>

<section class="next-panel">
    <p class="eyebrow">TAHAP BERIKUTNYA</p>
    <h2>Homepage / Frontend</h2>
    <p>PHASE 6 mulai membangun tampilan publik modern-humanis dengan homepage sebagai pusat pengalaman website.</p>
</section>

<?= $this->endSection() ?>
