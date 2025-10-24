<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\KegiatanModel;
use App\Models\KonganModel; // Tambahkan model kongan
use Config\Services;

class KegiatanController extends BaseController
{
  public function index()
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $kegiatanModel = new KegiatanModel();
    $data['kegiatan'] = $kegiatanModel->getKegiatanWithAnggota();
    $data['anggota'] = (new AnggotaModel())->findAll();
    return view('kegiatan/index', $data);
  }

  public function tambah_kegiatan()
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $kegiatanModel = new KegiatanModel();

    // Validasi inputan
    $validation = Services::validation();
    $validation->setRules([
      'id_anggota' => 'required',
      'nama_kegiatan' => 'required',
      'tanggal_kegiatan' => 'required'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
      return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    $kegiatanModel->insert([
      'id_anggota' => $this->request->getPost('id_anggota'),
      'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
      'tanggal_kegiatan' => $this->request->getPost('tanggal_kegiatan'),
      'dibuat_oleh' => session()->get('nama_user')
    ]);

    // Redirect dengan pesan sukses
    return redirect()->to('/kegiatan')->with('success', 'Data anggota berhasil disimpan!');
  }

  public function detail($id_kegiatan)
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $kegiatanModel = new KegiatanModel();
    $konganModel = new KonganModel();
    $anggotaModel = new AnggotaModel();

    // Ambil data kegiatan berdasarkan ID yang spesifik
    $data['kegiatan'] = $kegiatanModel->getKegiatanWithAnggota($id_kegiatan);
    $data['anggota'] = $anggotaModel->findAll();
    $data['kongan'] = $konganModel->getKonganWithAnggota($id_kegiatan);

    // Cek aktivitas anggota yang mengadakan kegiatan di kegiatan lain
    if (!empty($data['kegiatan'])) {
      $id_anggota_kegiatan = $data['kegiatan'][0]['id_anggota'];
      $data['aktivitas_anggota'] = $konganModel->getAktivitasAnggotaLain($id_anggota_kegiatan, $id_kegiatan);
    }

    if (empty($data['kegiatan'])) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException('Kegiatan tidak ditemukan: ' . $id_kegiatan);
    }

    return view('kegiatan/detail', $data);
  }

  public function tambah_kongan()
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $konganModel = new KonganModel();

    // Validasi inputan
    $validation = Services::validation();
    $validation->setRules([
      'id_kegiatan' => 'required|numeric',
      'id_anggota' => 'required|numeric',
      'jumlah' => 'required|numeric|greater_than[0]'  // Sesuaikan dengan name di form
    ]);

    if (!$validation->withRequest($this->request)->run()) {
      return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    }

    $id_kegiatan = $this->request->getPost('id_kegiatan');
    $id_anggota = $this->request->getPost('id_anggota');
    $jumlah = $this->request->getPost('jumlah'); // Sesuaikan dengan name di form

    // Cek duplikasi HANYA di kegiatan yang sama
    $existing = $konganModel->checkDuplicate($id_kegiatan, $id_anggota);

    if ($existing) {
      return redirect()->back()->withInput()->with('error', 'Anggota ini sudah memiliki kongan di kegiatan ini!');
    }

    try {
      $konganModel->insert([
        'id_kegiatan' => $id_kegiatan,
        'id_anggota' => $id_anggota,
        'jumlah' => $jumlah
      ]);

      return redirect()->to('/kegiatan/detail/' . $id_kegiatan)->with('success', 'Kongan berhasil ditambahkan!');
    } catch (\Exception $e) {
      return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kongan: ' . $e->getMessage());
    }
  }

  public function hapus_kongan($id_detail_kegiatan)
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $konganModel = new KonganModel();

    if (!$this->request->isAJAX()) {
      return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
    }

    try {
      $kongan = $konganModel->find($id_detail_kegiatan);

      if (!$kongan) {
        return $this->response->setJSON(['success' => false, 'message' => 'Data kongan tidak ditemukan']);
      }

      $konganModel->delete($id_detail_kegiatan);

      return $this->response->setJSON(['success' => true, 'message' => 'Kongan berhasil dihapus']);
    } catch (\Exception $e) {
      return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus kongan: ' . $e->getMessage()]);
    }
  }

  public function hapus($id_kegiatan)
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $kegiatanModel = new KegiatanModel();
    $konganModel = new KonganModel();

    // Cek apakah kegiatan memiliki detail kongan
    $detail_kongan = $konganModel->where('id_kegiatan', $id_kegiatan)->findAll();

    if (!empty($detail_kongan)) {
      return redirect()->back()->with('error', 'Kegiatan tidak dapat dihapus karena masih memiliki data kongan!');
    }

    try {
      $kegiatan = $kegiatanModel->find($id_kegiatan);

      if (!$kegiatan) {
        return redirect()->back()->with('error', 'Kegiatan tidak ditemukan!');
      }

      $kegiatanModel->delete($id_kegiatan);

      return redirect()->to('/kegiatan')->with('success', 'Kegiatan berhasil dihapus!');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
    }
  }

  public function import_kongan()
  {
    if (!session()->get('logged_in')) {
      return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu!');
    }

    if (session()->get('role') !== 'admin') {
      return redirect()->to('/login')->with('error', 'Akses ditolak!');
    }

    $konganModel = new KonganModel();
    $anggotaModel = new AnggotaModel();

    $id_kegiatan = $this->request->getPost('id_kegiatan');

    if (empty($_FILES['file']['name'])) {
      return redirect()->back()->with('error', 'File harus dipilih!');
    }

    $file = $this->request->getFile('file');
    $extension = $file->getClientExtension();

    if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
      return redirect()->back()->with('error', 'Format file harus CSV atau Excel!');
    }

    try {
      $data = [];
      $success_count = 0;
      $error_count = 0;
      $skip_count = 0;
      $error_details = []; // Array untuk menyimpan detail error
      $duplicate_details = []; // Array untuk duplikasi

      if ($extension == 'csv') {
        // Handle CSV
        if (($handle = fopen($file->getTempName(), "r")) !== FALSE) {
          $row = 0;
          while (($data_csv = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;

            if (empty(array_filter($data_csv))) continue;
            if ($row == 1) continue; // Skip header

            if (!isset($data_csv[0]) || empty(trim($data_csv[0]))) {
              $error_details[] = "Baris $row: Nama anggota kosong";
              $error_count++;
              continue;
            }

            $nama_anggota_raw = trim($data_csv[0]);

            if (!isset($data_csv[1]) || empty(trim($data_csv[1]))) {
              $skip_count++;
              continue;
            }

            $jumlah_raw = trim($data_csv[1]);
            $nama_anggota = trim(str_replace(['Rp', 'A.', 'A '], '', $nama_anggota_raw));
            $jumlah = (int) preg_replace('/[^0-9]/', '', $jumlah_raw);

            if (empty($nama_anggota)) {
              $error_details[] = "Baris $row: Nama '$nama_anggota_raw' tidak valid setelah dibersihkan";
              $error_count++;
              continue;
            }

            // Pencarian anggota yang lebih fleksibel
            $anggota = $anggotaModel->like('nama_anggota', $nama_anggota)->first();

            if (!$anggota) {
              // Coba pencarian alternatif
              $anggota = $anggotaModel
                ->groupStart()
                ->like('nama_anggota', $nama_anggota)
                ->orWhere("nama_anggota LIKE '%$nama_anggota%'")
                ->groupEnd()
                ->first();
            }

            if (!$anggota) {
              $error_details[] = "Baris $row: Anggota '$nama_anggota' tidak ditemukan di database";
              $error_count++;
              continue;
            }

            if ($jumlah <= 0) {
              $skip_count++;
              continue;
            }

            $existing = $konganModel->checkDuplicate($id_kegiatan, $anggota['id_anggota']);
            if ($existing) {
              $duplicate_details[] = "Baris $row: '$nama_anggota' sudah ada kongan sebelumnya";
              $error_count++;
              continue;
            }

            $data[] = [
              'id_kegiatan' => $id_kegiatan,
              'id_anggota' => $anggota['id_anggota'],
              'jumlah' => $jumlah
            ];

            $success_count++;
          }
          fclose($handle);
        }
      } else {
        // Handle Excel (.xlsx/.xls)
        try {
          require_once ROOTPATH . 'vendor/autoload.php';
          $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
          $spreadsheet = $reader->load($file->getTempName());
          $worksheet = $spreadsheet->getActiveSheet();
          $highestRow = $worksheet->getHighestRow();

          for ($row = 2; $row <= $highestRow; $row++) {
            $nama_anggota_raw = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
            $jumlah_raw = trim($worksheet->getCell('B' . $row)->getValue() ?? '');

            if (empty($nama_anggota_raw)) {
              $error_details[] = "Baris $row: Nama anggota kosong";
              $error_count++;
              continue;
            }

            if (empty($jumlah_raw)) {
              $skip_count++;
              continue;
            }

            $nama_anggota = trim(str_replace(['A.', 'A ', 'Rp'], '', $nama_anggota_raw));
            $jumlah = (int) preg_replace('/[^0-9]/', '', $jumlah_raw);

            if (empty($nama_anggota)) {
              $error_details[] = "Baris $row: Nama '$nama_anggota_raw' tidak valid setelah dibersihkan";
              $error_count++;
              continue;
            }

            $anggota = $anggotaModel->like('nama_anggota', $nama_anggota)->first();

            if (!$anggota) {
              $anggota = $anggotaModel
                ->groupStart()
                ->like('nama_anggota', $nama_anggota)
                ->orWhere("nama_anggota LIKE '%$nama_anggota%'")
                ->groupEnd()
                ->first();
            }

            if (!$anggota) {
              $error_details[] = "Baris $row: Anggota '$nama_anggota' tidak ditemukan di database";
              $error_count++;
              continue;
            }

            if ($jumlah <= 0) {
              $skip_count++;
              continue;
            }

            $existing = $konganModel->checkDuplicate($id_kegiatan, $anggota['id_anggota']);
            if ($existing) {
              $duplicate_details[] = "Baris $row: '$nama_anggota' sudah ada kongan sebelumnya";
              $error_count++;
              continue;
            }

            $data[] = [
              'id_kegiatan' => $id_kegiatan,
              'id_anggota' => $anggota['id_anggota'],
              'jumlah' => $jumlah
            ];

            $success_count++;
          }
        } catch (\Exception $e) {
          return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
      }

      if (!empty($data)) {
        $konganModel->insertBatch($data);

        $message = "Import kongan berhasil! $success_count data ditambahkan.";

        if ($skip_count > 0) {
          $message .= " ($skip_count anggota tidak nulis)";
        }

        if ($error_count > 0) {
          $message .= " ($error_count data bermasalah)";

          // Tambahkan detail error ke session untuk ditampilkan
          $all_errors = array_merge($error_details, $duplicate_details);
          session()->setFlashdata('import_errors', $all_errors);
        }

        return redirect()->to('/kegiatan/detail/' . $id_kegiatan)->with('success', $message);
      } else {
        $error_msg = 'Tidak ada data valid yang bisa diimpor!';

        if ($skip_count > 0) {
          $error_msg .= " $skip_count anggota tidak nulis kongan.";
        }
        if ($error_count > 0) {
          $error_msg .= " $error_count data bermasalah.";

          // Tampilkan detail error
          $all_errors = array_merge($error_details, $duplicate_details);
          session()->setFlashdata('import_errors', $all_errors);
        }

        return redirect()->back()->with('error', $error_msg);
      }
    } catch (\Exception $e) {
      return redirect()->back()->with('error', 'Gagal import file: ' . $e->getMessage());
    }
  }
}
