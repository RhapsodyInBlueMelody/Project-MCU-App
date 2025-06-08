<?php

if (!function_exists('generate_doku_signature')) {
    function generate_doku_signature($components, $secret_key, $request_body)
    {
        $json_body = json_encode($request_body, JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(hash('sha256', $json_body, true));
        $string_to_sign = implode("\n", [
            'Client-Id:' . $components['client_id'],
            'Request-Id:' . $components['request_id'],
            'Request-Timestamp:' . $components['request_timestamp'],
            'Request-Target:' . $components['request_target'],
            'Digest:' . $digest
        ]);
        $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $secret_key, true));
        return 'HMACSHA256=' . $signature;
    }
}
