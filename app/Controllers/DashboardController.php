<?php
// filepath: app/Controllers/DashboardController.php

namespace App\Controllers;

use App\Models\KegiatanModel;
use App\Models\AnggotaModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
  public function index()
  {
    // Redirect based on role
    $role = session()->get('role');

    if ($role === 'admin') {
      return redirect()->to('/dashboard/admin');
    } else {
      return redirect()->to('/dashboard/anggota');
    }
  }

  public function admin()
  {
    // Pastikan hanya admin yang bisa akses
    if (session()->get('role') !== 'admin') {
      return redirect()->back()->with('error', 'Akses ditolak! Hanya admin yang diizinkan.');
    }

    $kegiatanModel = new KegiatanModel();
    $anggotaModel = new AnggotaModel();
    $userModel = new UserModel();

    // 1. TOTAL KEGIATAN - Hitung semua kegiatan
    $totalKegiatan = $kegiatanModel->countAllResults();

    // 2. TOTAL ANGGOTA - Hitung semua anggota
    $totalAnggota = $anggotaModel->countAllResults();

    // 3. TOTAL UANG - Hitung semua uang dari kegiatan_detail
    $totalUangQuery = $kegiatanModel->db->query(
      "SELECT COALESCE(SUM(kd.jumlah), 0) as total_uang 
             FROM kegiatan_detail kd"
    );
    $totalUang = $totalUangQuery->getRow()->total_uang ?? 0;

    // 4. KEGIATAN BULAN INI
    $kegiatanBulanIni = $kegiatanModel
      ->where('MONTH(tanggal_kegiatan)', date('m'))
      ->where('YEAR(tanggal_kegiatan)', date('Y'))
      ->countAllResults();

    // 5. KEGIATAN AKAN DATANG (dari hari ini ke depan)
    $kegiatanAkanDatang = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota, 
                     DATEDIFF(kegiatan.tanggal_kegiatan, CURDATE()) as days_remaining')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.tanggal_kegiatan >=', date('Y-m-d'))
      ->orderBy('kegiatan.tanggal_kegiatan', 'ASC')
      ->limit(5)
      ->findAll();

    // 6. KEGIATAN TERBARU (5 terakhir berdasarkan created_at)
    $kegiatanTerbaru = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->orderBy('kegiatan.created_at', 'DESC')
      ->limit(5)
      ->findAll();

    // 7. ANGGOTA PALING AKTIF (berdasarkan jumlah kegiatan yang dibuat)
    $anggotaAktif = $anggotaModel
      ->select('anggota.*, COUNT(kegiatan.id_kegiatan) as total_kegiatan_dibuat')
      ->join('kegiatan', 'kegiatan.id_anggota = anggota.id_anggota', 'left')
      ->groupBy('anggota.id_anggota')
      ->orderBy('total_kegiatan_dibuat', 'DESC')
      ->limit(5)
      ->findAll();

    // 8. ANGGOTA PALING ROYAL (berdasarkan total kongan yang diberikan)
    $anggotaRoyal = $kegiatanModel->db->query(
      "SELECT a.nama_anggota, a.alamat, COALESCE(SUM(kd.jumlah), 0) as total_kongan
             FROM anggota a 
             LEFT JOIN kegiatan_detail kd ON kd.id_anggota = a.id_anggota
             GROUP BY a.id_anggota, a.nama_anggota, a.alamat
             ORDER BY total_kongan DESC
             LIMIT 5"
    )->getResultArray();

    // 9. STATISTIK BULANAN (6 bulan terakhir)
    $statistikBulanan = $kegiatanModel->db->query(
      "SELECT 
                DATE_FORMAT(k.tanggal_kegiatan, '%Y-%m') as bulan,
                COUNT(k.id_kegiatan) as total_kegiatan,
                COALESCE(SUM(kd.jumlah), 0) as total_uang
             FROM kegiatan k
             LEFT JOIN kegiatan_detail kd ON kd.id_kegiatan = k.id_kegiatan
             WHERE k.tanggal_kegiatan >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(k.tanggal_kegiatan, '%Y-%m')
             ORDER BY bulan DESC"
    )->getResultArray();

    // 10. KEGIATAN HARI INI
    $kegiatanHariIni = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.tanggal_kegiatan', date('Y-m-d'))
      ->findAll();

    // Ambil semua users dengan data anggota
    $users = $userModel
      ->select('users.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = users.id_anggota', 'left')
      ->orderBy('users.role DESC, users.username ASC')
      ->findAll();

    // Statistik users - HAPUS QUERY STATUS
    $totalAdmin = $userModel->where('role', 'admin')->countAllResults();
    $totalUserAnggota = $userModel->where('role', 'anggota')->countAllResults();
    // HAPUS: $totalUserAktif = $userModel->where('status', 'aktif')->countAllResults();

    $data = [
      'title' => 'Dashboard Admin',
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota') ?? 'Super Admin',
      'role' => session()->get('role'),

      // STATISTIK UTAMA
      'total_kegiatan' => $totalKegiatan,
      'total_anggota' => $totalAnggota,
      'total_uang' => $totalUang,
      'kegiatan_bulan_ini' => $kegiatanBulanIni,

      // DATA KEGIATAN
      'kegiatan_akan_datang' => $kegiatanAkanDatang,
      'kegiatan_terbaru' => $kegiatanTerbaru,
      'kegiatan_hari_ini' => $kegiatanHariIni,

      // DATA ANGGOTA
      'anggota_aktif' => $anggotaAktif,
      'anggota_royal' => $anggotaRoyal,

      // STATISTIK LANJUTAN
      'statistik_bulanan' => $statistikBulanan,

      // TAMBAHKAN DATA USERS
      'users' => $users,
      'total_admin' => $totalAdmin,
      'total_user_anggota' => $totalUserAnggota,
      'total_users' => count($users) // Ganti total_user_aktif dengan total_users
    ];

    return view('dashboard/admin', $data);
  }

  public function anggota()
  {
    // Pastikan user sudah login
    if (!session()->get('logged_in') || session()->get('role') !== 'anggota') {
      return redirect()->to('/login')->with('error', 'Silakan login sebagai anggota');
    }

    // Ambil history kongan yang diikuti user
    $idAnggota = session()->get('id_anggota');
    $db = \Config\Database::connect();
    $historyKongan = $db->table('kegiatan_detail kd')
        ->select('k.nama_kegiatan, k.tanggal_kegiatan, kd.jumlah, a.nama_anggota as pemilik_kegiatan')
        ->join('kegiatan k', 'k.id_kegiatan = kd.id_kegiatan')
        ->join('anggota a', 'a.id_anggota = k.id_anggota')
        ->where('kd.id_anggota', $idAnggota)
        ->orderBy('k.tanggal_kegiatan', 'DESC')
        ->get()
        ->getResultArray();

    $data['history_kongan'] = $historyKongan;
    
    $kegiatanModel = new KegiatanModel();
    $idAnggota = session()->get('id_anggota');

    // STATISTIK UNTUK ANGGOTA

    // 1. Total kegiatan di sistem
    $totalKegiatan = $kegiatanModel->countAllResults();

    // 2. Kegiatan yang dibuat oleh anggota ini
    $kegiatanSayaList = $kegiatanModel
    ->select('kegiatan.*, anggota.nama_anggota, 
              COUNT(kegiatan_detail.id_detail_kegiatan) as total_peserta,
              COALESCE(SUM(kegiatan_detail.jumlah), 0) as total_kongan')
    ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
    ->join('kegiatan_detail', 'kegiatan_detail.id_kegiatan = kegiatan.id_kegiatan', 'left')
    ->where('kegiatan.id_anggota', $idAnggota)
    ->groupBy('kegiatan.id_kegiatan')
    ->orderBy('kegiatan.tanggal_kegiatan', 'DESC')
    ->findAll();
    // \dd($kegiatanSayaList);

      // Hitung total kongan yang didapatkan dari semua kegiatan yang dikelola user
      $totalKonganDikelola = 0;
      foreach ($kegiatanSayaList as $kegiatan) {
          $totalKonganDikelola += (int)($kegiatan['total_kongan'] ?? 0);
      }

      // HITUNG TOTAL KONGAN DILIHAT DARI KEGIATAN YANG DIIKUTI
      $totalKonganDikelolaBersih = 0;
      foreach ($kegiatanSayaList as $kegiatan) {
          // Ambil total kongan (sudah didapat dari query: $kegiatan['total_kongan'])
          $total_kongan = (int)($kegiatan['total_kongan'] ?? 0);

          // 10% operasional
          $sepuluh_persen = $total_kongan * 0.10;

          // Potongan undangan
          $potongan_undangan = (int)($kegiatan['potongan_undangan_amount'] ?? 0);

          // Potongan tidak ikut
          $potTidakIkut = (int)($kegiatan['potongan_tidak_ikut_amount'] ?? 0);

          // Hitung total kegiatan di sistem
          $totalKegiatan = $kegiatanModel->countAllResults();

          // Hitung kegiatan yang diikuti oleh pemilik kegiatan ini
          $kegiatanDiikuti = $kegiatanModel->db->table('kegiatan_detail')
              ->where('id_anggota', $kegiatan['id_anggota'])
              ->distinct()
              ->select('id_kegiatan')
              ->countAllResults();

          // Hitung jumlah tidak ikut
          $jumlahTidakIkut = $totalKegiatan - $kegiatanDiikuti;
          $totalPotTidakIkut = $jumlahTidakIkut * $potTidakIkut;

          // Hitung total bersih
          $total_bersih = $total_kongan - $sepuluh_persen - $potongan_undangan - $totalPotTidakIkut;

          $totalBersih = $total_bersih;
      }

      $data['kegiatan_saya_list'] = $kegiatanSayaList;
      $data['totalKonganDikelola'] = $totalKonganDikelola;
      $data['totalBersih'] = $totalBersih;

    // 3. Total kongan yang diberikan oleh anggota ini
    $totalKonganQuery = $kegiatanModel->db->query(
      "SELECT COALESCE(SUM(jumlah), 0) as total_kongan
             FROM kegiatan_detail 
             WHERE id_anggota = ?",
      [$idAnggota]
    );
    $totalUangKongan = $totalKonganQuery->getRow()->total_kongan ?? 0;

    // 4. Kegiatan yang diikuti (memberikan kongan)
    $kegiatanDiikuti = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota as penyelenggara, kegiatan_detail.jumlah')
      ->join('kegiatan_detail', 'kegiatan_detail.id_kegiatan = kegiatan.id_kegiatan')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan_detail.id_anggota', $idAnggota)
      ->orderBy('kegiatan.tanggal_kegiatan', 'DESC')
      ->findAll();

    // 5. Kegiatan terbaru (semua kegiatan)
    $kegiatanTerbaru = $kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->orderBy('kegiatan.created_at', 'DESC')
      ->limit(8)
      ->findAll();

    // Hitung statistik
    $kegiatanSaya = count($kegiatanSayaList);
    $totalKonganSaya = count($kegiatanDiikuti);

    $data = [
      'title' => 'Dashboard Anggota',
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota'),
      'role' => session()->get('role'),

      // STATISTIK ANGGOTA
      'total_kegiatan' => $totalKegiatan,
      'kegiatan_saya' => $kegiatanSaya,
      'total_kongan_saya' => $totalKonganSaya,
      'total_uang_kongan' => $totalUangKongan,

      // DATA KEGIATAN
      'kegiatan_saya_list' => $kegiatanSayaList,
      'kegiatan_diikuti' => $kegiatanDiikuti,
      'kegiatan_terbaru' => $kegiatanTerbaru,
      'history_kongan' => $historyKongan,

      // DATA KONGAN DIKELOLA
      'totalBersih' => $totalBersih
    ];

    return view('dashboard/anggota', $data);
  }

  // API untuk mendapatkan statistik real-time (AJAX)
  public function getStats()
  {
    if (session()->get('role') !== 'admin') {
      return $this->response->setJSON(['error' => 'Unauthorized']);
    }

    $kegiatanModel = new KegiatanModel();
    $anggotaModel = new AnggotaModel();

    $totalKegiatan = $kegiatanModel->countAllResults();
    $totalAnggota = $anggotaModel->countAllResults();

    $totalUangQuery = $kegiatanModel->db->query(
      "SELECT COALESCE(SUM(kd.jumlah), 0) as total_uang FROM kegiatan_detail kd"
    );
    $totalUang = $totalUangQuery->getRow()->total_uang ?? 0;

    $kegiatanBulanIni = $kegiatanModel
      ->where('MONTH(tanggal_kegiatan)', date('m'))
      ->where('YEAR(tanggal_kegiatan)', date('Y'))
      ->countAllResults();

    return $this->response->setJSON([
      'total_kegiatan' => $totalKegiatan,
      'total_anggota' => $totalAnggota,
      'total_uang' => $totalUang,
      'kegiatan_bulan_ini' => $kegiatanBulanIni
    ]);
  }
}