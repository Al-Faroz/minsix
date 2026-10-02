<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="section-head">
    <div>
        <p class="eyebrow">HOMEPAGE</p>
        <h2>Konten Beranda</h2>
        <p class="muted">Layout dikunci oleh frontend. Admin/Operator hanya mengubah isi, CTA, statistik, pembiasaan, dan foto utama.</p>
    </div>
</div>

<div class="content-stack">
<?php foreach ($sections as $section): ?>
    <?php
    $data = $section['content_data'] ?? [];
    $key = $section['section_key'];
    ?>
    <section class="content-card">
        <div class="content-card__head">
            <div>
                <span class="section-key"><?= esc($key) ?></span>
                <h3><?= esc($section['title'] ?: ucfirst(str_replace('_', ' ', $key))) ?></h3>
            </div>
            <span class="content-card__meta">Urutan <?= (int) $section['display_order'] ?></span>
        </div>

        <form action="<?= site_url('manager/homepage/' . $section['id']) ?>" method="post" class="settings-grid">
            <?= csrf_field() ?>

            <div class="field">
                <label>Eyebrow</label>
                <input type="text" name="eyebrow" maxlength="200" value="<?= esc($section['eyebrow']) ?>">
            </div>
            <div class="field">
                <label>Judul</label>
                <input type="text" name="title" maxlength="255" value="<?= esc($section['title']) ?>">
            </div>

            <div class="field span-2">
                <label>Subjudul</label>
                <textarea name="subtitle" rows="2" maxlength="3000"><?= esc($section['subtitle']) ?></textarea>
            </div>

            <div class="field span-2">
                <label>Isi</label>
                <textarea name="body" rows="<?= in_array($key, ['hero','headmaster','contact'], true) ? 5 : 3 ?>" maxlength="30000"><?= esc($section['body']) ?></textarea>
            </div>

            <?php if ($key === 'stats'): ?>
                <?php
                $stats = [
                    'students' => ['Siswa', 'Jumlah siswa'],
                    'gtk' => ['GTK', 'Jumlah GTK'],
                    'classes' => ['Rombel', 'Jumlah rombel'],
                    'achievements' => ['Prestasi', 'Jumlah prestasi'],
                ];
                ?>
                <?php foreach ($stats as $statKey => [$defaultLabel, $helper]): ?>
                    <div class="field">
                        <label><?= esc($helper) ?></label>
                        <input type="text" name="stat_<?= esc($statKey) ?>_value" maxlength="40" placeholder="Kosongkan jika belum tervalidasi" value="<?= esc($data[$statKey]['value'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label>Label</label>
                        <input type="text" name="stat_<?= esc($statKey) ?>_label" maxlength="80" value="<?= esc($data[$statKey]['label'] ?? $defaultLabel) ?>">
                    </div>
                <?php endforeach ?>
            <?php endif ?>

            <?php if ($key === 'habits'): ?>
                <?php
                $habitDefaults = [
                    'Sambut siswa setiap pagi',
                    'Upacara bendera hari Senin',
                    'Salat Dhuha',
                    'Asmaul Husna',
                    'Surat-surat pendek dan doa',
                    'Salat Zuhur berjamaah',
                    "Pembelajaran Al-Qur'an metode Yanbu'a",
                ];
                $habitItems = $data['items'] ?? $habitDefaults;
                while (count($habitItems) < 7) $habitItems[] = '';
                ?>
                <?php foreach (array_slice($habitItems, 0, 7) as $idx => $habit): ?>
                    <div class="field span-2">
                        <label>Pembiasaan <?= $idx + 1 ?></label>
                        <input type="text" name="habit_items[]" maxlength="255" value="<?= esc($habit) ?>">
                    </div>
                <?php endforeach ?>
            <?php endif ?>

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

            <div class="field">
                <label>CTA Utama — Label</label>
                <input type="text" name="cta_label" maxlength="120" value="<?= esc($section['cta_label']) ?>">
            </div>
            <div class="field">
                <label>CTA Utama — URL</label>
                <input type="text" name="cta_url" maxlength="500" placeholder="/profil atau https://..." value="<?= esc($section['cta_url']) ?>">
            </div>
            <div class="field">
                <label>CTA Kedua — Label</label>
                <input type="text" name="secondary_cta_label" maxlength="120" value="<?= esc($section['secondary_cta_label']) ?>">
            </div>
            <div class="field">
                <label>CTA Kedua — URL</label>
                <input type="text" name="secondary_cta_url" maxlength="500" value="<?= esc($section['secondary_cta_url']) ?>">
            </div>

            <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Section</button></div>
        </form>
    </section>
<?php endforeach ?>
</div>

<?= $this->endSection() ?>
