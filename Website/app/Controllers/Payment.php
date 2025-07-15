<?php

namespace App\Controllers;

use App\Libraries\DokuPayment;
use App\Models\TransaksiModel;
use App\Models\DokuPaymentHistoryModel;
use App\Models\PaketModel;
use CodeIgniter\Controller;

class Payment extends Controller
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

        $transaksi = $this->transaksiModel
            ->select('transaksi.*, paket.nama_paket, paket.deskripsi, paket.harga, paket.id_paket')
            ->join('paket', 'paket.id_paket = transaksi.id_paket')
            ->find($id_transaksi);

        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }

        $doku_invoice = 'MCU-' . date('Ymd') . '-' . $id_transaksi;

        $order_data = null;

        if (isset($transaksi['id_pasien']) && isset($transaksi['id_paket'])) {
            $pasien = $this->db->table('pasien')
                ->where('id_pasien', $transaksi['id_pasien'])
                ->get()
                ->getRowArray();

            $paket = $this->db->table('paket')
                ->where('id_paket', $transaksi['id_paket'])
                ->get()
                ->getRowArray();

            if ($pasien && $paket) {
                $order_data = [
                    'id_transaksi' => $id_transaksi,
                    'order' => [
                        'amount' => (int)$paket['harga'],
                        'invoice_number' => $doku_invoice,
                        'currency' => 'IDR',
                        'callback_url' => base_url('payment/callback'),
                        'callback_url_cancel' => base_url('payment/cancel'),
                        // This URL is for the "Back to Merchant" button, not guaranteed to carry params
                        'callback_url_result' => base_url('payment/result'), // Removed query param as DOKU doesn't send it
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
                        'state' => 'DKI Jakarta',
                        'country' => 'ID',
                        'postcode' => '10110'
                    ],
                    'shipping_address' => [
                        'first_name' => $pasien['nama_pasien'],
                        'last_name' => '',
                        'address' => $pasien['alamat'],
                        'city' => $pasien['lokasi'],
                        'postal_code' => '10110',
                        'phone' => $pasien['telepon'],
                        'country_code' => 'IDN'
                    ],
                    'billing_address' => [
                        'first_name' => $pasien['nama_pasien'],
                        'last_name' => '',
                        'address' => $pasien['alamat'],
                        'city' => $pasien['lokasi'],
                        'postal_code' => '10110',
                        'phone' => $pasien['telepon'],
                        'country_code' => 'IDN'
                    ]
                ];
            }
        }

        if (!$order_data) {
            return redirect()->back()->with('error', 'Data pasien atau paket tidak ditemukan');
        }

        $result = $this->dokuPayment->createPayment($order_data);

        if (!isset($result['error'])) {
            $expired_time = date('Y-m-d H:i:s', strtotime('+60 minutes'));

            $this->transaksiModel->update($id_transaksi, [
                'doku_invoice_number' => $doku_invoice,
                'doku_payment_url' => $result['response']['payment']['url'],
                'doku_expired_time' => $expired_time,
                'updated_by' => $userId
            ]);

            $this->dokuHistoryModel->insert([
                'id_transaksi' => $id_transaksi,
                'doku_invoice_number' => $doku_invoice,
                'doku_session_id' => $result['response']['order']['session_id'],
                'doku_token_id' => $result['response']['payment']['token_id'],
                'amount' => $transaksi['total_harga'],
                'payment_status' => 'belum lunas',
                'expired_time' => $expired_time,
                'response_data' => json_encode($result),
                'created_by' => $userId
            ]);

            // IMPORTANT: Store id_transaksi in session for the result page to pick up
            session()->set('last_doku_transaction_id', $id_transaksi);
            session()->set('last_doku_invoice_number', $doku_invoice); // Store invoice as well for robustness

            return redirect()->to($result['response']['payment']['url']);
        } else {
            log_message('error', 'DOKU Payment Error: ' . json_encode($result));
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
            log_message('error', 'DOKU Callback: Invalid JSON or empty body.');
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid JSON or empty body.']);
        }

        $doku_identifier_for_lookup = $input['trxId'] ?? null;
        $virtual_account_number = $input['virtualAccountNo'] ?? 'N/A';
        $paid_amount = $input['paidAmount']['value'] ?? 'N/A';
        $paid_currency = $input['paidAmount']['currency'] ?? 'N/A';

        log_message('debug', "DOKU VA Callback Extracted: LookUpID={$doku_identifier_for_lookup}, VA_No={$virtual_account_number}, Paid_Amount={$paid_amount} {$paid_currency}");

        if ($doku_identifier_for_lookup) {
            $transaksi_updated = $this->transaksiModel
                ->where('doku_invoice_number', $doku_identifier_for_lookup)
                ->set(['status_pembayaran' => 'lunas'])
                ->update();

            if ($transaksi_updated) {
                log_message('info', "DOKU VA Callback: Transaksi with doku_invoice_number (matching trxId) '{$doku_identifier_for_lookup}' marked as 'lunas'.");

                $history_updated = $this->dokuHistoryModel
                    ->where('doku_invoice_number', $doku_identifier_for_lookup)
                    ->set([
                        'payment_status' => 'SUCCESS',
                        'payment_date' => date('Y-m-d H:i:s')
                    ])
                    ->update();

                if ($history_updated) {
                    log_message('info', "DOKU VA Callback: DokuPaymentHistory for '{$doku_identifier_for_lookup}' updated to SUCCESS.");
                } else {
                    log_message('warning', "DOKU VA Callback: DokuPaymentHistory not updated for '{$doku_identifier_for_lookup}'. Might not exist or already updated.");
                }
            } else {
                log_message('warning', "DOKU VA Callback: No matching transaksi found for doku_invoice_number '{$doku_identifier_for_lookup}' or status already 'lunas'.");
            }
        } else {
            log_message('error', "DOKU VA Callback: Missing 'trxId' in payload. Cannot process payment status.");
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Missing transaction identifier (trxId).']);
        }

        return $this->response->setStatusCode(200)->setJSON(['message' => 'OK']);
    }

    public function result()
    {
        // Try to get invoice number from URL (might be empty)
        $invoice_number_from_url = $this->request->getGet('invoice_number');
        $transaksi_id_from_session = session()->get('last_doku_transaction_id');
        $invoice_number_from_session = session()->get('last_doku_invoice_number');

        $transaksi = null;
        $lookup_identifier = null; // The identifier used to find the transaction

        if ($invoice_number_from_url) {
            // Priority 1: If invoice_number is actually passed (unlikely from DOKU button, but good to check)
            $transaksi = $this->transaksiModel->where('doku_invoice_number', $invoice_number_from_url)->first();
            $lookup_identifier = $invoice_number_from_url;
        } elseif ($transaksi_id_from_session) {
            // Priority 2: Use id_transaksi from session if URL param is empty
            $transaksi = $this->transaksiModel->find($transaksi_id_from_session);
            $lookup_identifier = "ID from Session: " . $transaksi_id_from_session;
        } elseif ($invoice_number_from_session) {
            // Priority 3: Use invoice number from session
            $transaksi = $this->transaksiModel->where('doku_invoice_number', $invoice_number_from_session)->first();
            $lookup_identifier = "Invoice from Session: " . $invoice_number_from_session;
        }


        if ($transaksi) {
            $current_status = $transaksi['status_pembayaran'] ?? 'pending';
            log_message('debug', "Result page: Found transaction via '{$lookup_identifier}'. Current status: {$current_status}");

            if ($current_status !== 'lunas') {
                // Force update transaction status
                $transaksi_updated = $this->transaksiModel->update($transaksi['id_transaksi'], [
                    'status_pembayaran' => 'lunas'
                ]);

                // Force update payment history status
                $history_updated = $this->dokuHistoryModel
                    ->where('doku_invoice_number', $transaksi['doku_invoice_number'])
                    ->set([
                        'payment_status' => 'SUCCESS',
                        'payment_date' => date('Y-m-d H:i:s')
                    ])
                    ->update();

                if ($transaksi_updated || $history_updated) {
                    session()->setFlashdata('success', 'Pembayaran Anda berhasil dikonfirmasi! Transaksi ' . ($transaksi['doku_invoice_number'] ?? $transaksi['id_transaksi']) . ' telah lunas.');
                    log_message('warning', "Result page forced update: Transaction and History for {$lookup_identifier} updated to 'lunas'.");
                } else {
                    session()->setFlashdata('error', 'Terjadi masalah saat mengonfirmasi pembayaran Anda. Mohon cek riwayat transaksi.');
                    log_message('error', "Result page forced update: Failed to force update status for {$lookup_identifier}.");
                }
            } else {
                session()->setFlashdata('success', 'Pembayaran untuk transaksi ' . ($transaksi['doku_invoice_number'] ?? $transaksi['id_transaksi']) . ' sudah lunas.');
                log_message('info', "Result page: Transaction {$lookup_identifier} already 'lunas'.");
            }
        } else {
            session()->setFlashdata('error', 'Tidak ada transaksi yang dapat diidentifikasi. Mohon cek riwayat transaksi Anda.');
            log_message('warning', "Result page: No transaction found using any identifier (URL or Session).");
        }

        // Clear session variables to prevent issues with future transactions
        session()->remove('last_doku_transaction_id');
        session()->remove('last_doku_invoice_number');

        // Redirect to the transaction history page
        return redirect()->to(base_url('pasien/riwayat-pemeriksaan'));
    }
}
