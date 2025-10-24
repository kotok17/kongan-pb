<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
      Tambah Kegiatan
    </button>
    <div>
      <!-- Tombol Import -->
      <form action="<?= base_url('/kegiatan/import') ?>" method="post" enctype="multipart/form-data" class="d-inline">
        <?= csrf_field() ?>
        <input type="file" name="file" accept=".csv,.xlsx" required style="display:none;" id="importFile">
        <button type="button" class="btn btn-success" id="btnImport">
          <i class="fa-solid fa-file-import"></i> Import Kegiatan
        </button>
      </form>
    </div>
  </div>

  <!-- Notifikasi - Pindah ke sini -->
  <?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show mx-3 mb-3" role="alert">
      <strong>Error!</strong> <?= esc(session('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show mx-3 mb-3" role="alert">
      <strong>Berhasil!</strong> <?= esc(session('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <div class="card-body">
    <table id="table_kegiatan" class="table table-bordered table-hover">
      <thead class="bg-light">
        <tr>
          <th>No</th>
          <th>Nama Kegiatan</th>
          <th>Tanggal Kegiatan</th>
          <th>Nama Anggota</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($kegiatan)): ?>
          <?php $no = 1; ?>
          <?php foreach ($kegiatan as $row) : ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= esc($row['nama_kegiatan']); ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_kegiatan'])); ?></td>
              <td><?= esc($row['nama_anggota']); ?></td>
              <td>
                <a href="<?= base_url('/kegiatan/detail/' . $row['id_kegiatan']) ?>" class="btn btn-info btn-sm">
                  <i class="fa-solid fa-eye"></i> Detail
                </a>
                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id_kegiatan'] ?>"
                  data-nama="<?= esc($row['nama_kegiatan']) ?>">
                  <i class="fa-solid fa-trash"></i> Hapus
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted">Belum ada data kegiatan</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal - Pindah keluar dari tabel -->
<div class="modal fade" id="modalKegiatan" tabindex="-1" aria-labelledby="modalKegiatanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalKegiatanLabel">Tambah Kegiatan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/kegiatan/tambah_kegiatan') ?>" method="post">
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
            <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan" required>
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
      const nama = $(this).data('nama');

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