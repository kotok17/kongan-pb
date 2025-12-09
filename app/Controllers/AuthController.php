<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{

  public function __construct()
  {
    // Load helper di constructor jika belum di-load di BaseController
    helper(['cookie', 'url', 'form']);
  }

  public function index()
  {
    // Cek apakah sudah login
    if (session()->get('logged_in')) {
        return redirect()->to('/dashboard');
    }

    // ✅ CEK REMEMBER TOKEN DARI COOKIE
    $rememberCookie = get_cookie('remember_token');
    if ($rememberCookie) {
        if ($this->loginFromRememberToken($rememberCookie)) {
            return redirect()->to('/dashboard');
        } else {
            // Token tidak valid, hapus cookie
            $this->response->deleteCookie('remember_token');
        }
    }

    return view('auth/login');
  }

  public function login()
  {
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');
    $remember = $this->request->getPost('remember');

    if (empty($username) || empty($password)) {
        return redirect()->back()->with('error', 'Username dan password harus diisi!');
    }

    $userModel = new UserModel();
    $user = $userModel->getUserWithAnggota($username);

    if (!$user) {
        return redirect()->back()->with('error', 'Username tidak ditemukan!');
    }

    if (!password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Password salah!');
    }

    // Set session data
    $sessionData = [
        'id_user' => $user['id_user'],
        'id_anggota' => $user['id_anggota'],
        'username' => $user['username'],
        'role' => $user['role'],
        'nama_anggota' => $user['nama_anggota'] ?? null,
        'logged_in' => true
    ];

    session()->set($sessionData);

    // ✅ HANDLE REMEMBER ME
    if ($remember === 'on' || $remember === '1') {
        // Generate secure remember token
        $rememberToken = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 days')); // 30 hari

        // Update remember token di database
        $userModel->update($user['id_user'], [
            'remember_token' => password_hash($rememberToken, PASSWORD_DEFAULT),
            'remember_expires' => $expiry,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Set cookie (30 hari)
        $this->response->setCookie([
            'name' => 'remember_token',
            'value' => $user['id_user'] . '|' . $rememberToken,
            'expire' => 30 * 24 * 60 * 60, // 30 hari dalam detik
            'httponly' => true,
            'secure' => false, // Set true jika menggunakan HTTPS
            'samesite' => 'Lax'
        ]);
    }

    return redirect()->to('/dashboard');
  }

  public function logout()
  {
    // ✅ HAPUS REMEMBER TOKEN SAAT LOGOUT
    $userId = session()->get('id_user');
    if ($userId) {
        $userModel = new UserModel();
        $userModel->update($userId, [
            'remember_token' => null,
            'remember_expires' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Hapus cookie
    $this->response->deleteCookie('remember_token');
    
    // Destroy session
    session()->destroy();
    
    return redirect()->to('/login')->with('success', 'Anda berhasil logout!');
  }

  // ✅ TAMBAHKAN METHOD BARU
  private function loginFromRememberToken(string $cookieValue): bool
  {
    $parts = explode('|', $cookieValue);
    if (count($parts) !== 2) {
        return false;
    }

    [$userId, $token] = $parts;

    $userModel = new UserModel();
    $user = $userModel->select('users.*, anggota.nama_anggota')
        ->join('anggota', 'anggota.id_anggota = users.id_anggota', 'left')
        ->find($userId);

    if (!$user || !$user['remember_token'] || !$user['remember_expires']) {
        return false;
    }

    // Cek apakah token expired
    if (strtotime($user['remember_expires']) < time()) {
        // Token expired, hapus dari database
        $userModel->update($userId, [
            'remember_token' => null,
            'remember_expires' => null
        ]);
        return false;
    }

    // Verify token
    if (!password_verify($token, $user['remember_token'])) {
        return false;
    }

    // Login berhasil, set session
    $sessionData = [
        'id_user' => $user['id_user'],
        'id_anggota' => $user['id_anggota'],
        'username' => $user['username'],
        'role' => $user['role'],
        'nama_anggota' => $user['nama_anggota'],
        'logged_in' => true
    ];

    session()->set($sessionData);
    return true;
  }
}