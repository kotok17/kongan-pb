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
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKongan">
        <i class="fas fa-plus me-2"></i>Tambah Kongan
      </button>
      <a href="<?= base_url('kegiatan/download_template_import') ?>" class="btn btn-outline-info btn-sm">
        <i class="fas fa-download me-1"></i>Template Import
      </a>
      <form action="<?= base_url('kegiatan/import_kongan') ?>" method="post" enctype="multipart/form-data"
        class="d-inline">
        <?= csrf_field() ?>
        <input type="hidden" name="id_kegiatan" value="<?= $kegiatan['id_kegiatan'] ?>">
        <input id="fileImport" type="file" name="import_file" class="d-none" accept=".xls,.xlsx,.csv">
        <button type="button" id="btnImport" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-upload me-1"></i>Import Excel
        </button>
      </form>

      <!-- ✅ TAMBAHKAN BUTTON HAPUS SEMUA -->
      <?php if (!empty($kongan)): ?>
      <button type="button" id="btnHapusSemua" class="btn btn-outline-danger btn-sm"
        data-id-kegiatan="<?= $kegiatan['id_kegiatan'] ?>" data-nama-kegiatan="<?= esc($kegiatan['nama_kegiatan']) ?>"
        data-total-kongan="<?= count($kongan) ?>">
        <i class="fas fa-trash-alt me-1"></i>Hapus Semua Data
      </button>
      <?php endif; ?>

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
      <?php if (!empty($kongan)): ?>
      <table id="table_kongan" class="table table-striped mb-0">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Anggota</th>
            <th>Tanggal</th>
            <th class="text-end">Jumlah</th>
            <?php if ($canManage): ?>
            <th class="text-center">Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; ?>
          <?php foreach ($kongan as $row): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= esc($row['nama_anggota']) ?></td>
            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
            <td class="text-end">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <?php if ($canManage): ?>
            <td class="text-center">
              <button class="btn btn-danger btn-sm btn-delete-kongan" data-id="<?= $row['id_detail_kegiatan'] ?>"
                data-nama="<?= esc($row['nama_anggota']) ?>"
                data-jumlah="<?= number_format($row['jumlah'], 0, ',', '.') ?>">
                <i class="fas fa-trash"></i>
              </button>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <div class="text-center text-muted py-5">
        <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
        <h5>Belum Ada Data Kongan</h5>
        <p class="mb-0">
          <?php if ($canManage): ?>
          Klik tombol "Tambah Kongan" untuk menambahkan data kongan
          <?php else: ?>
          Belum ada anggota yang memberikan kongan
          <?php endif; ?>
        </p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!empty($kongan)): ?>
<?php
  $totalKongan   = array_sum(array_column($kongan, 'jumlah'));
  $sepuluhPersen = $totalKongan * 0.10;
  $potTidakIkut  = (int)($kegiatan['potongan_tidak_ikut_amount'] ?? 0);
  $potUndangan   = (int)($kegiatan['potongan_undangan_amount'] ?? 0);
  
  // ✅ HITUNG KEGIATAN YANG TIDAK DIIKUTI OLEH AGUS
  $idAnggota = $kegiatan['id_anggota'];
  $db = \Config\Database::connect();
  
  // Total SEMUA kegiatan yang pernah dibuat (oleh siapa saja)
  $totalKegiatan = $db->table('kegiatan')
    ->countAllResults();
  
  // Kegiatan yang diikuti oleh anggota ini (id_anggota)
  $kegiatanDiikuti = $db->table('kegiatan_detail')
    ->where('id_anggota', $idAnggota)
    ->distinct()
    ->select('id_kegiatan')
    ->countAllResults();
  
  // Hitung berapa kegiatan yang TIDAK diikuti
  $jumlahTidakIkut = $totalKegiatan - $kegiatanDiikuti;
  
  // Total potongan tidak ikut
  $totalPotTidakIkut = $jumlahTidakIkut * $potTidakIkut;
  
  $totalBersih = $totalKongan - $sepuluhPersen - $totalPotTidakIkut - $potUndangan;
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
          <small class="text-muted d-block"><i class="fas fa-info-circle me-1"></i><?= $jumlahTidakIkut ?> kegiatan x Rp
            <?= number_format($potTidakIkut, 0, ',', '.') ?></small>
          <span class="fw-bold fs-5">Rp <?= number_format($totalPotTidakIkut, 0, ',', '.') ?></span>
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
          <label for="id_anggota" class="form-label fw-bold">
            Nama Anggota <span class="text-danger">*</span>
          </label>
          <select name="id_anggota" id="id_anggota" class="form-select form-select-lg" required>
            <option value="">-Pilih Anggota-</option>
            <?php foreach ($anggota as $item): ?>
            <option value="<?= esc($item['id_anggota']); ?>">
              <?= esc($item['nama_anggota']); ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="text" name="jumlah" id="jumlah" class="form-control" required placeholder="10.000">
          </div>
          <small class="text-muted">Minimal Rp 10.000</small>
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
$(document).ready(function() {
  // Inisialisasi DataTables hanya jika ada data
  <?php if (!empty($kongan)): ?>
  const table = $('#table_kongan').DataTable({
    paging: true,
    searching: true,
    ordering: true,
    responsive: true,
    columnDefs: [{
        targets: 0,
        orderable: false
      }, // No urut tidak bisa disort
      <?php if ($canManage): ?> {
        targets: -1,
        orderable: false
      } // Kolom aksi tidak bisa disort
      <?php endif; ?>
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
    }
  });
  <?php endif; ?>

  <?php if ($canManage): ?>
  // Import handler
  $('#btnImport').on('click', function() {
    Swal.fire({
      icon: 'info',
      title: 'Import Data Kongan',
      html: `
          <div class="text-start">
            <p class="mb-3"><strong>Sebelum import, pastikan:</strong></p>
            <ol class="text-muted small mb-3">
              <li>File dalam format Excel (.xlsx) atau CSV</li>
              <li>Kolom A: Nama Anggota (sesuai data master)</li>
              <li>Kolom B: Jumlah Kongan (angka saja, tanpa Rp)</li>
              <li>Baris pertama adalah header</li>
              <li>Data mulai dari baris kedua</li>
            </ol>
            <div class="alert alert-warning small mb-3">
              <i class="fas fa-exclamation-triangle me-1"></i>
              <strong>Penting:</strong> Download template terlebih dahulu untuk memastikan format yang benar!
            </div>
          </div>
        `,
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonColor: '#17a2b8',
      denyButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fas fa-download me-1"></i>Download Template',
      denyButtonText: '<i class="fas fa-upload me-1"></i>Pilih File Import',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then(result => {
      if (result.isConfirmed) {
        window.open('<?= base_url('kegiatan/download_template_import') ?>', '_blank');
      } else if (result.isDenied) {
        $('#fileImport').click();
      }
    });
  });

  // File change handler
  $('#fileImport').on('change', function() {
    if (!this.files || !this.files.length) return;

    const file = this.files[0];
    const size = (file.size / 1024 / 1024).toFixed(2);
    const allowedExts = ['xlsx', 'xls', 'csv'];
    const fileExt = file.name.split('.').pop().toLowerCase();

    if (!allowedExts.includes(fileExt)) {
      Swal.fire({
        icon: 'error',
        title: 'Format File Salah',
        text: 'Hanya file Excel (.xlsx, .xls) dan CSV (.csv) yang diizinkan',
        confirmButtonText: 'OK'
      });
      $(this).val('');
      return;
    }

    Swal.fire({
      icon: 'question',
      title: 'Konfirmasi Import',
      html: `
          <div class="text-start">
            <p class="mb-2"><strong>File:</strong> ${file.name}</p>
            <p class="mb-2"><strong>Ukuran:</strong> ${size} MB</p>
            <p class="mb-3"><strong>Format:</strong> ${fileExt.toUpperCase()}</p>
            <div class="alert alert-info small mb-0">
              <i class="fas fa-info-circle me-1"></i>
              Data yang sudah ada akan dilewati. Hanya data baru yang akan ditambahkan.
            </div>
          </div>
        `,
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fas fa-upload me-1"></i>Import Sekarang',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Mengimpor Data...',
          html: 'Mohon tunggu, sedang memproses file import',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => Swal.showLoading()
        });
        $(this).closest('form').submit();
      } else {
        $(this).val('');
      }
    });
  });

  // ✅ INISIALISASI SELECT2 SAAT MODAL DIBUKA
  $('#modalKongan').on('show.bs.modal', function() {
    setTimeout(function() {
      $('#id_anggota').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalKongan'),
        placeholder: '-Pilih Anggota-',
        allowClear: true,
        width: '100%',
        language: {
          noResults: function() {
            return 'Anggota tidak ditemukan';
          }
        }
      });
    }, 100);
  });

  // ✅ HAPUS SELECT2 SAAT MODAL DITUTUP
  $('#modalKongan').on('hide.bs.modal', function() {
    if ($('#id_anggota').hasClass('select2-hidden-accessible')) {
      $('#id_anggota').select2('destroy');
    }
    $(this).find('form')[0].reset();
  });

  // JavaScript untuk pencarian manual jika Select2 tidak berfungsi
  $('#searchAnggota').on('keyup', function() {
    const searchTerm = $(this).val().toLowerCase();
    const select = $('#id_anggota');

    select.find('option').each(function() {
      const optionText = $(this).text().toLowerCase();
      const optionData = $(this).data('nama') || '';

      if ($(this).val() === '') {
        // Skip option pertama (placeholder)
        return;
      }

      if (optionText.includes(searchTerm) || optionData.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Auto-select jika hanya ada satu hasil
  $('#searchAnggota').on('keyup', function() {
    const visibleOptions = $('#id_anggota option:visible').not(':first');
    if (visibleOptions.length === 1) {
      $('#id_anggota').val(visibleOptions.val());
    }
  });

  // Delete kongan handler
  $(document).on('click', '.btn-delete-kongan', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const jumlah = $(this).data('jumlah');

    Swal.fire({
      title: 'Hapus data?',
      html: `Kongan <strong>${nama}</strong><br>Rp ${jumlah}`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then(result => {
      if (!result.isConfirmed) return;

      $.ajax({
        url: `<?= base_url('kegiatan/hapus_kongan/') ?>${id}`,
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        data: `<?= csrf_token() ?>=<?= csrf_hash() ?>&_method=DELETE`,
        success: function(data) {
          if (data.success) {
            location.reload();
          } else {
            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
          }
        },
        error: function() {
          Swal.fire('Error', 'Tidak dapat menghapus data.', 'error');
        }
      });
    });
  });

  // ✅ HANDLER UNTUK HAPUS SEMUA DATA
  $(document).on('click', '#btnHapusSemua', function() {
    const idKegiatan = $(this).data('id-kegiatan');
    const namaKegiatan = $(this).data('nama-kegiatan');
    const totalKongan = $(this).data('total-kongan');

    Swal.fire({
      title: 'Hapus SEMUA Data Kongan?',
      html: `
        <div class="text-start">
          <p class="mb-3">Anda yakin ingin menghapus <strong>SEMUA DATA KONGAN</strong> dari kegiatan:</p>
          <div class="alert alert-warning">
            <strong>${namaKegiatan}</strong><br>
            <small>Total: ${totalKongan} data kongan</small>
          </div>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!<br>
            Semua data kongan akan dihapus permanen.
          </div>
        </div>
      `,
      icon: 'warning',
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonColor: '#dc3545',
      denyButtonColor: '#6c757d',
      cancelButtonColor: '#28a745',
      confirmButtonText: '<i class="fas fa-trash-alt me-1"></i>Ya, Hapus SEMUA!',
      denyButtonText: '<i class="fas fa-times me-1"></i>Batal',
      cancelButtonText: '<i class="fas fa-shield-alt me-1"></i>Lindungi Data',
      reverseButtons: true,
      customClass: {
        popup: 'swal-wide'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Konfirmasi ganda untuk keamanan
        Swal.fire({
          title: 'Konfirmasi Terakhir',
          html: `
            <div class="text-center">
              <p class="text-danger fw-bold mb-3">PERHATIAN!</p>
              <p>Ketik <strong>"HAPUS SEMUA"</strong> untuk melanjutkan:</p>
              <input type="text" id="confirmText" class="form-control text-center" placeholder="Ketik: HAPUS SEMUA" maxlength="11">
            </div>
          `,
          icon: 'error',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Hapus Semua Data',
          cancelButtonText: 'Batal',
          preConfirm: () => {
            const confirmText = document.getElementById('confirmText').value;
            if (confirmText !== 'HAPUS SEMUA') {
              Swal.showValidationMessage('Harap ketik "HAPUS SEMUA" dengan benar');
              return false;
            }
            return true;
          }
        }).then((finalResult) => {
          if (finalResult.isConfirmed) {
            // Proses hapus semua data
            Swal.fire({
              title: 'Menghapus Semua Data...',
              html: 'Mohon tunggu, sedang menghapus semua data kongan...',
              allowOutsideClick: false,
              allowEscapeKey: false,
              didOpen: () => Swal.showLoading()
            });

            $.ajax({
              url: `<?= base_url('kegiatan/hapus_semua_kongan/') ?>${idKegiatan}`,
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
              },
              data: `<?= csrf_token() ?>=<?= csrf_hash() ?>&_method=DELETE`,
              success: function(response) {
                if (response.success) {
                  Swal.fire({
                    title: 'Berhasil!',
                    html: `
                      <div class="text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Semua data kongan telah dihapus.</h5>
                        <p class="text-muted">Kembali ke halaman kegiatan dalam <strong>3 detik</strong>...</p>
                      </div>
                    `,
                    icon: 'success',
                    timer: 3000,
                    showCancelButton: false,
                    showDenyButton: false
                  }).then(() => {
                    location.reload();
                  });
                } else {
                  Swal.fire('Gagal', response.message || 'Terjadi kesalahan', 'error');
                }
              },
              error: function() {
                Swal.close();
                Swal.fire('Error', 'Tidak dapat menghapus data.', 'error');
              }
            });
          }
        });
      }
    });
  });

  // Format rupiah function
  function formatRupiah(angka) {
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
  }

  // Input formatting
  $('#jumlah').on('input', function() {
    const cleaned = $(this).val().replace(/[^0-9]/g, '');
    $(this).val(cleaned ? formatRupiah(cleaned) : '');
  });

  // Form validation
  $('#modalKongan form').on('submit', function(e) {
    e.preventDefault();

    const rawValue = $('#jumlah').val().replace(/[^0-9]/g, '');
    const nominal = parseInt(rawValue || '0', 10);

    // VALIDASI MINIMAL 10.000
    if (nominal < 10000) {
      Swal.fire({
        icon: 'warning',
        title: 'Nominal Terlalu Kecil',
        text: 'Minimal kongan adalah Rp 10.000',
        confirmButtonText: 'OK'
      });
      return false;
    }

    // KONFIRMASI JIKA DIATAS 50.000
    if (nominal > 50000) {
      const self = this;
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
          $('#jumlah').val(rawValue);
          self.submit();
        }
      });
      return false;
    }

    // Jika nominal antara 10.000 - 50.000, langsung submit
    $('#jumlah').val(rawValue);
    this.submit();
  });
  <?php endif; ?>
});
</script>

<?= $this->endSection() ?>