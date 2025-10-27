<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<!-- Header Section dengan Role-based Actions -->
<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-1 fw-bold text-primary">
          <i class="fas fa-calendar-check me-2"></i>Detail Kegiatan
        </h4>
        <p class="text-muted mb-0">
          <?php if (session()->get('role') === 'admin'): ?>
            <span class="badge bg-danger me-2">ADMIN</span>Akses penuh ke semua kegiatan
          <?php else: ?>
            <span class="badge bg-success me-2">ANGGOTA</span>
            <?php if (($kegiatan['id_anggota'] ?? null) == session()->get('id_anggota')): ?>
              Kegiatan Anda - dapat mengelola
            <?php else: ?>
              Kegiatan <?= esc($kegiatan['nama_anggota'] ?? ''); ?> - hanya lihat
            <?php endif; ?>
          <?php endif; ?>
        </p>
      </div>
      <div>
        <a href="<?= base_url('/kegiatan') ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
      </div>
    </div>

    <!-- Action Buttons - Role-based -->
    <?php
    $canManage = false;
    if (session()->get('role') === 'admin') {
      $canManage = true; // Admin bisa manage semua kegiatan
    } elseif (session()->get('role') === 'anggota' && $kegiatan[0]['id_anggota'] == session()->get('id_anggota')) {
      $canManage = true; // Anggota hanya bisa manage kegiatan sendiri
    }
    ?>

    <?php if ($canManage): ?>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
            <i class="fas fa-plus me-2"></i>Tambah Kongan
            <small class="d-block opacity-75">Tambah data kongan baru</small>
          </button>
        </div>
        <div class="col-md-4">
          <form action="<?= base_url('/kegiatan/import_kongan') ?>" method="post" enctype="multipart/form-data"
            class="h-100">
            <?= csrf_field() ?>
            <input type="hidden" name="id_kegiatan" value="<?= esc($kegiatan['id_kegiatan'] ?? 0); ?>">
            <input type="file" name="file" accept=".csv,.xlsx" required style="display:none;" id="importFile">
            <button type="button" class="btn btn-success w-100 h-100" id="btnImport">
              <i class="fas fa-file-import me-2"></i>Import Kongan
              <small class="d-block opacity-75">Upload file Excel/CSV</small>
            </button>
          </form>
        </div>
        <div class="col-md-4">
          <div class="btn-group w-100 h-100" role="group">
            <a href="<?= base_url('/undangan/preview/' . ($kegiatan['id_kegiatan'] ?? 0)) ?>" class="btn btn-info"
              target="_blank" style="flex: 1;">
              <i class="fas fa-eye me-1"></i>Preview
            </a>
            <a href="<?= base_url('/undangan/generate/' . ($kegiatan['id_kegiatan'] ?? 0)) ?>" class="btn btn-warning"
              target="_blank" style="flex: 1;">
              <i class="fas fa-file-pdf me-1"></i>PDF
            </a>
          </div>
          <small class="d-block opacity-75 text-center mt-1">Generate surat undangan</small>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info d-flex align-items-center" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <div>
          <strong>Info:</strong> Anda hanya dapat melihat detail kegiatan ini.
          <?php if (session()->get('role') === 'anggota'): ?>
            Hanya pemilik kegiatan yang dapat mengelola data kongan.
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Flash Messages -->
<div class="container-fluid px-0">
  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Terjadi kesalahan:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach (session('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>
      <strong>Berhasil!</strong> <?= esc(session('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-times-circle me-2"></i>
      <strong>Error!</strong> <?= esc(session('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('import_errors')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Detail Error Import:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach (session('import_errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif ?>
</div>

<!-- Kegiatan Info Card -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="row g-4">
      <div class="col-md-8">
        <div class="kegiatan-info">
          <h5 class="mb-3 fw-bold text-dark">
            <i class="fas fa-calendar-alt text-primary me-2"></i>
            <?= esc($kegiatan['nama_kegiatan'] ?? '') ?>
          </h5>
          <div class="info-grid">
            <?php if (isset($kegiatan) && !empty($kegiatan)): ?>
              <div class="info-item">
                <strong>Nama Kegiatan:</strong>
                <?= esc($kegiatan['nama_kegiatan'] ?? 'Tidak diketahui') ?>
              </div>
              <div class="info-item">
                <strong>Penyelenggara:</strong>
                <?= esc($kegiatan['nama_anggota'] ?? 'Tidak diketahui') ?>
              </div>
              <div class="info-item">
                <strong>Tanggal:</strong>
                <?= isset($kegiatan['tanggal_kegiatan']) ? date('d/m/Y', strtotime($kegiatan['tanggal_kegiatan'])) : 'Tidak diketahui' ?>
              </div>
              <div class="info-item">
                <strong>Deskripsi:</strong>
                <?= esc($kegiatan['deskripsi'] ?? 'Tidak ada deskripsi') ?>
              </div>
            <?php else: ?>
              <div class="alert alert-warning">Data kegiatan tidak tersedia</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-summary">
          <div class="stat-item">
            <div class="stat-number text-primary"><?= count($kongan ?? []) ?></div>
            <div class="stat-label">Total Peserta</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Data Kongan Table -->
<div class="card shadow-sm">
  <div class="card-header bg-light">
    <h6 class="mb-0 fw-bold">
      <i class="fas fa-coins text-warning me-2"></i>
      Data Kongan
      <span class="badge bg-primary ms-2"><?= count($kongan ?? []) ?> peserta</span>
    </h6>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table id="table_kongan" class="table table-hover mb-0">
        <thead class="bg-light">
          <tr>
            <th class="text-center" width="60">No</th>
            <th>Nama Anggota</th>
            <th class="text-end" width="150">Jumlah</th>
            <th class="text-center" width="100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($kongan)): ?>
            <?php $no = 1; ?>
            <?php foreach ($kongan as $row): ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="user-avatar me-2">
                      <i class="fas fa-user"></i>
                    </div>
                    <span class="fw-medium"><?= esc($row['nama_anggota']) ?></span>
                  </div>
                </td>
                <td class="text-end">
                  <span class="fw-bold text-success">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></span>
                </td>
                <td class="text-center">
                  <?php if ($canManage): ?>
                    <form action="<?= base_url('kegiatan/hapus_kongan/' . $row['id_detail_kegiatan']) ?>" method="post"
                      style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kongan ini?')">
                      <?= csrf_field() ?>
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted">
                      <i class="fas fa-eye" title="Hanya lihat"></i>
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="no-data-row">
              <td colspan="4" class="text-center text-muted py-5">
                <div class="empty-state">
                  <i class="fas fa-coins fa-3x mb-3 opacity-30"></i>
                  <p class="mb-0 fw-medium">Belum ada data kongan</p>
                  <small class="text-muted">
                    <?php if ($canManage): ?>
                      Klik tombol "Tambah Kongan" untuk menambah data
                    <?php else: ?>
                      Data kongan akan muncul di sini
                    <?php endif; ?>
                  </small>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>

        <!-- Summary Footer -->
        <?php if (!empty($kongan)): ?>
          <?php
          $total_kongan = array_sum(array_column($kongan, 'jumlah'));
          $sepuluh_persen = $total_kongan * 0.1;
          $potongan_undangan = 280000;

          // Cek aktivitas anggota
          $id_anggota_kegiatan = $kegiatan['id_anggota'] ?? null;
          $anggota_aktif_di_kegiatan_lain = false;
          $jumlah_kegiatan_ikut = 0;

          if (!empty($aktivitas_anggota)) {
            $anggota_aktif_di_kegiatan_lain = true;
            $jumlah_kegiatan_ikut = count($aktivitas_anggota);
          }

          $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : 20000;
          $total_bersih = $total_kongan - $sepuluh_persen - $potongan_undangan - $potongan_tidak_nulis;
          ?>
          <tfoot class="bg-light">
            <tr>
              <th colspan="3" class="text-end">Total Kongan:</th>
              <th class="text-end">
                <span class="fs-6 fw-bold text-primary">Rp <?= number_format($total_kongan, 0, ',', '.') ?></span>
              </th>
            </tr>
            <tr>
              <th colspan="3" class="text-end">10% Total Kongan:</th>
              <th class="text-end">
                <span class="text-danger">- Rp <?= number_format($sepuluh_persen, 0, ',', '.') ?></span>
              </th>
            </tr>
            <?php if (!$anggota_aktif_di_kegiatan_lain): ?>
              <tr>
                <th colspan="3" class="text-end">
                  <small>Pot. Tidak Nulis Kegiatan Lain (0x ikut):</small>
                </th>
                <th class="text-end">
                  <span class="text-danger">- Rp <?= number_format($potongan_tidak_nulis, 0, ',', '.') ?></span>
                </th>
              </tr>
            <?php else: ?>
              <tr>
                <th colspan="3" class="text-end text-success">
                  <small>Bonus Aktif Kegiatan Lain (<?= $jumlah_kegiatan_ikut ?>x ikut):</small>
                </th>
                <th class="text-end">
                  <span class="text-success">+ Rp 0</span>
                </th>
              </tr>
            <?php endif; ?>
            <tr>
              <th colspan="3" class="text-end">Potongan Undangan:</th>
              <th class="text-end">
                <span class="text-danger">- Rp <?= number_format($potongan_undangan, 0, ',', '.') ?></span>
              </th>
            </tr>
            <tr class="table-primary">
              <th colspan="3" class="text-end">
                <strong class="fs-6">Total Bersih:</strong>
              </th>
              <th class="text-end">
                <strong class="fs-5 text-success">Rp <?= number_format($total_bersih, 0, ',', '.') ?></strong>
              </th>
            </tr>
          </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Kongan - Hanya untuk yang bisa manage -->
<?php if ($canManage): ?>
  <div class="modal fade" id="modalKegiatan" tabindex="-1" aria-labelledby="modalKegiatanLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h1 class="modal-title fs-5" id="modalKegiatanLabel">
            <i class="fas fa-plus me-2"></i>Tambah Kongan Baru
          </h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('/kegiatan/tambah_kongan') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="id_kegiatan" value="<?= esc($kegiatan['id_kegiatan'] ?? 0) ?>">
          <div class="modal-body">
            <div class="mb-3">
              <label for="id_anggota" class="form-label">
                <i class="fas fa-user me-1"></i>Nama Anggota <span class="text-danger">*</span>
              </label>
              <select name="id_anggota" id="id_anggota" class="form-control select2" required>
                <option value="" disabled selected>-Pilih Anggota-</option>
                <?php foreach ($anggota as $row): ?>
                  <option value="<?= esc($row['id_anggota']) ?>">
                    <?= esc($row['nama_anggota']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="text-muted">Pilih anggota yang memberikan kongan</small>
            </div>
            <div class="mb-3">
              <label for="jumlah" class="form-label">
                <i class="fas fa-coins me-1"></i>Jumlah Kongan <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light">Rp</span>
                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" required
                  autocomplete="off">
              </div>
              <small class="text-muted">Masukkan angka tanpa titik atau koma. Contoh: 50000</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Batal
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i>Simpan Kongan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Modal Tambah Kongan (Revised) -->
<div class="modal fade" id="modalTambahKongan" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Kongan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('kegiatan/tambah_kongan') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" name="id_kegiatan" value="<?= $kegiatan['id_kegiatan'] ?? 0 ?>">
          <div class="mb-3">
            <label class="form-label">Anggota <span class="text-danger">*</span></label>
            <select name="id_anggota" class="form-control" required>
              <option value="">-Pilih Anggota-</option>
              <?php if (isset($anggota) && !empty($anggota)): ?>
                <?php foreach ($anggota as $row): ?>
                  <option value="<?= $row['id_anggota'] ?>">
                    <?= esc($row['nama_anggota']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" required min="1000" step="1000"
              placeholder="Contoh: 50000">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  /* Custom Styling */
  .kegiatan-info .info-grid {
    display: grid;
    gap: 12px;
  }

  .info-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
  }

  .stats-summary {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
  }

  .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
  }

  .stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #007bff, #6610f2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
  }

  .empty-state {
    padding: 3rem 1rem;
  }

  .table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa !important;
  }

  .btn-delete {
    transition: all 0.3s ease;
  }

  .btn-delete:hover {
    transform: scale(1.1);
  }

  @media (max-width: 768px) {
    .stat-number {
      font-size: 2rem;
    }

    .info-grid {
      gap: 8px;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    // Inisialisasi DataTables hanya jika ada data (bukan empty state)
    if (!$('#no-data-row').length) {
      $('#table_kongan').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "columnDefs": [{
            "targets": [0, 3], // No dan Aksi
            "orderable": false
          },
          {
            "targets": 2, // Kolom Jumlah
            "type": "num-fmt"
          }
        ],
        "language": {
          "search": "Cari:",
          "lengthMenu": "Tampilkan _MENU_ data per halaman",
          "zeroRecords": "Tidak ada data yang ditemukan",
          "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
          "infoEmpty": "Tidak ada data tersedia",
          "infoFiltered": "(difilter dari _MAX_ total data)",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          }
        }
      });
    }

    <?php if ($canManage): ?>
      // Modal handling
      $('#modalKegiatan').on('shown.bs.modal', function() {
        $('#id_anggota').select2({
          theme: 'bootstrap4',
          dropdownParent: $('#modalKegiatan'),
          placeholder: '-Pilih Anggota-',
          allowClear: true,
          width: '100%'
        });
      });

      $('#modalKegiatan').on('hidden.bs.modal', function() {
        if ($('#id_anggota').hasClass("select2-hidden-accessible")) {
          $('#id_anggota').select2('destroy');
        }
        $(this).find('form')[0].reset();
      });

      // Format input jumlah
      $('#jumlah').on('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        this.value = formatRupiah(value);
      });

      // Form validation
      $('form').on('submit', function(e) {
        if ($(this).find('input[name="file"]').length > 0) {
          return true;
        }

        e.preventDefault();
        let jumlahInput = $('#jumlah').val().replace(/[^0-9]/g, '');
        let jumlahAngka = parseInt(jumlahInput);

        if (!jumlahInput || jumlahAngka <= 0) {
          Swal.fire({
            title: 'Peringatan!',
            text: 'Jumlah kongan harus diisi dengan benar!',
            icon: 'warning',
            confirmButtonText: 'OK'
          });
          return false;
        }

        if (jumlahAngka > 50000) {
          Swal.fire({
            title: 'Jumlah Kongan Besar!',
            text: `Anda yakin ingin memasukkan kongan sebesar Rp ${formatRupiah(jumlahInput)}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, yakin!',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              $('#jumlah').val(jumlahInput);
              $(this)[0].submit();
            }
          });
        } else {
          $('#jumlah').val(jumlahInput);
          this.submit();
        }
      });

      // Delete confirmation
      $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data kongan akan dihapus permanen!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`<?= base_url('/kegiatan/hapus_kongan/') ?>${id}`, {
                method: 'DELETE',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire('Terhapus!', data.message, 'success')
                    .then(() => location.reload());
                } else {
                  Swal.fire('Error!', data.message, 'error');
                }
              });
          }
        });
      });

      // Import handling
      $(document).on('click', '#btnImport', function(e) {
        e.preventDefault();
        document.getElementById('importFile').click();
      });

      $(document).on('change', '#importFile', function(e) {
        if (this.files && this.files.length > 0) {
          Swal.fire({
            title: 'Mengupload file...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          this.form.submit();
        }
      });
    <?php endif; ?>
  });

  function formatRupiah(angka) {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
      split = number_string.split(','),
      sisa = split[0].length % 3,
      rupiah = split[0].substr(0, sisa),
      ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
      separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
  }
</script>

<?= $this->endSection() ?>