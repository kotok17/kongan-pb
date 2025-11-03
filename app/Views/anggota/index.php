<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

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
  <?php if (session()->getFlashdata('errors')) : ?>
    <ul style="color:red;">
      <?php foreach (session('errors') as $error) : ?>
        <li><?= esc($error) ?></li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>

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
          <tr>
            <td><?= $no++; ?></td>
            <td><?= esc($row['nama_anggota']); ?></td>
            <td><?= esc($row['no_hp']); ?></td>
            <td><?= esc($row['alamat']); ?></td>
            <td>
              <a href="#" class="btn btn-info btn-sm">
                <i class="fa-solid fa-eye"></i> Detail
              </a>
              <a href="#" class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square"></i> Edit
              </a>
              <a href="#" data-id="<?= $row['id_anggota'] ?>" class="btn btn-danger btn-sm btn-delete">
                <i class="fa-solid fa-trash"></i> Hapus
              </a>
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
          <form action="<?= site_url('anggota/tambah') ?>" method="post">
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
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.dataset.id; // ✅ ambil id dari atribut data-id

        Swal.fire({
          title: 'Yakin ingin menghapus data ini?',
          text: "Data tidak bisa dikembalikan setelah dihapus.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`<?= base_url('anggota/hapus') ?>/${id}`, { // ✅ perbaikan disini
                method: 'DELETE',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest'
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire('Terhapus!', data.message, 'success').then(() => {
                    location.reload();
                  });
                } else {
                  Swal.fire('Gagal!', data.message, 'error');
                }
              })
              .catch(() => {
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
              });
          }
        });
      });
    });
  });
</script>


<?= $this->endSection() ?>