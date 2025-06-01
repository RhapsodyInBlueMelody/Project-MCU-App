<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Authentication filter for role-based access control
 */
class AuthFilter implements FilterInterface
{
    /**
     * Verify authentication and role before controller execution
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get session instance
        $session = session();

        // Check if a specific role is required (from $arguments)
        $requiredRole = $arguments[0] ?? null;

        // Check if user is logged in
        if (!$session->get("isLoggedIn")) {
            return redirect()
                ->to("auth/{$requiredRole}/login")
                ->with("error", "Please log in to access this page.");
        }

        // If specific role is required, check user's role
        if ($requiredRole && $session->get("role") !== $requiredRole) {
            return redirect()
                ->to("auth/{$session->get("role")}/login")
                ->with(
                    "error",
                    "You do not have permission to access this page."
                );
        }

        // Authentication passed, continue
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
        // Post-processing if needed
        return $response;
    }
}
