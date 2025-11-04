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

<!-- Stats Cards - CENTER LAYOUT -->
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex flex-wrap justify-content-center gap-4">
      <!-- Total Kegiatan Card -->
      <div class="stats-card-wrapper">
        <div class="card border-0 shadow-sm card-hover stats-card">
          <div class="card-body text-center p-4">
            <div class="stats-icon bg-primary mb-3">
              <i class="fas fa-calendar-alt fa-lg text-white"></i>
            </div>
            <h3 class="fw-bold text-primary mb-1 counter" data-target="<?= $total_kegiatan ?? '0' ?>"
              id="total-kegiatan"><?= number_format($total_kegiatan, 0, ',', '.') ?></h3>
            <p class="text-muted mb-0 small fw-medium">Total Kegiatan</p>
          </div>
        </div>
      </div>

      <!-- Total Anggota Card -->
      <div class="stats-card-wrapper">
        <div class="card border-0 shadow-sm card-hover stats-card">
          <div class="card-body text-center p-4">
            <div class="stats-icon bg-success mb-3">
              <i class="fas fa-users fa-lg text-white"></i>
            </div>
            <h3 class="fw-bold text-success mb-1 counter" data-target="<?= $total_anggota ?? '0' ?>" id="total-anggota">
              <?= number_format($total_anggota, 0, ',', '.') ?></h3>
            <p class="text-muted mb-0 small fw-medium">Total Anggota</p>
          </div>
        </div>
      </div>

      <!-- Total Uang Card - PERBAIKAN DI SINI -->
      <div class="stats-card-wrapper">
        <div class="card border-0 shadow-sm card-hover stats-card">
          <div class="card-body text-center p-4">
            <div class="stats-icon bg-info mb-3">
              <i class="fas fa-money-bill-wave fa-lg text-white"></i>
            </div>
            <div class="mb-1">
              <h3 class="fw-bold text-info mb-0 d-inline" id="total-uang-display">
                Rp <?= number_format($total_uang ?? 0, 0, ',', '.') ?>
              </h3>
            </div>
            <p class="text-muted mb-0 small fw-medium">Total Uang Kongan</p>
          </div>
        </div>
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

  <!-- Kegiatan Akan Datang - REALTIME -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="feature-icon me-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
              <i class="fas fa-calendar-check text-white"></i>
            </div>
            <div>
              <h5 class="card-title mb-0 fw-bold">🗓️ Kegiatan Akan Datang</h5>
              <small class="text-muted">Update realtime</small>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <span class="badge bg-success me-2"><?= count($kegiatan_akan_datang ?? []) ?></span>
            <div class="text-success" id="last-update">
              <i class="fas fa-sync-alt fa-spin fa-xs"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-3">
        <?php if (!empty($kegiatan_akan_datang) && count($kegiatan_akan_datang) > 0): ?>
        <div class="upcoming-events-list">
          <?php foreach (array_slice($kegiatan_akan_datang, 0, 4) as $index => $kegiatan): ?>
          <div class="upcoming-event-item <?= $index < 3 ? 'mb-3' : '' ?>">
            <div class="d-flex align-items-center">
              <div class="event-status me-3">
                <?php
                    $days = (int)$kegiatan['days_remaining'];
                    if ($days == 0): ?>
                <div class="status-badge today">
                  <i class="fas fa-calendar-day"></i>
                </div>
                <?php elseif ($days <= 3): ?>
                <div class="status-badge urgent">
                  <i class="fas fa-exclamation"></i>
                </div>
                <?php else: ?>
                <div class="status-badge normal">
                  <i class="fas fa-calendar"></i>
                </div>
                <?php endif; ?>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <h6 class="mb-0 fw-medium"><?= esc($kegiatan['nama_kegiatan']) ?></h6>
                  <span class="badge <?= $days == 0 ? 'bg-danger' : ($days <= 3 ? 'bg-warning' : 'bg-success') ?>">
                    <?= $days == 0 ? 'Hari ini!' : ($days == 1 ? 'Besok' : $days . ' hari lagi') ?>
                  </span>
                </div>
                <div class="d-flex align-items-center text-muted small mb-1">
                  <i class="fas fa-user me-2"></i>
                  <span class="fw-medium"><?= esc($kegiatan['nama_anggota']) ?></span>
                  <span class="mx-2">•</span>
                  <i class="fas fa-calendar me-1"></i>
                  <span><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <div class="text-muted small">
                    <i class="fas fa-users me-1"></i>
                    <?= $kegiatan['total_peserta'] ?? '0' ?> peserta ikut kongan
                  </div>
                  <a href="<?= base_url('/kegiatan/detail/' . $kegiatan['id_kegiatan']) ?>"
                    class="btn btn-sm btn-outline-success">
                    <i class="fas fa-eye me-1"></i>Detail
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <?php if (count($kegiatan_akan_datang) > 4): ?>
        <div class="text-center mt-4">
          <a href="<?= base_url('/kegiatan') ?>" class="btn btn-sm btn-success px-4">
            Lihat Semua (<?= count($kegiatan_akan_datang) ?>) <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center text-muted py-5">
          <div class="empty-state">
            <i class="fas fa-calendar-plus fa-3x mb-3 opacity-30"></i>
            <p class="mb-0 fw-medium">Tidak ada kegiatan akan datang</p>
            <small class="text-muted">Kegiatan yang dijadwalkan akan muncul di sini</small>
            <div class="mt-3">
              <a href="<?= base_url('/kegiatan') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i>Tambah Kegiatan
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- User Management Section -->
<div class="row g-4 mb-4">
  <!-- Users List -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="feature-icon me-3" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
              <i class="fas fa-users-cog text-white"></i>
            </div>
            <div>
              <h5 class="card-title mb-0 fw-bold">👥 Manajemen User</h5>
              <small class="text-muted">Daftar user & password sistem</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-purple"><?= count($users ?? []) ?> users</span>
            <a href="<?= base_url('users/tambah') ?>" class="btn btn-sm btn-purple">
              <i class="fas fa-plus me-1"></i>Tambah User
            </a>
          </div>
        </div>
      </div>
      <div class="card-body pt-3">
        <?php if (!empty($users) && count($users) > 0): ?>

        <!-- TAMBAHKAN QUICK SEARCH BUTTONS -->
        <div class="search-buttons mb-3">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <small class="text-muted fw-medium me-2">Filter Cepat:</small>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="searchByRole('admin')">
              <i class="fas fa-user-shield me-1"></i>Admin
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="searchByRole('anggota')">
              <i class="fas fa-users me-1"></i>Anggota
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSearch()">
              <i class="fas fa-eraser me-1"></i>Reset Filter
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table id="table_users" class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th class="border-0">User</th>
                <th class="border-0">Role</th>
                <th class="border-0">Password</th>
                <th class="border-0 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr class="user-row">
                <td>
                  <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                      <?php if ($user['role'] === 'admin'): ?>
                      <i class="fas fa-user-shield text-danger"></i>
                      <?php else: ?>
                      <i class="fas fa-user text-primary"></i>
                      <?php endif; ?>
                    </div>
                    <div>
                      <div class="fw-medium"><?= esc($user['username']) ?></div>
                      <small class="text-muted">
                        <?= isset($user['nama_anggota']) ? esc($user['nama_anggota']) : 'Tidak ada anggota terkait' ?>
                      </small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-success' ?> text-uppercase">
                    <?= esc($user['role']) ?>
                  </span>
                </td>
                <td>
                  <div class="password-field">
                    <code class="password-display bg-light px-2 py-1 rounded" data-password="Hidden">
                          ••••••••
                        </code>
                    <button type="button" class="btn btn-sm btn-link p-0 ms-2 toggle-password"
                      title="Show/Hide Password">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-warning"
                      onclick="resetPassword(<?= $user['id_user'] ?>, '<?= esc($user['username']) ?>')"
                      title="Reset Password">
                      <i class="fas fa-key"></i>
                    </button>
                    <a href="<?= base_url('users/edit/' . $user['id_user']) ?>" class="btn btn-sm btn-outline-primary"
                      title="Edit User">
                      <i class="fas fa-edit"></i>
                    </a>
                    <?php if ($user['username'] !== session()->get('username')): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                      onclick="deleteUser(<?= $user['id_user'] ?>, '<?= esc($user['username']) ?>')" title="Hapus User">
                      <i class="fas fa-trash"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php else: ?>
        <div class="text-center text-muted py-5">
          <div class="empty-state">
            <i class="fas fa-users fa-3x mb-3 opacity-30"></i>
            <p class="mb-0 fw-medium">Belum ada user sistem</p>
            <small class="text-muted">User yang terdaftar akan muncul di sini</small>
            <div class="mt-3">
              <a href="<?= base_url('users/tambah') ?>" class="btn btn-sm btn-outline-purple">
                <i class="fas fa-plus me-1"></i>Tambah User
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- User Stats & Quick Actions -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-0 pb-0">
        <div class="d-flex align-items-center">
          <div class="feature-icon me-3" style="background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%);">
            <i class="fas fa-chart-pie text-white"></i>
          </div>
          <h5 class="card-title mb-0 fw-bold">📊 Statistik User</h5>
        </div>
      </div>
      <div class="card-body pt-3">
        <!-- User Stats -->
        <div class="user-stats mb-4">
          <div class="stat-item mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="fas fa-user-shield text-danger me-2"></i>
                <span class="fw-medium">Admin</span>
              </div>
              <div>
                <span class="badge bg-danger"><?= $total_admin ?? 0 ?></span>
              </div>
            </div>
          </div>

          <div class="stat-item mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="fas fa-users text-success me-2"></i>
                <span class="fw-medium">Anggota</span>
              </div>
              <div>
                <span class="badge bg-success"><?= $total_user_anggota ?? 0 ?></span>
              </div>
            </div>
          </div>

          <div class="stat-item mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="fas fa-users text-primary me-2"></i>
                <span class="fw-medium">Total User</span>
              </div>
              <div>
                <span class="badge bg-primary"><?= $total_users ?? 0 ?></span>
              </div>
            </div>
          </div>
        </div>

        <hr>

        <!-- Quick Actions -->
        <div class="quick-actions">
          <h6 class="fw-bold mb-3">⚡ Aksi Cepat</h6>

          <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetAllPasswords()">
              <i class="fas fa-key me-2"></i>Reset Semua Password
            </button>

            <a href="<?= base_url('users') ?>" class="btn btn-outline-purple btn-sm">
              <i class="fas fa-cog me-2"></i>Kelola Semua User
            </a>

            <button type="button" class="btn btn-outline-info btn-sm" onclick="exportUsers()">
              <i class="fas fa-download me-2"></i>Export Data User
            </button>
          </div>
        </div>

        <!-- Security Info -->
        <div class="security-info mt-4 p-3 bg-light rounded">
          <h6 class="fw-bold mb-2 text-warning">
            <i class="fas fa-shield-alt me-1"></i>Keamanan
          </h6>
          <small class="text-muted">
            • Password ditampilkan untuk keperluan admin<br>
            • Selalu ganti password default<br>
            • Jangan bagikan kredensial login
          </small>
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
          <div class="col-md-3">
            <div class="info-item">
              <strong class="text-primary">Sistem</strong>
              <p class="mb-0 small text-muted">Kongan Paguyuban Pemuda Bitung</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-item">
              <strong class="text-success">Versi</strong>
              <p class="mb-0 small text-muted">v1.0.0</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-item">
              <strong class="text-warning">Status</strong>
              <p class="mb-0 small text-success">
                <i class="fas fa-circle fa-xs me-1"></i>Online
              </p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-item">
              <strong class="text-info">Last Update</strong>
              <p class="mb-0 small text-muted" id="current-time">
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

/* Stats Card Wrapper - CENTERED LAYOUT */
.stats-card-wrapper {
  flex: 0 0 auto;
  width: 280px;
  margin: 0 10px;
}

/* Card Hover Effects */
.card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
}

.card-hover:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

/* Stats Cards */
.stats-card {
  min-height: 180px;
}

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

/* Responsive untuk Stats Cards */
@media (max-width: 1200px) {
  .stats-card-wrapper {
    width: 260px;
    margin: 0 5px;
  }
}

@media (max-width: 992px) {
  .stats-card-wrapper {
    width: 240px;
    margin: 0 5px 15px 5px;
  }
}

@media (max-width: 768px) {
  .stats-card-wrapper {
    width: 200px;
    margin: 0 5px 15px 5px;
  }

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

@media (max-width: 576px) {
  .d-flex.justify-content-center {
    justify-content: center !important;
    flex-wrap: wrap;
  }

  .stats-card-wrapper {
    width: 160px;
    margin: 5px;
  }

  .stats-card {
    min-height: 160px;
  }

  .stats-card .card-body {
    padding: 1rem 0.5rem;
  }

  .stats-icon {
    width: 50px !important;
    height: 50px !important;
  }
}

/* Upcoming Events Styling */
.upcoming-event-item {
  padding: 1rem;
  border-radius: 10px;
  background: rgba(0, 0, 0, 0.02);
  border-left: 4px solid var(--bs-success);
  transition: all 0.3s ease;
}

.upcoming-event-item:hover {
  background: rgba(0, 0, 0, 0.04);
  transform: translateX(3px);
}

.status-badge {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.9rem;
}

.status-badge.today {
  background: linear-gradient(135deg, #dc3545, #c82333);
  animation: pulse-red 2s infinite;
}

.status-badge.urgent {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  animation: pulse-yellow 3s infinite;
}

.status-badge.normal {
  background: linear-gradient(135deg, #28a745, #20c997);
}

@keyframes pulse-red {

  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
  }

  50% {
    box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
  }
}

@keyframes pulse-yellow {

  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
  }

  50% {
    box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
  }
}

/* Real-time indicator */
#last-update {
  font-size: 0.75rem;
}

/* User Management Styles */
.bg-purple,
.btn-purple {
  background-color: #6f42c1 !important;
  border-color: #6f42c1 !important;
  color: white;
}

.btn-outline-purple {
  color: #6f42c1;
  border-color: #6f42c1;
}

.btn-outline-purple:hover {
  background-color: #6f42c1;
  border-color: #6f42c1;
  color: white;
}

.user-row {
  transition: all 0.2s ease;
}

.user-row:hover {
  background-color: rgba(111, 66, 193, 0.05);
}

.user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 8px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}

.password-field {
  display: flex;
  align-items: center;
}

.password-display {
  min-width: 80px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.password-display:hover {
  background-color: #e9ecef !important;
}

.toggle-password {
  font-size: 0.8rem;
  color: #6c757d;
}

.toggle-password:hover {
  color: #495057;
}

.stat-item {
  padding: 0.75rem;
  border-radius: 8px;
  background: rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;
}

.stat-item:hover {
  background: rgba(0, 0, 0, 0.04);
}

.security-info {
  border-left: 4px solid #ffc107;
}

/* Responsive */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.9rem;
  }

  .btn-group .btn {
    padding: 0.25rem 0.5rem;
  }
}

/* DataTable Custom Styles */
.dataTables_wrapper {
  font-size: 0.9rem;
}

.dataTables_filter {
  margin-bottom: 1rem;
}

.dataTables_filter input {
  border-radius: 6px;
  border: 1px solid #ced4da;
  padding: 0.375rem 0.75rem;
  width: 250px !important;
  margin-left: 0.5rem;
}

.dataTables_filter input:focus {
  border-color: #6f42c1;
  box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.dataTables_length select {
  border-radius: 6px;
  border: 1px solid #ced4da;
  padding: 0.375rem 1.75rem 0.375rem 0.75rem;
  margin: 0 0.5rem;
}

.dataTables_info {
  color: #6c757d;
  font-size: 0.875rem;
}

.dataTables_paginate {
  margin-top: 1rem;
}

.dataTables_paginate .paginate_button {
  padding: 0.375rem 0.75rem !important;
  margin: 0 2px !important;
  border-radius: 6px !important;
  border: 1px solid #dee2e6 !important;
  color: #6f42c1 !important;
  background: white !important;
}

.dataTables_paginate .paginate_button:hover {
  background: #6f42c1 !important;
  color: white !important;
  border-color: #6f42c1 !important;
}

.dataTables_paginate .paginate_button.current {
  background: #6f42c1 !important;
  color: white !important;
  border-color: #6f42c1 !important;
}

.dataTables_paginate .paginate_button.disabled {
  color: #6c757d !important;
  background: #f8f9fa !important;
  border-color: #dee2e6 !important;
}

/* Responsive DataTable */
@media (max-width: 768px) {
  .dataTables_filter input {
    width: 200px !important;
  }

  .dataTables_length,
  .dataTables_filter {
    text-align: center;
    margin-bottom: 0.5rem;
  }

  .dataTables_wrapper .row {
    margin: 0;
  }

  .table-responsive .dataTables_scrollBody {
    overflow-x: auto;
  }
}

/* Custom search buttons */
.search-buttons {
  margin-bottom: 1rem;
}

.search-buttons .btn {
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}

/* Loading state untuk DataTable */
.dataTables_processing {
  background: rgba(255, 255, 255, 0.9) !important;
  border: 1px solid #6f42c1 !important;
  color: #6f42c1 !important;
  border-radius: 6px !important;
}

/* Stats Card Number - Responsive Font Size */
.stats-card h3 {
  font-size: clamp(1.5rem, 2.5vw, 1.75rem);
  line-height: 1.2;
}

/* Total Uang specific styling */
#total-uang-display {
  font-size: clamp(1.3rem, 2.2vw, 1.6rem);
  word-break: break-word;
}
</style>

<script>
// Counter Animation - PERBAIKAN
function animateCounters() {
  // Animate Total Kegiatan
  const kegiatanElement = document.getElementById('total-kegiatan');
  const kegiatanTarget = parseInt(kegiatanElement.getAttribute('data-target')) || 0;
  animateSingleCounter(kegiatanElement, kegiatanTarget, false);

  // Animate Total Anggota  
  const anggotaElement = document.getElementById('total-anggota');
  const anggotaTarget = parseInt(anggotaElement.getAttribute('data-target')) || 0;
  animateSingleCounter(anggotaElement, anggotaTarget, false);

  // Animate Total Uang - FORMAT RUPIAH
  const uangElement = document.getElementById('total-uang-display');
  const uangTarget = <?= $total_uang ?? 0 ?>;
  animateSingleCounter(uangElement, uangTarget, true);
}

function animateSingleCounter(element, target, isRupiah = false) {
  const duration = 2000; // 2 detik
  const steps = 60;
  const increment = target / steps;
  let current = 0;
  let step = 0;

  const timer = setInterval(() => {
    step++;
    current += increment;

    if (step >= steps) {
      current = target;
      clearInterval(timer);
    }

    if (isRupiah) {
      element.innerHTML = 'Rp ' + Math.floor(current).toLocaleString('id-ID');
    } else {
      element.textContent = Math.floor(current).toLocaleString('id-ID');
    }
  }, duration / steps);
}

// Real-time clock update
function updateClock() {
  const now = new Date();
  const options = {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  };
  document.getElementById('current-time').innerHTML =
    '<i class="fas fa-clock me-1"></i>' +
    now.toLocaleDateString('id-ID', options);
}

// Update last refresh indicator
function updateRefreshIndicator() {
  const indicator = document.getElementById('last-update');
  indicator.innerHTML = '<i class="fas fa-check fa-xs text-success"></i>';
  setTimeout(() => {
    indicator.innerHTML = '<i class="fas fa-sync-alt fa-spin fa-xs"></i>';
  }, 2000);
}

// Auto refresh stats setiap 30 detik
function refreshStats() {
  fetch('<?= base_url('api/dashboard/stats') ?>')
    .then(response => response.json())
    .then(data => {
      // Update Total Kegiatan
      const kegiatanEl = document.getElementById('total-kegiatan');
      if (kegiatanEl) {
        animateSingleCounter(kegiatanEl, data.total_kegiatan || 0, false);
      }

      // Update Total Anggota
      const anggotaEl = document.getElementById('total-anggota');
      if (anggotaEl) {
        animateSingleCounter(anggotaEl, data.total_anggota || 0, false);
      }

      // Update Total Uang
      const uangEl = document.getElementById('total-uang-display');
      if (uangEl) {
        animateSingleCounter(uangEl, data.total_uang || 0, true);
      }

      updateRefreshIndicator();
    })
    .catch(error => {
      console.log('Error refreshing stats:', error);
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

  // Update clock every second
  setInterval(updateClock, 1000);
  updateClock();

  // Simulate refresh every 30 seconds
  setInterval(updateRefreshIndicator, 30000);

  // Jalankan refresh pertama kali setelah 5 detik
  setTimeout(refreshStats, 5000);

  // Set interval refresh setiap 30 detik
  setInterval(refreshStats, 30000);
});

// Toggle Password Visibility
document.addEventListener('DOMContentLoaded', function() {
  const toggleButtons = document.querySelectorAll('.toggle-password');

  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const passwordDisplay = this.parentElement.querySelector('.password-display');
      const actualPassword = passwordDisplay.getAttribute('data-password');
      const icon = this.querySelector('i');

      if (icon.classList.contains('fa-eye')) {
        // Show password
        passwordDisplay.textContent = actualPassword;
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        // Hide password
        passwordDisplay.textContent = '•'.repeat(actualPassword.length);
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });
});

// Reset Password Function
function resetPassword(userId, username) {
  Swal.fire({
    title: 'Reset Password?',
    text: `Generate password baru untuk user "${username}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ffc107',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Reset!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Ajax request untuk reset password
      fetch(`<?= base_url('users/reset-password') ?>/${userId}`, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: 'Password Direset!',
              html: `Password baru untuk <strong>${username}</strong>:<br><br>
                     <code class="bg-warning px-3 py-2 rounded">${data.new_password}</code><br><br>
                     <small class="text-muted">Catat password ini dengan baik!</small>`,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Error!', data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error!', 'Terjadi kesalahan saat reset password', 'error');
        });
    }
  });
}

// Delete User Function
function deleteUser(userId, username) {
  Swal.fire({
    title: 'Hapus User?',
    text: `User "${username}" akan dihapus permanen!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`<?= base_url('users/delete') ?>/${userId}`, {
          method: 'DELETE',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Terhapus!', data.message, 'success').then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Error!', data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error!', 'Terjadi kesalahan saat menghapus user', 'error');
        });
    }
  });
}

// Reset All Passwords Function
function resetAllPasswords() {
  Swal.fire({
    title: 'Reset Semua Password?',
    text: 'Ini akan mereset password semua user (kecuali admin yang sedang login)',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ffc107',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Reset Semua!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Implementasi reset semua password
      Swal.fire('Info', 'Fitur ini akan segera tersedia', 'info');
    }
  });
}

// Export Users Function
function exportUsers() {
  Swal.fire({
    title: 'Export Data User',
    text: 'Download data user dalam format Excel?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#17a2b8',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Download!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.open(`<?= base_url('users/export') ?>`, '_blank');
    }
  });
}

// DataTable initialization untuk table users
$(document).ready(function() {
  // Initialize DataTable untuk table users
  $('#table_users').DataTable({
    responsive: true,
    pageLength: 10,
    lengthMenu: [
      [5, 10, 25, 50, -1],
      [5, 10, 25, 50, "Semua"]
    ],
    order: [
      [1, 'asc']
    ], // Sort by role column
    columnDefs: [{
        targets: [3], // Column Aksi (index 3)
        orderable: false,
        searchable: false,
        width: "120px",
        className: "text-center"
      },
      {
        targets: [2], // Column Password (index 2) 
        orderable: false,
        searchable: false,
        width: "150px"
      },
      {
        targets: [1], // Column Role (index 1)
        width: "100px"
      }
    ],
    language: {
      search: "Cari User:",
      searchPlaceholder: "Username, nama, role...",
      lengthMenu: "Tampilkan _MENU_ user per halaman",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ user",
      infoEmpty: "Menampilkan 0 sampai 0 dari 0 user",
      infoFiltered: "(difilter dari _MAX_ total user)",
      paginate: {
        first: "Pertama",
        last: "Terakhir",
        next: "Selanjutnya",
        previous: "Sebelumnya"
      },
      emptyTable: "Tidak ada data user",
      zeroRecords: "Tidak ada user yang cocok dengan pencarian"
    },
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
      '<"row"<"col-sm-12"tr>>' +
      '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    drawCallback: function(settings) {
      // Re-initialize toggle password setelah DataTable di-render
      initializeTogglePassword();
    }
  });
});

// Function untuk initialize toggle password (dipanggil setelah DataTable render)
function initializeTogglePassword() {
  $('.toggle-password').off('click').on('click', function() {
    const passwordDisplay = $(this).closest('.password-field').find('.password-display');
    const actualPassword = passwordDisplay.attr('data-password');
    const icon = $(this).find('i');

    if (icon.hasClass('fa-eye')) {
      // Show password
      if (actualPassword !== 'Hidden') {
        passwordDisplay.text(actualPassword);
      } else {
        // Fetch real password via AJAX jika diperlukan
        passwordDisplay.text('••••••••');
      }
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      // Hide password
      passwordDisplay.text('••••••••');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });
}

// Custom search functions
function searchByRole(role) {
  const table = $('#table_users').DataTable();
  table.column(1).search(role).draw();
}

function clearSearch() {
  const table = $('#table_users').DataTable();
  table.search('').columns().search('').draw();
}

// ... rest of existing script ...
</script>

<?= $this->endSection() ?>