<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'username' => 'anggota1',
                'password' => password_hash('anggota123', PASSWORD_DEFAULT),
                'role' => 'anggota'
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
