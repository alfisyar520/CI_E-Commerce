<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transaksialteralamat extends Migration
{
    public function up()
    {
        $field = [
            'alamat' => [
                'type' => 'text',
            ],
            'ongkir' => [
                'type' => 'INT',
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
        ];
        $this->forge->addColumn('transaksi', $field);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi', ['alamat', 'ongkir', 'status']);
    }
}
