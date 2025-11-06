<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<style>
.swal-wide {
  width: 600px !important;
}

@media (max-width: 768px) {
  .swal-wide {
    width: 90% !important;
  }
}

/* Avatar placeholder styling */
.avatar-placeholder {
  width: 100px;
  height: 100px;
}

/* Card hover effect untuk statistik */
.modal-body .card {
  transition: transform 0.2s ease-in-out;
}

.modal-body .card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table styling untuk history */
.table-hover tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>

<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota"> <i
          class="fas fa-plus"></i>Tambah
        Anggota
      </button>
    </div>
    <div>
      <!-- Tombol Import -->
      <form action="<?= base_url('/anggota/import') ?>" method="post" enctype="multipart/form-data" class="d-inline">
        <?= csrf_field() ?>
        <input type="file" name="file" accept=".csv,.xlsx" required style="display:none;" id="importFile">
        <button type="button" class="btn btn-success" id="btnImport">
          <i class="fa-solid fa-file-import"></i> Import Anggota
        </button>
      </form>
    </div>
  </div>
  <?php if (session()->getFlashdata('success')) : ?>
  <div>
    <div class="alert alert-success alert-dismissible fade show col-md-7" role="alert">
      <strong>Berhasil!</strong> <?= esc(session('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php endif ?>
  <div class="card-body">
    <table id="table_anggota" class="table table-bordered table-hover">
      <thead class="bg-light">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>No. Telepon</th>
          <th>Alamat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        <?php foreach ($anggota as $row) : ?>
        <?php
          // Cek apakah anggota memiliki kegiatan atau kongan
          $db = \Config\Database::connect();
          $hasKegiatan = $db->table('kegiatan')->where('id_anggota', $row['id_anggota'])->countAllResults();
          $hasKongan = $db->table('kegiatan_detail')->where('id_anggota', $row['id_anggota'])->countAllResults();
          $hasUser = $db->table('users')->where('id_anggota', $row['id_anggota'])->countAllResults();
          $canDelete = ($hasKegiatan == 0 && $hasKongan == 0 && $hasUser == 0);
          ?>
        <tr<?= !$canDelete ? ' class="table-warning"' : '' ?>>
          <td><?= $no++; ?></td>
          <td>
            <?= esc($row['nama_anggota']); ?>
            <?php if (!$canDelete): ?>
            <span class="badge bg-warning text-dark ms-2" title="Tidak dapat dihapus">
              <i class="fas fa-lock"></i>
            </span>
            <?php endif; ?>
          </td>
          <td><?= esc($row['no_hp']); ?></td>
          <td><?= esc($row['alamat']); ?></td>
          <td>
            <a href="#" class="btn btn-info btn-sm btn-detail" data-id="<?= $row['id_anggota'] ?>"
              data-bs-toggle="modal" data-bs-target="#modalDetailAnggota">
              <i class="fa-solid fa-eye"></i> Detail
            </a>
            <a href="#" class="btn btn-warning btn-sm">
              <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <button class="btn btn-danger btn-sm btn-hapus" data-id="<?= $row['id_anggota'] ?>"
              data-nama="<?= esc($row['nama_anggota']) ?>"
              title="<?= $canDelete ? 'Hapus' : 'Tidak dapat dihapus - memiliki data terkait' ?>">
              <i class="fas fa-trash"></i>
            </button>
          </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
    </table>
    <!-- Modal -->
    <div class="modal fade" id="modalTambahAnggota" tabindex="-1" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Anggota</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="<?= site_url('anggota/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
              <div class="mb-3">
                <label for="" class="control-label">Nama Anggota :</label>
                <input type="text" class="form-control" name="nama_anggota" placeholder="John Doe"
                  value="<?= old('nama_anggota') ?>" required>
              </div>
              <div class="mb-3">
                <label for="" class="control-label">No. Telepon :</label>
                <input type="number" class="form-control" name="no_hp" placeholder="08131xxxxx" pattern="[0-9]+"
                  value="<?= old('no_hp') ?>" required>
              </div>
              <div class="mb-3">
                <label for="" class="control-label">Alamat :</label>
                <input type="text" class="form-control" name="alamat" placeholder="Kp Bitung Rt 02/05"
                  value="<?= old('alamat') ?>" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Detail Anggota -->
    <div class="modal fade" id="modalDetailAnggota" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="modalDetailLabel">
              <i class="fas fa-user-circle me-2"></i>Detail Anggota
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="modalDetailContent">
            <!-- Loading spinner -->
            <div class="text-center py-4" id="loadingDetail">
              <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2 text-muted">Memuat data...</p>
            </div>

            <!-- Content akan diisi via AJAX -->
            <div id="detailContent" style="display: none;">
              <!-- Data anggota -->
              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="text-center">
                    <div
                      class="avatar-placeholder bg-info text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                      style="width: 100px; height: 100px; font-size: 2rem;">
                      <i class="fas fa-user"></i>
                    </div>
                    <h5 class="fw-bold" id="detailNama">-</h5>
                  </div>
                </div>
                <div class="col-md-8">
                  <table class="table table-borderless">
                    <tr>
                      <td width="30%" class="fw-semibold"><i class="fas fa-phone me-2 text-success"></i>No. Telepon</td>
                      <td>: <span id="detailNoHp">-</span></td>
                    </tr>
                    <tr>
                      <td class="fw-semibold"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Alamat</td>
                      <td>: <span id="detailAlamat">-</span></td>
                    </tr>
                  </table>
                </div>
              </div>

              <!-- Statistik Kongan -->
              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="card border-info">
                    <div class="card-body text-center p-3">
                      <i class="fas fa-hand-holding-heart fa-2x text-info mb-2"></i>
                      <h6 class="card-title mb-1">Total Kongan</h6>
                      <h5 class="text-info fw-bold" id="statTotalKongan">Rp 0</h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card border-success">
                    <div class="card-body text-center p-3">
                      <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                      <h6 class="card-title mb-1">Jumlah Kegiatan</h6>
                      <h5 class="text-success fw-bold" id="statJumlahKegiatan">0</h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card border-warning">
                    <div class="card-body text-center p-3">
                      <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                      <h6 class="card-title mb-1">Rata-rata</h6>
                      <h5 class="text-warning fw-bold" id="statRataRata">Rp 0</h5>
                    </div>
                  </div>
                </div>
              </div>

              <!-- History Kongan -->
              <div class="mb-3">
                <h6 class="fw-bold border-bottom pb-2">
                  <i class="fas fa-history me-2"></i>History Kongan
                </h6>
                <div id="historyKonganContent">
                  <!-- History akan diisi via JavaScript -->
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Tombol Import membuka dialog file
document.getElementById('btnImport').addEventListener('click', function() {
  document.getElementById('importFile').click();
});

// Kirim form otomatis setelah pilih file
document.getElementById('importFile').addEventListener('change', function() {
  if (this.files.length > 0) {
    this.form.submit();
  }
});

// DataTables
$(function() {
  $('#table_anggota').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
});

// SweetAlert delete
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = this.dataset.id; // ✅ ambil id dari atribut data-id
      const nama = this.dataset.nama; // ambil nama dari atribut data-nama

      Swal.fire({
        title: 'Hapus Anggota?',
        html: `Apakah Anda yakin ingin menghapus anggota <strong>${nama}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading
          Swal.fire({
            title: 'Menghapus...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
          });

          // AJAX request dengan CSRF token
          $.ajax({
            url: `<?= base_url('anggota/hapus/') ?>${id}`,
            type: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: {
              '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
              '_method': 'DELETE'
            },
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  title: 'Berhasil!',
                  text: response.message,
                  icon: 'success',
                  confirmButtonText: 'OK'
                }).then(() => {
                  location.reload();
                });
              } else {
                // Handle error dari response sukses tapi success: false
                Swal.fire({
                  title: 'Tidak Dapat Dihapus!',
                  html: `
                        <p class="mb-2">${response.message}</p>
                        ${response.detail ? `<small class="text-muted">${response.detail}</small>` : ''}
                    `,
                  icon: 'warning',
                  confirmButtonText: 'OK',
                  customClass: {
                    popup: 'swal-wide'
                  }
                });
              }
            },
            error: function(xhr, status, error) {
              console.error('AJAX Error:', xhr.responseText);
              let errorMessage = 'Terjadi kesalahan saat menghapus data';
              let errorDetail = '';

              try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
                errorDetail = response.detail || '';
              } catch (e) {
                if (xhr.status === 403) {
                  errorMessage =
                    'Akses ditolak. Anda tidak memiliki izin untuk menghapus data ini.';
                } else if (xhr.status === 404) {
                  errorMessage = 'Data tidak ditemukan.';
                } else if (xhr.status === 500) {
                  errorMessage = 'Terjadi kesalahan server.';
                }
              }

              // Tampilkan error dengan detail jika ada
              Swal.fire({
                title: 'Tidak Dapat Dihapus!',
                html: `
                        <p class="mb-2">${errorMessage}</p>
                        ${errorDetail ? `<small class="text-muted">${errorDetail}</small>` : ''}
                    `,
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                  popup: 'swal-wide'
                }
              });
            }
          });
        }
      });
    });
  });
});

// Handle modal detail anggota
document.addEventListener('DOMContentLoaded', function() {
  // Event listener untuk button detail
  document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      loadDetailAnggota(id);
    });
  });

  // Function untuk load detail anggota
  function loadDetailAnggota(id) {
    // Show loading
    document.getElementById('loadingDetail').style.display = 'block';
    document.getElementById('detailContent').style.display = 'none';

    // AJAX request
    fetch(`<?= base_url('anggota/detail/') ?>${id}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Populate data anggota
          document.getElementById('detailNama').textContent = data.data.anggota.nama_anggota;
          document.getElementById('detailNoHp').textContent = data.data.anggota.no_hp;
          document.getElementById('detailAlamat').textContent = data.data.anggota.alamat;

          // Populate statistik
          document.getElementById('statTotalKongan').textContent =
            'Rp ' + parseInt(data.data.statistik.total_kongan).toLocaleString('id-ID');
          document.getElementById('statJumlahKegiatan').textContent = data.data.statistik.jumlah_kegiatan;
          document.getElementById('statRataRata').textContent =
            'Rp ' + parseInt(data.data.statistik.rata_rata).toLocaleString('id-ID');

          // Populate history kongan
          const historyContainer = document.getElementById('historyKonganContent');
          if (data.data.history_kongan.length > 0) {
            let historyHtml = '<div class="table-responsive"><table class="table table-sm table-hover">';
            historyHtml += '<thead class="table-light">';
            historyHtml +=
              '<tr><th>Tanggal</th><th>Kegiatan</th><th>Pemilik</th><th class="text-end">Jumlah</th></tr>';
            historyHtml += '</thead><tbody>';

            data.data.history_kongan.forEach(item => {
              const tanggal = new Date(item.tanggal_kegiatan).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
              });
              const jumlah = parseInt(item.jumlah).toLocaleString('id-ID');

              historyHtml += `
              <tr>
                <td><small class="text-muted">${tanggal}</small></td>
                <td>
                  <strong>${item.nama_kegiatan}</strong>
                  <br><small class="text-muted">oleh ${item.pemilik_kegiatan}</small>
                </td>
                <td><span class="badge bg-info">${item.pemilik_kegiatan}</span></td>
                <td class="text-end">
                  <span class="fw-bold text-success">Rp ${jumlah}</span>
                </td>
              </tr>
            `;
            });

            historyHtml += '</tbody></table></div>';
            historyContainer.innerHTML = historyHtml;
          } else {
            historyContainer.innerHTML = `
            <div class="text-center py-4 text-muted">
              <i class="fas fa-inbox fa-3x mb-3"></i>
              <p>Belum pernah memberikan kongan di kegiatan apapun</p>
            </div>
          `;
          }

          // Hide loading, show content
          document.getElementById('loadingDetail').style.display = 'none';
          document.getElementById('detailContent').style.display = 'block';

        } else {
          // Error
          Swal.fire({
            title: 'Error!',
            text: data.message || 'Gagal memuat data anggota',
            icon: 'error',
            confirmButtonText: 'OK'
          });

          // Hide modal
          bootstrap.Modal.getInstance(document.getElementById('modalDetailAnggota')).hide();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          title: 'Error!',
          text: 'Terjadi kesalahan saat memuat data',
          icon: 'error',
          confirmButtonText: 'OK'
        });

        // Hide modal
        bootstrap.Modal.getInstance(document.getElementById('modalDetailAnggota')).hide();
      });
  }

  // Reset modal saat ditutup
  document.getElementById('modalDetailAnggota').addEventListener('hidden.bs.modal', function() {
    document.getElementById('loadingDetail').style.display = 'block';
    document.getElementById('detailContent').style.display = 'none';
  });
});
</script>


<?= $this->endSection() ?>