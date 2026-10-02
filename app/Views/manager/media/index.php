<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="form-panel media-upload-panel">
    <div class="section-head">
        <div>
            <p class="eyebrow">MEDIA LIBRARY</p>
            <h2>Upload Media</h2>
            <p class="muted">Gambar JPG/PNG/WebP atau PDF, maksimal 8 MB. Media dapat dipakai ulang pada modul konten.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/media/upload') ?>" method="post" enctype="multipart/form-data" class="settings-grid">
        <?= csrf_field() ?>
        <div class="field span-2">
            <label for="file">File</label>
            <input id="file" name="file" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" required>
        </div>
        <div class="field">
            <label for="alt_text">Alt text</label>
            <input id="alt_text" name="alt_text" type="text" maxlength="255" value="<?= esc(old('alt_text')) ?>">
            <small>Disarankan untuk gambar agar aksesibilitas dan SEO lebih baik.</small>
        </div>
        <div class="field"><label for="caption">Caption</label><input id="caption" name="caption" type="text" maxlength="1000" value="<?= esc(old('caption')) ?>"></div>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Upload Media</button></div>
    </form>
</section>

<section class="media-section">
    <div class="section-head section-head--inline">
        <div><p class="eyebrow">PUSTAKA MEDIA</p><h2>Media Tersimpan</h2></div>
        <span class="count-badge"><?= count($media) ?> item di halaman ini</span>
    </div>

    <?php if ($media === []): ?>
        <div class="empty-state">Belum ada media. Upload file pertama melalui form di atas.</div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <article class="media-card">
                    <div class="media-preview">
                        <?php if ($item['media_type'] === 'IMAGE'): ?>
                            <img src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['original_name']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="document-preview">PDF</div>
                        <?php endif ?>
                    </div>

                    <div class="media-card-body">
                        <strong title="<?= esc($item['original_name']) ?>"><?= esc($item['original_name']) ?></strong>
                        <span><?= esc(strtoupper($item['extension'])) ?> · <?= number_format(((int) $item['file_size']) / 1024, 0, ',', '.') ?> KB</span>
                        <?php if ($item['width'] && $item['height']): ?><span><?= (int) $item['width'] ?> × <?= (int) $item['height'] ?> px</span><?php endif ?>

                        <details>
                            <summary>Edit metadata</summary>
                            <form action="<?= site_url('manager/media/' . $item['id'] . '/update') ?>" method="post" class="mini-form">
                                <?= csrf_field() ?>
                                <label>Alt text<input type="text" name="alt_text" maxlength="255" value="<?= esc($item['alt_text']) ?>"></label>
                                <label>Caption<textarea name="caption" rows="2" maxlength="1000"><?= esc($item['caption']) ?></textarea></label>
                                <button class="btn ghost" type="submit">Simpan Metadata</button>
                            </form>
                        </details>

                        <div class="media-actions">
                            <a class="text-link" href="<?= base_url($item['relative_path']) ?>" target="_blank" rel="noopener">Buka</a>
                            <form action="<?= site_url('manager/media/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus media ini? File fisik juga akan dihapus.');">
                                <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach ?>
        </div>

        <div class="pager-wrap"><?= $pager->links('media', 'default_full') ?></div>
    <?php endif ?>
</section>

<?= $this->endSection() ?>
