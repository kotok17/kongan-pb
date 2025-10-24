<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<!-- Welcome Section -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white shadow-lg border-0 overflow-hidden">
      <div class="card-body p-4 position-relative">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="mb-2 fw-bold">Selamat Datang, <?= esc($username) ?>! 👋</h2>
            <p class="mb-1 opacity-90">Kelola sistem kongan dengan mudah dan efisien</p>
            <small class="opacity-75 badge bg-white bg-opacity-20 px-3 py-1 rounded-pill">
              Login sebagai <strong>Administrator</strong>
            </small>
          </div>
          <div class="col-md-4 text-end d-none d-md-block position-relative">
            <div class="admin-icon">
              <i class="fas fa-user-shield fa-4x opacity-30"></i>
            </div>
          </div>
        </div>
        <!-- Decorative elements -->
        <div class="position-absolute top-0 end-0 p-3">
          <div class="circle-decoration"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-primary mb-3">
          <i class="fas fa-calendar-alt fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-primary mb-1 counter" data-target="<?= $total_kegiatan ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Total Kegiatan</p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-success mb-3">
          <i class="fas fa-users fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-success mb-1 counter" data-target="<?= $total_anggota ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Total Anggota</p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-warning mb-3">
          <i class="fas fa-coins fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-warning mb-1 counter" data-target="<?= $total_kongan ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Total Kongan</p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-info mb-3">
          <i class="fas fa-money-bill-wave fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-info mb-1">Rp <span class="counter"
            data-target="<?= ($total_uang ?? 0) / 1000 ?>"><?= number_format($total_uang ?? 0, 0, ',', '.') ?></span>
        </h3>
        <p class="text-muted mb-0 small fw-medium">Total Uang</p>
      </div>
    </div>
  </div>
</div>

<!-- Main Content Row -->
<div class="row g-4 mb-4">
  <!-- Quick Actions -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center">
          <div class="feature-icon me-3">
            <i class="fas fa-bolt text-primary"></i>
          </div>
          <h5 class="card-title mb-0 fw-bold">⚡ Aksi Cepat</h5>
        </div>
      </div>
      <div class="card-body pt-3">
        <div class="d-grid gap-3">
          <a href="<?= base_url('/kegiatan') ?>" class="btn btn-outline-primary btn-lg action-btn">
            <div class="d-flex align-items-center">
              <i class="fas fa-calendar-plus me-3"></i>
              <div class="text-start">
                <div class="fw-bold">Kelola Kegiatan</div>
                <small class="text-muted">Tambah & atur kegiatan kongan</small>
              </div>
            </div>
          </a>
          <a href="<?= base_url('/anggota') ?>" class="btn btn-outline-success btn-lg action-btn">
            <div class="d-flex align-items-center">
              <i class="fas fa-user-plus me-3"></i>
              <div class="text-start">
                <div class="fw-bold">Kelola Anggota</div>
                <small class="text-muted">Tambah & atur data anggota</small>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activities -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center">
          <div class="feature-icon me-3">
            <i class="fas fa-chart-line text-success"></i>
          </div>
          <h5 class="card-title mb-0 fw-bold">📊 Kegiatan Terbaru</h5>
        </div>
      </div>
      <div class="card-body pt-3">
        <?php if (!empty($kegiatan_terbaru) && count($kegiatan_terbaru) > 0): ?>
        <div class="activity-list">
          <?php foreach (array_slice($kegiatan_terbaru, 0, 4) as $index => $kegiatan): ?>
          <div class="activity-item <?= $index < 3 ? 'mb-3' : '' ?>">
            <div class="d-flex align-items-center">
              <div class="activity-icon me-3">
                <i class="fas fa-calendar fa-sm"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 fw-medium"><?= esc($kegiatan['nama_kegiatan']) ?></h6>
                <div class="d-flex align-items-center text-muted small">
                  <i class="fas fa-user me-2"></i>
                  <span><?= esc($kegiatan['nama_anggota']) ?></span>
                  <span class="mx-2">•</span>
                  <i class="fas fa-clock me-1"></i>
                  <span><?= date('d M', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
          <a href="<?= base_url('/kegiatan') ?>" class="btn btn-sm btn-outline-primary px-4">
            Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
          <div class="empty-state">
            <i class="fas fa-calendar-times fa-3x mb-3 opacity-30"></i>
            <p class="mb-0 fw-medium">Belum ada kegiatan</p>
            <small class="text-muted">Kegiatan yang dibuat akan muncul di sini</small>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- System Info Footer -->
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm system-info">
      <div class="card-body py-3">
        <div class="row text-center g-3">
          <div class="col-md-4">
            <div class="info-item">
              <strong class="text-primary">Sistem</strong>
              <p class="mb-0 small text-muted">Kongan Paguyuban Pemuda Bitung</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item">
              <strong class="text-success">Versi</strong>
              <p class="mb-0 small text-muted">v1.0.0</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item">
              <strong class="text-info">Last Login</strong>
              <p class="mb-0 small text-muted">
                <i class="fas fa-clock me-1"></i>
                <?= date('d M Y, H:i:s') ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Gradient Background */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Card Hover Effects */
.card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

/* Stats Cards */
.stats-card .stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Action Buttons */
.action-btn {
  border: 2px solid;
  border-radius: 12px;
  padding: 1rem;
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: translateX(5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Feature Icons */
.feature-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-rgb) 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.1rem;
}

/* Activity List */
.activity-item {
  padding: 0.75rem;
  border-radius: 8px;
  transition: background-color 0.2s ease;
}

.activity-item:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.activity-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-info) 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

/* System Info */
.system-info {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.info-item {
  padding: 0.5rem;
}

/* Decorative Elements */
.circle-decoration {
  width: 100px;
  height: 100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  position: absolute;
  top: -50px;
  right: -50px;
}

/* Counter Animation */
.counter {
  display: inline-block;
}

/* Empty State */
.empty-state {
  padding: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
  .circle-decoration {
    display: none;
  }

  .action-btn:hover {
    transform: none;
  }

  .card-hover:hover {
    transform: translateY(-4px);
  }
}
</style>

<script>
// Counter Animation
function animateCounters() {
  const counters = document.querySelectorAll('.counter');

  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target')) || parseInt(counter.textContent);
    const increment = target / 100;
    let current = 0;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      counter.textContent = Math.floor(current).toLocaleString('id-ID');
    }, 20);
  });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
  // Animate counters
  setTimeout(animateCounters, 500);

  // Add loading animation to cards
  const cards = document.querySelectorAll('.card-hover');
  cards.forEach((card, index) => {
    setTimeout(() => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'all 0.6s ease';

      setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, 100);
    }, index * 100);
  });
});
</script>

<?= $this->endSection() ?>