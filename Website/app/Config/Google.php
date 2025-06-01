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
    public string $clientId = "835532383930-4g8p3qoo8sos4o0i4mktfnn8lt1evssc.apps.googleusercontent.com";

    /**
     * Google OAuth Client Secret
     *
     * @var string
     */
    public string $clientSecret = "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";

    /**
     * Default redirect URI
     *
     * @var string
     */
    public string $redirectUri = "http://localhost:8080/index.php/auth/google/callback";

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

        // For security, load these from environment variables in production
        if (getenv("GOOGLE_CLIENT_ID")) {
            $this->clientId = getenv("GOOGLE_CLIENT_ID");
        }

        if (getenv("GOOGLE_CLIENT_SECRET")) {
            $this->clientSecret = getenv("GOOGLE_CLIENT_SECRET");
        }

        // Set dynamic redirect URI based on current environment
        $this->redirectUri = site_url("auth/google/callback");
    }
}
