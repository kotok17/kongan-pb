<?php

namespace App\Controllers;

use App\Models\KegiatanModel;
use App\Models\AnggotaModel;

class KegiatanController extends BaseController
{
  protected $kegiatanModel;
  protected $anggotaModel;

  public function __construct()
  {
    $this->kegiatanModel = new KegiatanModel();
    $this->anggotaModel = new AnggotaModel();
    $this->db = \Config\Database::connect();
  }

  public function index()
  {
    // TAMBAHKAN PENGECEKAN SESSION DI AWAL
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')
        ->with('error', 'Session expired, silakan login ulang');
    }

    // Cek role untuk admin
    $role = session()->get('role');
    $isAdmin = ($role === 'admin');
    $idAnggota = session()->get('id_anggota');

    // Debug session data (hapus setelah testing)
    // log_message('debug', 'User role: ' . $role . ', ID Anggota: ' . $idAnggota);

    if ($isAdmin) {
      // Admin bisa lihat semua kegiatan
      $kegiatan = $this->kegiatanModel
        ->select('kegiatan.*, anggota.nama_anggota, 
                         COUNT(kegiatan_detail.id_detail_kegiatan) as total_peserta,
                         COALESCE(SUM(kegiatan_detail.jumlah), 0) as total_uang')
        ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
        ->join('kegiatan_detail', 'kegiatan_detail.id_kegiatan = kegiatan.id_kegiatan', 'left')
        ->groupBy('kegiatan.id_kegiatan')
        ->orderBy('kegiatan.tanggal_kegiatan', 'DESC')
        ->findAll();
    } else {
      // Pastikan anggota punya id_anggota
      if (empty($idAnggota)) {
        session()->setFlashdata('error', 'Data anggota tidak ditemukan, silakan login ulang');
        return redirect()->to('/logout');
      }

      // Anggota hanya lihat kegiatan miliknya
      $kegiatan = $this->kegiatanModel
        ->select('kegiatan.*, anggota.nama_anggota, 
                         COUNT(kegiatan_detail.id_detail_kegiatan) as total_peserta,
                         COALESCE(SUM(kegiatan_detail.jumlah), 0) as total_uang')
        ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
        ->join('kegiatan_detail', 'kegiatan_detail.id_kegiatan = kegiatan.id_kegiatan', 'left')
        ->where('kegiatan.id_anggota', $idAnggota)
        ->groupBy('kegiatan.id_kegiatan')
        ->orderBy('kegiatan.tanggal_kegiatan', 'DESC')
        ->findAll();
    }

    // Ambil semua anggota untuk dropdown (hanya untuk admin)
    $anggota = [];
    if ($isAdmin) {
      $anggota = $this->anggotaModel->findAll();
    }

    $data = [
      'title' => 'Kelola Kegiatan',
      'kegiatan' => $kegiatan,
      'anggota' => $anggota,
      'is_admin' => $isAdmin,
      'role' => $role,
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota')
    ];

    return view('kegiatan/index', $data);
  }

  // TAMBAHKAN METHOD INI UNTUK DEBUG
  public function debug_session()
  {
    // Method untuk debug session (hapus di production)
    $sessionData = [
      'logged_in' => session()->get('logged_in'),
      'role' => session()->get('role'),
      'username' => session()->get('username'),
      'id_anggota' => session()->get('id_anggota'),
      'nama_anggota' => session()->get('nama_anggota'),
      'all_session' => session()->get()
    ];

    return $this->response->setJSON($sessionData);
  }

  public function tambah_kegiatan()
  {
    $anggota = $this->anggotaModel->findAll();

    $data = [
      'title' => 'Tambah Kegiatan',
      'anggota' => $anggota,
      'is_admin' => (session()->get('role') === 'admin'),
      'role' => session()->get('role'),
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota')
    ];

    return view('kegiatan/index', $data);
  }

  public function simpan()
  {
    $validation = \Config\Services::validation();

    $rules = [
      'nama_kegiatan' => 'required|min_length[3]|max_length[100]',
      'tanggal_kegiatan' => 'required|valid_date',
      'deskripsi' => 'permit_empty|max_length[500]'
    ];

    // Admin bisa pilih anggota, user biasa otomatis pakai id_anggota dari session
    if (session()->get('role') === 'admin') {
      $rules['id_anggota'] = 'required|integer';
      $idAnggota = $this->request->getPost('id_anggota');
    } else {
      $idAnggota = session()->get('id_anggota');
    }

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    $data = [
      'id_anggota' => $idAnggota,
      'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
      'tanggal_kegiatan' => $this->request->getPost('tanggal_kegiatan'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($this->kegiatanModel->insert($data)) {
      return redirect()->to('/kegiatan')
        ->with('success', 'Kegiatan berhasil ditambahkan!');
    } else {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Gagal menambahkan kegiatan!');
    }
  }

  public function detail($id)
  {
    // Ambil single row kegiatan (bukan array of arrays)
    $kegiatan = $this->kegiatanModel
      ->select('kegiatan.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.id_kegiatan', $id)
      ->first(); // Ini mengembalikan single row, bukan array of rows

    // Debug untuk memastikan struktur data
    // var_dump($kegiatan); die(); // Uncomment untuk debug

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Cek akses - admin bisa lihat semua, anggota hanya miliknya
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Anda tidak memiliki akses ke kegiatan ini!');
    }

    // Ambil detail kongan dengan pengecekan
    $db = \Config\Database::connect();
    $kongan = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id)
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->get()
      ->getResultArray();

    // Pastikan $kongan adalah array
    if (!is_array($kongan)) {
      $kongan = [];
    }

    // Statistik dengan pengecekan
    $totalPeserta = count($kongan);
    $totalUang = !empty($kongan) ? array_sum(array_column($kongan, 'jumlah')) : 0;

    // Ambil semua anggota
    $anggota = $this->anggotaModel->findAll();
    if (!is_array($anggota)) {
      $anggota = [];
    }

    $data = [
      'title' => 'Detail Kegiatan',
      'kegiatan' => $kegiatan, // Single array, bukan array of arrays
      'kongan' => $kongan,
      'total_peserta' => $totalPeserta,
      'total_uang' => $totalUang,
      'is_admin' => (session()->get('role') === 'admin'),
      'role' => session()->get('role'),
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota'),
      'anggota' => $anggota
    ];

    return view('kegiatan/detail', $data);
  }

  public function edit($id)
  {
    $kegiatan = $this->kegiatanModel->find($id);

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Cek akses - admin bisa edit semua, anggota hanya miliknya
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Anda tidak memiliki akses untuk mengedit kegiatan ini!');
    }

    $anggota = $this->anggotaModel->findAll();

    $data = [
      'title' => 'Edit Kegiatan',
      'kegiatan' => $kegiatan,
      'anggota' => $anggota,
      'is_admin' => (session()->get('role') === 'admin'),
      'role' => session()->get('role'),
      'username' => session()->get('username'),
      'nama_anggota' => session()->get('nama_anggota')
    ];

    return view('kegiatan/edit', $data);
  }

  public function update($id)
  {
    $kegiatan = $this->kegiatanModel->find($id);

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Cek akses
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Anda tidak memiliki akses untuk mengedit kegiatan ini!');
    }

    $rules = [
      'nama_kegiatan' => 'required|min_length[3]|max_length[100]',
      'tanggal_kegiatan' => 'required|valid_date',
      'deskripsi' => 'permit_empty|max_length[500]'
    ];

    // Admin bisa ubah anggota, user biasa tidak bisa
    if (session()->get('role') === 'admin') {
      $rules['id_anggota'] = 'required|integer';
      $idAnggota = $this->request->getPost('id_anggota');
    } else {
      $idAnggota = $kegiatan['id_anggota']; // Tetap pakai yang lama
    }

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    $data = [
      'id_anggota' => $idAnggota,
      'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
      'tanggal_kegiatan' => $this->request->getPost('tanggal_kegiatan'),
      'deskripsi' => $this->request->getPost('deskripsi'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($this->kegiatanModel->update($id, $data)) {
      return redirect()->to('/kegiatan/detail/' . $id)
        ->with('success', 'Kegiatan berhasil diperbarui!');
    } else {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Gagal memperbarui kegiatan!');
    }
  }

  public function hapus($id)
  {
    $kegiatan = $this->kegiatanModel->find($id);

    if (!$kegiatan) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Kegiatan tidak ditemukan!'
      ]);
    }

    // Cek akses - admin bisa hapus semua, anggota hanya miliknya
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Anda tidak memiliki akses untuk menghapus kegiatan ini!'
      ]);
    }

    $db = \Config\Database::connect();

    // Hapus detail kegiatan dulu
    $db->table('kegiatan_detail')->where('id_kegiatan', $id)->delete();

    // Kemudian hapus kegiatan
    if ($this->kegiatanModel->delete($id)) {
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Kegiatan berhasil dihapus!'
      ]);
    } else {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menghapus kegiatan!'
      ]);
    }
  }

  public function tambah_kongan()
  {
    $rules = [
      'id_kegiatan' => 'required|integer',
      'id_anggota' => 'required|integer',
      'jumlah' => 'required|integer|greater_than[0]'
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()
        ->withInput()
        ->with('errors', $this->validator->getErrors());
    }

    $idKegiatan = $this->request->getPost('id_kegiatan');
    $idAnggota = $this->request->getPost('id_anggota');
    $jumlah = $this->request->getPost('jumlah');

    // Cek apakah kegiatan ada
    $kegiatan = $this->kegiatanModel->find($idKegiatan);
    if (!$kegiatan) {
      return redirect()->back()
        ->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Cek akses
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->back()
        ->with('error', 'Anda tidak memiliki akses untuk menambah kongan!');
    }

    // Cek apakah anggota sudah ada di kegiatan ini
    $db = \Config\Database::connect();
    $existing = $db->table('kegiatan_detail')
      ->where('id_kegiatan', $idKegiatan)
      ->where('id_anggota', $idAnggota)
      ->get()
      ->getRowArray();

    if ($existing) {
      return redirect()->back()
        ->with('error', 'Anggota sudah memberikan kongan pada kegiatan ini!');
    }

    $data = [
      'id_kegiatan' => $idKegiatan,
      'id_anggota' => $idAnggota,
      'jumlah' => $jumlah,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($db->table('kegiatan_detail')->insert($data)) {
      // Ambil nama anggota untuk pesan
      $anggota = $this->anggotaModel->find($idAnggota);

      return redirect()->to('/kegiatan/detail/' . $idKegiatan)
        ->with('success', 'Kongan dari ' . $anggota['nama_anggota'] . ' sebesar Rp ' . number_format($jumlah) . ' berhasil ditambahkan!');
    } else {
      return redirect()->back()
        ->with('error', 'Gagal menambahkan kongan!');
    }
  }

  public function hapus_kongan($id)
  {
    // Debug log untuk melihat apa yang terjadi
    log_message('info', 'Hapus kongan called with ID: ' . $id);
    log_message('info', 'User role: ' . session()->get('role'));
    log_message('info', 'Request method: ' . $this->request->getMethod());

    // Pastikan request method benar
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request type'
      ]);
    }

    try {
      // Cek apakah user memiliki akses
      $role = session()->get('role');
      if ($role !== 'admin' && $role !== 'superadmin') {
        // Jika bukan admin/superadmin, cek kepemilikan
        $detailKongan = $this->db->table('kegiatan_detail')
          ->select('kegiatan_detail.*, kegiatan.id_anggota')
          ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
          ->where('kegiatan_detail.id_detail_kegiatan', $id)
          ->get()
          ->getRowArray();

        if (!$detailKongan || $detailKongan['id_anggota'] != session()->get('id_anggota')) {
          return $this->response->setJSON([
            'success' => false,
            'message' => 'Anda tidak memiliki akses untuk menghapus data ini!'
          ]);
        }
      }

      // Ambil data detail kongan untuk log
      $konganDetail = $this->db->table('kegiatan_detail')
        ->select('kegiatan_detail.*, anggota.nama_anggota, kegiatan.nama_kegiatan')
        ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
        ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
        ->where('kegiatan_detail.id_detail_kegiatan', $id)
        ->get()
        ->getRowArray();

      if (!$konganDetail) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Data kongan tidak ditemukan!'
        ]);
      }

      // Hapus data
      $deleted = $this->db->table('kegiatan_detail')
        ->where('id_detail_kegiatan', $id)
        ->delete();

      if ($deleted) {
        // Log aktivitas
        log_message('info', 'Kongan deleted: ' . json_encode([
          'id_detail' => $id,
          'nama_anggota' => $konganDetail['nama_anggota'],
          'jumlah' => $konganDetail['jumlah'],
          'nama_kegiatan' => $konganDetail['nama_kegiatan'],
          'deleted_by' => session()->get('username')
        ]));

        return $this->response->setJSON([
          'success' => true,
          'message' => 'Kongan berhasil dihapus!',
          'data' => [
            'nama_anggota' => $konganDetail['nama_anggota'],
            'jumlah' => $konganDetail['jumlah']
          ]
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menghapus data kongan!'
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Error hapus kongan: ' . $e->getMessage());
      log_message('error', 'Error trace: ' . $e->getTraceAsString());

      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }
}
