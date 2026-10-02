<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>SEO Global</h2>
            <p class="muted">Default ini dipakai ketika halaman atau konten tidak memiliki metadata yang lebih spesifik.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/seo') ?>" method="post" class="settings-grid">
        <?= csrf_field() ?>

        <div class="field span-2">
            <label>Default Site Title</label>
            <input type="text" name="seo_default_title" maxlength="255" value="<?= esc(old('seo_default_title', $settings['seo_default_title'] ?? 'MIN 6 JEMBER')) ?>">
            <small>Homepage menggunakan nilai ini. Halaman lain mengikuti pola: Nama Halaman | MIN 6 JEMBER.</small>
        </div>

        <div class="field span-2">
            <label>Default Meta Description</label>
            <textarea name="seo_default_description" rows="3" maxlength="320"><?= esc(old('seo_default_description', $settings['seo_default_description'] ?? '')) ?></textarea>
        </div>

        <div class="field span-2">
            <label>Default OG Image</label>
            <select name="seo_default_og_media_id">
                <option value="">— Belum ditentukan —</option>
                <?php foreach ($images as $image): ?>
                    <option value="<?= (int) $image['id'] ?>" <?= (int) old('seo_default_og_media_id', $settings['seo_default_og_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>
                        #<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
            <small>Disarankan foto rasio sekitar 1.91:1 untuk preview sosial.</small>
        </div>

        <div class="field span-2">
            <label>Canonical Base URL</label>
            <input type="url" name="seo_canonical_base_url" maxlength="500" placeholder="https://domain-resmi.sch.id" value="<?= esc(old('seo_canonical_base_url', $settings['seo_canonical_base_url'] ?? '')) ?>">
            <small>Kosongkan saat development. Isi domain HTTPS final saat production; jangan isi URL localhost.</small>
        </div>

        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan SEO</button></div>
    </form>
</section>

<section class="content-card">
    <p class="eyebrow">OUTPUT</p>
    <h2 style="margin:.15rem 0 .7rem">Metadata Publik</h2>
    <p class="muted">Frontend menghasilkan title, meta description, canonical URL, Open Graph, Twitter Card, structured data institusi, dan sitemap berdasarkan konten yang aktif.</p>
    <a class="text-link" href="<?= site_url('sitemap.xml') ?>" target="_blank">Buka sitemap.xml ↗</a>
</section>

<?= $this->endSection() ?>
