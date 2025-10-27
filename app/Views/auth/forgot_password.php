<?php
// filepath: app/Views/auth/forgot_password.php
// Sesuaikan pesan jika perlu

?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="login-box">
  <div class="login-logo">
    <b>KONGAN</b> App
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">
        <i class="fas fa-key text-warning"></i><br>
        Reset Password
      </p>
      <p class="text-muted small mb-4">
        Masukkan username Anda untuk reset password
      </p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle"></i>
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('forgot-password/process') ?>" method="post">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required
            value="<?= old('username') ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-warning btn-block">
              <i class="fas fa-paper-plane"></i> Reset Password
            </button>
          </div>
        </div>
      </form>

      <hr class="my-3">

      <div class="text-center">
        <a href="<?= base_url('login') ?>" class="text-primary">
          <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
      </div>

      <div class="text-center mt-3">
        <small class="text-muted">
          <i class="fas fa-info-circle"></i>
          Password baru akan ditampilkan setelah reset berhasil
        </small>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>