<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Doku extends BaseConfig
{
    public $clientId = '';
    public $secretKey = '';
    public $baseUrl = '';
    public $isProduction = false;
    
    public function __construct()
    {
        parent::__construct();
        
        // Load from .env
        $this->clientId = getenv('DOKU_CLIENT_ID') ?: $this->clientId;
        $this->secretKey = getenv('DOKU_SECRET_KEY') ?: $this->secretKey;
        $this->baseUrl = $this->isProduction 
            ? 'https://api.doku.com'
            : 'https://api-sandbox.doku.com';
    }
}
