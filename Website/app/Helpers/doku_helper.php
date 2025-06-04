<?php

if (!function_exists('generate_doku_signature')) {
    function generate_doku_signature($components, $secret_key, $request_body)
    {
        // Convert request request_body to JSON
        $json_body = json_encode($request_body);

        // Generate Digest
        $digest = base64_encode(hash('sha256', $json_body, true));

        // Create string to sign 
        $string_to_sign = implode('|', [
            "Client-Id:{$components['client_id']}",
            "Request-Id:{$components['request_id']}",
            "Request-Timestamp:{$components['request_timestamp']}",
            "Request-Target:{components['request_target']}",
            "Digest:{$digest}",
        ]);

        //Generate signature 
        $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));

        return "HMACSHA256={$signature}";
    }
}

if (!function_exists('generate_uuid')) {
    function generate_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
?>
