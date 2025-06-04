<?php

namespace App\Models;

use CodeIgniter\Model;

class DokuPaymentHistoryModel extends Model
{
    protected $table = 'doku_payment_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_transaksi',
        'doku_invoice_number',
        'doku_session_id',
        'doku_token_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'expired_time',
        'response_data',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
