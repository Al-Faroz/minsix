<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">SELAMAT DATANG</p>
        <h2>Fondasi CMS sudah siap.</h2>
        <p>Autentikasi, dua role, proteksi route, CSRF, login throttling, audit login, dan perubahan password sudah disiapkan.</p>
    </div>
    <span class="phase-badge">PHASE 1</span>
</section>

<section class="status-grid">
    <?php
    $cards = [
        ['01','Authentication','Login, logout, rate limit, session regeneration, dan password hash.','SIAP'],
        ['02','Role','ADMIN untuk sistem + konten. OPERATOR hanya konten.','DIKUNCI'],
        ['03','Database','Schema memakai SQL dump tanpa Migration/Seeder.','SQL DUMP'],
        ['04','Manager UI','Sidebar responsif siap diisi modul CMS berikutnya.','SIAP DIISI'],
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

<section class="next-panel">
    <p class="eyebrow">TAHAP BERIKUTNYA</p>
    <h2>Settings, Feature Toggle & Media</h2>
    <p>PHASE 2 akan menghidupkan pengaturan global, ON/OFF fitur, dan Media Library.</p>
</section>

<?= $this->endSection() ?>
