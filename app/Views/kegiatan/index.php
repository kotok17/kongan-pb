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
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (isset($kegiatan) && !empty($kegiatan) && is_array($kegiatan)): ?>
          <?php foreach ($kegiatan as $index => $item): ?>
            <?php if (is_array($item) && isset($item['id_kegiatan'])): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= esc($item['nama_kegiatan'] ?? 'Tidak ada nama') ?></td>
                <td>
                  <?php
                  $tanggal = $item['tanggal_kegiatan'] ?? date('Y-m-d');
                  echo date('d M Y', strtotime($tanggal));
                  ?>
                </td>
                <td><?= esc($item['nama_anggota'] ?? 'Tidak diketahui') ?></td>
                <td>
                  <!-- Tombol Detail - semua role bisa akses -->
                  <a href="<?= base_url('/kegiatan/detail/' . $item['id_kegiatan']) ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Detail
                  </a>

                  <?php if ($role === 'admin'): ?>
                    <!-- Tombol Hapus hanya untuk admin -->
                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $item['id_kegiatan'] ?>">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">
              <div class="text-muted py-3">
                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                <p class="mb-0">
                  <?php if ($role === 'admin'): ?>
                    Belum ada kegiatan yang dibuat
                  <?php else: ?>
                    Anda belum membuat kegiatan apapun
                  <?php endif; ?>
                </p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($role === 'admin'): ?>
  <!-- Modal Tambah Kegiatan - hanya untuk admin -->
  <div class="modal fade" id="modalKegiatan" tabindex="-1" aria-labelledby="modalKegiatanLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modalKegiatanLabel">Tambah Kegiatan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('kegiatan/simpan') ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="mb-3">
              <label for="nama_kegiatan" class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" placeholder="Nama Kegiatan"
                required maxlength="100">
            </div>
            <div class="mb-3">
              <label for="id_anggota" class="form-label">Nama Anggota <span class="text-danger">*</span></label>
              <select name="id_anggota" id="id_anggota" class="form-control select2" required>
                <option value="" disabled selected>-Pilih Anggota-</option>
                <?php foreach ($anggota as $row) : ?>
                  <option value="<?= esc($row['id_anggota']); ?>">
                    <?= esc($row['nama_anggota']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
                value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                placeholder="Deskripsi kegiatan (opsional)" maxlength="500"></textarea>
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
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function() {
    // Inisialisasi DataTables
    $('#table_kegiatan').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

    // Inisialisasi Select2 saat modal dibuka
    $('#modalKegiatan').on('shown.bs.modal', function() {
      $('#id_anggota').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modalKegiatan'),
        placeholder: '-Pilih Anggota-',
        allowClear: true,
        width: '100%'
      });
    });

    // Reset form saat modal ditutup
    $('#modalKegiatan').on('hidden.bs.modal', function() {
      if ($('#id_anggota').hasClass("select2-hidden-accessible")) {
        $('#id_anggota').select2('destroy');
      }
      $(this).find('form')[0].reset();
    });

    // SweetAlert untuk hapus kegiatan
    $(document).on('click', '.btn-delete', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const nama = $(this).data('nama_anggota');

      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: `Kegiatan "${nama}" akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`<?= base_url('/kegiatan/hapus/') ?>${id}`, {
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
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire('Error!', 'Kegiatan tidak dapat dihapus karena masih memiliki data kongan!',
                'error');
            });
        }
      });
    });

    // Tombol Import
    $('#btnImport').on('click', function() {
      $('#importFile').click();
    });

    $('#importFile').on('change', function() {
      if (this.files.length > 0) {
        this.form.submit();
      }
    });
  });
</script>

<?= $this->endSection() ?>