<?php
// filepath: app/Controllers/ForgotPasswordController.php

namespace App\Controllers;

use App\Models\UserModel;

class ForgotPasswordController extends BaseController
{
  protected $userModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
  }

  public function index()
  {
    if (session()->get('logged_in')) {
      return redirect()->to('/dashboard');
    }

    $data = [
      'title' => 'Lupa Password'
    ];

    return view('auth/forgot_password', $data);
  }

  public function process()
  {
    $rules = [
      'username' => 'required|min_length[3]|max_length[50]'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Username minimal 3 karakter');
    }

    $username = $this->request->getPost('username');

    // Cek apakah username ada - TANPA FILTER STATUS
    $user = $this->userModel->where('username', $username)->first();

    if (!$user) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Username tidak ditemukan!');
    }

    // Generate password baru
    $newPassword = $this->generateRandomPassword();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password di database
    $updateData = [
      'password' => $hashedPassword,
      'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($this->userModel->update($user['id_user'], $updateData)) {
      $this->logResetPassword($user, $newPassword);

      return redirect()->to('/forgot-password')
        ->with('success', 'Password berhasil direset! Password baru: <strong>' . $newPassword . '</strong><br><small class="text-warning">Harap catat dan simpan dengan baik!</small>');
    } else {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Gagal mereset password. Silakan coba lagi.');
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

  private function logResetPassword($user, $newPassword)
  {
    $logData = [
      'timestamp' => date('Y-m-d H:i:s'),
      'username' => $user['username'],
      'new_password' => $newPassword,
      'ip_address' => $this->request->getIPAddress()
    ];

    $logMessage = "PASSWORD RESET - " . json_encode($logData);
    log_message('info', $logMessage);

    $logFile = WRITEPATH . 'logs/password_reset.log';
    $logEntry = date('Y-m-d H:i:s') . " - Reset password untuk: {$user['username']} - Password baru: {$newPassword}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
  }
}
