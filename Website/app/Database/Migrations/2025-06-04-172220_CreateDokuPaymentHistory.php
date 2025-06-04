<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokuPaymentHistory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'doku_invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'doku_session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'doku_token_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'payment_date' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'expired_time' => [
                'type' => 'DATETIME'
            ],
            'response_data' => [
                'type' => 'TEXT'
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_transaksi');
        $this->forge->addKey('doku_invoice_number');
        $this->forge->createTable('doku_payment_history');
    }

    public function down()
    {
        $this->forge->dropTable('doku_payment_history');
    }
}
