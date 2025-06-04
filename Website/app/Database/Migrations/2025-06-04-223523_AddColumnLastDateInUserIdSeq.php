<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnLastDateInUserIdSeq extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user_id_seq', [
            'last_date' => [
                'type' => 'DATE',
                'null' => false,
                'after' => 'last_seq', // Optional: place after 'last_seq' column
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user_id_seq', 'last_date');
    }
}
