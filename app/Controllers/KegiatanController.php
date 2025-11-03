<?php

namespace App\Controllers;

use App\Models\KegiatanModel;
use App\Models\AnggotaModel;
use CodeIgniter\Controller;
use App\Models\DetailKegiatanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

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
      ->first();

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Cek akses - admin bisa lihat semua, anggota hanya miliknya
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->to('/kegiatan')
        ->with('error', 'Anda tidak memiliki akses ke kegiatan ini!');
    }

    // Ambil detail kongan dengan pengecekan - DIURUTKAN BERDASARKAN NAMA ANGGOTA
    $db = \Config\Database::connect();
    $kongan = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id)
      ->orderBy('anggota.nama_anggota', 'ASC') // DIURUTKAN BERDASARKAN NAMA
      ->get()
      ->getResultArray();

    // Pastikan $kongan adalah array
    if (!is_array($kongan)) {
      $kongan = [];
    }

    // Statistik dengan pengecekan
    $totalPeserta = count($kongan);
    $totalUang = !empty($kongan) ? array_sum(array_column($kongan, 'jumlah')) : 0;

    // Ambil aktivitas anggota untuk menghitung bonus/potongan
    $aktivitas_anggota = [];
    if (isset($kegiatan['id_anggota'])) {
      $aktivitas_anggota = $db->table('kegiatan_detail')
        ->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
        ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
        ->where('kegiatan_detail.id_anggota', $kegiatan['id_anggota'])
        ->where('kegiatan.id_anggota !=', $kegiatan['id_anggota'])
        ->orderBy('kegiatan_detail.created_at', 'DESC')
        ->get()
        ->getResultArray();
    }

    // Ambil semua anggota - DIURUTKAN BERDASARKAN NAMA
    $anggota = $this->anggotaModel
      ->orderBy('nama_anggota', 'ASC') // DIURUTKAN BERDASARKAN NAMA
      ->findAll();

    if (!is_array($anggota)) {
      $anggota = [];
    }

    $data = [
      'title' => 'Detail Kegiatan',
      'kegiatan' => $kegiatan,
      'kongan' => $kongan,
      'total_peserta' => $totalPeserta,
      'total_uang' => $totalUang,
      'aktivitas_anggota' => $aktivitas_anggota,
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
    // Cek AJAX request
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request type'
      ]);
    }

    // Cek apakah user admin
    if (session()->get('role') !== 'admin') {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Akses ditolak! Hanya admin yang bisa menghapus kegiatan.'
      ]);
    }

    try {
      // Cek apakah kegiatan ada
      $kegiatan = $this->kegiatanModel->find($id);

      if (!$kegiatan) {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Kegiatan tidak ditemukan!'
        ]);
      }

      // VALIDASI: CEK APAKAH ADA DATA KONGAN
      $db = \Config\Database::connect();
      $detailKongan = $db->table('kegiatan_detail')
        ->where('id_kegiatan', $id)
        ->countAllResults();

      if ($detailKongan > 0) {
        return $this->response->setJSON([
          'success' => false,
          'message' => "Kegiatan tidak dapat dihapus!<br>Terdapat <strong>{$detailKongan} data kongan</strong> yang terkait.<br><small class='text-muted'>Hapus semua data kongan terlebih dahulu.</small>",
          'has_data' => true,
          'total_kongan' => $detailKongan
        ]);
      }

      // Hapus kegiatan (data kongan sudah kosong)
      if ($this->kegiatanModel->delete($id)) {
        return $this->response->setJSON([
          'success' => true,
          'message' => 'Kegiatan berhasil dihapus!'
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menghapus kegiatan dari database'
        ]);
      }
    } catch (\Exception $e) {
      log_message('error', 'Error deleting kegiatan: ' . $e->getMessage());
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
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

  public function update_pengaturan($id_kegiatan)
  {
    if (!$this->validate_access($id_kegiatan)) {
      return redirect()->back()->with('error', 'Akses ditolak');
    }

    $mode = $this->request->getPost('potongan_tidak_ikut_mode');
    $amountTidakIkut = (int) preg_replace('/\D/', '', (string) $this->request->getPost('potongan_tidak_ikut_amount'));
    $amountUndangan  = (int) preg_replace('/\D/', '', (string) $this->request->getPost('potongan_undangan_amount'));

    if (!in_array($mode, ['activity_based', 'always', 'none'])) {
      $mode = 'activity_based';
    }
    if ($amountTidakIkut < 0) $amountTidakIkut = 0;
    if ($amountUndangan < 0) $amountUndangan = 0;

    try {
      $updated = $this->kegiatanModel->update($id_kegiatan, [
        'potongan_tidak_ikut_mode'   => $mode,
        'potongan_tidak_ikut_amount' => $amountTidakIkut,
        'potongan_undangan_amount'   => $amountUndangan,
      ]);

      if ($updated) {
        return redirect()->to('/kegiatan/detail/' . $id_kegiatan)
          ->with('success', 'Pengaturan potongan berhasil disimpan');
      } else {
        return redirect()->back()
          ->with('error', 'Gagal menyimpan pengaturan. Tidak ada perubahan data.');
      }
    } catch (\Exception $e) {
      log_message('error', 'Error update pengaturan: ' . $e->getMessage());
      return redirect()->back()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  public function export_pdf($id_kegiatan)
  {
    // Validasi akses
    if (!$this->validate_access($id_kegiatan)) {
      return redirect()->to('/kegiatan')->with('error', 'Akses ditolak');
    }

    // Gunakan query builder langsung seperti di method detail()
    $db = \Config\Database::connect();

    // Ambil data kegiatan dengan cara yang sama seperti method detail()
    $kegiatan = $db->table('kegiatan')
      ->select('kegiatan.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.id_kegiatan', $id_kegiatan)
      ->get()
      ->getRowArray();

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')->with('error', 'Kegiatan tidak ditemukan');
    }

    // Debug: Log data kegiatan
    log_message('debug', 'Data kegiatan untuk export: ' . json_encode($kegiatan));

    // Pastikan id_anggota ada
    if (!isset($kegiatan['id_anggota']) || empty($kegiatan['id_anggota'])) {
      log_message('error', 'ID Anggota tidak ditemukan dalam data kegiatan');
      return redirect()->to('/kegiatan')->with('error', 'Data kegiatan tidak lengkap - ID Anggota tidak ada');
    }

    // Ambil data kongan - DIURUTKAN BERDASARKAN NAMA ANGGOTA
    $kongan = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id_kegiatan)
      ->orderBy('anggota.nama_anggota', 'ASC') // DIURUTKAN BERDASARKAN NAMA A-Z
      ->get()
      ->getResultArray();

    // Pastikan kongan adalah array
    if (!is_array($kongan)) {
      $kongan = [];
    }

    // Hitung total dan ringkasan dengan pengecekan
    $total_kongan = !empty($kongan) ? array_sum(array_column($kongan, 'jumlah')) : 0;
    $sepuluh_persen = $total_kongan * 0.1;
    $potongan_undangan = 280000;

    // Cek aktivitas anggota
    $aktivitas_anggota = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $kegiatan['id_anggota'])
      ->where('kegiatan.id_anggota !=', $kegiatan['id_anggota'])
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->get()
      ->getResultArray();

    if (!is_array($aktivitas_anggota)) {
      $aktivitas_anggota = [];
    }

    $anggota_aktif_di_kegiatan_lain = !empty($aktivitas_anggota);
    $jumlah_kegiatan_ikut = count($aktivitas_anggota);
    $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : 20000;
    $total_bersih = $total_kongan - $sepuluh_persen - $potongan_undangan - $potongan_tidak_nulis;

    // ambil pengaturan potongan dari kegiatan
    $potMode = $kegiatan['potongan_tidak_ikut_mode'] ?? 'activity_based';
    $potTidakIkut = (int)($kegiatan['potongan_tidak_ikut_amount'] ?? 20000);
    $potUndangan  = (int)($kegiatan['potongan_undangan_amount'] ?? 280000);

    // Hitung potongan tidak ikut sesuai mode
    switch ($potMode) {
      case 'none':
        $potongan_tidak_nulis = 0;
        break;
      case 'always':
        $potongan_tidak_nulis = $potTidakIkut;
        break;
      case 'activity_based':
      default:
        $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : $potTidakIkut;
        break;
    }

    $total_bersih = $total_kongan - $sepuluh_persen - $potUndangan - $potongan_tidak_nulis;

    // kirim variabel baru ke view export
    $html = view('kegiatan/export_pdf', [
      'kegiatan' => $kegiatan,
      'kongan' => $kongan,
      'total_kongan' => $total_kongan,
      'sepuluh_persen' => $sepuluh_persen,
      'potongan_undangan' => $potUndangan,
      'potongan_tidak_nulis' => $potongan_tidak_nulis,
      'potongan_mode' => $potMode,
      'total_bersih' => $total_bersih,
      'anggota_aktif_di_kegiatan_lain' => $anggota_aktif_di_kegiatan_lain,
      'jumlah_kegiatan_ikut' => $jumlah_kegiatan_ikut
    ]);

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'Hasil_Kongan_' . str_replace(' ', '_', $kegiatan['nama_kegiatan']) . '_' . date('Y-m-d') . '.pdf';

    return $this->response
      ->setHeader('Content-Type', 'application/pdf')
      ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
      ->setBody($dompdf->output());
  }

  public function export_excel($id_kegiatan)
  {
    // Validasi akses
    if (!$this->validate_access($id_kegiatan)) {
      return redirect()->to('/kegiatan')->with('error', 'Akses ditolak');
    }

    // Gunakan query builder langsung seperti di method detail()
    $db = \Config\Database::connect();

    // Ambil data kegiatan dengan cara yang sama seperti method detail()
    $kegiatan = $db->table('kegiatan')
      ->select('kegiatan.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.id_kegiatan', $id_kegiatan)
      ->get()
      ->getRowArray();

    if (!$kegiatan) {
      return redirect()->to('/kegiatan')->with('error', 'Kegiatan tidak ditemukan');
    }

    // Debug: Log data kegiatan
    log_message('debug', 'Data kegiatan untuk export: ' . json_encode($kegiatan));

    // Pastikan id_anggota ada
    if (!isset($kegiatan['id_anggota']) || empty($kegiatan['id_anggota'])) {
      log_message('error', 'ID Anggota tidak ditemukan dalam data kegiatan');
      return redirect()->to('/kegiatan')->with('error', 'Data kegiatan tidak lengkap - ID Anggota tidak ada');
    }

    // Ambil data kongan - DIURUTKAN BERDASARKAN NAMA ANGGOTA
    $kongan = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id_kegiatan)
      ->orderBy('anggota.nama_anggota', 'ASC') // DIURUTKAN BERDASARKAN NAMA A-Z
      ->get()
      ->getResultArray();

    // Pastikan kongan adalah array
    if (!is_array($kongan)) {
      $kongan = [];
    }

    // Hitung total dan ringkasan dengan pengecekan
    $total_kongan = !empty($kongan) ? array_sum(array_column($kongan, 'jumlah')) : 0;
    $sepuluh_persen = $total_kongan * 0.1;
    $potongan_undangan = 280000;

    // Cek aktivitas anggota
    $aktivitas_anggota = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $kegiatan['id_anggota'])
      ->where('kegiatan.id_anggota !=', $kegiatan['id_anggota'])
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->get()
      ->getResultArray();

    if (!is_array($aktivitas_anggota)) {
      $aktivitas_anggota = [];
    }

    $anggota_aktif_di_kegiatan_lain = !empty($aktivitas_anggota);
    $jumlah_kegiatan_ikut = count($aktivitas_anggota);
    $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : 20000;
    $total_bersih = $total_kongan - $sepuluh_persen - $potongan_undangan - $potongan_tidak_nulis;

    // ambil pengaturan potongan dari kegiatan
    $potMode = $kegiatan['potongan_tidak_ikut_mode'] ?? 'activity_based';
    $potTidakIkut = (int)($kegiatan['potongan_tidak_ikut_amount'] ?? 20000);
    $potUndangan  = (int)($kegiatan['potongan_undangan_amount'] ?? 280000);

    // Hitung potongan tidak ikut sesuai mode
    switch ($potMode) {
      case 'none':
        $potongan_tidak_nulis = 0;
        break;
      case 'always':
        $potongan_tidak_nulis = $potTidakIkut;
        break;
      case 'activity_based':
      default:
        $potongan_tidak_nulis = $anggota_aktif_di_kegiatan_lain ? 0 : $potTidakIkut;
        break;
    }

    $total_bersih = $total_kongan - $sepuluh_persen - $potUndangan - $potongan_tidak_nulis;

    try {
      // Buat spreadsheet
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();

      // Header
      $sheet->setCellValue('A1', 'HASIL KONGAN KEGIATAN');
      $sheet->mergeCells('A1:D1');
      $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
      $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      // Info Kegiatan
      $sheet->setCellValue('A3', 'Nama Kegiatan:');
      $sheet->setCellValue('B3', $kegiatan['nama_kegiatan'] ?? 'N/A');
      $sheet->setCellValue('A4', 'Penyelenggara:');
      $sheet->setCellValue('B4', $kegiatan['nama_anggota'] ?? 'N/A');
      $sheet->setCellValue('A5', 'Tanggal:');
      $sheet->setCellValue('B5', isset($kegiatan['tanggal_kegiatan']) ? date('d/m/Y', strtotime($kegiatan['tanggal_kegiatan'])) : 'N/A');
      $sheet->setCellValue('A6', 'Total Peserta:');
      $sheet->setCellValue('B6', count($kongan));

      // Header tabel
      $sheet->setCellValue('A8', 'No');
      $sheet->setCellValue('B8', 'Nama Anggota');
      $sheet->setCellValue('C8', 'Jumlah Kongan');
      $sheet->setCellValue('D8', 'Keterangan');

      // Style header tabel
      $sheet->getStyle('A8:D8')->getFont()->setBold(true);
      $sheet->getStyle('A8:D8')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE0E0E0');
      $sheet->getStyle('A8:D8')->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

      // Data kongan (SUDAH TERURUT DARI QUERY)
      $row = 9;
      $no = 1;
      foreach ($kongan as $item) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $item['nama_anggota'] ?? 'N/A');
        $sheet->setCellValue('C' . $row, $item['jumlah'] ?? 0);
        $sheet->setCellValue('D' . $row, 'Rp ' . number_format($item['jumlah'] ?? 0, 0, ',', '.'));
        $row++;
      }

      // Border untuk data
      if ($row > 9) {
        $sheet->getStyle('A9:D' . ($row - 1))->getBorders()->getAllBorders()
          ->setBorderStyle(Border::BORDER_THIN);
      }

      // Summary
      $row += 2;
      $summaryStartRow = $row;

      $sheet->setCellValue('B' . $row, 'Total Kongan:');
      $sheet->setCellValue('D' . $row, 'Rp ' . number_format($total_kongan, 0, ',', '.'));
      $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

      $row++;
      $sheet->setCellValue('B' . $row, '10% Total Kongan:');
      $sheet->setCellValue('D' . $row, '- Rp ' . number_format($sepuluh_persen, 0, ',', '.'));

      $row++;
      if (!$anggota_aktif_di_kegiatan_lain) {
        $sheet->setCellValue('B' . $row, 'Potongan Tidak Nulis (0x ikut):');
        $sheet->setCellValue('D' . $row, '- Rp ' . number_format($potongan_tidak_nulis, 0, ',', '.'));
      } else {
        $sheet->setCellValue('B' . $row, 'Bonus Aktif (' . $jumlah_kegiatan_ikut . 'x ikut):');
        $sheet->setCellValue('D' . $row, '+ Rp 0');
      }

      $row++;
      $sheet->setCellValue('B' . $row, 'Potongan Undangan:');
      $sheet->setCellValue('D' . $row, '- Rp ' . number_format($potongan_undangan, 0, ',', '.'));

      $row++;
      $sheet->setCellValue('B' . $row, 'TOTAL BERSIH:');
      $sheet->setCellValue('D' . $row, 'Rp ' . number_format($total_bersih, 0, ',', '.'));
      $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true)->setSize(12);
      $sheet->getStyle('B' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFD4E6F1');

      // Border untuk summary
      $sheet->getStyle('B' . $summaryStartRow . ':D' . $row)->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

      // Auto width
      $sheet->getColumnDimension('A')->setAutoSize(true);
      $sheet->getColumnDimension('B')->setAutoSize(true);
      $sheet->getColumnDimension('C')->setAutoSize(true);
      $sheet->getColumnDimension('D')->setAutoSize(true);

      $filename = 'Hasil_Kongan_' . str_replace(' ', '_', $kegiatan['nama_kegiatan']) . '_' . date('Y-m-d') . '.xlsx';

      $writer = new Xlsx($spreadsheet);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $filename . '"');
      header('Cache-Control: max-age=0');

      $writer->save('php://output');
      exit();
    } catch (\Exception $e) {
      log_message('error', 'Error generating Excel: ' . $e->getMessage());
      return redirect()->to('/kegiatan')->with('error', 'Gagal generate Excel: ' . $e->getMessage());
    }
  }

  private function validate_access($id_kegiatan)
  {
    $role = session()->get('role');
    $id_anggota_session = session()->get('id_anggota');

    if ($role === 'admin') {
      return true;
    }

    if ($role === 'anggota') {
      $kegiatanModel = new KegiatanModel();
      $kegiatan = $kegiatanModel->find($id_kegiatan);

      if ($kegiatan && $kegiatan['id_anggota'] == $id_anggota_session) {
        return true;
      }
    }

    return false;
  }

  public function hapus_kongan($id)
  {
    // Pastikan request adalah POST atau DELETE
    if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid request method'
      ]);
    }

    $db = \Config\Database::connect();

    // Ambil data kongan dulu untuk validasi
    $kongan = $db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, kegiatan.id_anggota as pemilik_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_detail_kegiatan', $id)
      ->get()
      ->getRowArray();

    if (!$kongan) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Data kongan tidak ditemukan!'
      ]);
    }

    // Validasi akses
    $role = session()->get('role');
    $id_anggota_session = session()->get('id_anggota');

    if ($role !== 'admin' && $kongan['pemilik_kegiatan'] != $id_anggota_session) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Anda tidak memiliki akses untuk menghapus kongan ini!'
      ]);
    }

    // Hapus kongan
    if ($db->table('kegiatan_detail')->delete(['id_detail_kegiatan' => $id])) {
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Kongan berhasil dihapus!'
      ]);
    } else {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menghapus kongan!'
      ]);
    }
  }

  public function import_kongan()
  {
    $idKegiatan = $this->request->getPost('id_kegiatan');

    // Validasi kegiatan
    $kegiatan = $this->kegiatanModel->find($idKegiatan);
    if (!$kegiatan) {
      return redirect()->back()->with('error', 'Kegiatan tidak ditemukan!');
    }

    // Validasi akses
    if (session()->get('role') !== 'admin' && $kegiatan['id_anggota'] != session()->get('id_anggota')) {
      return redirect()->back()->with('error', 'Anda tidak memiliki akses!');
    }

    // Validasi file
    $file = $this->request->getFile('file');
    if (!$file->isValid()) {
      return redirect()->back()->with('error', 'File tidak valid!');
    }

    // Validasi ekstensi
    $extension = $file->getClientExtension();
    if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
      return redirect()->back()->with('error', 'Format file harus CSV atau Excel!');
    }

    try {
      // Load file Excel/CSV
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
      $sheet = $spreadsheet->getActiveSheet();
      $rows = $sheet->toArray();

      $db = \Config\Database::connect();
      $imported = 0;
      $errors = [];

      // Skip header row
      for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        // Validasi data
        if (empty($row[0]) || empty($row[1])) {
          $errors[] = "Baris " . ($i + 1) . ": Data tidak lengkap";
          continue;
        }

        $namaAnggota = trim($row[0]);
        $jumlah = (int)$row[1];

        if ($jumlah <= 0) {
          $errors[] = "Baris " . ($i + 1) . ": Jumlah kongan tidak valid";
          continue;
        }

        // Cari anggota berdasarkan nama
        $anggota = $this->anggotaModel->where('nama_anggota', $namaAnggota)->first();
        if (!$anggota) {
          $errors[] = "Baris " . ($i + 1) . ": Anggota '$namaAnggota' tidak ditemukan";
          continue;
        }

        // Cek duplikasi
        $existing = $db->table('kegiatan_detail')
          ->where('id_kegiatan', $idKegiatan)
          ->where('id_anggota', $anggota['id_anggota'])
          ->get()
          ->getRowArray();

        if ($existing) {
          $errors[] = "Baris " . ($i + 1) . ": Anggota '$namaAnggota' sudah ada";
          continue;
        }

        // Insert data
        $data = [
          'id_kegiatan' => $idKegiatan,
          'id_anggota' => $anggota['id_anggota'],
          'jumlah' => $jumlah,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($db->table('kegiatan_detail')->insert($data)) {
          $imported++;
        } else {
          $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data";
        }
      }

      $message = "Berhasil import $imported data kongan";
      if (!empty($errors)) {
        return redirect()->to('/kegiatan/detail/' . $idKegiatan)
          ->with('success', $message)
          ->with('import_errors', $errors);
      }

      return redirect()->to('/kegiatan/detail/' . $idKegiatan)
        ->with('success', $message);
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function download_template_import()
  {
    try {
      // Buat spreadsheet baru
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();

      // Set judul
      $sheet->setCellValue('A1', 'TEMPLATE IMPORT KONGAN');
      $sheet->mergeCells('A1:B1');
      $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
      $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      // Instruksi
      $sheet->setCellValue('A2', 'Petunjuk Penggunaan:');
      $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

      $sheet->setCellValue('A3', '1. Isi nama anggota sesuai dengan nama yang terdaftar di sistem');
      $sheet->setCellValue('A4', '2. Isi jumlah kongan dengan angka tanpa titik atau koma (contoh: 50000)');
      $sheet->setCellValue('A5', '3. Jangan ubah atau hapus baris header (Nama Anggota dan Jumlah)');
      $sheet->setCellValue('A6', '4. Simpan file dalam format Excel (.xlsx) atau CSV');
      $sheet->setCellValue('A7', '5. Upload file melalui form import kongan');

      // Style instruksi
      $sheet->getStyle('A3:A7')->getFont()->setSize(9);
      $sheet->getStyle('A3:A7')->getAlignment()->setWrapText(true);

      // Header tabel
      $sheet->setCellValue('A9', 'Nama Anggota');
      $sheet->setCellValue('B9', 'Jumlah');

      // Style header
      $sheet->getStyle('A9:B9')->getFont()->setBold(true)->setSize(11);
      $sheet->getStyle('A9:B9')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF4CAF50');
      $sheet->getStyle('A9:B9')->getFont()->getColor()->setARGB('FFFFFFFF');
      $sheet->getStyle('A9:B9')->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
      $sheet->getStyle('A9:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      // Contoh data
      $sheet->setCellValue('A10', 'Contoh: Ahmad Fauzi');
      $sheet->setCellValue('B10', '50000');
      $sheet->setCellValue('A11', 'Contoh: Siti Nurhaliza');
      $sheet->setCellValue('B11', '75000');

      // Style contoh data
      $sheet->getStyle('A10:B11')->getFont()->setItalic(true);
      $sheet->getStyle('A10:B11')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFF3F3F3');
      $sheet->getStyle('A10:B11')->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

      // Daftar anggota yang tersedia
      $sheet->setCellValue('D9', 'DAFTAR ANGGOTA TERSEDIA');
      $sheet->mergeCells('D9:E9');
      $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(11);
      $sheet->getStyle('D9')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF2196F3');
      $sheet->getStyle('D9')->getFont()->getColor()->setARGB('FFFFFFFF');
      $sheet->getStyle('D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      $sheet->setCellValue('D10', 'No');
      $sheet->setCellValue('E10', 'Nama Anggota');
      $sheet->getStyle('D10:E10')->getFont()->setBold(true);
      $sheet->getStyle('D10:E10')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFE3F2FD');
      $sheet->getStyle('D10:E10')->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

      // Ambil semua anggota dan urutkan berdasarkan nama
      $anggota = $this->anggotaModel->orderBy('nama_anggota', 'ASC')->findAll();

      $row = 11;
      $no = 1;
      foreach ($anggota as $item) {
        $sheet->setCellValue('D' . $row, $no++);
        $sheet->setCellValue('E' . $row, $item['nama_anggota']);
        $sheet->getStyle('D' . $row . ':E' . $row)->getBorders()->getAllBorders()
          ->setBorderStyle(Border::BORDER_THIN);
        $row++;
      }

      // Auto width untuk semua kolom
      $sheet->getColumnDimension('A')->setWidth(30);
      $sheet->getColumnDimension('B')->setWidth(15);
      $sheet->getColumnDimension('D')->setWidth(5);
      $sheet->getColumnDimension('E')->setWidth(30);

      // Set row height untuk instruksi
      for ($i = 3; $i <= 7; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
      }

      // Protection (optional) - protect sheet tapi allow insert rows
      // $sheet->getProtection()->setSheet(true);
      // $sheet->getStyle('A10:B1000')->getProtection()->setLocked(false);

      $filename = 'Template_Import_Kongan_' . date('Y-m-d') . '.xlsx';

      $writer = new Xlsx($spreadsheet);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $filename . '"');
      header('Cache-Control: max-age=0');

      $writer->save('php://output');
      exit();
    } catch (\Exception $e) {
      log_message('error', 'Error generating template: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Gagal generate template: ' . $e->getMessage());
    }
  }
}
