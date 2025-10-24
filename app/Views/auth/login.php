<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="login-box">
  <div class="login-logo">
    <b>KONGAN</b> App
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Silakan login untuk melanjutkan</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form action="<?= base_url('/login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>