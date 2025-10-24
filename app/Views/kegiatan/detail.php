<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
        <i class="fas fa-plus"></i> Tambah Kongan
      </button>
      <!-- Tombol Import -->
      <form action="<?= base_url('/kegiatan/import_kongan') ?>" method="post" enctype="multipart/form-data"
        class="d-inline">
        <?= csrf_field() ?>
        <input type="hidden" name="id_kegiatan" value="<?= esc($kegiatan[0]['id_kegiatan']); ?>">
        <input type="file" name="file" accept=".csv,.xlsx" required style="display:none;" id="importFile">
        <button type="button" class="btn btn-success" id="btnImport">
          <i class="fa-solid fa-file-import"></i> Import Kongan
        </button>
      </form>
    </div>
    <div>
      <a href="<?= base_url('/kegiatan') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger mx-3 mb-3">
      <ul class="mb-0">
        <?php foreach (session('errors') as $error) : ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show mx-3 mb-3" role="alert">
      <strong>Berhasil!</strong> <?= esc(session('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show mx-3 mb-3" role="alert">
      <strong>Error!</strong> <?= esc(session('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <?php if (session()->getFlashdata('import_errors')) : ?>
    <div class="alert alert-warning alert-dismissible fade show mx-3 mb-3" role="alert">
      <strong>Detail Error Import:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach (session('import_errors') as $error) : ?>
          <li><?= esc($error) ?></li>
        <?php endforeach ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif ?>

  <div class="card-body">
    <div class="mb-3">
      <h5>Nama Anggota: <?= esc($kegiatan[0]['nama_anggota']); ?></h5>
      <h6>Kegiatan: <?= esc($kegiatan[0]['nama_kegiatan']); ?></h6>
      <p class="text-muted">Tanggal: <?= date('d-m-Y', strtotime($kegiatan[0]['tanggal_kegiatan'])); ?></p>
    </div>

    <table id="table_kongan" class="table table-bordered table-hover">
      <thead class="bg-light">
        <tr>
          <th>No</th>
          <th>Nama Anggota</th>
          <th>Jumlah</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($kongan)): ?>
          <?php $no = 1; ?>
          <?php foreach ($kongan as $row) : ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= esc($row['nama_anggota']); ?></td>
              <td class="text-end">Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
              <td>
                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id_detail_kegiatan'] ?>">
                  <i class="fas fa-trash"></i> Hapus
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted">Belum ada data kongan</td>
          </tr>
        <?php endif; ?>
      </tbody>
      <?php if (!empty($kongan)): ?>
        <?php
        $total_kongan = array_sum(array_column($kongan, 'jumlah'));
        $sepuluh_persen = $total_kongan * 0.1;
        $potongan_undangan = 280000;

        // Cek apakah anggota yang mengadakan kegiatan ini pernah nulis di kegiatan lain
        $id_anggota_kegiatan = $kegiatan[0]['id_anggota'];
        $anggota_aktif_di_kegiatan_lain = false;
        $jumlah_kegiatan_ikut = 0;

        // Cek apakah anggota ini pernah nulis di kegiatan lain (selain kegiatannya sendiri)
        if (!empty($aktivitas_anggota)) {
          $anggota_aktif_di_kegiatan_lain = true;
          $jumlah_kegiatan_ikut = count($aktivitas_anggota);
        }

        // Jika anggota yang mengadakan kegiatan tidak pernah nulis di kegiatan lain, kena potongan
        $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : 20000;

        $total_bersih = $total_kongan - $sepuluh_persen - $potongan_undangan - $potongan_tidak_nulis;
        ?>
        <tfoot class="bg-light">
          <tr>
            <th colspan="2" class="text-end">Total Kongan:</th>
            <th class="text-end">
              Rp <?= number_format($total_kongan, 0, ',', '.'); ?>
            </th>
            <th></th>
          </tr>
          <tr>
            <th colspan="2" class="text-end">10% Total Kongan:</th>
            <th class="text-end text-danger">
              - Rp <?= number_format($sepuluh_persen, 0, ',', '.'); ?>
            </th>
            <th></th>
          </tr>
          <?php if (!$anggota_aktif_di_kegiatan_lain): ?>
            <tr>
              <th colspan="2" class="text-end">
                Pot. Tidak Nulis Kegiatan Lain (0x ikut):
              </th>
              <th class="text-end text-danger">
                - Rp <?= number_format($potongan_tidak_nulis, 0, ',', '.'); ?>
              </th>
              <th></th>
            </tr>
          <?php else: ?>
            <tr>
              <th colspan="2" class="text-end text-success">
                Bonus Aktif Kegiatan Lain (<?= $jumlah_kegiatan_ikut ?>x ikut):
              </th>
              <th class="text-end text-success">
                + Rp 0
              </th>
              <th></th>
            </tr>
          <?php endif; ?>
          <tr>
            <th colspan="2" class="text-end">Potongan Undangan:</th>
            <th class="text-end text-danger">
              - Rp <?= number_format($potongan_undangan, 0, ',', '.'); ?>
            </th>
            <th></th>
          </tr>
          <tr class="table-primary">
            <th colspan="2" class="text-end"><strong>Total Bersih:</strong></th>
            <th class="text-end">
              <strong>Rp <?= number_format($total_bersih, 0, ',', '.'); ?></strong>
            </th>
            <th></th>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<!-- Modal Tambah Kongan -->
<div class="modal fade" id="modalKegiatan" tabindex="-1" aria-labelledby="modalKegiatanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalKegiatanLabel">Tambah Kongan</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('/kegiatan/tambah_kongan') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id_kegiatan" value="<?= esc($kegiatan[0]['id_kegiatan']); ?>">
        <div class="modal-body">
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
            <label for="jumlah" class="form-label">Jumlah Kongan <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" required
                autocomplete="off">
            </div>
            <small class="text-muted">Masukkan angka tanpa titik atau koma</small>
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
    // Inisialisasi DataTables hanya jika ada data
    if ($('#table_kongan tbody tr').length > 1 || $('#table_kongan tbody tr:first td').length > 1) {
      $('#table_kongan').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    }

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

    // Reset form dan destroy Select2 saat modal ditutup
    $('#modalKegiatan').on('hidden.bs.modal', function() {
      if ($('#id_anggota').hasClass("select2-hidden-accessible")) {
        $('#id_anggota').select2('destroy');
      }
      $(this).find('form')[0].reset();
    });

    // Format input jumlah menjadi rupiah
    $('#jumlah').on('input', function() {
      let value = this.value.replace(/[^0-9]/g, '');
      this.value = formatRupiah(value);
    });

    // Validasi jumlah kongan sebelum submit
    $('form').on('submit', function(e) {
      // Skip validasi untuk form import
      if ($(this).find('input[name="file"]').length > 0) {
        return true;
      }

      e.preventDefault(); // Hentikan submit dulu

      let jumlahInput = $('#jumlah').val().replace(/[^0-9]/g, '');
      let jumlahAngka = parseInt(jumlahInput);

      // Validasi jika kosong
      if (!jumlahInput || jumlahAngka <= 0) {
        Swal.fire({
          title: 'Peringatan!',
          text: 'Jumlah kongan harus diisi dengan benar!',
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        return false;
      }

      // Validasi jika lebih dari 50.000
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
            // Jika yakin, lanjutkan submit
            $('#jumlah').val(jumlahInput); // Set nilai tanpa format
            $(this)[0].submit(); // Submit form asli
          }
          // Jika batal, tidak ada aksi (form tidak disubmit)
        });
      } else {
        // Jika kurang dari atau sama dengan 50.000, langsung submit
        $('#jumlah').val(jumlahInput); // Set nilai tanpa format
        this.submit(); // Submit form
      }
    });

    // SweetAlert untuk delete - gunakan event delegation
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
  });

  // Event handler untuk tombol import - gunakan event delegation
  $(document).on('click', '#btnImport', function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('importFile');
    if (fileInput) {
      fileInput.click();
    }
  });

  // Event handler untuk file input - gunakan event delegation
  $(document).on('change', '#importFile', function(e) {
    if (this.files && this.files.length > 0) {
      // Tampilkan loading atau konfirmasi
      Swal.fire({
        title: 'Mengupload file...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Submit form
      this.form.submit();
    }
  });

  // Format angka ke rupiah
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