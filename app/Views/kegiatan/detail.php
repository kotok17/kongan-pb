<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h4 class="mb-1"><?= esc($kegiatan['nama_kegiatan'] ?? 'Detail Kegiatan') ?></h4>
      <div class="text-muted small">
        <i class="fas fa-user me-1"></i><?= esc($kegiatan['nama_anggota'] ?? '-') ?> ·
        <i
          class="fas fa-calendar-alt ms-2 me-1"></i><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'] ?? date('Y-m-d'))) ?>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="<?= base_url('kegiatan') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
      </a>

      <?php if ($canManage): ?>
      <a href="<?= base_url('kegiatan/export_pdf/' . $kegiatan['id_kegiatan']) ?>"
        class="btn btn-outline-danger btn-sm">
        <i class="fas fa-file-pdf me-1"></i>Export PDF
      </a>
      <a href="<?= base_url('kegiatan/export_excel/' . $kegiatan['id_kegiatan']) ?>"
        class="btn btn-outline-success btn-sm">
        <i class="fas fa-file-excel me-1"></i>Export Excel
      </a>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKongan">
        <i class="fas fa-plus me-1"></i>Tambah Kongan
      </button>
      <form action="<?= base_url('kegiatan/import_kongan/' . $kegiatan['id_kegiatan']) ?>" method="post"
        enctype="multipart/form-data" class="d-inline">
        <?= csrf_field() ?>
        <input id="fileImport" type="file" name="import_file" class="d-none" accept=".xls,.xlsx">
        <button type="button" id="btnImport" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-upload me-1"></i>Import Excel
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!$canManage && $isOwner): ?>
<div class="alert alert-info mt-3">
  <i class="fas fa-info-circle me-2"></i>
  Anda hanya dapat melihat detail kegiatan ini. Pengelolaan data kongan dilakukan oleh admin.
</div>
<?php endif; ?>

<div class="card shadow-sm mt-4">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-bold"><i class="fas fa-hand-holding-heart me-2"></i>Data Kongan</h6>
    <span class="badge bg-primary">Total: <?= count($kongan) ?> anggota</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table id="table_kongan" class="table table-striped mb-0">
        <thead class="table-dark">
          <tr>
            <th>Nama Anggota</th>
            <th>Tanggal</th>
            <th class="text-end">Jumlah</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($kongan)): ?>
          <?php foreach ($kongan as $row): ?>
          <tr>
            <td><?= esc($row['nama_anggota']) ?></td>
            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
            <td class="text-end">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td class="text-center">
              <?php if ($canManage): ?>
              <button class="btn btn-danger btn-sm btn-delete-kongan" data-id="<?= $row['id_detail_kegiatan'] ?>"
                data-nama="<?= esc($row['nama_anggota']) ?>"
                data-jumlah="<?= number_format($row['jumlah'], 0, ',', '.') ?>">
                <i class="fas fa-trash"></i>
              </button>
              <?php else: ?>
              <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted py-4">Belum ada data kongan.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (!empty($kongan)): ?>
<?php
  $totalKongan   = array_sum(array_column($kongan, 'jumlah'));
  $sepuluhPersen = $totalKongan * 0.10;
  $potTidakIkut  = (int)($kegiatan['potongan_tidak_ikut_amount'] ?? 0);
  $potUndangan   = (int)($kegiatan['potongan_undangan_amount'] ?? 0);
  $totalBersih   = $totalKongan - $sepuluhPersen - $potTidakIkut - $potUndangan;
  ?>
<div class="card shadow-sm mt-4">
  <div class="card-header bg-dark text-white fw-bold">
    <i class="fas fa-calculator me-2"></i>Ringkasan Perhitungan
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block">Total Kongan</small>
          <span class="fw-bold fs-5">Rp <?= number_format($totalKongan, 0, ',', '.') ?></span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block">10% Operasional</small>
          <span class="fw-bold fs-5">Rp <?= number_format($sepuluhPersen, 0, ',', '.') ?></span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block">Potongan Tidak Ikut</small>
          <span class="fw-bold fs-5">Rp <?= number_format($potTidakIkut, 0, ',', '.') ?></span>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block">Potongan Undangan</small>
          <span class="fw-bold fs-5">Rp <?= number_format($potUndangan, 0, ',', '.') ?></span>
        </div>
      </div>
      <div class="col-12">
        <div class="p-3 bg-success text-white rounded border">
          <small class="text-white-50 d-block">Total Bersih</small>
          <span class="fw-bold fs-4">Rp <?= number_format($totalBersih, 0, ',', '.') ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($canManage): ?>
<div class="modal fade" id="modalKongan" tabindex="-1" aria-labelledby="modalKonganLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?= base_url('kegiatan/tambah_kongan') ?>" method="post" class="modal-content">
      <?= csrf_field() ?>
      <input type="hidden" name="id_kegiatan" value="<?= $kegiatan['id_kegiatan'] ?>">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalKonganLabel"><i class="fas fa-plus-circle me-1"></i>Tambah Kongan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Anggota <span class="text-danger">*</span></label>
          <select name="id_anggota" class="form-select select2" required>
            <option value="">- Pilih Anggota -</option>
            <?php foreach ($anggota as $item): ?>
            <option value="<?= $item['id_anggota'] ?>"><?= esc($item['nama_anggota']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" name="jumlah" id="jumlah" class="form-control" required placeholder="0">
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const table = new DataTable('#table_kongan', {
    paging: true,
    searching: true,
    ordering: true,
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
    }
  });

  <?php if ($canManage): ?>
  document.getElementById('btnImport')?.addEventListener('click', () => document.getElementById('fileImport')
    ?.click());

  const importInput = document.getElementById('fileImport');
  const importForm = importInput?.closest('form');

  importInput?.addEventListener('change', () => {
    if (!importInput.files?.length) return;

    const file = importInput.files[0];
    const size = (file.size / 1024 / 1024).toFixed(2); // MB

    Swal.fire({
      icon: 'question',
      title: 'Import data kongan?',
      html: `
        <div class="text-start">
          <p class="mb-2"><strong>File:</strong> ${file.name}</p>
          <p class="mb-2"><strong>Ukuran:</strong> ${size} MB</p>
          <small class="text-muted">Pastikan format sesuai template.</small>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fas fa-upload me-1"></i>Import',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Mengunggah...',
          text: 'Mohon tunggu sebentar',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => Swal.showLoading()
        });
        importForm?.submit();
      } else {
        importInput.value = '';
      }
    });
  });

  document.querySelectorAll('.btn-delete-kongan').forEach(btn => {
    btn.addEventListener('click', () => {
      Swal.fire({
        title: 'Hapus data?',
        html: `Kongan <strong>${btn.dataset.nama}</strong><br>Rp ${btn.dataset.jumlah}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`<?= base_url('kegiatan/hapus_kongan/') ?>${btn.dataset.id}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: `<?= csrf_token() ?>=<?= csrf_hash() ?>&amp;_method=DELETE`
        }).then(r => r.json()).then(data => {
          if (data.success) location.reload();
          else Swal.fire('Gagal', data.message ?? 'Terjadi kesalahan', 'error');
        }).catch(() => Swal.fire('Error', 'Tidak dapat menghapus data.', 'error'));
      });
    });
  });

  const modalForm = document.querySelector('#modalKongan form');
  const jumlahInput = document.querySelector('#jumlah');

  const formatRupiah = (angka) => {
    const numberString = angka.replace(/[^,\d]/g, '');
    const split = numberString.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi) || [];
    if (ribuan.length) {
      const separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }
    return split[1] !== undefined ? `${rupiah},${split[1]}` : rupiah;
  };

  jumlahInput?.addEventListener('input', () => {
    const cleaned = jumlahInput.value.replace(/[^0-9]/g, '');
    jumlahInput.value = cleaned ? formatRupiah(cleaned) : '';
  });

  modalForm?.addEventListener('submit', (e) => {
    const rawValue = (jumlahInput.value || '').replace(/[^0-9]/g, '');
    const nominal = parseInt(rawValue || '0', 10);

    if (nominal < 10000) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Nominal Terlalu Kecil',
        text: 'Minimal kongan adalah Rp 10.000',
        confirmButtonText: 'OK'
      });
      return;
    }

    if (nominal > 50000) {
      e.preventDefault();
      Swal.fire({
        icon: 'question',
        title: 'Nominal Besar',
        html: `Anda yakin ingin memasukkan kongan sebesar <strong>Rp ${formatRupiah(rawValue)}</strong>?`,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          jumlahInput.value = rawValue;
          modalForm.submit();
        }
      });
      return;
    }

    jumlahInput.value = rawValue;
  });
  <?php endif; ?>
});
</script>

<?= $this->endSection() ?>