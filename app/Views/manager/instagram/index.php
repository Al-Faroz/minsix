<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="section-head">
    <div>
        <p class="eyebrow">INSTAGRAM HYBRID</p>
        <h2>Instagram Content</h2>
        <p class="muted">Operator mengelola fallback manual. Data API ditampilkan sebagai cache read-only dan akan diisi melalui sinkronisasi.</p>
    </div>
</div>

<?php if ($feature && (int) $feature['is_enabled'] !== 1): ?>
<div class="feature-state off"><strong>Instagram sedang OFF di website publik.</strong><span>Fallback manual dan cache API tetap dapat dikelola.</span></div>
<?php endif ?>

<section class="content-card">
    <div class="section-head"><div><p class="eyebrow">FALLBACK MANUAL</p><h2>Tambah Post Manual</h2></div></div>
    <form action="<?= site_url('manager/instagram') ?>" method="post" class="settings-grid">
        <?= csrf_field() ?>
        <div class="field span-2">
            <label>Gambar *</label>
            <select name="local_media_id" required>
                <option value="">— Pilih dari Media Library —</option>
                <?php foreach ($images as $image): ?>
                    <option value="<?= (int) $image['id'] ?>" <?= (int) old('local_media_id', 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="field span-2"><label>Permalink Instagram</label><input type="url" name="permalink" maxlength="500" value="<?= esc(old('permalink')) ?>"></div>
        <div class="field span-2"><label>Caption pendek</label><textarea name="caption" rows="3" maxlength="5000"><?= esc(old('caption')) ?></textarea></div>
        <div class="field"><label>Tanggal</label><input type="datetime-local" name="published_at" value="<?= esc(old('published_at')) ?>"></div>
        <div class="field"><label>Urutan</label><input type="number" name="sort_order" value="<?= esc(old('sort_order', '0')) ?>"></div>
        <label class="checkbox-row span-2"><input type="checkbox" name="is_visible" value="1" <?= old('is_visible', '1') ? 'checked' : '' ?>><span><strong>Tampilkan</strong><small>Fallback ini boleh tampil pada carousel.</small></span></label>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Tambah Fallback</button></div>
    </form>
</section>

<section class="content-card">
    <div class="section-head"><div><p class="eyebrow">MANUAL</p><h2>Fallback Aktif</h2><p class="muted">Dipakai jika mode MANUAL atau cache API tidak tersedia.</p></div><a class="btn ghost" href="<?= site_url('manager/media') ?>">Media Library</a></div>
    <?php if ($manualPosts === []): ?>
        <div class="empty-state">Belum ada fallback manual.</div>
    <?php else: ?>
        <div class="instagram-manager-grid">
        <?php foreach ($manualPosts as $post): ?>
            <?php $selectedMedia = null; foreach ($images as $image) { if ((int) $image['id'] === (int) $post['local_media_id']) { $selectedMedia = $image; break; } } ?>
            <article class="instagram-manager-card">
                <div class="instagram-manager-preview">
                    <?php if ($selectedMedia): ?><img src="<?= base_url($selectedMedia['relative_path']) ?>" alt="<?= esc($selectedMedia['alt_text'] ?: $selectedMedia['original_name']) ?>"><?php else: ?><span>Media tidak tersedia</span><?php endif ?>
                </div>
                <form action="<?= site_url('manager/instagram/' . $post['id']) ?>" method="post" class="mini-form">
                    <?= csrf_field() ?>
                    <label>Gambar<select name="local_media_id" required><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) $image['id'] === (int) $post['local_media_id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></label>
                    <label>Permalink<input type="url" name="permalink" maxlength="500" value="<?= esc($post['permalink']) ?>"></label>
                    <label>Caption<textarea name="caption" rows="3" maxlength="5000"><?= esc($post['caption']) ?></textarea></label>
                    <label>Tanggal<input type="datetime-local" name="published_at" value="<?= $post['published_at'] ? esc(date('Y-m-d\TH:i', strtotime($post['published_at']))) : '' ?>"></label>
                    <label>Urutan<input type="number" name="sort_order" value="<?= (int) $post['sort_order'] ?>"></label>
                    <label class="checkbox-row"><input type="checkbox" name="is_visible" value="1" <?= (int) $post['is_visible'] === 1 ? 'checked' : '' ?>><span><strong>Tampilkan</strong></span></label>
                    <button class="btn ghost" type="submit">Simpan</button>
                </form>
                <form action="<?= site_url('manager/instagram/' . $post['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus fallback Instagram ini?');">
                    <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                </form>
            </article>
        <?php endforeach ?>
        </div>
    <?php endif ?>
</section>

<section class="content-card">
    <div class="section-head"><div><p class="eyebrow">LOCAL CACHE</p><h2>Cache dari API</h2><p class="muted">Read-only. Sinkronisasi tidak menghapus cache lama ketika API gagal.</p></div></div>
    <?php if ($apiPosts === []): ?>
        <div class="empty-state">Belum ada cache Instagram API.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="cms-table"><thead><tr><th>Media ID</th><th>Type</th><th>Items</th><th>Terbit</th><th>Fetch</th><th>Permalink</th></tr></thead><tbody>
        <?php foreach ($apiPosts as $post): ?><tr>
            <td><?= esc($post['instagram_media_id'] ?: '—') ?></td>
            <td><?= esc($post['media_type'] ?: '—') ?></td>
            <?php $children = ! empty($post['children_json']) ? json_decode((string) $post['children_json'], true) : []; ?>
            <td><?= is_array($children) && $children !== [] ? count($children) : 1 ?></td>
            <td><?= $post['published_at'] ? esc(date('d-m-Y H:i', strtotime($post['published_at']))) : '—' ?></td>
            <td><?= $post['fetched_at'] ? esc(date('d-m-Y H:i', strtotime($post['fetched_at']))) : '—' ?></td>
            <td><?php if ($post['permalink']): ?><a class="text-link" href="<?= esc($post['permalink']) ?>" target="_blank" rel="noopener">Buka ↗</a><?php else: ?>—<?php endif ?></td>
        </tr><?php endforeach ?>
        </tbody></table></div>
    <?php endif ?>
</section>

<?= $this->endSection() ?>
