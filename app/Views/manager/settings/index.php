<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="form-panel">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>Identitas & Kontak Website</h2>
            <p class="muted">Pengaturan ini berlaku secara global. Operator tidak dapat mengubahnya.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/settings') ?>" method="post" class="settings-grid">
        <?= csrf_field() ?>

        <div class="field span-2">
            <label for="site_name">Nama website</label>
            <input id="site_name" name="site_name" type="text" maxlength="180" required value="<?= esc(old('site_name', $settings['site_name'] ?? 'MIN 6 Jember')) ?>">
        </div>

        <div class="field span-2">
            <label for="site_tagline">Tagline</label>
            <input id="site_tagline" name="site_tagline" type="text" maxlength="255" value="<?= esc(old('site_tagline', $settings['site_tagline'] ?? 'Berakhlakul Karimah dan Berprestasi')) ?>">
        </div>

        <div class="field span-2">
            <label for="address">Alamat</label>
            <textarea id="address" name="address" rows="3" maxlength="1000"><?= esc(old('address', $settings['address'] ?? '')) ?></textarea>
        </div>

        <div class="field"><label for="phone">Telepon</label><input id="phone" name="phone" type="text" maxlength="80" value="<?= esc(old('phone', $settings['phone'] ?? '')) ?>"></div>
        <div class="field"><label for="whatsapp">WhatsApp</label><input id="whatsapp" name="whatsapp" type="text" maxlength="80" value="<?= esc(old('whatsapp', $settings['whatsapp'] ?? '')) ?>"></div>
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" maxlength="180" value="<?= esc(old('email', $settings['email'] ?? '')) ?>"></div>
        <div class="field"><label for="instagram_username">Username Instagram</label><input id="instagram_username" name="instagram_username" type="text" maxlength="120" value="<?= esc(old('instagram_username', $settings['instagram_username'] ?? 'min6jember')) ?>"></div>

        <div class="field span-2">
            <label for="instagram_url">URL Instagram</label>
            <input id="instagram_url" name="instagram_url" type="url" maxlength="500" value="<?= esc(old('instagram_url', $settings['instagram_url'] ?? 'https://www.instagram.com/min6jember')) ?>">
        </div>

        <div class="field span-2">
            <label for="google_maps_embed">Google Maps embed / URL</label>
            <textarea id="google_maps_embed" name="google_maps_embed" rows="4" maxlength="3000"><?= esc(old('google_maps_embed', $settings['google_maps_embed'] ?? '')) ?></textarea>
            <small>Simpan URL/embed yang nanti akan dipakai pada section kontak. Integrasi visual dikerjakan di frontend.</small>
        </div>

        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Pengaturan</button></div>
    </form>
</section>

<?= $this->endSection() ?>
