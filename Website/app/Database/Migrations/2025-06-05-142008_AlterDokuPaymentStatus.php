<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterDokuPaymentStatus extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('doku_payment_history', [
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum lunas', 'lunas', 'batal'],
                'default'    => 'belum lunas',
                'null'       => false,
            ]
        ]);
    }

    public function down()
    {
        // You may want to revert to previous type, e.g., VARCHAR(20)
        $this->forge->modifyColumn('doku', [
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'belum lunas',
                'null' => false,
            ]
        ]);
    }
}
