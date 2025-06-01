<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Base controller for authenticated sections
 */
class AuthenticatedController extends Controller
{
    /**
     * Required role for this controller
     */
    protected $requiredRole;

    /**
     * Login redirect URL if not authenticated
     */
    protected $loginRedirectUrl;

    /**
     * Session instance
     */
    protected $session;

    /**
     * Initialize controller, set up role requirements and redirects
     */
    public function __construct(string $role, string $loginRedirect)
    {
        $this->requiredRole = $role;
        $this->loginRedirectUrl = $loginRedirect;
        $this->session = \Config\Services::session();

        // Check authentication
        $this->checkAuthentication();
    }

    /**
     * Initialize controller
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // Additional initialization if needed
    }

    /**
     * Check if user is authenticated with the correct role
     */
    protected function checkAuthentication()
    {
        // Skip in CLI environment (for testing/commands)
        if (is_cli()) {
            return;
        }

        // Check if user is logged in
        if (!$this->session->get("isLoggedIn")) {
            $this->redirectToLogin(
                "You must be logged in to access this area."
            );
        }

        // Check if user has the required role
        if ($this->session->get("role") !== $this->requiredRole) {
            $this->redirectToLogin(
                "You do not have permission to access this area."
            );
        }
    }

    /**
     * Redirect to login page with error message
     */
    protected function redirectToLogin(string $message = "")
    {
        if ($message) {
            $this->session->setFlashdata("error", $message);
        }

        header("Location: " . site_url($this->loginRedirectUrl));
        exit();
    }

    /**
     * Log user activity
     */
    protected function logActivity(
        string $action,
        string $description,
        array $additionalData = []
    ) {
        $activityData = [
            "user_id" => $this->session->get("user_id"),
            "role" => $this->session->get("role"),
            "action" => $action,
            "description" => $description,
            "ip_address" => $this->request->getIPAddress(),
            "user_agent" => $this->request->getUserAgent()->getAgentString(),
            "additional_data" => json_encode($additionalData),
            "created_at" => date("Y-m-d H:i:s"),
        ];

        // Log to database if you have an activity log model
        // $activityLogModel = model('ActivityLogModel');
        // $activityLogModel->insert($activityData);

        // Also log to system log
        log_message(
            "info",
            "{$action}: {$description} by User ID: {$activityData["user_id"]} ({$activityData["role"]})"
        );
    }
}
