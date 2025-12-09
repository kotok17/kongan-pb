<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<!-- Header Card berdasarkan role -->
<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mb-1">
        <?php if ($role === 'admin'): ?>
        <i class="fas fa-calendar-alt text-primary me-2"></i>Data Kegiatan
        <?php else: ?>
        <i class="fas fa-calendar-user text-success me-2"></i>Kegiatan Saya
        <?php endif; ?>
      </h4>
      <p class="text-muted mb-0">
        <?php if ($role === 'admin'): ?>
        Kelola semua kegiatan kongan
        <?php else: ?>
        Kegiatan yang Anda buat
        <?php endif; ?>
      </p>
    </div>

    <?php if ($role === 'admin'): ?>
    <!-- Tombol hanya untuk admin -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
      <i class="fas fa-plus me-2"></i>Tambah Kegiatan
    </button>
    <?php endif; ?>
  </div>
</div>
<br>

<!-- Tabel dengan aksi berdasarkan role -->
<div class="card-body">
  <div class="table-responsive">
    <table id="table_kegiatan" class="table table-striped table-hover">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Kegiatan</th>
          <th>Tanggal</th>
          <th>Nama Anggota</th>
          <th>Total Peserta</th>
          <th>Total Uang</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (isset($kegiatan) && !empty($kegiatan) && is_array($kegiatan)): ?>
        <?php foreach ($kegiatan as $index => $item): ?>
        <?php if (is_array($item) && isset($item['id_kegiatan'])): ?>
        <tr>
          <td></td> <!-- Kosongkan, akan diisi otomatis oleh DataTables -->
          <td>
            <strong><?= esc($item['nama_kegiatan'] ?? 'Tidak ada nama') ?></strong>
            <?php if (!empty($item['deskripsi'])): ?>
            <br><small
              class="text-muted"><?= esc(substr($item['deskripsi'], 0, 50)) ?><?= strlen($item['deskripsi']) > 50 ? '...' : '' ?></small>
            <?php endif; ?>
          </td>
          <td data-order="<?= $item['tanggal_kegiatan'] ?? date('Y-m-d') ?>">
            <?php
                  $tanggal = $item['tanggal_kegiatan'] ?? date('Y-m-d');
                  $tanggalObj = new DateTime($tanggal);
                  $now = new DateTime();
                  $diff = $now->diff($tanggalObj);

                  echo '<span class="fw-bold">' . $tanggalObj->format('d M Y') . '</span>';

                  // Badge status kegiatan
                  if ($tanggalObj->format('Y-m-d') == $now->format('Y-m-d')) {
                    echo '<br><span class="badge bg-success">Hari Ini</span>';
                  } elseif ($tanggalObj > $now) {
                    echo '<br><span class="badge bg-info">' . $diff->days . ' hari lagi</span>';
                  } else {
                    echo '<br><span class="badge bg-secondary">' . $diff->days . ' hari lalu</span>';
                  }
                  ?>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div
                class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                style="width: 32px; height: 32px; font-size: 14px;">
                <?= strtoupper(substr($item['nama_anggota'] ?? 'N', 0, 1)) ?>
              </div>
              <div>
                <?= esc($item['nama_anggota'] ?? 'Tidak diketahui') ?>
              </div>
            </div>
          </td>
          <td class="text-center">
            <span class="badge bg-info fs-6">
              <i class="fas fa-users me-1"></i>
              <?= isset($item['total_peserta']) ? number_format($item['total_peserta'], 0, ',', '.') : '0' ?>
            </span>
          </td>
          <td class="text-end">
            <strong class="text-success">
              Rp <?= isset($item['total_uang']) ? number_format($item['total_uang'], 0, ',', '.') : '0' ?>
            </strong>
          </td>
          <td>
            <!-- Tombol Detail - semua role bisa akses -->
            <a href="<?= base_url('/kegiatan/detail/' . $item['id_kegiatan']) ?>" class="btn btn-info btn-sm"
              data-bs-toggle="tooltip" title="Lihat Detail">
              <i class="fas fa-eye"></i> Detail
            </a>

            <?php if ($role === 'admin'): ?>
            <!-- Tombol Edit untuk admin -->
            <a href="<?= base_url('/kegiatan/edit/' . $item['id_kegiatan']) ?>" class="btn btn-warning btn-sm"
              data-bs-toggle="tooltip" title="Edit Kegiatan">
              <i class="fas fa-edit"></i>
            </a>

            <!-- Tombol Hapus hanya untuk admin -->
            <?php
                    $hasPeserta = isset($item['total_peserta']) && (int)$item['total_peserta'] > 0;
                    ?>
            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $item['id_kegiatan'] ?>"
              data-nama="<?= esc($item['nama_kegiatan']) ?>" data-peserta="<?= $item['total_peserta'] ?? 0 ?>"
              data-bs-toggle="tooltip" title="<?= $hasPeserta ? 'Kegiatan ada data kongan' : 'Hapus Kegiatan' ?>">
              <i class="fas fa-trash"></i>
              <?php if ($hasPeserta): ?>
              <span class="badge bg-white text-danger ms-1"><?= $item['total_peserta'] ?></span>
              <?php endif; ?>
            </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php else: ?>
        <tr class="no-data-row">
          <td colspan="7" class="text-center">
            <div class="text-muted py-5">
              <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
              <h5 class="mb-2">Belum Ada Kegiatan</h5>
              <p class="mb-0">
                <?php if ($role === 'admin'): ?>
                Klik tombol "Tambah Kegiatan" untuk membuat kegiatan baru
                <?php else: ?>
                Anda belum membuat kegiatan apapun
                <?php endif; ?>
              </p>
            </div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
      <?php if (isset($kegiatan) && !empty($kegiatan) && is_array($kegiatan)): ?>
      <tfoot class="table-light">
        <tr>
          <th colspan="4" class="text-end">TOTAL:</th>
          <th class="text-center">
            <span class="badge bg-info fs-6">
              <i class="fas fa-users me-1"></i>
              <?php
                $totalPeserta = 0;
                foreach ($kegiatan as $item) {
                  $totalPeserta += isset($item['total_peserta']) ? (int)$item['total_peserta'] : 0;
                }
                echo number_format($totalPeserta, 0, ',', '.');
                ?>
            </span>
          </th>
          <th class="text-end">
            <strong class="text-success fs-5">
              Rp <?php
                    $totalUang = 0;
                    foreach ($kegiatan as $item) {
                      $totalUang += isset($item['total_uang']) ? (int)$item['total_uang'] : 0;
                    }
                    echo number_format($totalUang, 0, ',', '.');
                    ?>
            </strong>
          </th>
          <th></th>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php if ($role === 'admin'): ?>
<!-- Modal Tambah Kegiatan - hanya untuk admin -->
<div class="modal fade" id="modalKegiatan" tabindex="-1" aria-labelledby="modalKegiatanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h1 class="modal-title fs-5" id="modalKegiatanLabel">
          <i class="fas fa-plus-circle me-2"></i>Tambah Kegiatan Baru
        </h1>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('kegiatan/simpan') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama_kegiatan" class="form-label fw-bold">
              Nama Kegiatan <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
              placeholder="Contoh: Pernikahan Ahmad & Siti" required maxlength="100">
          </div>
          <div class="mb-3">
            <label for="id_anggota" class="form-label fw-bold">
              Nama Anggota <span class="text-danger">*</span>
            </label>
            <select name="id_anggota" id="id_anggota" class="form-control" required>
              <option value="" disabled selected>-Pilih Anggota-</option>
              <?php foreach ($anggota as $row) : ?>
              <option value="<?= esc($row['id_anggota']); ?>">
                <?= esc($row['nama_anggota']); ?>
              </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Pilih anggota yang membuat kegiatan</small>
          </div>
          <div class="mb-3">
            <label for="tanggal_kegiatan" class="form-label fw-bold">
              Tanggal Kegiatan <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
              value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="mb-3">
            <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
              placeholder="Deskripsi kegiatan (opsional)" maxlength="500"></textarea>
            <small class="text-muted">Maksimal 500 karakter</small>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Simpan Kegiatan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
/* Styling minimal - tanpa animasi berlebihan */
.user-avatar {
  font-weight: bold;
}

.table th {
  vertical-align: middle;
  white-space: nowrap;
}

.table td {
  vertical-align: middle;
}

.badge {
  font-weight: 500;
}

/* Simple hover - hanya background */
.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

/* Button spacing */
.btn-sm {
  margin: 2px;
}

/* Style untuk badge peserta di tombol hapus */
.btn-delete .badge {
  font-size: 0.65rem;
  padding: 0.15em 0.4em;
}

/* SweetAlert custom width */
.swal-wide {
  width: 600px !important;
}

/* Alert inside SweetAlert */
.swal2-html-container .alert {
  text-align: left;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .swal-wide {
    width: 95% !important;
  }
}
</style>

<!-- ✅ 1. Tambahkan CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Jika kamu ingin tema Bootstrap 4 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2-bootstrap4.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
  // Inisialisasi DataTables
  var table = $('#table_kegiatan').DataTable({
    "paging": true,
    "lengthChange": true,
    "lengthMenu": [
      [10, 25, 50, -1],
      [10, 25, 50, "Semua"]
    ],
    "searching": true,
    "ordering": true,
    "order": [
      [2, 'desc']
    ], // Sort by tanggal kegiatan descending (kolom index 2)
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "columnDefs": [{
      // Kolom nomor urut otomatis
      "targets": 0,
      "orderable": false,
      "searchable": false,

    }]
  });

  // Redraw nomor urut setiap kali tabel di-sort atau di-page
  table.on('order.dt search.dt', function() {
    table.column(0, {
        search: 'applied',
        order: 'applied'
      })
      .nodes().each(function(cell, i) {
        if ($(cell).closest('tr').hasClass('no-data-row')) return;
        cell.innerHTML = i + 1;
      });
  }).draw();

  // Inisialisasi Tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });



  // ✅ 2. Inisialisasi Select2 saat modal dibuka
  $('#modalKegiatan').on('shown.bs.modal', function() {
    if ($('#id_anggota').hasClass("select2-hidden-accessible")) {
      $('#id_anggota').select2('destroy');
    }
    $('#id_anggota').select2({
      theme: 'bootstrap4',
      dropdownParent: $('#modalKegiatan'),
      placeholder: '-Pilih Anggota-',
      minimumInputLength: 1, // Aktifkan pencarian minimal 1 karakter
      allowClear: true,
      width: '100%'
    });
  });

  // ✅ 3. Hapus Select2 saat modal ditutup
  $('#modalKegiatan').on('hidden.bs.modal', function() {
    if ($('#id_anggota').hasClass("select2-hidden-accessible")) {
      $('#id_anggota').select2('destroy');
    }
    $(this).find('form')[0].reset();
  });

  // SweetAlert untuk hapus kegiatan - DENGAN VALIDASI
  $(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const peserta = parseInt($(this).data('peserta')) || 0;

    // Cek apakah ada data kongan
    if (peserta > 0) {
      Swal.fire({
        title: 'Tidak Dapat Menghapus!',
        html: `
            <div class="text-start">
              <p class="mb-3">Kegiatan <strong>"${nama}"</strong> tidak dapat dihapus karena:</p>
              <div class="alert alert-warning mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terdapat <strong>${peserta} data kongan</strong> yang terkait dengan kegiatan ini.
              </div>
              <p class="text-muted small mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Hapus semua data kongan terlebih dahulu jika ingin menghapus kegiatan ini.
              </p>
            </div>
          `,
        icon: 'error',
        confirmButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-arrow-left me-1"></i> OK, Mengerti',
        footer: `<a href="<?= base_url('/kegiatan/detail/') ?>${id}" class="btn btn-sm btn-outline-info">
                     <i class="fas fa-eye me-1"></i>Lihat Detail Kegiatan
                   </a>`,
        customClass: {
          popup: 'swal-wide'
        }
      });
      return;
    }

    // Jika tidak ada data kongan, lanjutkan konfirmasi hapus
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      html: `
          <div class="text-start">
            <p>Kegiatan <strong>"${nama}"</strong> akan dihapus permanen!</p>
            <div class="alert alert-info small mb-0">
              <i class="fas fa-info-circle me-1"></i>
              Kegiatan ini tidak memiliki data kongan yang terkait.
            </div>
          </div>
        `,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, hapus!',
      cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: 'Menghapus...',
          text: 'Mohon tunggu sebentar',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // PERBAIKAN: Gunakan FormData untuk CSRF token
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch(`<?= base_url('/kegiatan/hapus/') ?>${id}`, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
              }).then(() => location.reload());
            } else {
              // Tampilkan error dengan detail
              Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus!',
                html: data.message,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                footer: data.has_data ?
                  `<small class="text-muted">
                       <i class="fas fa-lightbulb me-1"></i>
                       Tip: Buka detail kegiatan untuk menghapus data kongan
                     </small>` : null
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus kegiatan!', 'error');
          });
      }
    });
  });
});
</script>

<!-- ✅ 4. Tambahkan JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?= $this->endSection() ?>