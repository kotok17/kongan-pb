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

  public function tambah()
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

  public function delete($id)
  {
    $anggotaModel = new AnggotaModel();

    if ($anggotaModel->delete($id)) {
      return $this->response->setJSON([
        'success' => true,
        'message' => 'Data berhasil dihapus'
      ]);
    } else {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal menghapus data'
      ]);
    }
  }
}
