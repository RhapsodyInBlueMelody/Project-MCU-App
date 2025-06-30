<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Google extends BaseConfig
{
    /**
     * Google OAuth Client ID
     *
     * @var string
     */
    public string $clientId = '';

    /**
     * Google OAuth Client Secret
     *
     * @var string
     */
    public string $clientSecret = '';

    /**
     * Default redirect URI
     *
     * @var string
     */
    public string $redirectUri = '';

    /**
     * Requested OAuth scopes
     *
     * @var array
     */
    public array $scopes = ["email", "profile"]; // Add any other scopes you need

    /**
     * Initialize the configuration
     */
    public function __construct()
    {
        parent::__construct();

        $this->clientId = getenv("GOOGLE_CLIENT_ID") ?: '';
        $this->clientSecret = getenv("GOOGLE_CLIENT_SECRET") ?: '';
        $this->redirectUri = site_url("auth/google/callback");
    }
}
