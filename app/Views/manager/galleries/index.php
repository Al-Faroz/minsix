<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">KABAR MADRASAH</p>
        <h2 style="margin:.1rem 0">Galeri</h2>
        <p class="muted" style="margin:.35rem 0 0">Kelola album kegiatan dan foto yang sudah tersimpan di Media Library.</p>
    </div>
    <div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/galleries/new') ?>">+ Tambah Album</a></div>
</div>

<?php if ($kabarFeature && (int) $kabarFeature['is_enabled'] !== 1): ?>
    <div class="feature-state off"><strong>Kabar Madrasah sedang OFF di website publik.</strong><span>Galeri tetap dapat dikelola, tetapi tidak akan tampil sampai fitur induk Kabar Madrasah diaktifkan kembali.</span></div>
<?php elseif ($feature && (int) $feature['is_enabled'] !== 1): ?>
    <div class="feature-state off"><strong>Galeri sedang OFF di website publik.</strong><span>Data tetap dapat dikelola dari CMS. Hanya Admin yang dapat mengubah pengaturan fitur.</span></div>
<?php endif ?>

<?php if ($galleries === []): ?>
    <div class="empty-state">Belum ada album galeri.</div>
<?php else: ?>
<div class="table-wrap">
<table class="cms-table">
<thead><tr><th>Album</th><th>Tanggal</th><th>Foto</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($galleries as $item): ?>
<tr>
    <td><div class="table-title"><strong><?= esc($item['title']) ?></strong><small>/<?= esc($item['slug']) ?></small></div></td>
    <td><?= $item['gallery_date'] ? esc(date('d-m-Y', strtotime($item['gallery_date']))) : '—' ?></td>
    <td><?= (int) $item['item_count'] ?> foto</td>
    <td><span class="status-chip <?= strtolower($item['status']) ?>"><?= esc($item['status']) ?></span></td>
    <td><div class="table-actions"><a class="text-link" href="<?= site_url('manager/galleries/' . $item['id'] . '/edit') ?>">Edit</a><form action="<?= site_url('manager/galleries/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus album ini? Foto di Media Library tidak ikut terhapus.');"><?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button></form></div></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<div class="pager-wrap"><?= $pager->links('galleries', 'default_full') ?></div>
<?php endif ?>

<?= $this->endSection() ?>
