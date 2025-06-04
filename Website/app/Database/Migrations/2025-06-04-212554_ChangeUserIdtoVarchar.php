<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeUserIdToVarchar extends Migration
{
    public function up()
    {
        // 1. Drop all FKs that reference users.user_id
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_created_by;');
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_updated_by;');
        $this->db->query('ALTER TABLE admin DROP FOREIGN KEY fk_admin_created_by;');
        $this->db->query('ALTER TABLE admin DROP FOREIGN KEY fk_admin_updated_by;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY fk_dokter_created_by;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY fk_dokter_updated_by;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY dokter_user_id_foreign;');
        $this->db->query('ALTER TABLE pasien DROP FOREIGN KEY fk_pasien_created_by;');
        $this->db->query('ALTER TABLE pasien DROP FOREIGN KEY fk_pasien_updated_by;');
        $this->db->query('ALTER TABLE spesialisasi DROP FOREIGN KEY fk_spesialisasi_created_by;');
        $this->db->query('ALTER TABLE spesialisasi DROP FOREIGN KEY fk_spesialisasi_updated_by;');
        $this->db->query('ALTER TABLE petugas_lab DROP FOREIGN KEY fk_petugas_lab_created_by;');
        $this->db->query('ALTER TABLE petugas_lab DROP FOREIGN KEY fk_petugas_lab_updated_by;');
        $this->db->query('ALTER TABLE izin_admin DROP FOREIGN KEY fk_izin_admin_created_by;');
        $this->db->query('ALTER TABLE izin_admin DROP FOREIGN KEY fk_izin_admin_updated_by;');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY fk_paket_created_by;');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY fk_paket_updated_by;');
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY fk_transaksi_created_by;');
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY fk_transaksi_updated_by;');

        // 2. Modify columns to VARCHAR(32) for all referencing columns
        $this->forge->modifyColumn('users', [
            'user_id' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('admin', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('dokter', [
            'user_id' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('pasien', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('spesialisasi', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('petugas_lab', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('izin_admin', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('paket', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);
        $this->forge->modifyColumn('transaksi', [
            'created_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'updated_by' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
        ]);

        // 3. Re-add FKs with the new type
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT dokter_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id);');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function down()
    {
        // 1. Drop all FKs
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_created_by;');
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_updated_by;');
        $this->db->query('ALTER TABLE admin DROP FOREIGN KEY fk_admin_created_by;');
        $this->db->query('ALTER TABLE admin DROP FOREIGN KEY fk_admin_updated_by;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY dokter_user_id_foreign;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY fk_dokter_created_by;');
        $this->db->query('ALTER TABLE dokter DROP FOREIGN KEY fk_dokter_updated_by;');
        $this->db->query('ALTER TABLE pasien DROP FOREIGN KEY fk_pasien_created_by;');
        $this->db->query('ALTER TABLE pasien DROP FOREIGN KEY fk_pasien_updated_by;');
        $this->db->query('ALTER TABLE spesialisasi DROP FOREIGN KEY fk_spesialisasi_created_by;');
        $this->db->query('ALTER TABLE spesialisasi DROP FOREIGN KEY fk_spesialisasi_updated_by;');
        $this->db->query('ALTER TABLE petugas_lab DROP FOREIGN KEY fk_petugas_lab_created_by;');
        $this->db->query('ALTER TABLE petugas_lab DROP FOREIGN KEY fk_petugas_lab_updated_by;');
        $this->db->query('ALTER TABLE izin_admin DROP FOREIGN KEY fk_izin_admin_created_by;');
        $this->db->query('ALTER TABLE izin_admin DROP FOREIGN KEY fk_izin_admin_updated_by;');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY fk_paket_created_by;');
        $this->db->query('ALTER TABLE paket DROP FOREIGN KEY fk_paket_updated_by;');
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY fk_transaksi_created_by;');
        $this->db->query('ALTER TABLE transaksi DROP FOREIGN KEY fk_transaksi_updated_by;');

        // 2. Revert column types to BIGINT(20)
        $this->forge->modifyColumn('users', [
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('admin', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('dokter', [
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('pasien', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('spesialisasi', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('petugas_lab', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('izin_admin', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('paket', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->modifyColumn('transaksi', [
            'created_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
        ]);

        // 3. Re-add FKs with reverted types
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE admin ADD CONSTRAINT fk_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT dokter_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(user_id);');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE dokter ADD CONSTRAINT fk_dokter_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE pasien ADD CONSTRAINT fk_pasien_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE spesialisasi ADD CONSTRAINT fk_spesialisasi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE petugas_lab ADD CONSTRAINT fk_petugas_lab_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE izin_admin ADD CONSTRAINT fk_izin_admin_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE paket ADD CONSTRAINT fk_paket_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->db->query('ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_updated_by FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;');
    }
}
