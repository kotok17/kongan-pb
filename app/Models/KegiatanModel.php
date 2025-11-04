<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanModel extends Model
{
  protected $table = 'kegiatan';
  protected $primaryKey = 'id_kegiatan';
  protected $allowedFields = [
    'id_anggota',
    'nama_kegiatan',
    'tanggal_kegiatan',
    'deskripsi',
    'potongan_tidak_ikut_mode',
    'potongan_tidak_ikut_amount',
    'potongan_undangan_amount',
    'created_at',
    'updated_at'
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

  public function getDetailWithOwner($id_kegiatan)
  {
    return $this->select('kegiatan.*, anggota.nama_anggota, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = kegiatan.id_anggota')
      ->where('kegiatan.id_kegiatan', $id_kegiatan)
      ->first();
  }

  public function hasKegiatanWithinYears(int $idAnggota, string $tanggalBaru, int $years = 3, ?int $excludeId = null): bool
  {
    $builder = $this->builder()
      ->where('id_anggota', $idAnggota);

    if ($excludeId !== null) {
      $builder->where('id_kegiatan !=', $excludeId);
    }

    $events = $builder->get()->getResultArray();
    if (empty($events)) {
      return false;
    }

    $target = new \DateTime($tanggalBaru);
    $minDays = $years * 365;

    foreach ($events as $event) {
      if (empty($event['tanggal_kegiatan'])) {
        continue;
      }
      $diff = $target->diff(new \DateTime($event['tanggal_kegiatan']));
      if (($diff->invert === 0 || $diff->invert === 1) && $diff->days < $minDays) {
        return true;
      }
    }

    return false;
  }
}