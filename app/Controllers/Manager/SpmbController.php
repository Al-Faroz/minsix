<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use App\Models\SiteFeatureModel;
use App\Models\SpmbFaqModel;
use App\Models\SpmbPeriodModel;
use App\Models\SpmbRequirementModel;
use CodeIgniter\HTTP\RedirectResponse;

class SpmbController extends BaseController
{
    public function index(): string
    {
        $model = new SpmbPeriodModel();

        return view('manager/spmb/index', [
            'title' => 'SPMB | CMS MIN 6 Jember',
            'pageTitle' => 'SPMB',
            'periods' => $model
                ->orderBy('is_current', 'DESC')
                ->orderBy('academic_year', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate(20, 'spmb'),
            'pager' => $model->pager,
            'feature' => (new SiteFeatureModel())->where('feature_key', 'spmb')->first(),
        ]);
    }

    public function new(): string
    {
        return $this->formView(null);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validatePeriod()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! $this->datesAreValid()) {
            return redirect()->back()->withInput()->with('error', 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
        }

        $qrId = $this->validQrMediaId($this->request->getPost('qr_media_id'));
        $brochureId = $this->validBrochureMediaId($this->request->getPost('brochure_media_id'));

        if ($this->request->getPost('qr_media_id') && $qrId === null) {
            return redirect()->back()->withInput()->with('error', 'Media QR harus berupa gambar.');
        }

        if ($this->request->getPost('brochure_media_id') && $brochureId === null) {
            return redirect()->back()->withInput()->with('error', 'Brosur harus berupa dokumen PDF dari Media Library.');
        }

        $isCurrent = $this->request->getPost('is_current') ? 1 : 0;
        $db = db_connect();
        $model = new SpmbPeriodModel();

        $db->transBegin();

        try {
            if ($isCurrent === 1) {
                $unsetCurrent = $db->table('spmb_periods')->set('is_current', 0)->update();
                if ($unsetCurrent === false) {
                    throw new \RuntimeException('Gagal melepas Current SPMB sebelumnya.');
                }
            }

            $id = $model->insert([
                'academic_year' => trim((string) $this->request->getPost('academic_year')),
                'title' => trim((string) $this->request->getPost('title')),
                'summary' => trim((string) $this->request->getPost('summary')),
                'content' => trim((string) $this->request->getPost('content')),
                'start_date' => $this->nullablePost('start_date'),
                'end_date' => $this->nullablePost('end_date'),
                'registration_url' => trim((string) $this->request->getPost('registration_url')),
                'qr_media_id' => $qrId,
                'brochure_media_id' => $brochureId,
                'contact_name' => trim((string) $this->request->getPost('contact_name')),
                'contact_phone' => trim((string) $this->request->getPost('contact_phone')),
                'status' => (string) $this->request->getPost('status'),
                'is_current' => $isCurrent,
                'created_by' => (int) session()->get('auth_user_id'),
                'updated_by' => (int) session()->get('auth_user_id'),
            ]);

            if ($id === false || ! $db->transStatus()) {
                throw new \RuntimeException('Insert periode SPMB gagal.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Simpan SPMB gagal: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Periode SPMB gagal disimpan.');
        }

        $this->audit((int) $id, 'SPMB_CREATED', 'Periode SPMB dibuat.');
        if ($isCurrent === 1) {
            $this->audit((int) $id, 'SPMB_CURRENT_CHANGED', 'Periode SPMB ditetapkan sebagai current.');
        }

        return redirect()->to(site_url('manager/spmb/' . $id . '/edit'))
            ->with('success', 'Periode SPMB berhasil dibuat. Silakan lengkapi persyaratan dan FAQ.');
    }

    public function edit(int $id): string
    {
        $period = (new SpmbPeriodModel())->find($id);

        if (! $period) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Periode SPMB tidak ditemukan.');
        }

        return $this->formView($period);
    }

    public function update(int $id): RedirectResponse
    {
        $model = new SpmbPeriodModel();
        $period = $model->find($id);

        if (! $period) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB tidak ditemukan.');
        }

        if (! $this->validatePeriod()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! $this->datesAreValid()) {
            return redirect()->back()->withInput()->with('error', 'Tanggal tutup tidak boleh lebih awal dari tanggal buka.');
        }

        $qrId = $this->validQrMediaId($this->request->getPost('qr_media_id'));
        $brochureId = $this->validBrochureMediaId($this->request->getPost('brochure_media_id'));

        if ($this->request->getPost('qr_media_id') && $qrId === null) {
            return redirect()->back()->withInput()->with('error', 'Media QR harus berupa gambar.');
        }

        if ($this->request->getPost('brochure_media_id') && $brochureId === null) {
            return redirect()->back()->withInput()->with('error', 'Brosur harus berupa dokumen PDF dari Media Library.');
        }

        $isCurrent = $this->request->getPost('is_current') ? 1 : 0;
        $db = db_connect();
        $db->transBegin();

        try {
            if ($isCurrent === 1) {
                $unsetCurrent = $db->table('spmb_periods')->where('id !=', $id)->set('is_current', 0)->update();
                if ($unsetCurrent === false) {
                    throw new \RuntimeException('Gagal melepas Current SPMB lainnya.');
                }
            }

            $updated = $model->update($id, [
                'academic_year' => trim((string) $this->request->getPost('academic_year')),
                'title' => trim((string) $this->request->getPost('title')),
                'summary' => trim((string) $this->request->getPost('summary')),
                'content' => trim((string) $this->request->getPost('content')),
                'start_date' => $this->nullablePost('start_date'),
                'end_date' => $this->nullablePost('end_date'),
                'registration_url' => trim((string) $this->request->getPost('registration_url')),
                'qr_media_id' => $qrId,
                'brochure_media_id' => $brochureId,
                'contact_name' => trim((string) $this->request->getPost('contact_name')),
                'contact_phone' => trim((string) $this->request->getPost('contact_phone')),
                'status' => (string) $this->request->getPost('status'),
                'is_current' => $isCurrent,
                'updated_by' => (int) session()->get('auth_user_id'),
            ]);

            if ($updated === false || ! $db->transStatus()) {
                throw new \RuntimeException('Update periode SPMB gagal.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Update SPMB gagal: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Periode SPMB gagal diperbarui.');
        }

        $this->audit($id, 'SPMB_UPDATED', 'Periode SPMB diperbarui.');

        if ((int) $period['is_current'] !== $isCurrent) {
            $this->audit(
                $id,
                'SPMB_CURRENT_CHANGED',
                $isCurrent === 1 ? 'Periode SPMB ditetapkan sebagai current.' : 'Status current periode SPMB dilepas.'
            );
        }

        return redirect()->to(site_url('manager/spmb/' . $id . '/edit'))->with('success', 'Periode SPMB berhasil diperbarui.');
    }

    public function setCurrent(int $id): RedirectResponse
    {
        $model = new SpmbPeriodModel();
        $period = $model->find($id);

        if (! $period) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB tidak ditemukan.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            $unsetCurrent = $db->table('spmb_periods')->set('is_current', 0)->update();
            $setCurrent = $model->update($id, [
                'is_current' => 1,
                'updated_by' => (int) session()->get('auth_user_id'),
            ]);

            if ($unsetCurrent === false || $setCurrent === false || ! $db->transStatus()) {
                throw new \RuntimeException('Pergantian Current SPMB gagal.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Current SPMB gagal diubah: {message}', ['message' => $e->getMessage()]);
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Current SPMB gagal diubah.');
        }

        $this->audit($id, 'SPMB_CURRENT_CHANGED', 'Periode ' . $period['academic_year'] . ' ditetapkan sebagai current.');

        return redirect()->to(site_url('manager/spmb'))->with('success', 'Current SPMB berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new SpmbPeriodModel();
        $period = $model->find($id);

        if (! $period) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB tidak ditemukan.');
        }

        if ((int) $period['is_current'] === 1) {
            return redirect()->to(site_url('manager/spmb'))
                ->with('error', 'Periode current tidak dapat dihapus. Tetapkan periode lain sebagai current atau lepas status Current terlebih dahulu.');
        }

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB gagal dihapus.');
        }

        $this->audit($id, 'SPMB_DELETED', 'Periode SPMB dihapus: ' . $period['academic_year']);

        return redirect()->to(site_url('manager/spmb'))->with('success', 'Periode SPMB berhasil dihapus.');
    }

    public function addRequirement(int $periodId): RedirectResponse
    {
        if (! $this->periodExists($periodId)) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB tidak ditemukan.');
        }

        if (! $this->validate([
            'requirement_text' => 'required|min_length[2]|max_length[3000]',
            'display_order' => 'permit_empty|integer',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $id = (new SpmbRequirementModel())->insert([
            'spmb_period_id' => $periodId,
            'requirement_text' => trim((string) $this->request->getPost('requirement_text')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
        ]);

        if ($id === false) {
            return redirect()->back()->with('error', 'Persyaratan gagal ditambahkan.');
        }

        $this->audit($periodId, 'SPMB_REQUIREMENT_CREATED', 'Persyaratan SPMB ditambahkan.');
        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'Persyaratan berhasil ditambahkan.');
    }

    public function updateRequirement(int $periodId, int $requirementId): RedirectResponse
    {
        $model = new SpmbRequirementModel();
        $row = $model->where('id', $requirementId)->where('spmb_period_id', $periodId)->first();

        if (! $row) {
            return redirect()->back()->with('error', 'Persyaratan tidak ditemukan.');
        }

        if (! $this->validate([
            'requirement_text' => 'required|min_length[2]|max_length[3000]',
            'display_order' => 'permit_empty|integer',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $updated = $model->update($requirementId, [
            'requirement_text' => trim((string) $this->request->getPost('requirement_text')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
        ]);

        if ($updated === false) {
            return redirect()->back()->with('error', 'Persyaratan gagal diperbarui.');
        }

        $this->audit($periodId, 'SPMB_REQUIREMENT_UPDATED', 'Persyaratan SPMB diperbarui.');
        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'Persyaratan berhasil diperbarui.');
    }

    public function deleteRequirement(int $periodId, int $requirementId): RedirectResponse
    {
        $model = new SpmbRequirementModel();
        $row = $model->where('id', $requirementId)->where('spmb_period_id', $periodId)->first();

        if (! $row) {
            return redirect()->back()->with('error', 'Persyaratan tidak ditemukan.');
        }

        if ($model->delete($requirementId) === false) {
            return redirect()->back()->with('error', 'Persyaratan gagal dihapus.');
        }

        $this->audit($periodId, 'SPMB_REQUIREMENT_DELETED', 'Persyaratan SPMB dihapus.');

        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'Persyaratan berhasil dihapus.');
    }

    public function addFaq(int $periodId): RedirectResponse
    {
        if (! $this->periodExists($periodId)) {
            return redirect()->to(site_url('manager/spmb'))->with('error', 'Periode SPMB tidak ditemukan.');
        }

        if (! $this->validate([
            'question' => 'required|min_length[3]|max_length[255]',
            'answer' => 'required|min_length[2]|max_length[5000]',
            'display_order' => 'permit_empty|integer',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $id = (new SpmbFaqModel())->insert([
            'spmb_period_id' => $periodId,
            'question' => trim((string) $this->request->getPost('question')),
            'answer' => trim((string) $this->request->getPost('answer')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
        ]);

        if ($id === false) {
            return redirect()->back()->with('error', 'FAQ gagal ditambahkan.');
        }

        $this->audit($periodId, 'SPMB_FAQ_CREATED', 'FAQ SPMB ditambahkan.');
        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function updateFaq(int $periodId, int $faqId): RedirectResponse
    {
        $model = new SpmbFaqModel();
        $row = $model->where('id', $faqId)->where('spmb_period_id', $periodId)->first();

        if (! $row) {
            return redirect()->back()->with('error', 'FAQ tidak ditemukan.');
        }

        if (! $this->validate([
            'question' => 'required|min_length[3]|max_length[255]',
            'answer' => 'required|min_length[2]|max_length[5000]',
            'display_order' => 'permit_empty|integer',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $updated = $model->update($faqId, [
            'question' => trim((string) $this->request->getPost('question')),
            'answer' => trim((string) $this->request->getPost('answer')),
            'display_order' => (int) ($this->request->getPost('display_order') ?: 0),
        ]);

        if ($updated === false) {
            return redirect()->back()->with('error', 'FAQ gagal diperbarui.');
        }

        $this->audit($periodId, 'SPMB_FAQ_UPDATED', 'FAQ SPMB diperbarui.');
        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'FAQ berhasil diperbarui.');
    }

    public function deleteFaq(int $periodId, int $faqId): RedirectResponse
    {
        $model = new SpmbFaqModel();
        $row = $model->where('id', $faqId)->where('spmb_period_id', $periodId)->first();

        if (! $row) {
            return redirect()->back()->with('error', 'FAQ tidak ditemukan.');
        }

        if ($model->delete($faqId) === false) {
            return redirect()->back()->with('error', 'FAQ gagal dihapus.');
        }

        $this->audit($periodId, 'SPMB_FAQ_DELETED', 'FAQ SPMB dihapus.');

        return redirect()->to(site_url('manager/spmb/' . $periodId . '/edit'))->with('success', 'FAQ berhasil dihapus.');
    }

    private function formView(?array $period): string
    {
        $requirements = [];
        $faq = [];

        if ($period) {
            $requirements = (new SpmbRequirementModel())
                ->where('spmb_period_id', $period['id'])
                ->orderBy('display_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();

            $faq = (new SpmbFaqModel())
                ->where('spmb_period_id', $period['id'])
                ->orderBy('display_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
        }

        return view('manager/spmb/form', [
            'title' => ($period ? 'Edit' : 'Tambah') . ' SPMB | CMS MIN 6 Jember',
            'pageTitle' => $period ? 'Edit SPMB' : 'Tambah SPMB',
            'period' => $period,
            'requirements' => $requirements,
            'faq' => $faq,
            'images' => (new MediaModel())->where('media_type', 'IMAGE')->orderBy('id', 'DESC')->findAll(),
            'documents' => (new MediaModel())->where('media_type', 'DOCUMENT')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    private function validatePeriod(): bool
    {
        return $this->validate([
            'academic_year' => 'required|min_length[4]|max_length[20]',
            'title' => 'required|min_length[3]|max_length[255]',
            'summary' => 'permit_empty|max_length[5000]',
            'content' => 'permit_empty|max_length[60000]',
            'start_date' => 'permit_empty|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]',
            'registration_url' => 'permit_empty|valid_url_strict|max_length[500]',
            'qr_media_id' => 'permit_empty|is_natural_no_zero',
            'brochure_media_id' => 'permit_empty|is_natural_no_zero',
            'contact_name' => 'permit_empty|max_length[180]',
            'contact_phone' => 'permit_empty|max_length[50]',
            'status' => 'required|in_list[DRAFT,PUBLISHED,ARCHIVED]',
        ]);
    }

    private function datesAreValid(): bool
    {
        $start = $this->nullablePost('start_date');
        $end = $this->nullablePost('end_date');

        if ($start === null || $end === null) {
            return true;
        }

        return strtotime($end) >= strtotime($start);
    }

    private function nullablePost(string $key): ?string
    {
        $value = trim((string) $this->request->getPost($key));
        return $value === '' ? null : $value;
    }

    private function validQrMediaId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;
        return (new MediaModel())->where('id', $id)->where('media_type', 'IMAGE')->first() ? $id : null;
    }

    private function validBrochureMediaId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;
        $media = (new MediaModel())->where('id', $id)->where('media_type', 'DOCUMENT')->first();

        if (! $media) {
            return null;
        }

        return strtolower((string) $media['extension']) === 'pdf' ? $id : null;
    }

    private function periodExists(int $periodId): bool
    {
        return (new SpmbPeriodModel())->find($periodId) !== null;
    }

    private function audit(int $id, string $action, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'SPMB',
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
