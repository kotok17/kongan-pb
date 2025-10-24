<?php

namespace App\Controllers;

use App\Models\UserModel; // Ganti ke UserModel

class AuthController extends BaseController
{
  public function index()
  {
    return view('auth/login');
  }

  public function login()
  {
    $userModel = new UserModel(); // Gunakan UserModel

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    // Validasi input
    if (empty($username) || empty($password)) {
      return redirect()->back()->with('error', 'Username dan password harus diisi!');
    }

    // Cek user di database menggunakan tabel users
    $user = $userModel->where('username', $username)->first();

    if ($user && password_verify($password, $user['password'])) {
      // Set session
      session()->set([
        'id_user' => $user['id_user'],
        'username' => $user['username'],
        'role' => $user['role'],
        'logged_in' => true
      ]);

      // Redirect berdasarkan role
      if ($user['role'] === 'admin') {
        return redirect()->to('/dashboard/admin');
      } else {
        return redirect()->to('/dashboard/anggota');
      }
    } else {
      return redirect()->back()->with('error', 'Username atau password salah!');
    }
  }

  public function logout()
  {
    session()->destroy();
    return redirect()->to('/login')->with('success', 'Berhasil logout!');
  }
}
