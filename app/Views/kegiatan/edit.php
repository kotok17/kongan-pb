<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h4 class="mb-1">Edit Kegiatan</h4>
      <small class="text-muted">Perbarui informasi kegiatan berikut.</small>
    </div>
    <a href="<?= base_url('kegiatan') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
  </div>
</div>

<div class="card shadow-sm mt-4">
  <div class="card-body">
    <form action="<?= base_url('kegiatan/update/' . ($kegiatan['id_kegiatan'] ?? 0)) ?>" method="post">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
        <input type="text" name="nama_kegiatan" class="form-control"
          value="<?= old('nama_kegiatan', $kegiatan['nama_kegiatan'] ?? '') ?>" required maxlength="100"
          placeholder="Nama kegiatan">
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Penanggung Jawab <span class="text-danger">*</span></label>
        <select name="id_anggota" class="form-select" required>
          <option value="">- Pilih Anggota -</option>
          <?php foreach ($anggota as $item): ?>
          <option value="<?= $item['id_anggota'] ?>"
            <?= (int)$item['id_anggota'] === (int)($kegiatan['id_anggota'] ?? 0) ? 'selected' : '' ?>>
            <?= esc($item['nama_anggota']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Tanggal Kegiatan <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_kegiatan" class="form-control"
          value="<?= old('tanggal_kegiatan', $kegiatan['tanggal_kegiatan'] ?? date('Y-m-d')) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3" maxlength="500"
          placeholder="Deskripsi kegiatan (opsional)"><?= old('deskripsi', $kegiatan['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i>Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>