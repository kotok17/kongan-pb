<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailKegiatanModel extends Model
{
  protected $table = 'kegiatan_detail';
  protected $primaryKey = 'id_detail_kegiatan';
  protected $allowedFields = ['id_kegiatan', 'id_anggota', 'jumlah', 'created_at', 'updated_at'];
  protected $useTimestamps = false;

  /**
   * Ambil data kongan berdasarkan ID kegiatan
   * @param int $id_kegiatan
   * @return array
   */
  public function getKonganByKegiatan($id_kegiatan)
  {
    return $this->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id_kegiatan)
      ->orderBy('anggota.nama_anggota', 'ASC')
      ->findAll();
  }

  /**
   * Ambil aktivitas anggota di kegiatan lain (untuk bonus)
   * @param int $id_anggota
   * @return array
   */
  public function getAktivitasAnggota($id_anggota)
  {
    return $this->db->table('kegiatan_detail')
      ->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $id_anggota)
      ->where('kegiatan.id_anggota !=', $id_anggota) // Bukan kegiatan sendiri
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->get()
      ->getResultArray();
  }

  /**
   * Cek apakah anggota sudah memberikan kongan di kegiatan tertentu
   * @param int $id_kegiatan
   * @param int $id_anggota
   * @return bool
   */
  public function isAlreadyContributed($id_kegiatan, $id_anggota)
  {
    $result = $this->where('id_kegiatan', $id_kegiatan)
      ->where('id_anggota', $id_anggota)
      ->first();

    return $result !== null;
  }

  /**
   * Ambil total kongan per kegiatan
   * @param int $id_kegiatan
   * @return int
   */
  public function getTotalKongan($id_kegiatan)
  {
    $result = $this->selectSum('jumlah')
      ->where('id_kegiatan', $id_kegiatan)
      ->first();

    return $result['jumlah'] ?? 0;
  }

  /**
   * Ambil jumlah peserta per kegiatan
   * @param int $id_kegiatan
   * @return int
   */
  public function countPeserta($id_kegiatan)
  {
    return $this->where('id_kegiatan', $id_kegiatan)->countAllResults();
  }

  /**
   * Hapus semua kongan berdasarkan ID kegiatan
   * @param int $id_kegiatan
   * @return bool
   */
  public function deleteByKegiatan($id_kegiatan)
  {
    return $this->where('id_kegiatan', $id_kegiatan)->delete();
  }

  /**
   * Ambil kongan terbesar dalam kegiatan
   * @param int $id_kegiatan
   * @return array|null
   */
  public function getHighestContribution($id_kegiatan)
  {
    return $this->select('kegiatan_detail.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan_detail.id_anggota')
      ->where('kegiatan_detail.id_kegiatan', $id_kegiatan)
      ->orderBy('kegiatan_detail.jumlah', 'DESC')
      ->first();
  }

  /**
   * Ambil statistik kongan per anggota
   * @param int $id_anggota
   * @return array
   */
  public function getAnggotaStatistics($id_anggota)
  {
    $builder = $this->db->table($this->table);

    $result = $builder->select('
                COUNT(*) as total_kegiatan_ikut,
                SUM(jumlah) as total_kongan_diberikan,
                AVG(jumlah) as rata_rata_kongan,
                MAX(jumlah) as kongan_terbesar,
                MIN(jumlah) as kongan_terkecil
            ')
      ->where('id_anggota', $id_anggota)
      ->get()
      ->getRowArray();

    return $result;
  }

  /**
   * Ambil riwayat kongan anggota
   * @param int $id_anggota
   * @param int $limit
   * @return array
   */
  public function getRiwayatKongan($id_anggota, $limit = 10)
  {
    return $this->select('kegiatan_detail.*, kegiatan.nama_kegiatan, kegiatan.tanggal_kegiatan')
      ->join('kegiatan', 'kegiatan.id_kegiatan = kegiatan_detail.id_kegiatan')
      ->where('kegiatan_detail.id_anggota', $id_anggota)
      ->orderBy('kegiatan_detail.created_at', 'DESC')
      ->limit($limit)
      ->findAll();
  }
}
