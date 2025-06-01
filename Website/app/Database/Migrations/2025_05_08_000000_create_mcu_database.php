<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMcuDatabase extends Migration
{
    public function up()
    {
        // Load database forge and db instance
        $this->forge = \Config\Database::forge();
        $this->db = \Config\Database::connect();

        // Table: users
        $this->forge->addField([
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['pasien', 'dokter', 'admin', 'petugas_lab'],
            ],
            'google_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('google_id');
        $this->forge->createTable('users');

        // Table: spesialisasi
        $this->forge->addField([
            'id_spesialisasi' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'nama_spesialisasi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_spesialisasi', true);
        $this->forge->addUniqueKey('nama_spesialisasi');
        $this->forge->createTable('spesialisasi');

        // Table: pasien
        $this->forge->addField([
            'id_pasien' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'nama_pasien' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'telepon' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pasien', true);
        $this->forge->createTable('pasien');

        // Table: dokter
        $this->forge->addField([
            'id_dokter' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'nama_dokter' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'id_spesialisasi' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_dokter', true);
        $this->forge->addForeignKey('id_spesialisasi', 'spesialisasi', 'id_spesialisasi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokter');

        // Table: petugas_lab
        $this->forge->addField([
            'id_petugas_lab' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'nama_petugas_lab' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_petugas_lab', true);
        $this->forge->createTable('petugas_lab');

        // Table: admin
        $this->forge->addField([
            'id_admin' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'nama_admin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_admin', true);
        $this->forge->createTable('admin');

        // Table: izin_admin
        $this->forge->addField([
            'id_izin' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'id_admin' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'izin' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_izin', true);
        $this->forge->addForeignKey('id_admin', 'admin', 'id_admin', 'CASCADE', 'CASCADE');
        $this->forge->createTable('izin_admin');

        // Table: paket
        $this->forge->addField([
            'id_paket' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_paket' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_paket', true);
        $this->forge->createTable('paket');

        // Table: transaksi
        $this->forge->addField([
            'id_transaksi' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pasien' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'id_paket' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'tanggal_transaksi' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'total_harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'status_pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['pending','lunas','batal'],
                'default' => 'pending',
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_transaksi', true);
        $this->forge->addForeignKey('id_pasien', 'pasien', 'id_pasien', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_paket', 'paket', 'id_paket', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaksi');

        // Add foreign keys for audit fields using raw SQL (after all tables are created)
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Load database forge
        $this->forge = \Config\Database::forge();

        $this->forge->dropTable('izin_admin', true);
        $this->forge->dropTable('admin', true);
        $this->forge->dropTable('petugas_lab', true);
        $this->forge->dropTable('dokter', true);
        $this->forge->dropTable('pasien', true);
        $this->forge->dropTable('spesialisasi', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('transaksi', true);
        $this->forge->dropTable('paket', true);
    }
}
