<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah admin sudah ada
        $existingAdmin = $this->db->table('users')->where('role', 'admin')->get()->getRow();

        if (!$existingAdmin) {
            // Insert admin user
            $adminData = [
                'id_anggota' => null,
                'username' => 'sadmin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('users')->insert($adminData);
            echo "✅ Admin berhasil dibuat - Username: sadmin, Password: admin123\n";
        } else {
            echo "ℹ️ Admin sudah ada di database\n";
        }

        // Insert beberapa user anggota contoh
        $anggotaList = $this->db->table('anggota')->limit(3)->get()->getResult();

        foreach ($anggotaList as $anggota) {
            $username = strtolower(str_replace(' ', '', $anggota->nama_anggota));

            // Cek apakah username sudah ada
            $existingUser = $this->db->table('users')->where('username', $username)->get()->getRow();

            if (!$existingUser) {
                $userData = [
                    'id_anggota' => $anggota->id_anggota,
                    'username' => $username,
                    'password' => password_hash('123456', PASSWORD_DEFAULT),
                    'role' => 'anggota',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('users')->insert($userData);
                echo "✅ User anggota: {$username} (password: 123456)\n";
            }
        }
    }
}
