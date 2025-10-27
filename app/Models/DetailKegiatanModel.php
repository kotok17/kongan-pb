<?php
// filepath: app/Models/DetailKegiatanModel.php

namespace App\Models;

use CodeIgniter\Model;

class DetailKegiatanModel extends Model
{
  protected $table = 'kegiatan_detail';
  protected $primaryKey = 'id_detail_kegiatan';
  protected $useAutoIncrement = true;
  protected $returnType = 'array';
  protected $useSoftDeletes = false;
  protected $protectFields = true;
  protected $allowedFields = [
    'id_kegiatan',
    'id_anggota',
    'jumlah',
    'created_at',
    'updated_at'
  ];

  // Dates
  protected $useTimestamps = true;
  protected $dateFormat = 'datetime';
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';

  // Validation
  protected $validationRules = [
    'id_kegiatan' => 'required|integer',
    'id_anggota' => 'required|integer',
    'jumlah' => 'required|integer|greater_than[0]'
  ];

  protected $validationMessages = [
    'id_kegiatan' => [
      'required' => 'ID Kegiatan harus diisi',
      'integer' => 'ID Kegiatan harus berupa angka'
    ],
    'id_anggota' => [
      'required' => 'Anggota harus dipilih',
      'integer' => 'ID Anggota harus berupa angka'
    ],
    'jumlah' => [
      'required' => 'Jumlah kongan harus diisi',
      'integer' => 'Jumlah kongan harus berupa angka',
      'greater_than' => 'Jumlah kongan harus lebih dari 0'
    ]
  ];

  protected $skipValidation = false;
  protected $cleanValidationRules = true;

  // Callbacks
  protected $allowCallbacks = true;

  /**
   * Get detail kegiatan dengan informasi anggota
   */
  public function getDetailWithAnggota($idKegiatan)
  {
    return $this->select('kegiatan_detail.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $idKegiatan)
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->findAll();
  }

  /**
   * Get total kongan untuk kegiatan tertentu
   */
  public function getTotalKongan($idKegiatan)
  {
    $result = $this->select('COALESCE(SUM(jumlah), 0) as total')
      ->where('id_kegiatan', $idKegiatan)
      ->get()
      ->getRow();

    return $result ? (int)$result->total : 0;
  }

  /**
   * Get jumlah peserta untuk kegiatan tertentu
   */
  public function getTotalPeserta($idKegiatan)
  {
    return $this->where('id_kegiatan', $idKegiatan)->countAllResults();
  }

  /**
   * Cek apakah anggota sudah memberikan kongan pada kegiatan ini
   */
  public function isAnggotaExists($idKegiatan, $idAnggota)
  {
    return $this->where('id_kegiatan', $idKegiatan)
      ->where('id_anggota', $idAnggota)
      ->first() !== null;
  }

  /**
   * Get kongan yang diberikan oleh anggota tertentu
   */
  public function getKonganByAnggota($idAnggota)
  {
    return $this->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $idAnggota)
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->findAll();
  }

  /**
   * Get statistik kongan per bulan
   */
  public function getStatistikBulanan($tahun = null)
  {
    $tahun = $tahun ?? date('Y');

    return $this->select('
                MONTH(kegiatan_detail.created_at) as bulan,
                COUNT(*) as total_kongan,
                SUM(kegiatan_detail.jumlah) as total_uang
            ')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('YEAR(kegiatan_detail.created_at)', $tahun)
      ->groupBy('MONTH(kegiatan_detail.created_at)')
      ->orderBy('bulan', 'ASC')
      ->findAll();
  }
}
