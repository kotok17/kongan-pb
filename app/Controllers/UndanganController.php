<?php

namespace App\Controllers;

use App\Models\KegiatanModel;
use App\Models\AnggotaModel;
// use Dompdf\Dompdf;
// use Dompdf\Options;

class UndanganController extends BaseController
{
  public function preview($id_kegiatan)
  {
    // Cek akses - hanya admin atau pemilik kegiatan
    if (session()->get('role') !== 'admin') {
      $kegiatanModel = new KegiatanModel();
      $kegiatan = $kegiatanModel->find($id_kegiatan);

      if (!$kegiatan || $kegiatan['id_anggota'] != session()->get('id_anggota')) {
        return redirect()->back()->with('error', 'Akses ditolak!');
      }
    }

    $kegiatanModel = new KegiatanModel();

    // Ambil data kegiatan dengan join anggota
    $kegiatan = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota, anggota.alamat')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.id_kegiatan', $id_kegiatan)
      ->first();

    if (!$kegiatan) {
      return redirect()->back()->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Data untuk undangan
    $data = [
      'kegiatan' => $kegiatan,
      'nomor_undangan' => $this->generateNomorUndangan($kegiatan),
      'tanggal_undangan' => date('Y-m-d'),
      'tempat_kegiatan' => $kegiatan['alamat'] ?? 'Sekretariat Pemuda Bitung'
    ];

    return view('undangan/template', $data);
  }

  public function generate($id_kegiatan)
  {
    // Sementara redirect ke preview dulu
    // Nanti bisa ditambahkan Dompdf setelah diinstall
    return redirect()->to("/undangan/preview/$id_kegiatan");
  }

  private function generateNomorUndangan($kegiatan)
  {
    $tahun = date('Y', strtotime($kegiatan['tanggal_kegiatan']));
    $bulan = date('m', strtotime($kegiatan['tanggal_kegiatan']));

    return "225/PB-Bitung/XII/{$tahun}";
  }
}
