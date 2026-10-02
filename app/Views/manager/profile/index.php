<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="section-head">
    <div>
        <p class="eyebrow">KONTEN PROFIL</p>
        <h2>Profil MIN 6 JEMBER</h2>
        <p class="muted">Struktur section dikunci. Admin/Operator hanya mengubah isi dan foto.</p>
    </div>
</div>

<div class="content-stack">
    <?php foreach ($sections as $section): ?>
        <section class="content-card">
            <div class="content-card__head">
                <div>
                    <span class="section-key"><?= esc($section['section_key']) ?></span>
                    <h3><?= esc($section['title']) ?></h3>
                </div>
                <span class="content-card__meta">Urutan <?= (int) $section['display_order'] ?></span>
            </div>

            <form action="<?= site_url('manager/profile/' . $section['id']) ?>" method="post" class="settings-grid">
                <?= csrf_field() ?>

                <div class="field span-2">
                    <label>Judul Section</label>
                    <input type="text" name="title" maxlength="255" required value="<?= esc($section['title']) ?>">
                </div>

                <div class="field span-2">
                    <label>Isi</label>
                    <textarea name="body" rows="<?= in_array($section['section_key'], ['history','timeline','headmaster_message'], true) ? 10 : 6 ?>" maxlength="30000"><?= esc($section['body']) ?></textarea>
                    <?php if ($section['section_key'] === 'timeline'): ?>
                        <small>Untuk sementara tulis timeline per baris. Struktur visual timeline akan dibentuk oleh frontend.</small>
                    <?php endif ?>
                </div>

                <div class="field span-2">
                    <label>Foto Utama</label>
                    <select name="primary_media_id">
                        <option value="">— Tanpa foto —</option>
                        <?php foreach ($images as $image): ?>
                            <option value="<?= (int) $image['id'] ?>" <?= (int) ($section['primary_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>
                                #<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Section</button></div>
            </form>
        </section>
    <?php endforeach ?>
</div>

<?= $this->endSection() ?>
