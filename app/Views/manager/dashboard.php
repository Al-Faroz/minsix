<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero-panel">
    <div>
        <p class="eyebrow">CMS WEBSITE</p>
        <h2>Kelola website MIN 6 JEMBER dari satu tempat.</h2>
        <p>Perbarui profil, program, GTK, kabar madrasah, SPMB, media, dan konten Instagram tanpa mengubah struktur desain website.</p>
    </div>
    <span class="phase-badge">SIAP</span>
</section>

<section class="metric-grid">
    <article class="metric-card"><span>BERITA</span><strong><?= (int) $stats['news'] ?></strong><small>Total konten</small></article>
    <article class="metric-card"><span>PRESTASI</span><strong><?= (int) $stats['achievements'] ?></strong><small>Total data</small></article>
    <article class="metric-card"><span>AGENDA</span><strong><?= (int) $stats['events'] ?></strong><small>Mendatang & published</small></article>
    <article class="metric-card"><span>GTK</span><strong><?= (int) $stats['gtk_active'] ?></strong><small>GTK aktif</small></article>
    <article class="metric-card"><span>GALERI</span><strong><?= (int) $stats['galleries'] ?></strong><small>Total album</small></article>
    <article class="metric-card"><span>SPMB CURRENT</span><strong><?= esc($spmbCurrent['academic_year'] ?? '—') ?></strong><small><?= esc($spmbCurrent['status'] ?? 'Belum ditetapkan') ?></small></article>
</section>

<section class="content-card" style="margin-top:16px">
    <div class="section-head">
        <div>
            <p class="eyebrow">KONTEN TERBARU</p>
            <h2>Perubahan terakhir</h2>
            <p class="muted">Enam konten yang paling baru diperbarui di CMS.</p>
        </div>
    </div>

    <?php if ($recentContent === []): ?>
        <div class="empty-state">Belum ada aktivitas konten.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="cms-table">
                <thead><tr><th>Jenis</th><th>Konten</th><th>Status</th><th>Diperbarui</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($recentContent as $item): ?>
                    <tr>
                        <td><span class="status-chip category"><?= esc($item['type']) ?></span></td>
                        <td><div class="table-title"><strong><?= esc($item['title']) ?></strong></div></td>
                        <td>
                            <?php if ($item['status']): ?>
                                <span class="status-chip <?= strtolower($item['status']) ?>"><?= esc($item['status']) ?></span>
                            <?php else: ?>—<?php endif ?>
                        </td>
                        <td><?= $item['changed_at'] ? esc(date('d-m-Y H:i', strtotime($item['changed_at']))) : '—' ?></td>
                        <td><a class="text-link" href="<?= esc($item['url']) ?>">Buka</a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>

<section class="quick-grid">
    <a class="quick-card" href="<?= site_url('manager/news/new') ?>"><span>01</span><strong>Tambah Berita</strong><small>Publikasi informasi terbaru</small></a>
    <a class="quick-card" href="<?= site_url('manager/achievements/new') ?>"><span>02</span><strong>Tambah Prestasi</strong><small>Catat prestasi siswa/madrasah</small></a>
    <a class="quick-card" href="<?= site_url('manager/events/new') ?>"><span>03</span><strong>Tambah Agenda</strong><small>Jadwalkan kegiatan</small></a>
    <a class="quick-card" href="<?= site_url('manager/spmb') ?>"><span>04</span><strong>Update SPMB</strong><small>Kelola periode penerimaan murid baru</small></a>
</section>

<section class="next-panel">
    <p class="eyebrow">WEBSITE PUBLIK</p>
    <h2>Konten berubah, tampilan tetap konsisten.</h2>
    <p>CMS mengatur isi website; layout, tipografi, warna, dan pola responsive tetap dijaga oleh sistem desain MIN 6 JEMBER.</p>
</section>

<?= $this->endSection() ?>
