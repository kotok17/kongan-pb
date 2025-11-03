<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPotonganFieldsToKegiatan extends Migration
{
  public function up()
  {
    $fields = [
      'potongan_tidak_ikut_mode' => [
        'type'       => 'VARCHAR',
        'constraint' => 20,
        'default'    => 'activity_based', // activity_based|always|none
        'null'       => false,
        'after'      => 'nama_kegiatan'
      ],
      'potongan_tidak_ikut_amount' => [
        'type'       => 'INT',
        'default'    => 20000,
        'null'       => false,
        'after'      => 'potongan_tidak_ikut_mode'
      ],
      'potongan_undangan_amount' => [
        'type'       => 'INT',
        'default'    => 280000,
        'null'       => false,
        'after'      => 'potongan_tidak_ikut_amount'
      ],
    ];

    $this->forge->addColumn('kegiatan', $fields);
  }

  public function down()
  {
    $this->forge->dropColumn('kegiatan', [
      'potongan_tidak_ikut_mode',
      'potongan_tidak_ikut_amount',
      'potongan_undangan_amount'
    ]);
  }
}
