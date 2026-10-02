<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\HomepageSectionModel;
use App\Models\MediaModel;
use CodeIgniter\HTTP\RedirectResponse;

class HomepageController extends BaseController
{
    public function index(): string
    {
        $sections = (new HomepageSectionModel())->orderBy('display_order', 'ASC')->findAll();

        foreach ($sections as &$section) {
            $decoded = [];
            if (! empty($section['content_json'])) {
                $value = json_decode((string) $section['content_json'], true);
                $decoded = is_array($value) ? $value : [];
            }
            $section['content_data'] = $decoded;
        }
        unset($section);

        return view('manager/homepage/index', [
            'title' => 'Beranda | CMS MIN 6 Jember',
            'pageTitle' => 'Beranda',
            'sections' => $sections,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new HomepageSectionModel();
        $section = $model->find($id);

        if (! $section) {
            return redirect()->to(site_url('manager/homepage'))->with('error', 'Section Beranda tidak ditemukan.');
        }

        if (! $this->validate([
            'eyebrow' => 'permit_empty|max_length[200]',
            'title' => 'permit_empty|max_length[255]',
            'subtitle' => 'permit_empty|max_length[3000]',
            'body' => 'permit_empty|max_length[30000]',
            'primary_media_id' => 'permit_empty|is_natural_no_zero',
            'cta_label' => 'permit_empty|max_length[120]',
            'cta_url' => 'permit_empty|max_length[255]',
            'secondary_cta_label' => 'permit_empty|max_length[120]',
            'secondary_cta_url' => 'permit_empty|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $mediaId = $this->request->getPost('primary_media_id');
        $mediaId = $mediaId !== null && $mediaId !== '' ? (int) $mediaId : null;

        if ($mediaId !== null && ! (new MediaModel())->where('id', $mediaId)->where('media_type', 'IMAGE')->first()) {
            return redirect()->back()->withInput()->with('error', 'Foto utama tidak valid.');
        }

        $contentJson = $this->buildContentJson((string) $section['section_key']);

        $updated = $model->update($id, [
            'eyebrow' => trim((string) $this->request->getPost('eyebrow')),
            'title' => trim((string) $this->request->getPost('title')),
            'subtitle' => trim((string) $this->request->getPost('subtitle')),
            'body' => trim((string) $this->request->getPost('body')),
            'content_json' => $contentJson,
            'primary_media_id' => $mediaId,
            'cta_label' => trim((string) $this->request->getPost('cta_label')),
            'cta_url' => trim((string) $this->request->getPost('cta_url')),
            'secondary_cta_label' => trim((string) $this->request->getPost('secondary_cta_label')),
            'secondary_cta_url' => trim((string) $this->request->getPost('secondary_cta_url')),
            'updated_by' => (int) session()->get('auth_user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($updated === false) {
            return redirect()->back()->withInput()->with('error', 'Konten Beranda gagal disimpan.');
        }

        $this->audit($id, 'HOMEPAGE_UPDATED', 'Section Beranda diperbarui: ' . $section['section_key']);

        return redirect()->to(site_url('manager/homepage'))->with('success', 'Section Beranda berhasil diperbarui.');
    }

    private function buildContentJson(string $sectionKey): ?string
    {
        if ($sectionKey === 'stats') {
            $keys = ['students', 'gtk', 'classes', 'achievements'];
            $result = [];

            foreach ($keys as $key) {
                $value = trim((string) $this->request->getPost('stat_' . $key . '_value'));
                $label = trim((string) $this->request->getPost('stat_' . $key . '_label'));

                if ($value !== '' || $label !== '') {
                    $result[$key] = ['value' => $value, 'label' => $label];
                }
            }

            return $result === [] ? null : json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if ($sectionKey === 'habits') {
            $items = $this->request->getPost('habit_items') ?? [];
            if (! is_array($items)) {
                return null;
            }

            $items = array_values(array_filter(
                array_map(static fn ($item): string => trim((string) $item), $items),
                static fn (string $item): bool => $item !== ''
            ));

            return $items === [] ? null : json_encode(['items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return null;
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'HOMEPAGE',
                'record_id' => $id,
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
