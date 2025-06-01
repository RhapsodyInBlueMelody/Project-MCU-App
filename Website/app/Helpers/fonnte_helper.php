<?php

if (!function_exists('send_whatsapp_message')) {
    function send_whatsapp_message($target, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . getenv('FONNTE_TOKEN'),
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['success' => false, 'error' => $error_msg];
        }

        curl_close($curl);
        return ['success' => true, 'response' => $response];
    }
}

