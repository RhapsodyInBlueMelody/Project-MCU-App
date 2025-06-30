<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserIdSeqTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'last_seq' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ]
        ]);
        $this->forge->addKey('last_seq', false, true); // Not a PK, but for uniqueness.
        $this->forge->createTable('user_id_seq', true);

        // Insert initial sequence value
        $db = \Config\Database::connect();
        $db->table('user_id_seq')->insert(['last_seq' => 0]);
    }

    public function down()
    {
        $this->forge->dropTable('user_id_seq', true);
    }
}
