<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\KegiatanModel;
use App\Models\KonganModel;

class DashboardController extends BaseController
{
  public function admin()
  {
    $kegiatanModel = new KegiatanModel();
    $anggotaModel = new AnggotaModel();
    $konganModel = new KonganModel();

    $data['username'] = session()->get('username');

    // Statistik real-time
    $data['total_kegiatan'] = $kegiatanModel->countAll();
    $data['total_anggota'] = $anggotaModel->countAll();
    $data['total_kongan'] = $konganModel->countAll();

    // Total uang kongan
    $total_uang_result = $konganModel->selectSum('jumlah')->first();
    $data['total_uang'] = $total_uang_result['jumlah'] ?? 0;

    // Kegiatan terbaru dengan join anggota
    $data['kegiatan_terbaru'] = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->orderBy('kegiatan.tanggal_kegiatan', 'DESC')
      ->limit(5)
      ->findAll();

    return view('dashboard/admin', $data);
  }

  public function anggota()
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'anggota') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $data['username'] = session()->get('username');
    return view('dashboard/anggota', $data);
  }
}
