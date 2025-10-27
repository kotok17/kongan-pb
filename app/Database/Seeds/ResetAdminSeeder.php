<?php
// filepath: app/Database/Seeds/ResetAdminSeeder.php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResetAdminSeeder extends Seeder
{
  public function run()
  {
    // Hapus admin lama jika ada
    $this->db->table('users')->where('username', 'sadmin')->delete();

    // Buat admin baru dengan password yang pasti benar
    $adminData = [
      'id_anggota' => null,
      'username' => 'sadmin',
      'password' => password_hash('admin123', PASSWORD_DEFAULT),
      'role' => 'admin',
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    $this->db->table('users')->insert($adminData);

    echo "✅ Admin berhasil direset!\n";
    echo "Username: sadmin\n";
    echo "Password: admin123\n";

    // Tampilkan hash untuk verifikasi
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "Hash baru: {$newHash}\n";

    // Test verify
    $verify = password_verify('admin123', $newHash);
    echo "Verify test: " . ($verify ? 'BERHASIL' : 'GAGAL') . "\n";
  }
}
