<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Security filter class for CSRF and other security measures
 */
class SecurityFilter implements FilterInterface
{
    /**
     * Apply security measures before controller execution
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip for specific routes or API endpoints if needed

        // Add security headers
        $this->addSecurityHeaders();

        return $request;
    }

    /**
     * Process after controller execution (if needed)
     */
    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Any post-processing if needed
        return $response;
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders()
    {
        // Content Security Policy headers
        $csp =
            "default-src 'self'; " .
            "script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com 'unsafe-inline'; " .
            "style-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; " .
            "font-src 'self' https://cdnjs.cloudflare.com; " .
            "img-src 'self' data: https://upload.wikimedia.org https://cdnjs.cloudflare.com; " .
            "connect-src 'self'";

        header("Content-Security-Policy: $csp");
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
}
