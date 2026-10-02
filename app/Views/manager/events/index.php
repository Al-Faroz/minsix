<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<div class="toolbar">
<div><p class="eyebrow">KABAR MADRASAH</p><h2 style="margin:.1rem 0">Agenda</h2><p class="muted" style="margin:.35rem 0 0">Kelola jadwal kegiatan yang dapat ditampilkan sebagai agenda mendatang.</p></div>
<div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/events/new') ?>">+ Tambah Agenda</a></div>
</div>
<?php if ($feature && (int) $feature['is_enabled'] !== 1): ?><div class="feature-state off"><strong>Agenda sedang OFF di website publik.</strong><span>Data tetap dapat dikelola.</span></div><?php endif ?>
<?php if ($events === []): ?><div class="empty-state">Belum ada agenda.</div><?php else: ?>
<div class="table-wrap"><table class="cms-table"><thead><tr><th>Agenda</th><th>Waktu</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($events as $item): ?><tr>
<td><div class="table-title"><strong><?= esc($item['title']) ?></strong><small>/<?= esc($item['slug']) ?></small></div></td>
<td><?= esc(date('d-m-Y H:i', strtotime($item['start_at']))) ?><?php if ($item['end_at']): ?><br><small>s.d. <?= esc(date('d-m-Y H:i', strtotime($item['end_at']))) ?></small><?php endif ?></td>
<td><?= esc($item['location'] ?: '—') ?></td>
<td><span class="status-chip <?= strtolower($item['status']) ?>"><?= esc($item['status']) ?></span></td>
<td><div class="table-actions"><a class="text-link" href="<?= site_url('manager/events/' . $item['id'] . '/edit') ?>">Edit</a><form action="<?= site_url('manager/events/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus agenda ini?');"><?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button></form></div></td>
</tr><?php endforeach ?>
</tbody></table></div><div class="pager-wrap"><?= $pager->links('events', 'default_full') ?></div><?php endif ?>
<?= $this->endSection() ?>
