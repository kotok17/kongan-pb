<?= $this->extend('layouts/dashboard_static') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
  <h3>Edit User</h3>
  <form action="<?= site_url('users/update/' . $user['id_user']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" name="username" id="username" class="form-control" value="<?= esc($user['username']) ?> ">
    </div>
    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select name="role" id="role" class="form-select">
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="anggota" <?= $user['role'] === 'anggota' ? 'selected' : '' ?>>Anggota</option>
      </select>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
      <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
    </div>
    <button type="submit" class="btn btn-primary">Update User</button>
    <a href="<?= site_url('dashboard/admin') ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>

<?= $this->endSection() ?>