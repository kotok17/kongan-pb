<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
  protected $table = 'users';
  protected $primaryKey = 'id_user';
  protected $useAutoIncrement = true;
  protected $returnType = 'array';
  protected $useSoftDeletes = false;
  protected $protectFields = true;
  protected $allowedFields = [
    'username',
    'password',
    'nama_user',
    'role',
    'id_anggota',
    'created_at',
    'updated_at'
  ];

  protected $useTimestamps = true;
  protected $dateFormat = 'datetime';
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';

  // Validation
  protected $validationRules = [
    'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id_user,{id_user}]',
    'password' => 'required|min_length[6]',
    'role' => 'required|in_list[admin,anggota]'
  ];

  protected $validationMessages = [
    'username' => [
      'required' => 'Username harus diisi',
      'min_length' => 'Username minimal 3 karakter',
      'max_length' => 'Username maksimal 50 karakter',
      'is_unique' => 'Username sudah digunakan'
    ],
    'password' => [
      'required' => 'Password harus diisi',
      'min_length' => 'Password minimal 6 karakter'
    ],
    'role' => [
      'required' => 'Role harus dipilih',
      'in_list' => 'Role harus admin atau anggota'
    ]
  ];

  protected $skipValidation = false;
  protected $cleanValidationRules = true;
  protected $allowCallbacks = true;

  protected function hashPassword(array $data)
  {
    if (!isset($data['data']['password'])) {
      return $data;
    }

    $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    return $data;
  }

  /**
   * Get user with anggota info (jika ada relasi)
   */
  public function getUserWithAnggota($username)
  {
    return $this->select('users.*, anggota.nama_anggota, anggota.alamat, anggota.no_hp')
      ->join('anggota', 'anggota.id_anggota = users.id_anggota', 'left')
      ->where('users.username', $username)
      ->first();
  }

  /**
   * Check if username exists
   */
  public function isUsernameExists($username, $excludeId = null)
  {
    $builder = $this->where('username', $username);

    if ($excludeId) {
      $builder->where('id_user !=', $excludeId);
    }

    return $builder->first() !== null;
  }

  /**
   * Get all users (tanpa filter status karena kolom tidak ada)
   */
  public function getAllUsers()
  {
    return $this->findAll();
  }
}