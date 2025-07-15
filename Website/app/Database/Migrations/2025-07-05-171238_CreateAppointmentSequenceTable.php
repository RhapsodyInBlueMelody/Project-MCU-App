<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppointmentSequenceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'last_date' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'last_seq' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey("last_date", true);
        $this->forge->createTable('appointment_id_seq');
    }

    public function down()
    {
        $this->forge->dropTable('appointment_id_seq');
    }
}
