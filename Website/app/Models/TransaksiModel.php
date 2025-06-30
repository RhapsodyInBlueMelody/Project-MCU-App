<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $allowedFields = [
        'id_pasien', 
        'id_paket', 
        'tanggal_transaksi', 
        'id_janji_temu',
        'total_harga', 
        'status_pembayaran',
        'doku_invoice_number',
        'doku_payment_url',
        'doku_payment_method',
        'doku_expired_time',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
