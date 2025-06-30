<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDokuColumnsToTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaksi', [
            'doku_invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id_transaksi'
            ],
            'doku_payment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'status_pembayaran'
            ],
            'doku_payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'doku_payment_url'
            ],
            'doku_expired_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'doku_payment_method'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi', ['doku_invoice_number', 'doku_payment_url', 'doku_payment_method', 'doku_expired_time']);
    }
}
