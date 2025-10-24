<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanModel extends Model
{
  protected $table = 'kegiatan';
  protected $primaryKey = 'id_kegiatan';
  protected $allowedFields = [
    'id_anggota',
    'tanggal_kegiatan',
    'nama_kegiatan',
    'dibuat_oleh'
  ];

  public function getKegiatanWithAnggota($id_kegiatan = null)
  {
    $builder = $this->select('kegiatan.*, anggota.nama_anggota')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota');

    if ($id_kegiatan !== null) {
      // Jika ada ID kegiatan, ambil kegiatan spesifik
      return $builder->where('kegiatan.id_kegiatan', $id_kegiatan)->findAll();
    }

    // Jika tidak ada ID, return builder untuk chaining
    return $builder;
  }
}
