<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
  public function index()
  {
    // Jika sudah login, redirect ke dashboard
    if (session()->get('logged_in')) {
      $role = session()->get('role');
      if ($role === 'admin') {
        return redirect()->to('dashboard/admin');
      } else {
        return redirect()->to('dashboard/anggota');
      }
    }

    return view('auth/login');
  }

  public function login()
  {
    $rules = [
      'username' => 'required|min_length[3]',
      'password' => 'required|min_length[3]'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    $userModel = new UserModel();
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    // Cari user berdasarkan username
    $user = $userModel
      ->select('users.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = users.id_anggota', 'left')
      ->where('users.username', $username)
      ->first();

    if (!$user) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Username tidak ditemukan!');
    }

    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Password salah!');
    }

    // Login berhasil - set session
    $sessionData = [
      'id_user' => $user['id_user'],
      'id_anggota' => $user['id_anggota'],
      'username' => $user['username'],
      'nama_anggota' => $user['nama_anggota'] ?? 'Admin',
      'role' => $user['role'],
      'logged_in' => true
    ];

    session()->set($sessionData);

    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
      return redirect()->to('dashboard/admin')
        ->with('success', 'Login berhasil sebagai Admin!');
    } else {
      return redirect()->to('dashboard/anggota')
        ->with('success', 'Login berhasil sebagai Anggota!');
    }
  }

  public function logout()
  {
    session()->destroy();
    return redirect()->to('/login')->with('success', 'Logout berhasil!');
  }
}
