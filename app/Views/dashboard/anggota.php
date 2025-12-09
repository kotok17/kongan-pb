<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<!-- Welcome Section untuk Anggota -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-success text-white shadow-lg border-0 overflow-hidden">
      <div class="card-body p-4 position-relative">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="mb-2 fw-bold">Selamat Datang, <?= esc($username) ?>! 👋</h2>
            <p class="mb-1 opacity-90">Pantau kegiatan dan kongan Paguyuban Pemuda Bitung</p>
            <small class="opacity-75 badge bg-white bg-opacity-20 px-3 py-1 rounded-pill">
              Login sebagai <strong>Anggota</strong>
            </small>
          </div>
          <div class="col-md-4 text-end d-none d-md-block position-relative">
            <div class="member-icon">
              <i class="fas fa-user fa-4x opacity-30"></i>
            </div>
          </div>
        </div>
        <!-- Decorative elements -->
        <div class="floating-shapes">
          <div class="shape shape-1"></div>
          <div class="shape shape-2"></div>
          <div class="shape shape-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats Cards untuk Anggota -->
<div class="row g-4 mb-4">
  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-primary mb-3">
          <i class="fas fa-calendar-alt fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-primary mb-1 counter" data-target="<?= $total_kegiatan ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Total Kongan</p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-success mb-3">
          <i class="fas fa-user-check fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-success mb-1 counter" data-target="<?= $kegiatan_saya ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Kongan Saya</p>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 card-hover stats-card">
      <div class="card-body text-center p-4">
        <div class="stats-icon bg-warning mb-3">
          <i class="fas fa-coins fa-lg text-white"></i>
        </div>
        <h3 class="fw-bold text-warning mb-1 counter" data-target="<?= $total_kongan_saya ?? '0' ?>">0</h3>
        <p class="text-muted mb-0 small fw-medium">Total Mengikuti Kongan Lainnya</p>
      </div>
    </div>
  </div>
</div>

<!-- Kegiatan Timeline untuk Anggota -->
<div class="row g-4 mb-4">
  <!-- Kegiatan Saya yang Akan Datang -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <?php
      $totalBersih  = 0;
      if (!empty($kegiatan_saya_list)) {
        foreach ($kegiatan_saya_list as $kegiatan) {
          $totalBersih += (int)($kegiatan['total_kongan'] ?? 0);
        }
      }
      ?>
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="feature-icon me-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
              <i class="fas fa-user-calendar text-white"></i>
            </div>
            <div>
              <h5 class="card-title mb-0 fw-bold">📅 Kegiatan Saya</h5>
              <small class="text-muted">Kegiatan yang saya kelola</small>
              <!-- Tambahkan total kongan dikelola di sini -->
              <div class="mt-1 fw-bold text-success">
                Total Kongan yang didapatkan: Rp <?= number_format($totalBersih, 0, ',', '.') ?>
              </div>
            </div>
          </div>
          <span class="badge bg-success"><?= count($kegiatan_saya_list ?? []) ?></span>
        </div>
      </div>
      <div class="card-body pt-3">
        <?php if (!empty($kegiatan_saya_list) && count($kegiatan_saya_list) > 0): ?>
        <div class="timeline">
          <?php foreach (array_slice($kegiatan_saya_list, 0, 4) as $index => $kegiatan): ?>
          <div class="timeline-item <?= $index < 3 ? 'mb-4' : '' ?>">
            <div
              class="timeline-marker <?= strtotime($kegiatan['tanggal_kegiatan']) >= strtotime(date('Y-m-d')) ? 'bg-success' : 'bg-secondary' ?>">
            </div>
            <div class="timeline-content">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-1 fw-medium"><?= esc($kegiatan['nama_kegiatan']) ?></h6>
                <span
                  class="badge <?= strtotime($kegiatan['tanggal_kegiatan']) >= strtotime(date('Y-m-d')) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                  <?php
                      $tanggal_kegiatan = strtotime($kegiatan['tanggal_kegiatan']);
                      $sekarang = time();
                      if ($tanggal_kegiatan >= strtotime(date('Y-m-d'))) {
                        $days = ceil(($tanggal_kegiatan - $sekarang) / (60 * 60 * 24));
                        echo $days == 0 ? 'Hari ini' : $days . ' hari lagi';
                      } else {
                        $days = floor(($sekarang - $tanggal_kegiatan) / (60 * 60 * 24));
                        echo $days . ' hari lalu';
                      }
                      ?>
                </span>
              </div>
              <div class="d-flex align-items-center text-muted small mb-2">
                <i class="fas fa-calendar me-2"></i>
                <span class="fw-medium"><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
                <span class="mx-2">•</span>
                <i class="fas fa-coins me-1"></i>
                <span>Rp. <?= number_format($kegiatan['total_kongan'] ?? 0, 0, ',', '.') ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($kegiatan_saya_list) > 4): ?>
        <div class="text-center mt-4">
          <a href="<?= base_url('/kegiatan') ?>" class="btn btn-sm btn-success px-4">
            Lihat Semua (<?= count($kegiatan_saya_list) ?>) <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center text-muted py-5">
          <div class="empty-state">
            <i class="fas fa-user-calendar fa-3x mb-3 opacity-30"></i>
            <p class="mb-0 fw-medium">Belum ada kegiatan yang Anda kelola</p>
            <small class="text-muted">Kegiatan yang Anda buat akan muncul di sini</small>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Kegiatan Umum/Semua Kegiatan -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="feature-icon me-3" style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
              <i class="fas fa-calendar-week text-white"></i>
            </div>
            <div>
              <h5 class="card-title mb-0 fw-bold">🗓️ Kegiatan Terbaru</h5>
              <small class="text-muted">Kegiatan terbaru paguyuban</small>
            </div>
          </div>
          <span class="badge bg-primary"><?= count($kegiatan_terbaru ?? []) ?></span>
        </div>
      </div>
      <div class="card-body pt-3">
        <?php if (!empty($kegiatan_terbaru) && count($kegiatan_terbaru) > 0): ?>
        <div class="timeline">
          <?php foreach (array_slice($kegiatan_terbaru, 0, 4) as $index => $kegiatan): ?>
          <div class="timeline-item <?= $index < 3 ? 'mb-4' : '' ?>">
            <div class="timeline-marker bg-primary"></div>
            <div class="timeline-content">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-1 fw-medium"><?= esc($kegiatan['nama_kegiatan']) ?></h6>
                <span class="badge bg-primary-subtle text-primary">
                  <?php
                      $tanggal_kegiatan = strtotime($kegiatan['tanggal_kegiatan']);
                      $sekarang = time();
                      if ($tanggal_kegiatan >= strtotime(date('Y-m-d'))) {
                        $days = ceil(($tanggal_kegiatan - $sekarang) / (60 * 60 * 24));
                        echo $days == 0 ? 'Hari ini' : $days . ' hari lagi';
                      } else {
                        $days = floor(($sekarang - $tanggal_kegiatan) / (60 * 60 * 24));
                        echo $days . ' hari lalu';
                      }
                      ?>
                </span>
              </div>
              <div class="d-flex align-items-center text-muted small mb-2">
                <i class="fas fa-calendar me-2"></i>
                <span class="fw-medium"><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
                <span class="mx-2">•</span>
                <i class="fas fa-user me-1"></i>
                <span><?= esc($kegiatan['nama_anggota']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($kegiatan_terbaru) > 4): ?>
        <!-- <div class="text-center mt-4">
          <a href="<?= base_url('/kegiatan') ?>" class="btn btn-sm btn-primary px-4">
            Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div> -->
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center text-muted py-5">
          <div class="empty-state">
            <i class="fas fa-calendar-week fa-3x mb-3 opacity-30"></i>
            <p class="mb-0 fw-medium">Belum ada kegiatan terbaru</p>
            <small class="text-muted">Kegiatan akan muncul di sini</small>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- History Kongan yang Diikuti -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-primary text-white fw-bold">
        <i class="fas fa-history me-2"></i>History Kongan yang Anda Ikuti
      </div>
      <div class="card-body">
        <?php
          $totalKonganHistory = 0;
          foreach ($history_kongan as $row) {
            $totalKonganHistory += $row['jumlah'];
          }
        ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="historyKonganTable">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama Kegiatan</th>
                <th>Tanggal</th>
                <th>Pemilik Kegiatan</th>
                <th class="text-end">Jumlah Kongan</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              foreach ($history_kongan as $row): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($row['nama_kegiatan']) ?></td>
                <td><?= date('d M Y', strtotime($row['tanggal_kegiatan'])) ?></td>
                <td><?= esc($row['pemilik_kegiatan']) ?></td>
                <td class="text-end">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-end">Total Kongan</th>
                <th class="text-end">Rp <?= number_format($totalKonganHistory, 0, ',', '.') ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Info Panel untuk Anggota -->
<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm bg-gradient-light">
      <div class="card-body p-4">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
              <div class="info-icon me-3">
                <i class="fas fa-info-circle text-primary"></i>
              </div>
              <h5 class="mb-0 fw-bold">📢 Informasi untuk Anggota</h5>
            </div>
            <ul class="list-unstyled mb-0">
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Anda dapat melihat semua kegiatan
                paguyuban</li>
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Pantau kegiatan yang Anda kelola
                melalui dashboard ini</li>
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Lihat detail kongan untuk setiap
                kegiatan</li>
              <li class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>Hubungi admin jika ada pertanyaan
                <p>Silakan hubungi admin melalui chat Whatsapp. <a href="http://wa.me/081310152142"
                    target="_blank">Whatsapp!</a></p>
              </li>
            </ul>
          </div>
          <div class="col-md-4 text-center">
            <div class="member-badge">
              <i class="fas fa-users fa-3x text-primary mb-2"></i>
              <p class="fw-bold text-primary mb-0">Anggota Paguyuban</p>
              <small class="text-muted">Pemuda Bitung</small>
            </div>
          </div>
        </div>
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
              <strong class="text-success">Sistem</strong>
              <p class="mb-0 small text-muted">Kongan Paguyuban Pemuda Bitung</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item">
              <strong class="text-primary">Status</strong>
              <p class="mb-0 small text-muted">Anggota Aktif</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-item">
              <strong class="text-info">Last Login</strong>
              <p class="mb-0 small text-muted">
                <i class="fas fa-clock me-1"></i>
                <?= date('d M Y, H:i') ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Success Gradient Background */
.bg-gradient-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-light {
  background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
}

/* Floating Shapes */
.floating-shapes {
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  pointer-events: none;
}

.shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 10%;
  right: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 60px;
  height: 60px;
  top: 60%;
  right: 20%;
  animation-delay: 2s;
}

.shape-3 {
  width: 40px;
  height: 40px;
  top: 30%;
  right: 5%;
  animation-delay: 4s;
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }

  50% {
    transform: translateY(-20px) rotate(180deg);
  }
}

/* Card Hover Effects */
.card-hover {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Timeline */
.timeline {
  position: relative;
}

.timeline-item {
  position: relative;
  padding-left: 2rem;
}

.timeline-marker {
  position: absolute;
  left: 0;
  top: 0.5rem;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: 5.5px;
  top: 1.5rem;
  bottom: -1rem;
  width: 1px;
  background: linear-gradient(to bottom, #e9ecef, transparent);
}

.timeline-content {
  background: rgba(0, 0, 0, 0.02);
  padding: 1rem;
  border-radius: 8px;
  border-left: 3px solid var(--bs-success);
  transition: all 0.3s ease;
}

.timeline-content:hover {
  background: rgba(0, 0, 0, 0.04);
  transform: translateX(2px);
}

/* Feature Icons */
.feature-icon,
.info-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--bs-success) 0%, var(--bs-info) 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.1rem;
}

/* Member Badge */
.member-badge {
  padding: 1.5rem;
  background: rgba(0, 123, 255, 0.05);
  border-radius: 15px;
  border: 2px dashed rgba(0, 123, 255, 0.2);
}

/* System Info */
.system-info {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Counter Animation */
.counter {
  display: inline-block;
}

/* Responsive */
@media (max-width: 768px) {
  .floating-shapes {
    display: none;
  }

  .card-hover:hover {
    transform: translateY(-4px);
  }

  .timeline-content:hover {
    transform: none;
  }
}
</style>

<script>
$(document).ready(function() {
  $('#historyKonganTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
    },
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    order: [
      [0, 'asc']
    ],
    columnDefs: [{
      targets: [0, 4],
      className: 'text-end'
    }]
  });
});
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