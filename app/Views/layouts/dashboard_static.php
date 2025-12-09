<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Dashboard' ?></title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Tambahkan ini di head -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5.min.css"
    rel="stylesheet" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
  /* Fixed Header */
  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  /* Content padding untuk menghindari overlap dengan fixed navbar */
  body {
    padding-top: 80px;
  }

  /* Navbar Brand Styling */
  .navbar-brand {
    font-weight: bold;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Navigation Links */
  .navbar-nav .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.3s ease;
  }

  .navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
  }

  /* Active Link */
  .navbar-nav .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
  }

  /* User Info Container */
  .user-section {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  /* User Info */
  .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Logout Button dengan Custom CSS */
  .logout-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }

  .logout-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
    color: white;
  }

  .logout-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.5);
  }

  .logout-btn i {
    margin-right: 6px;
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    body {
      padding-top: 70px;
    }

    .navbar-brand {
      font-size: 1.2rem;
    }

    .user-section {
      flex-direction: column;
      gap: 10px;
    }

    .logout-btn span {
      display: none;
    }

    .logout-btn {
      padding: 6px 12px;
    }
  }

  @media (max-width: 576px) {
    .user-info .user-details {
      display: none;
    }
  }
  </style>
</head>

<body>

  <!-- Fixed Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
      <!-- Brand/Logo -->
      <?php
      $dashboardUrl = session()->get('role') === 'admin' ? '/dashboard/admin' : '/dashboard/anggota';
      ?>
      <a class="navbar-brand" href="<?= base_url($dashboardUrl) ?>">
        <i class="fas fa-users"></i>
        <span>KONGAN App</span>
      </a>

      <!-- Mobile Toggle Button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Menu -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <!-- Dashboard Link -->
          <li class="nav-item">
            <a class="nav-link <?= current_url() == base_url($dashboardUrl) ? 'active' : '' ?>"
              href="<?= base_url($dashboardUrl) ?>">
              <i class="fas fa-tachometer-alt me-1"></i>Dashboard
            </a>
          </li>

          <?php if (session()->get('role') === 'admin'): ?>
          <!-- Admin Menu -->
          <li class="nav-item">
            <a class="nav-link <?= strpos(current_url(), 'kegiatan') !== false ? 'active' : '' ?>"
              href="<?= base_url('/kegiatan') ?>">
              <i class="fas fa-calendar-alt me-1"></i>Kegiatan
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos(current_url(), 'anggota') !== false ? 'active' : '' ?>"
              href="<?= base_url('/anggota') ?>">
              <i class="fas fa-users me-1"></i>Anggota
            </a>
          </li>
          <?php elseif (session()->get('role') === 'anggota'): ?>
          <!-- Anggota Menu -->
          <li class="nav-item">
            <a class="nav-link <?= strpos(current_url(), 'kegiatan') !== false ? 'active' : '' ?>"
              href="<?= base_url('/kegiatan') ?>">
              <i class="fas fa-calendar-alt me-1"></i>Kegiatan
            </a>
          </li>
          <?php endif; ?>
        </ul>

        <!-- User Info & Logout Section -->
        <div class="user-section">
          <!-- User Info -->
          <div class="user-info">
            <div class="user-avatar">
              <i class="fas fa-user"></i>
            </div>
            <div class="user-details d-none d-md-block">
              <div style="font-size: 0.9rem; font-weight: 500;">
                <?= esc(session()->get('username')) ?>
              </div>
              <div style="font-size: 0.75rem; opacity: 0.8;">
                <?= ucfirst(session()->get('role')) ?>
              </div>
            </div>
          </div>

          <!-- Logout Button -->
          <button onclick="confirmLogout()" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span class="d-none d-sm-inline">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container-fluid px-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($msg = session()->getFlashdata('success')): ?>
    <script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      html: <?= json_encode($msg) ?>,
      timer: 2500,
      showConfirmButton: false
    });
    </script>
    <?php endif; ?>

    <?php if ($msg = session()->getFlashdata('error')): ?>
    <script>
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      html: <?= json_encode($msg) ?>,
    });
    </script>
    <?php endif; ?>

    <!-- Page Content -->
    <?= $this->renderSection('content') ?>
  </div>

  <!-- Footer -->
  <footer class="bg-light text-center text-muted py-3 mt-5">
    <div class="container">
      <small>
        &copy; <?= date('Y') ?> KONGAN App - Paguyuban Pemuda Bitung
      </small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
  // Logout confirmation with SweetAlert
  function confirmLogout() {
    Swal.fire({
      title: '🚪 Konfirmasi Logout',
      text: 'Apakah Anda yakin ingin keluar dari aplikasi?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Ya, Logout',
      cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
      reverseButtons: true,
      customClass: {
        confirmButton: 'btn btn-danger px-4',
        cancelButton: 'btn btn-secondary px-4'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: 'Logging out...',
          text: 'Tunggu sebentar',
          icon: 'info',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });

        // Redirect to logout
        setTimeout(() => {
          window.location.href = '<?= base_url('/logout') ?>';
        }, 1500);
      }
    });
  }

  // Auto hide alerts after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 5000);
    });
  });

  // Active link highlighting
  document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(function(link) {
      if (currentUrl.includes(link.getAttribute('href'))) {
        link.classList.add('active');
      }
    });
  });

  // Add pulse animation on hover
  document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.querySelector('.logout-btn');

    logoutBtn.addEventListener('mouseenter', function() {
      this.style.animation = 'pulse 0.5s ease-in-out';
    });

    logoutBtn.addEventListener('mouseleave', function() {
      this.style.animation = '';
    });
  });
  </script>

  <!-- Tambahkan CSS Animation -->
  <style>
  @keyframes pulse {
    0% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.05);
    }

    100% {
      transform: scale(1);
    }
  }
  </style>

</body>

</html>