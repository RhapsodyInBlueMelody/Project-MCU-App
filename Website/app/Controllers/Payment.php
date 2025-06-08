<?php

namespace App\Controllers;

use App\Libraries\DokuPayment;
use App\Models\TransaksiModel;
use App\Models\DokuPaymentHistoryModel;
use App\Models\PaketModel;

class Payment extends BaseController
{
    protected $db;
    protected $dokuPayment;
    protected $transaksiModel;
    protected $dokuHistoryModel;
    protected $paketModel;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->dokuPayment = new DokuPayment();
        $this->transaksiModel = new TransaksiModel();
        $this->dokuHistoryModel = new DokuPaymentHistoryModel();
        $this->paketModel = new PaketModel();
    }
    
    public function checkout($id_transaksi)
    {
        $userId = session()->get('user_id');
    
        // Get transaction data with package details
        $transaksi = $this->transaksiModel
            ->select('transaksi.*, paket.nama_paket, paket.deskripsi, paket.harga, paket.id_paket')
            ->join('paket', 'paket.id_paket = transaksi.id_paket')
            ->find($id_transaksi);
    
        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }
    
        // Format package details for DOKU
        $doku_invoice = 'MCU-' . date('Ymd') . '-' . $id_transaksi;
    
        $order_data = null;
    
        // Add customer info if available
        if (isset($transaksi['id_pasien']) && isset($transaksi['id_paket'])) {
            // Get pasien row
            $pasien = $this->db->table('pasien')
                ->where('id_pasien', $transaksi['id_pasien'])
                ->get()
                ->getRowArray();
    
            // Get paket row
            $paket = $this->db->table('paket')
                ->where('id_paket', $transaksi['id_paket'])
                ->get()
                ->getRowArray();
    
            if ($pasien && $paket) {
                $order_data = [
                    'id_transaksi' => $id_transaksi, // still needed for signature/request_id!
                    'order' => [
                        'amount' => (int)$paket['harga'],
                        'invoice_number' => $doku_invoice,
                        'currency' => 'IDR',
                        'callback_url' => base_url('payment/callback'),
                        'callback_url_cancel' => base_url('payment/cancel'),
                        'callback_url_result' => base_url('payment/result'),
                        'line_items' => [[
                            'id' => $paket['id_paket'],
                            'name' => $paket['nama_paket'],
                            'price' => (int)$paket['harga'],
                            'quantity' => 1,
                            'category' => 'health-and-personal-care',
                            'url' => base_url('paket/detail/' . $paket['id_paket']),
                            'description' => substr($paket['deskripsi'], 0, 100)
                        ]]
                    ],
                    'payment' => [
                        'payment_due_date' => 60,
                        'type' => 'SALE',
                        'payment_method_types' => [
                            'VIRTUAL_ACCOUNT_BCA',
                            'CREDIT_CARD',
                            'QRIS'
                        ]
                    ],
                    'customer' => [
                        'id' => $pasien['id_pasien'],
                        'name' => $pasien['nama_pasien'],
                        'phone' => $pasien['telepon'],
                        'email' => $pasien['email'],
                        'address' => $pasien['alamat'],
                        'city' => $pasien['lokasi'],
                        'state' => 'DKI Jakarta', // or other data
                        'country' => 'ID',
                        'postcode' => '10110'     // or other data
                    ],
                    'shipping_address' => [
                        'first_name' => $pasien['nama_pasien'],
                        'last_name' => '', // Split if you can
                        'address' => $pasien['alamat'],
                        'city' => $pasien['lokasi'],
                        'postal_code' => '10110',
                        'phone' => $pasien['telepon'],
                        'country_code' => 'IDN'
                    ],
                    'billing_address' => [
                        'first_name' => $pasien['nama_pasien'],
                        'last_name' => '', // Split if you can
                        'address' => $pasien['alamat'],
                        'city' => $pasien['lokasi'],
                        'postal_code' => '10110',
                        'phone' => $pasien['telepon'],
                        'country_code' => 'IDN'
                    ]
                ];
            }
        }
    
        // Guard: If order_data is not set, redirect with error
        if (!$order_data) {
            return redirect()->back()->with('error', 'Data pasien atau paket tidak ditemukan');
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
                'updated_by' => $userId
            ]);
    
            // Store in payment history
            $this->dokuHistoryModel->insert([
                'id_transaksi' => $id_transaksi,
                'doku_invoice_number' => $doku_invoice,
                'doku_session_id' => $result['response']['order']['session_id'],
                'doku_token_id' => $result['response']['payment']['token_id'],
                'amount' => $transaksi['total_harga'], // or $paket['harga'], depending on your logic
                'payment_status' => 'belum lunas',
                'expired_time' => $expired_time,
                'response_data' => json_encode($result),
                'created_by' => $userId
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

    public function callback()
    {
        $raw = $this->request->getBody();
        log_message('debug', 'DOKU Callback Raw Body: ' . $raw);
    
        $input = json_decode($raw, true);
        log_message('debug', 'DOKU Callback Decoded: ' . json_encode($input));
    
        if (!$input) {
            log_message('error', 'DOKU Callback: Invalid JSON');
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid JSON']);
        }
    
        $invoice_number = $input['order']['invoice_number'] ?? null;
        $status = $input['transaction']['status'] ?? null;
    
        log_message('debug', "DOKU Callback Extracted: invoice_number={$invoice_number}, status={$status}");
    
        // Confirm the model is loaded
        $this->transaksiModel = $this->transaksiModel ?? new \App\Models\TransaksiModel();
    
        // Log the list of invoice numbers in the DB for debugging
        $invoices = $this->transaksiModel->select('doku_invoice_number')->findAll();
        log_message('debug', 'Current doku_invoice_numbers in transaksi: ' . json_encode(array_column($invoices, 'doku_invoice_number')));
    
        if ($invoice_number && $status === 'SUCCESS') {
            $this->transaksiModel->where('doku_invoice_number', $invoice_number)
                ->set(['status_pembayaran' => 'lunas'])
                ->update();
            $affected = $this->transaksiModel->db->affectedRows();
            log_message('info', "DOKU Callback: Payment for invoice {$invoice_number} marked as 'lunas' (Rows updated: $affected)");
        } else {
            log_message('warning', "DOKU Callback: No action taken for invoice {$invoice_number}, status {$status}");
        }
    
        return $this->response->setStatusCode(200)->setJSON(['message' => 'OK']);
    }

    public function result()
    {
        // You can get query params from DOKU if they send any (e.g., ?invoice_number=MCU-...)
        $invoice_number = $this->request->getGet('invoice_number');
    
        // Optionally, show payment status based on invoice_number
        $status = null;
        if ($invoice_number) {
            $transaksi = $this->transaksiModel->where('doku_invoice_number', $invoice_number)->first();
            $status = $transaksi['status_pembayaran'] ?? null;
        }
    
        return view('payment/result', [
            'invoice_number' => $invoice_number,
            'status' => $status,
        ]);
    }
}
