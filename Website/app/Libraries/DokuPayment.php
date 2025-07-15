<?php

namespace App\Libraries;

use CodeIgniter\Config\Services;
use CodeIgniter\Log\Logger; // Import Logger

class DokuPayment
{
    private $client_id;
    private $secret_key;
    private $base_url;
    protected $logger; // Declare logger property

    public function __construct()
    {
        $this->client_id  = getenv('DOKU_CLIENT_ID');
        $this->secret_key = getenv('DOKU_SECRET_KEY');
        $this->base_url   = "https://api-sandbox.doku.com";
        $this->logger = Services::logger(); // Initialize logger
    }

    public function createPayment($order_data)
    {
        helper('doku');

        $components = [
            'client_id' => $this->client_id,
            'request_id' => (string)$order_data['id_transaksi'],
            'request_timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'request_target' => '/checkout/v1/payment'
        ];

        $request_body = [
            'order' => $order_data['order'],
            'payment' => $order_data['payment'],
            'customer' => $order_data['customer'],
            'shipping_address' => $order_data['shipping_address'],
            'billing_address' => $order_data['billing_address'],
        ];

        $signature = generate_doku_signature($components, $this->secret_key, $request_body);

        $headers = [
            'Client-Id' => $components['client_id'],
            'Request-Id' => $components['request_id'],
            'Request-Timestamp' => $components['request_timestamp'],
            'Signature' => $signature,
            'Content-Type' => 'application/json'
        ];

        $this->logger->debug('DOKU Create Payment Request Headers: ' . json_encode($headers));
        $this->logger->debug('DOKU Create Payment Request Body: ' . json_encode($request_body, JSON_UNESCAPED_SLASHES));
        $this->logger->debug('Server UTC now: ' . gmdate('Y-m-d\TH:i:s\Z'));

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post(
                $this->base_url . $components['request_target'],
                [
                    'headers' => $headers,
                    'body'    => json_encode($request_body, JSON_UNESCAPED_SLASHES)
                ]
            );
        } catch (\Throwable $e) {
            $this->logger->error('DOKU Create Payment Exception: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $status = $response->getStatusCode();
        $body = $response->getBody();

        if ($status >= 200 && $status < 300) {
            return json_decode($body, true);
        } else {
            $this->logger->error('DOKU Create Payment Error: HTTP ' . $status . ' Body: ' . $body);
            return [
                'error' => true,
                'message' => 'HTTP ' . $status,
                'response_body' => $body
            ];
        }
    }
}
