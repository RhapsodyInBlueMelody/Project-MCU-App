<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class DokuPayment
{
    private $client_id;
    private $secret_key;
    private $base_url;
    
    public function __construct()
    {
        // Load from .env or config
        $this->client_id = 'BRN-0216-1747201336667'; // Best to put in .env
        $this->secret_key = 'SK-9aFaeTyPyx2rN0wJGRB4'; // Best to put in .env
        $this->base_url = 'https://api-sandbox.doku.com'; // Change to production URL when ready
    }
    
    public function createPayment($order_data)
    {
        helper('doku'); // Load the helper
        
        // Prepare components for signature
        $components = [
            'client_id' => $this->client_id,
            'request_id' => generate_uuid(),
            'request_timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'request_target' => '/checkout/v1/payment'
        ];
        
        // Prepare request body
        $request_body = [
            'order' => [
                'amount' => $order_data['amount'],
                'invoice_number' => $order_data['invoice_number'],
                'currency' => 'IDR',
                'line_items' => $order_data['items'] ?? []
            ],
            'payment' => [
                'payment_due_date' => 60 // 60 minutes
            ]
        ];
        
        // Generate signature
        $signature = generate_doku_signature($components, $this->secret_key, $request_body);
        
        // Prepare headers
        $headers = [
            'Client-Id' => $components['client_id'],
            'Request-Id' => $components['request_id'],
            'Request-Timestamp' => $components['request_timestamp'],
            'Signature' => $signature,
            'Content-Type' => 'application/json'
        ];
        
        try {
            // Create CURL request
            $client = Services::curlrequest();
            
            $response = $client->setHeaders($headers)
                             ->setBody(json_encode($request_body))
                             ->post($this->base_url . $components['request_target']);
            
            return json_decode($response->getBody(), true);
            
        } catch (\Exception $e) {
            log_message('error', 'DOKU Payment Error: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
