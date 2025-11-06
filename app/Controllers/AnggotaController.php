<?php

namespace App\Controllers;

use Config\Services;
use App\Models\AnggotaModel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class AnggotaController extends BaseController
{
  public function index()
  {
    $anggotaModel = new AnggotaModel();
    $data['anggota'] = $anggotaModel->findAll();
    return view('anggota/index', $data);
  }

  public function import()
  {
    $file = $this->request->getFile('file');

    if (!$file->isValid() || $file->hasMoved()) {
      return redirect()->back()->with('error', 'File tidak valid atau sudah diupload.');
    }

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file->getTempName());
    $sheet = $spreadsheet->getActiveSheet()->toArray();

    $anggotaModel = new AnggotaModel();

    // Mulai dari baris ke-2 (karena baris pertama biasanya header)
    for ($i = 1; $i < count($sheet); $i++) {
      for ($i = 1; $i < count($sheet); $i++) {
        $nama_anggota = trim($sheet[$i][1] ?? '');
        $no_hp = trim($sheet[$i][2] ?? '');
        $alamat = trim($sheet[$i][3] ?? '');

        if ($nama_anggota != '') {
          $anggotaModel->insert([
            'nama_anggota' => $nama_anggota,
            'no_hp' => $no_hp,
            'alamat' => $alamat
          ]);
        }
      }
    }

    return redirect()->to('/anggota')->with('success', 'Data anggota berhasil diimport!');
  }

  public function simpan()
  {
    $anggotaModel = new AnggotaModel();

    // Validasi inputan
    $validation = Services::validation();
    $validation->setRules([
      'nama_anggota' => 'required|min_length[3]|max_length[100]',
      'no_hp'        => 'required|numeric|min_length[10]|max_length[15]',
      'alamat'       => 'required|min_length[5]|max_length[255]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
      return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    // Sanitasi input
    $nama_anggota = strip_tags($this->request->getPost('nama_anggota'));
    $no_hp        = strip_tags($this->request->getPost('no_hp'));
    $alamat       = strip_tags($this->request->getPost('alamat'));

    try {
      $anggotaModel->insert([
        'nama_anggota' => $nama_anggota,
        'no_hp'        => $no_hp,
        'alamat'       => $alamat
      ]);
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data anggota.');
    }

    // Redirect dengan pesan sukses
    return redirect()->to('/anggota')->with('success', 'Data anggota berhasil disimpan!');
  }

  public function hapus($id)
  {
    // Validasi CSRF untuk request AJAX
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Request tidak valid'
      ]);
    }

    // Validasi admin access
    if (session()->get('role') !== 'admin') {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Akses ditolak. Hanya admin yang dapat menghapus data anggota.'
      ]);
    }

    $anggotaModel = new AnggotaModel();

    // Cek apakah anggota ada
    $anggota = $anggotaModel->find($id);
    if (!$anggota) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Data anggota tidak ditemukan'
      ]);
    }

    $db = \Config\Database::connect();

    // ✅ CEK APAKAH ANGGOTA PERNAH MEMBUAT KEGIATAN
    $hasKegiatan = $db->table('kegiatan')
      ->where('id_anggota', $id)
      ->countAllResults();

    if ($hasKegiatan > 0) {
      // Ambil nama kegiatan untuk info
      $kegiatanList = $db->table('kegiatan')
        ->select('nama_kegiatan, tanggal_kegiatan')
        ->where('id_anggota', $id)
        ->orderBy('tanggal_kegiatan', 'DESC')
        ->get()
        ->getResultArray();

      $namaKegiatan = array_map(function ($k) {
        return $k['nama_kegiatan'] . ' (' . date('d/m/Y', strtotime($k['tanggal_kegiatan'])) . ')';
      }, $kegiatanList);

      return $this->response->setJSON([
        'success' => false,
        'message' => 'Tidak dapat menghapus anggota ini karena pernah membuat kegiatan kongan.',
        'detail' => 'Kegiatan yang dibuat: ' . implode(', ', array_slice($namaKegiatan, 0, 3)) .
          (count($namaKegiatan) > 3 ? ' dan ' . (count($namaKegiatan) - 3) . ' kegiatan lainnya' : '')
      ]);
    }

    // ✅ CEK APAKAH ANGGOTA PERNAH IKUT KONGAN DI KEGIATAN LAIN
    $hasKongan = $db->table('kegiatan_detail kd')
      ->select('k.nama_kegiatan, k.tanggal_kegiatan, kd.jumlah')
      ->join('kegiatan k', 'k.id_kegiatan = kd.id_kegiatan')
      ->where('kd.id_anggota', $id)
      ->countAllResults();

    if ($hasKongan > 0) {
      // Ambil info kongan untuk detail
      $konganList = $db->table('kegiatan_detail kd')
        ->select('k.nama_kegiatan, k.tanggal_kegiatan, kd.jumlah')
        ->join('kegiatan k', 'k.id_kegiatan = kd.id_kegiatan')
        ->where('kd.id_anggota', $id)
        ->orderBy('k.tanggal_kegiatan', 'DESC')
        ->limit(3)
        ->get()
        ->getResultArray();

      $infoKongan = array_map(function ($k) {
        return $k['nama_kegiatan'] . ' (Rp ' . number_format($k['jumlah'], 0, ',', '.') . ')';
      }, $konganList);

      return $this->response->setJSON([
        'success' => false,
        'message' => 'Tidak dapat menghapus anggota ini karena pernah memberikan kongan di kegiatan lain.',
        'detail' => 'Kongan di: ' . implode(', ', $infoKongan) .
          ($hasKongan > 3 ? ' dan ' . ($hasKongan - 3) . ' kegiatan lainnya' : '')
      ]);
    }

    // ✅ CEK APAKAH ANGGOTA MEMILIKI AKUN USER
    $hasUser = $db->table('users')
      ->where('id_anggota', $id)
      ->countAllResults();

    if ($hasUser > 0) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Tidak dapat menghapus anggota ini karena masih memiliki akun user aktif.',
        'detail' => 'Hapus akun user terlebih dahulu sebelum menghapus data anggota.'
      ]);
    }

    // ✅ JIKA TIDAK ADA RELASI, LANJUTKAN HAPUS
    try {
      if ($anggotaModel->delete($id)) {
        return $this->response->setJSON([
          'success' => true,
          'message' => 'Data anggota berhasil dihapus'
        ]);
      } else {
        return $this->response->setJSON([
          'success' => false,
          'message' => 'Gagal menghapus data anggota'
        ]);
      }
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
    }
  }

  public function detail($id)
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Request tidak valid'
      ]);
    }

    $anggotaModel = new AnggotaModel();
    $anggota = $anggotaModel->find($id);

    if (!$anggota) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Data anggota tidak ditemukan'
      ]);
    }

    // Ambil history kongan anggota
    $db = \Config\Database::connect();
    $historyKongan = $db->table('kegiatan_detail kd')
      ->select('k.nama_kegiatan, k.tanggal_kegiatan, kd.jumlah, kd.created_at, a_pemilik.nama_anggota as pemilik_kegiatan')
      ->join('kegiatan k', 'k.id_kegiatan = kd.id_kegiatan')
      ->join('anggota a_pemilik', 'a_pemilik.id_anggota = k.id_anggota')
      ->where('kd.id_anggota', $id)
      ->orderBy('k.tanggal_kegiatan', 'DESC')
      ->get()
      ->getResultArray();

    // Hitung statistik
    $totalKongan = array_sum(array_column($historyKongan, 'jumlah'));
    $jumlahKegiatan = count($historyKongan);

    return $this->response->setJSON([
      'success' => true,
      'data' => [
        'anggota' => $anggota,
        'history_kongan' => $historyKongan,
        'statistik' => [
          'total_kongan' => $totalKongan,
          'jumlah_kegiatan' => $jumlahKegiatan,
          'rata_rata' => $jumlahKegiatan > 0 ? $totalKongan / $jumlahKegiatan : 0
        ]
      ]
    ]);
  }
}
