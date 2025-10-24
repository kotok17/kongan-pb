<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h1>Dashboard Anggota</h1>
          <p>Selamat datang, <?= esc($username) ?>!</p>

          <div class="alert alert-info">
            <h5>Informasi</h5>
            <p>Anda dapat melihat kegiatan yang tersedia melalui menu di sidebar.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>