<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">KONTEN</p>
        <h2 style="margin:.1rem 0">Program</h2>
        <p class="muted" style="margin:.35rem 0 0">Kelola pembelajaran, pembiasaan, keagamaan, ekstrakurikuler, dan program prestasi.</p>
    </div>
    <div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/programs/new') ?>">+ Tambah Program</a></div>
</div>

<?php if ($programs === []): ?>
    <div class="empty-state">Belum ada Program. Tambahkan program pertama.</div>
<?php else: ?>
<div class="table-wrap">
<table class="cms-table">
    <thead><tr><th>Program</th><th>Kategori</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($programs as $program): ?>
        <tr>
            <td><div class="table-title"><strong><?= esc($program['name']) ?></strong><small>/<?= esc($program['slug']) ?></small></div></td>
            <td><span class="status-chip category"><?= esc($program['category']) ?></span></td>
            <td><?= (int) $program['display_order'] ?></td>
            <td><span class="status-chip <?= strtolower($program['status']) ?>"><?= esc($program['status']) ?></span></td>
            <td>
                <div class="table-actions">
                    <a class="text-link" href="<?= site_url('manager/programs/' . $program['id'] . '/edit') ?>">Edit</a>
                    <form action="<?= site_url('manager/programs/' . $program['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus program <?= esc(addslashes($program['name'])) ?>?');">
                        <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
</div>
<?php endif ?>

<?= $this->endSection() ?>
