<?php

namespace App\Models;

use CodeIgniter\Model;

class KonganModel extends Model
{
  protected $table = 'kegiatan_detail';
  protected $primaryKey = 'id_detail_kegiatan';
  protected $allowedFields = ['id_kegiatan', 'id_anggota', 'jumlah'];
  protected $useTimestamps = false;

  public function getKonganWithAnggota($id_kegiatan)
  {
    return $this->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id_kegiatan)
      ->orderBy('anggota.nama_anggota', 'ASC') // Urutkan berdasarkan nama anggota A-Z
      ->findAll();
  }

  public function checkDuplicate($id_kegiatan, $id_anggota)
  {
    return $this->where([
      'id_kegiatan' => $id_kegiatan,
      'id_anggota' => $id_anggota
    ])->first();
  }

  public function getTotalKongan($id_kegiatan)
  {
    $result = $this->selectSum('jumlah')
      ->where('id_kegiatan', $id_kegiatan)
      ->first();

    return $result['jumlah'] ?? 0;
  }

  public function getAnggotaTidakAktif()
  {
    // Ambil semua anggota yang tidak pernah ada di kegiatan_detail
    $db = \Config\Database::connect();

    $query = $db->query("
        SELECT a.id_anggota, a.nama_anggota 
        FROM anggota a 
        LEFT JOIN kegiatan_detail kd ON a.id_anggota = kd.id_anggota 
        WHERE kd.id_anggota IS NULL
    ");

    return $query->getResultArray();
  }

  // Atau jika ingin anggota yang tidak aktif dalam periode tertentu
  public function getAnggotaTidakAktifDalamPeriode($bulan_terakhir = 6)
  {
    $db = \Config\Database::connect();

    $query = $db->query("
        SELECT a.id_anggota, a.nama_anggota 
        FROM anggota a 
        WHERE a.id_anggota NOT IN (
            SELECT DISTINCT kd.id_anggota 
            FROM kegiatan_detail kd 
            JOIN kegiatan k ON kd.id_kegiatan = k.id_kegiatan 
            WHERE k.tanggal_kegiatan >= DATE_SUB(NOW(), INTERVAL ? MONTH)
        )
        ORDER BY a.nama_anggota ASC
    ", [$bulan_terakhir]);

    return $query->getResultArray();
  }

  public function getAktivitasAnggotaLain($id_anggota, $id_kegiatan_sekarang)
  {
    // Cek apakah anggota ini pernah nulis di kegiatan lain (bukan kegiatannya sendiri)
    return $this->select('kegiatan_detail.*, kegiatan.nama_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $id_anggota)
      ->where('kegiatan_detail.id_kegiatan !=', $id_kegiatan_sekarang)
      ->findAll();
  }
}
