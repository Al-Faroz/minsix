<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<div class="toolbar">
<div><p class="eyebrow">KABAR MADRASAH</p><h2 style="margin:.1rem 0">Prestasi</h2><p class="muted" style="margin:.35rem 0 0">Kelola prestasi peserta didik, tim, maupun madrasah.</p></div>
<div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/achievements/new') ?>">+ Tambah Prestasi</a></div>
</div>
<?php if ($kabarFeature && (int) $kabarFeature['is_enabled'] !== 1): ?>
    <div class="feature-state off"><strong>Kabar Madrasah sedang OFF di website publik.</strong><span>Prestasi tetap dapat dikelola, tetapi tidak akan tampil sampai fitur induk Kabar Madrasah diaktifkan kembali.</span></div>
<?php elseif ($feature && (int) $feature['is_enabled'] !== 1): ?>
    <div class="feature-state off"><strong>Prestasi sedang OFF di website publik.</strong><span>Data tetap dapat dikelola dari CMS. Hanya Admin yang dapat mengubah pengaturan fitur.</span></div>
<?php endif ?>
<?php if ($achievements === []): ?><div class="empty-state">Belum ada prestasi.</div><?php else: ?>
<div class="table-wrap"><table class="cms-table"><thead><tr><th>Prestasi</th><th>Peserta</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($achievements as $item): ?><tr>
<td><div class="table-title"><strong><?= esc($item['title']) ?></strong><small><?= esc(trim(($item['award'] ?: '') . ($item['level'] ? ' · ' . $item['level'] : ''))) ?></small></div></td>
<td><?= esc($item['participant_name'] ?: 'Madrasah/Tim') ?></td>
<td><?= $item['achievement_date'] ? esc(date('d-m-Y', strtotime($item['achievement_date']))) : '—' ?></td>
<td><span class="status-chip <?= strtolower($item['status']) ?>"><?= esc($item['status']) ?></span></td>
<td><div class="table-actions"><a class="text-link" href="<?= site_url('manager/achievements/' . $item['id'] . '/edit') ?>">Edit</a><form action="<?= site_url('manager/achievements/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus prestasi ini?');"><?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button></form></div></td>
</tr><?php endforeach ?>
</tbody></table></div><div class="pager-wrap"><?= $pager->links('achievements', 'default_full') ?></div><?php endif ?>
<?= $this->endSection() ?>
