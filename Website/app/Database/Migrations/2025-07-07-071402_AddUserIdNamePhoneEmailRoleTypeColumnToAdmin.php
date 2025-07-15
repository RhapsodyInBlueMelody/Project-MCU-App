<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdNamePhoneEmailRoleTypeColumnToAdmin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admin', [
            'user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'id_admin'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'nama_admin',
            ],
            'telepon_admin' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],
            'role_type' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ]
        ]);


        $this->db->query('
            ALTER TABLE `admin`
            ADD CONSTRAINT `fk_admin_user_id`
            FOREIGN KEY (`user_id`)
            REFERENCES `users`(`user_id`)
            ON UPDATE CASCADE;
            ');
    }

    public function down()
    {

        $this->db->query('
            ALTER TABLE `admin`
            DROP FOREIGN KEY `fk_admin_user_id`
            ');

        $this->forge->dropColumn('admin', ['user_id', 'telepon_admin', 'telepon_admin', 'role_type']);
    }
}
