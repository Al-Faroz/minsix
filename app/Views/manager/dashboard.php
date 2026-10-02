<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">PHASE 2</p>
        <h2>Fondasi konten mulai aktif.</h2>
        <p>Pengaturan global, kontrol ON/OFF fitur publik, dan Media Library sudah tersedia. Modul konten inti akan dibangun pada phase berikutnya.</p>
    </div>
    <span class="phase-badge">PHASE 2</span>
</section>

<section class="status-grid">
    <?php
    $cards = [
        ['01','Website Settings','Identitas, kontak, Instagram resmi, dan lokasi global.','ADMIN'],
        ['02','Feature Toggle','Kabar/Berita, Agenda, Prestasi, Galeri, Instagram, dan SPMB.','ADMIN'],
        ['03','Media Library','Upload JPG/PNG/WebP/PDF, metadata, reuse, dan delete.','ADMIN + OPERATOR'],
        ['04','Security','CSRF, auth/role filter, protected uploads, audit log.','AKTIF'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <article class="status-card">
            <span><?= esc($card[0]) ?></span>
            <h3><?= esc($card[1]) ?></h3>
            <p><?= esc($card[2]) ?></p>
            <strong><?= esc($card[3]) ?></strong>
        </article>
    <?php endforeach ?>
</section>

<section class="quick-grid">
    <?php if (session()->get('auth_role') === 'ADMIN'): ?>
        <a class="quick-card" href="<?= site_url('manager/settings') ?>"><span>01</span><strong>Pengaturan Website</strong><small>Identitas & kontak</small></a>
        <a class="quick-card" href="<?= site_url('manager/features') ?>"><span>02</span><strong>Pengaturan Fitur</strong><small>ON/OFF publik</small></a>
    <?php endif ?>
    <a class="quick-card" href="<?= site_url('manager/media') ?>"><span>03</span><strong>Media Library</strong><small>Upload & kelola media</small></a>
</section>

<section class="next-panel">
    <p class="eyebrow">TAHAP BERIKUTNYA</p>
    <h2>Profil, Program & GTK</h2>
    <p>PHASE 3 mulai menghidupkan master konten utama MIN 6 Jember.</p>
</section>

<?= $this->endSection() ?>
