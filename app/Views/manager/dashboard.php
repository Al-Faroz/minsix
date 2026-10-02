<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">PHASE 3</p>
        <h2>Konten inti madrasah mulai terbentuk.</h2>
        <p>Profil fixed-section, Program, serta master GTK dengan multi-role telah tersedia untuk Admin dan Operator.</p>
    </div>
    <span class="phase-badge">PHASE 3</span>
</section>

<section class="quick-grid">
    <a class="quick-card" href="<?= site_url('manager/profile') ?>"><span>01</span><strong>Profil</strong><small>Tentang, visi, sejarah, timeline, sambutan, identitas, lokasi</small></a>
    <a class="quick-card" href="<?= site_url('manager/programs') ?>"><span>02</span><strong>Program</strong><small>Pembelajaran, karakter, keagamaan, ekstrakurikuler</small></a>
    <a class="quick-card" href="<?= site_url('manager/gtk') ?>"><span>03</span><strong>GTK</strong><small>Satu orang satu master, banyak jabatan</small></a>
</section>

<section class="next-panel">
    <p class="eyebrow">TAHAP BERIKUTNYA</p>
    <h2>Kabar Madrasah</h2>
    <p>PHASE 4 akan menghidupkan Berita, Agenda, Prestasi, dan Galeri dengan mengikuti feature toggle PHASE 2.</p>
</section>

<?= $this->endSection() ?>
