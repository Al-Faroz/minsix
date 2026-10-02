<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">PENERIMAAN MURID BARU</p>
        <h2 style="margin:.1rem 0">SPMB</h2>
        <p class="muted" style="margin:.35rem 0 0">Kelola informasi SPMB per tahun ajaran. Hanya satu periode yang dapat ditandai Current.</p>
    </div>
    <div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/spmb/new') ?>">+ Tambah Periode</a></div>
</div>

<?php if ($feature && (int) $feature['is_enabled'] !== 1): ?>
    <div class="feature-state off">
        <strong>SPMB sedang OFF di website publik.</strong>
        <span>Periode dan seluruh konten SPMB tetap dapat dikelola di CMS. Hanya Admin yang dapat mengubah ON/OFF fitur.</span>
    </div>
<?php endif ?>

<?php if ($periods === []): ?>
    <div class="empty-state">Belum ada periode SPMB.</div>
<?php else: ?>
<div class="table-wrap">
<table class="cms-table">
<thead><tr><th>Periode</th><th>Pendaftaran</th><th>Current</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($periods as $item): ?>
<tr>
    <td>
        <div class="table-title">
            <strong><?= esc($item['academic_year']) ?> — <?= esc($item['title']) ?></strong>
            <small><?= esc($item['summary'] ?: 'Belum ada ringkasan') ?></small>
        </div>
    </td>
    <td>
        <?php if ($item['start_date'] || $item['end_date']): ?>
            <?= $item['start_date'] ? esc(date('d-m-Y', strtotime($item['start_date']))) : '—' ?>
            <br><small>s.d. <?= $item['end_date'] ? esc(date('d-m-Y', strtotime($item['end_date']))) : '—' ?></small>
        <?php else: ?>—<?php endif ?>
    </td>
    <td>
        <?php if ((int) $item['is_current'] === 1): ?>
            <span class="status-chip current">CURRENT</span>
        <?php else: ?>
            <form action="<?= site_url('manager/spmb/' . $item['id'] . '/current') ?>" method="post" onsubmit="return confirm('Jadikan periode ini sebagai Current SPMB? Current sebelumnya akan dilepas.');">
                <?= csrf_field() ?>
                <button class="text-link" type="submit">Jadikan Current</button>
            </form>
        <?php endif ?>
    </td>
    <td><span class="status-chip <?= strtolower($item['status']) ?>"><?= esc($item['status']) ?></span></td>
    <td>
        <div class="table-actions">
            <a class="text-link" href="<?= site_url('manager/spmb/' . $item['id'] . '/edit') ?>">Edit</a>
            <form action="<?= site_url('manager/spmb/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus periode SPMB <?= esc(addslashes($item['academic_year'])) ?>? Persyaratan dan FAQ pada periode ini juga akan dihapus.');">
                <?= csrf_field() ?>
                <button class="text-danger" type="submit">Hapus</button>
            </form>
        </div>
    </td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<div class="pager-wrap"><?= $pager->links('spmb', 'default_full') ?></div>
<?php endif ?>

<?= $this->endSection() ?>
