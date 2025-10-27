<?php
// filepath: app/Controllers/UserController.php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AnggotaModel;

class UserController extends BaseController
{
  protected $userModel;
  protected $anggotaModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->anggotaModel = new AnggotaModel();
  }

  public function resetPassword($id)
  {
    // Cek akses admin
    if (session()->get('role') !== 'admin') {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Akses ditolak!'
      ]);
    }

    $user = $this->userModel->find($id);
    if (!$user) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'User tidak ditemukan!'
      ]);
    }

    // Generate password baru
    $newPassword = $this->generateRandomPassword(8);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($this->userModel->update($id, ['password' => $hashedPassword])) {
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Password berhasil direset!',
        'new_password' => $newPassword
      ]);
    } else {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal mereset password!'
      ]);
    }
  }

  public function delete($id)
  {
    if (session()->get('role') !== 'admin') {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Akses ditolak!'
      ]);
    }

    $user = $this->userModel->find($id);
    if (!$user) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'User tidak ditemukan!'
      ]);
    }

    // Jangan bisa hapus diri sendiri
    if ($user['username'] === session()->get('username')) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Tidak bisa menghapus akun sendiri!'
      ]);
    }

    if ($this->userModel->delete($id)) {
      return $this->response->setJSON([
        'success' => true,
        'message' => 'User berhasil dihapus!'
      ]);
    } else {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menghapus user!'
      ]);
    }
  }

  private function generateRandomPassword($length = 8)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}
