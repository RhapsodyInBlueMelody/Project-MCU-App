<?php

namespace App\Controllers;

use App\Libraries\DokuPayment;
use App\Models\TransaksiModel;
use App\Models\DokuPaymentHistoryModel;
use App\Models\PaketModel;

class Payment extends BaseController
{
    protected $dokuPayment;
    protected $transaksiModel;
    protected $dokuHistoryModel;
    protected $paketModel;
    
    public function __construct()
    {
        $this->dokuPayment = new DokuPayment();
        $this->transaksiModel = new TransaksiModel();
        $this->dokuHistoryModel = new DokuPaymentHistoryModel();
        $this->paketModel = new PaketModel();
    }
    
    public function checkout($id_transaksi)
    {
        // Get transaction data with package details
        $transaksi = $this->transaksiModel
            ->select('transaksi.*, paket.nama_paket, paket.deskripsi, paket.harga')
            ->join('paket', 'paket.id_paket = transaksi.id_paket')
            ->find($id_transaksi);

        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }

        // Format package details for DOKU
        $doku_invoice = 'MCU-' . date('Ymd') . '-' . $id_transaksi;
        
        // Prepare order data for DOKU
        $order_data = [
            'amount' => (int)$transaksi['total_harga'],
            'invoice_number' => $doku_invoice,
            'currency' => 'IDR',
            'line_items' => [
                [
                    'id' => $transaksi['id_paket'],
                    'name' => $transaksi['nama_paket'],
                    'price' => (int)$transaksi['harga'],
                    'quantity' => 1,
                    'category' => 'health-and-personal-care',
                    'url' => base_url('paket/detail/' . $transaksi['id_paket']),
                    // Optional: Add an image URL if you have one
                    'description' => substr($transaksi['deskripsi'], 0, 100) // Truncate description if too long
                ]
            ],
            'payment' => [
                'payment_due_date' => 60 // 60 minutes expiry
            ]
        ];
        
        // Add customer info if available
        if (isset($transaksi['id_pasien'])) {
            // Assuming you have a pasien table with these fields
            $pasien = $this->db->table('pasien')
                              ->where('id_pasien', $transaksi['id_pasien'])
                              ->get()
                              ->getRowArray();
            
            if ($pasien) {
                $order_data['customer'] = [
                    'id' => $pasien['id_pasien'],
                    'name' => $pasien['nama'] ?? 'Unknown',
                    'email' => $pasien['email'] ?? '',
                    'phone' => $pasien['telepon'] ?? '',
                    // Add other customer details if available
                ];
            }
        }
        
        // Create payment in DOKU
        $result = $this->dokuPayment->createPayment($order_data);
        
        if (!isset($result['error'])) {
            // Calculate expiry time
            $expired_time = date('Y-m-d H:i:s', strtotime('+60 minutes'));
            
            // Update transaksi table
            $this->transaksiModel->update($id_transaksi, [
                'doku_invoice_number' => $doku_invoice,
                'doku_payment_url' => $result['response']['payment']['url'],
                'doku_expired_time' => $expired_time,
                'updated_by' => 'RhapsodyInBlueMelody'
            ]);
            
            // Store in payment history
            $this->dokuHistoryModel->insert([
                'id_transaksi' => $id_transaksi,
                'doku_invoice_number' => $doku_invoice,
                'doku_session_id' => $result['response']['order']['session_id'],
                'doku_token_id' => $result['response']['payment']['token_id'],
                'amount' => $transaksi['total_harga'],
                'payment_status' => 'PENDING',
                'expired_time' => $expired_time,
                'response_data' => json_encode($result),
                'created_by' => 'RhapsodyInBlueMelody'
            ]);
            
            // Redirect to DOKU payment page
            return redirect()->to($result['response']['payment']['url']);
        } else {
            // Log the error
            log_message('error', 'DOKU Payment Error: ' . json_encode($result));
            
            // Handle error
            return redirect()->back()->with('error', 'Gagal membuat pembayaran: ' . ($result['message'] ?? 'Unknown error'));
        }
    }
    
    public function checkStatus($id_transaksi)
    {
        $transaksi = $this->transaksiModel->find($id_transaksi);
        if (!$transaksi) {
            return $this->response->setJSON(['error' => 'Transaksi tidak ditemukan']);
        }

        $paymentHistory = $this->dokuHistoryModel
            ->where('id_transaksi', $id_transaksi)
            ->orderBy('created_at', 'DESC')
            ->first();

        return $this->response->setJSON([
            'status' => $transaksi['status_pembayaran'],
            'payment_method' => $transaksi['doku_payment_method'],
            'payment_url' => $transaksi['doku_payment_url'],
            'expired_time' => $transaksi['doku_expired_time'],
            'last_update' => $paymentHistory ? $paymentHistory['updated_at'] : null
        ]);
    }
}
