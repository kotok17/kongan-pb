<?php
// filepath: app/Views/auth/login.php

?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="login-container">
  <div class="login-wrapper">
    <!-- Left Panel - Branding & Info -->
    <div class="login-brand-panel">
      <div class="brand-content">
        <!-- Logo Container -->
        <div class="logo-container">
          <div class="logo-wrapper">
            <!-- Tempat untuk logo PB -->
            <img src="<?= base_url('assets/logo-pb.png') ?>" alt="Logo PB" class="pb-logo" id="pb-logo">
            <!-- Fallback jika logo belum ada -->
            <div class="logo-fallback" id="logo-fallback">
              <i class="fas fa-trophy"></i>
            </div>
          </div>
          <div class="brand-text">
            <h2 class="brand-title">KONGAN <span class="text-warning">PB</span></h2>
            <p class="brand-subtitle">Sistem Manajemen Kongan Digital</p>
          </div>
        </div>

        <!-- Features List -->
        <div class="features-list">
          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="feature-text">
              <h6>Manajemen Anggota</h6>
              <small>Kelola data anggota dengan mudah</small>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="feature-text">
              <h6>Kegiatan Digital</h6>
              <small>Atur jadwal dan kongan kegiatan</small>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="feature-text">
              <h6>Tracking Keuangan</h6>
              <small>Monitor alur dana kongan real-time</small>
            </div>
          </div>
        </div>

        <!-- Footer Info -->
        <div class="brand-footer">
          <p class="mb-0">
            <i class="fas fa-shield-alt text-success me-2"></i>
            <small>Sistem keamanan terjamin & data terenkripsi</small>
          </p>
        </div>
      </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="login-form-panel">
      <div class="form-content">
        <!-- Header -->
        <div class="form-header">
          <div class="welcome-icon">
            <img src="<?= base_url('assets/logo-pb.png') ?>" alt="Logo PB" class="pb-logo" id="pb-logo">
          </div>
          <h4 class="form-title">Selamat Datang!</h4>
          <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-modern">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success alert-modern">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
          </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= base_url('/login/process') ?>" method="post" class="login-form">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="username" class="form-label">
              <i class="fas fa-user me-2"></i>Username
            </label>
            <div class="input-wrapper">
              <input type="text" name="username" id="username" class="form-control form-control-modern"
                placeholder="Masukkan username Anda" required autocomplete="username">
              <div class="input-icon">
                <i class="fas fa-user"></i>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <i class="fas fa-lock me-2"></i>Password
            </label>
            <div class="input-wrapper">
              <input type="password" name="password" id="password" class="form-control form-control-modern"
                placeholder="Masukkan password Anda" required autocomplete="current-password">
              <div class="input-icon">
                <i class="fas fa-lock"></i>
              </div>
              <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="fas fa-eye" id="password-icon"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="remember" name="remember">
              <label class="form-check-label" for="remember">
                Ingat saya
              </label>
            </div>
          </div>

          <button type="submit" class="btn btn-login">
            <span class="btn-text">
              <i class="fas fa-sign-in-alt me-2"></i>
              Masuk Sekarang
            </span>
            <div class="btn-loader">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
          </button>
        </form>

        <!-- Additional Links -->
        <div class="form-footer">
          <div class="forgot-password">
            <a href="<?= base_url('forgot-password') ?>" class="forgot-link">
              <i class="fas fa-key me-2"></i>
              Lupa Password?
            </a>
          </div>

          <div class="help-info">
            <div class="help-item">
              <i class="fas fa-question-circle text-info me-2"></i>
              <small>Butuh bantuan? Hubungi admin</small>
            </div>
            <div class="help-item">
              <i class="fas fa-clock text-muted me-2"></i>
              <small>Sistem aktif 24/7</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Background Elements -->
  <div class="background-elements">
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
  </div>
</div>

<style>
  /* Modern Login Styles */
  .login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
  }

  .login-wrapper {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    max-width: 1000px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 600px;
    animation: slideUp 0.8s ease-out;
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Left Panel - Branding */
  .login-brand-panel {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }

  .login-brand-panel::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    opacity: 0.1;
  }

  .brand-content {
    position: relative;
    z-index: 2;
  }

  .logo-container {
    text-align: center;
    margin-bottom: 40px;
  }

  .logo-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
  }

  .pb-logo {
    width: 80px;
    height: 80px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    background: white;
    padding: 10px;
    object-fit: contain;
    transition: all 0.3s ease;
  }

  .pb-logo:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
  }

  .logo-fallback {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .brand-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .brand-subtitle {
    opacity: 0.9;
    font-size: 1rem;
    margin-bottom: 0;
  }

  .features-list {
    margin-bottom: 30px;
  }

  .feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
  }

  .feature-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(5px);
  }

  .feature-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 1.1rem;
  }

  .feature-text h6 {
    margin-bottom: 5px;
    font-weight: 600;
  }

  .feature-text small {
    opacity: 0.8;
  }

  .brand-footer {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
  }

  /* Right Panel - Form */
  .login-form-panel {
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .form-header {
    text-align: center;
    margin-bottom: 30px;
  }

  .welcome-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #4a90e2, #357abd);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 10px 30px rgba(74, 144, 226, 0.3);
  }

  .form-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .form-subtitle {
    color: #7f8c8d;
    margin-bottom: 0;
  }

  .alert-modern {
    border: none;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 20px;
    font-weight: 500;
  }

  .login-form {
    margin-bottom: 30px;
  }

  .form-group {
    margin-bottom: 25px;
  }

  .form-label {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
  }

  .input-wrapper {
    position: relative;
  }

  .form-control-modern {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px 50px 15px 20px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
  }

  .form-control-modern:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    background: white;
  }

  .input-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    pointer-events: none;
    transition: all 0.3s ease;
  }

  .form-control-modern:focus+.input-icon {
    color: #4a90e2;
  }

  .password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #adb5bd;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    transition: all 0.3s ease;
  }

  .password-toggle:hover {
    color: #4a90e2;
    background: rgba(74, 144, 226, 0.1);
  }

  .form-check {
    display: flex;
    align-items: center;
  }

  .form-check-input {
    margin-right: 10px;
    transform: scale(1.1);
  }

  .form-check-label {
    color: #6c757d;
    font-weight: 500;
  }

  .btn-login {
    width: 100%;
    background: linear-gradient(135deg, #4a90e2, #357abd);
    border: none;
    border-radius: 10px;
    padding: 15px;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
  }

  .btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
  }

  .btn-login:active {
    transform: translateY(0);
  }

  .btn-loader {
    display: none;
  }

  .btn-login.loading .btn-text {
    display: none;
  }

  .btn-login.loading .btn-loader {
    display: block;
  }

  .form-footer {
    text-align: center;
  }

  .forgot-link {
    color: #4a90e2;
    text-decoration: none;
    font-weight: 500;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
    margin-bottom: 20px;
  }

  .forgot-link:hover {
    background: rgba(74, 144, 226, 0.1);
    color: #357abd;
  }

  .help-info {
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
  }

  .help-item {
    margin-bottom: 10px;
  }

  .help-item small {
    color: #6c757d;
  }

  /* Background Elements */
  .background-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
  }

  .bg-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    animation: float 6s ease-in-out infinite;
  }

  .circle-1 {
    width: 200px;
    height: 200px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }

  .circle-2 {
    width: 150px;
    height: 150px;
    bottom: 20%;
    right: 15%;
    animation-delay: 2s;
  }

  .circle-3 {
    width: 100px;
    height: 100px;
    top: 50%;
    right: 5%;
    animation-delay: 4s;
  }

  @keyframes float {

    0%,
    100% {
      transform: translateY(0px) scale(1);
    }

    50% {
      transform: translateY(-20px) scale(1.05);
    }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .login-wrapper {
      grid-template-columns: 1fr;
      max-width: 450px;
    }

    .login-brand-panel {
      padding: 30px 20px;
      order: 2;
    }

    .login-form-panel {
      padding: 30px 20px;
      order: 1;
    }

    .brand-title {
      font-size: 1.8rem;
    }

    .feature-item {
      padding: 12px;
    }

    .feature-icon {
      width: 35px;
      height: 35px;
      margin-right: 12px;
    }
  }

  @media (max-width: 480px) {
    .login-container {
      padding: 10px;
    }

    .login-form-panel {
      padding: 20px 15px;
    }

    .pb-logo,
    .logo-fallback {
      width: 60px;
      height: 60px;
    }

    .brand-title {
      font-size: 1.5rem;
    }
  }

  /* Logo detection and fallback */
  .pb-logo {
    display: block;
  }

  .logo-fallback {
    display: none;
  }

  /* Jika logo tidak bisa load, show fallback */
  .pb-logo[src*="logo-pb.png"]:not([src])~.logo-fallback,
  .pb-logo[src=""]:not([src])~.logo-fallback {
    display: flex;
  }
</style>

<script>
  // Toggle Password Visibility
  function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.classList.remove('fa-eye');
      passwordIcon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      passwordIcon.classList.remove('fa-eye-slash');
      passwordIcon.classList.add('fa-eye');
    }
  }

  // Handle Form Submission with Loading State
  document.querySelector('.login-form').addEventListener('submit', function(e) {
    const submitBtn = document.querySelector('.btn-login');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // Reset after 5 seconds if something goes wrong
    setTimeout(() => {
      submitBtn.classList.remove('loading');
      submitBtn.disabled = false;
    }, 5000);
  });

  // Logo Error Handling
  document.addEventListener('DOMContentLoaded', function() {
    const logoImg = document.getElementById('pb-logo');
    const logoFallback = document.getElementById('logo-fallback');

    logoImg.addEventListener('error', function() {
      logoImg.style.display = 'none';
      logoFallback.style.display = 'flex';
    });

    logoImg.addEventListener('load', function() {
      logoImg.style.display = 'block';
      logoFallback.style.display = 'none';
    });
  });

  // Add smooth animations on input focus
  document.querySelectorAll('.form-control-modern').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.classList.add('focused');
    });

    input.addEventListener('blur', function() {
      this.parentElement.classList.remove('focused');
    });
  });
</script>

<?= $this->endSection() ?>