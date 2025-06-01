<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PatientModel;
use App\Models\DokterModel;
use App\Models\AdminModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Google as GoogleConfig;
use Google\Client;
use Google\Service\Oauth2;

class AuthController extends BaseController
{
    protected $session;
    protected $userModel;
    protected $patientModel;
    protected $dokterModel;
    protected $adminModel;
    protected $googleConfig;
    protected $client;

    /**
     * Constructor sets up dependencies and Google client
     */
    public function __construct()
    {
        helper(["form", "url", "text"]);

        $this->session = session();
        $this->userModel = new UserModel();
        $this->patientModel = new PatientModel();
        $this->dokterModel = new DokterModel();
        $this->adminModel = new AdminModel();

        try {
            $this->googleConfig = new GoogleConfig();
            $this->client = new Client();
            $this->client->setClientId($this->googleConfig->clientId);
            $this->client->setClientSecret($this->googleConfig->clientSecret);
            $this->client->addScope("email");
            $this->client->addScope("profile");
        } catch (\Exception $e) {
            log_message(
                "error",
                "Google client initialization error: " . $e->getMessage()
            );
        }
    }


    /**
     * Patient login page
     */
    public function patientLogin()
    {
        // If already logged in as patient, redirect to dashboard
        if (
            $this->session->get("role") === "patient" &&
            $this->session->get("isLoggedIn")
        ) {
            return redirect()->to("/patient/dashboard");
        }

        $this->session->set("intended_role", "patient");
        $data = ["title" => "Patient Login"];
        return view("auth/login/patient", $data);
    }

    /**
     * Doctor login page
     */
    public function doctorLogin()
    {
        // If already logged in as doctor, redirect to dashboard
        if (
            $this->session->get("role") === "doctor" &&
            $this->session->get("isLoggedIn")
        ) {
            return redirect()->to("/doctor/dashboard");
        }

        $this->session->set("intended_role", "doctor");
        $data["title"] = "Doctor Login";
        return view("auth/login/doctor", $data);
    }

    /**
     * Admin login page
     */
    public function adminLogin()
    {
        // If already logged in as admin, redirect to dashboard
        if (
            $this->session->get("role") === "admin" &&
            $this->session->get("isLoggedIn")
        ) {
            return redirect()->to("/admin/dashboard");
        }

        $data["title"] = "Admin Login";
        return view("auth/login/admin", $data);
    }

    /**
     * Authenticates users based on role
     */
    public function authenticate(): RedirectResponse
    {
        // Validate CSRF token
        if (!$this->validateRequest()) {
            return redirect()
                ->back()
                ->with("error", "Invalid form submission.");
        }

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $role = $this->request->getPost("role");

        // Validate required fields
        if (empty($username) || empty($password) || empty($role)) {
            return redirect()
                ->to("auth/{$role}/login")
                ->with("error", "Username and Password are required.");
        }

        // Sanitize inputs
        $username = trim(htmlspecialchars($username));

        // Authenticate based on role
        $authenticated = false;
        $redirectUrl = "/";

        switch ($role) {
            case "patient":
                $authenticated = $this->authenticatePatient(
                    $username,
                    $password
                );
                $redirectUrl = "/patient/dashboard";
                break;
            case "doctor":
                $authenticated = $this->authenticateDoctor(
                    $username,
                    $password
                );
                $redirectUrl = "/doctor/dashboard";
                break;
            case "admin":
                $authenticated = $this->authenticateAdmin($username, $password);
                $redirectUrl = "/admin/dashboard";
                break;
            default:
                return redirect()
                    ->to("/")
                    ->with("error", "Invalid role specified.");
        }

        if ($authenticated) {
            // Log successful login
            log_message(
                "info",
                "User '{$username}' successfully logged in as {$role}"
            );
            return redirect()->to($redirectUrl);
        } else {
            // Log failed login attempt
            log_message(
                "warning",
                "Failed login attempt for user '{$username}' as {$role}"
            );
            return redirect()
                ->to("auth/{$role}/login")
                ->with("error", "Invalid username or password.");
        }
    }

    /**
     * Patient authentication method
     */
    private function authenticatePatient(
        string $username,
        string $password
    ): bool {
        $user = $this->userModel
            ->where("role", "patient")
            ->where(function ($query) use ($username) {
                $query
                    ->where("username", $username)
                    ->orWhere("email", $username);
            })
            ->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user["password"])) {
            return false;
        }

        // Get patient profile
        $patient = $this->patientModel
            ->where("user_id", $user["user_id"])
            ->first();

        if (!$patient) {
            return false;
        }

        // Set session data
        $this->session->set([
            "user_id" => $user["user_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => "patient",
            "patient_id" => $patient["PASIEN_ID"],
            "full_name" => $patient["NAMA_LENGKAP"],
            "isLoggedIn" => true,
        ]);

        return true;
    }

    /**
     * Doctor authentication method
     */
    private function authenticateDoctor(
        string $username,
        string $password
    ): bool {
        $user = $this->userModel
            ->where("role", "doctor")
            ->where(function ($query) use ($username) {
                $query
                    ->where("username", $username)
                    ->orWhere("email", $username);
            })
            ->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user["password"])) {
            return false;
        }

        // Get doctor profile and check verification status
        $doctor = $this->dokterModel
            ->where("user_id", $user["user_id"])
            ->first();

        if (!$doctor) {
            return false;
        }

        // Check if doctor is verified
        if (
            $doctor["is_verified"] != 1 ||
            $doctor["verification_status"] !== "approved"
        ) {
            $statusMessage = "Your account has not been verified yet.";

            if ($doctor["verification_status"] === "rejected") {
                $statusMessage =
                    "Your account verification was rejected. Reason: " .
                    ($doctor["verification_notes"] ?? "No reason provided.");
            }

            session()->setFlashdata("error", $statusMessage);
            return false;
        }

        // Set session data
        $this->session->set([
            "user_id" => $user["user_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => "doctor",
            "doctor_id" => $doctor["ID_DOKTER"],
            "full_name" => $doctor["NAMA_DOKTER"],
            "specialization" => $doctor["id_spesialisasi"],
            "isLoggedIn" => true,
        ]);

        return true;
    }

    /**
     * Admin authentication method
     */
    private function authenticateAdmin(string $username, string $password): bool
    {
        $user = $this->userModel
            ->where("role", "admin")
            ->where(function ($query) use ($username) {
                $query
                    ->where("username", $username)
                    ->orWhere("email", $username);
            })
            ->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user["password"])) {
            return false;
        }

        // Check if admin record exists
        $admin = $this->adminModel->where("user_id", $user["user_id"])->first();

        if (!$admin) {
            return false;
        }

        // Set session data
        $this->session->set([
            "user_id" => $user["user_id"],
            "admin_id" => $admin["admin_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "name" => $admin["name"],
            "role" => "admin",
            "role_type" => $admin["role_type"],
            "isLoggedIn" => true,
        ]);

        return true;
    }

    /**
     * Default register method redirects to patient registration
     */
    public function register(): RedirectResponse
    {
        return redirect()->to("/auth/register/patient");
    }

    /**
     * Patient registration form
     */
    public function registerPatient()
    {
        $data = [
                "title" => "Patient Register",
                "google_data" => [
                    "intended_role" => "patient"
                ]
            ];
        return view("auth/register/patient", $data);
    }

    /**
     * Doctor registration form
     */
    public function registerDoctor()
    {
        $spesialisasiModel = new \App\Models\SpesialisasiModel();
        $data["specializations"] = $spesialisasiModel->findAll();
        $data["title"] = "Doctor Registration";

        return view("auth/register/doctor", $data);
    }

    /**
     * Handles doctor registration
     */
    public function saveDoctorRegistration()
    {
        // Validate CSRF token
        if (!$this->validateRequest()) {
            return redirect()
                ->back()
                ->with("error", "Invalid form submission.");
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            "nama_dokter" => "required|min_length[3]|max_length[255]",
            "email" => "required|valid_email|is_unique[Users.email]",
            "username" =>
                "required|alpha_numeric_space|min_length[3]|is_unique[Users.username]",
            "password" => "required|min_length[8]|matches[password_confirm]",
            "password_confirm" => "required",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[Dokter.NO_LISENSI]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user account with sanitized inputs
            $userData = [
                "username" => htmlspecialchars(
                    $this->request->getPost("username"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "email" => filter_var(
                    $this->request->getPost("email"),
                    FILTER_SANITIZE_EMAIL
                ),
                "password" => password_hash(
                    $this->request->getPost("password"),
                    PASSWORD_DEFAULT
                ),
                "role" => "doctor",
                "is_active" => 1,
                "created_at" => date("Y-m-d H:i:s"),
            ];

            $userId = $this->userModel->insert($userData);

            // Create doctor profile
            $doctorData = [
                "user_id" => $userId,
                "NAMA_DOKTER" => htmlspecialchars(
                    $this->request->getPost("nama_dokter"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "id_spesialisasi" => (int) $this->request->getPost(
                    "id_spesialisasi"
                ),
                "NO_TELP_DOKTER" => htmlspecialchars(
                    $this->request->getPost("no_telp_dokter"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "NO_LISENSI" => htmlspecialchars(
                    $this->request->getPost("no_lisensi"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "is_verified" => 0,
                "verification_status" => "pending",
                "created_by" => $userId,
                "updated_by" => $userId,
            ];

            $this->dokterModel->insert($doctorData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed");
            }

            // Send verification email if needed (implement in production)
            // $this->sendVerificationEmail($userData['email']);

            return redirect()
                ->to("auth/doctor/login")
                ->with(
                    "success",
                    "Registration successful! Your account is pending verification. We will contact you via email once your account has been verified."
                );
        } catch (\Exception $e) {
            $db->transRollback();
            log_message(
                "error",
                "Doctor registration failed: " . $e->getMessage()
            );
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    "error",
                    "Registration failed. Please try again later or contact support."
                );
        }
    }

    /**
     * Google login initiation
     */
    public function googleLogin($role = "patient")
    {
        // Validate role
        if (!in_array($role, ["patient", "doctor", "admin"])) {
            return redirect()
                ->to("auth/login")
                ->with("error", "Invalid role specified for Google login.");
        }

        // Store intended role in session
        $this->session->set("intended_role", $role);

        // Set the redirect URI based on role
        $redirectUri = base_url("auth/google/callback/{$role}");
        $this->client->setRedirectUri($redirectUri);

        // Generate the authorization URL and redirect
        $authUrl = $this->client->createAuthUrl();
        return redirect()->to($authUrl);
    }

    /**
     * Google callback handler
     */
    public function googleCallback($role = "patient")
    {
        // Validate role
        if (!in_array($role, ["patient", "doctor", "admin"])) {
            return redirect()
                ->to("auth/login")
                ->with("error", "Invalid role specified for Google login.");
        }

        // Set the correct redirect URI
        $redirectUri = base_url("auth/google/callback/{$role}");
        $this->client->setRedirectUri($redirectUri);

        $code = $this->request->getGet("code");

        if (!$code) {
            log_message("error", "No code received from Google");
            return redirect()
                ->to("auth/{$role}/login")
                ->with(
                    "error",
                    "Google Sign-In failed: No authorization code received."
                );
        }

        try {
            // Exchange authorization code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token["error"])) {
                log_message("error", "Google token error: " . $token["error"]);
                return redirect()
                    ->to("auth/{$role}/login")
                    ->with(
                        "error",
                        "Google authentication error: " . $token["error"]
                    );
            }

            $this->client->setAccessToken($token);

            // Get user profile information
            $googleService = new Oauth2($this->client);
            $userInfo = $googleService->userinfo->get();

            $email = $userInfo->getEmail();
            $name = $userInfo->getName();
            $googleId = $userInfo->getId();
            $picture = $userInfo->getPicture();

            // Check if user exists by Google ID
            $existingUser = $this->userModel
                ->where("google_id", $googleId)
                ->first();

            if ($existingUser) {
                // User exists, check role
                if ($existingUser["role"] !== $role) {
                    return redirect()
                        ->to("auth/{$role}/login")
                        ->with(
                            "error",
                            "This Google account is associated with a different role."
                        );
                }

                // If doctor, verify account status
                if ($role === "doctor") {
                    $doctor = $this->dokterModel
                        ->where("user_id", $existingUser["user_id"])
                        ->first();

                    if (!$doctor) {
                        return redirect()
                            ->to("auth/doctor/login")
                            ->with("error", "Doctor profile not found.");
                    }

                    if (
                        $doctor["verification_status"] !== "approved" ||
                        $doctor["is_verified"] != 1
                    ) {
                        $message = "Your account is pending verification.";

                        if ($doctor["verification_status"] === "rejected") {
                            $message =
                                "Your account verification was rejected. Reason: " .
                                ($doctor["verification_notes"] ??
                                    "No reason provided.");
                        }

                        return redirect()
                            ->to("auth/doctor/login")
                            ->with("error", $message);
                    }
                }

                // Set session data and redirect
                $this->setUserSession($existingUser);
                return redirect()->to($this->getRedirectUrl($role));
            }

            // Check if user exists by email
            $existingUserByEmail = $this->userModel
                ->where("email", $email)
                ->first();

            if ($existingUserByEmail) {
                // Update with Google ID
                $this->userModel->update($existingUserByEmail["user_id"], [
                    "google_id" => $googleId,
                ]);

                // Same role checks as above
                if ($existingUserByEmail["role"] !== $role) {
                    return redirect()
                        ->to("auth/{$role}/login")
                        ->with(
                            "error",
                            "This email is associated with a different role."
                        );
                }

                if ($role === "doctor") {
                    $doctor = $this->dokterModel
                        ->where("user_id", $existingUserByEmail["user_id"])
                        ->first();

                    if (
                        !$doctor ||
                        $doctor["verification_status"] !== "approved" ||
                        $doctor["is_verified"] != 1
                    ) {
                        return redirect()
                            ->to("auth/doctor/login")
                            ->with(
                                "error",
                                "Your doctor account is not verified or was rejected."
                            );
                    }
                }

                $this->setUserSession($existingUserByEmail);
                return redirect()->to($this->getRedirectUrl($role));
            }

            // New user - store data in session for additional registration
            $googleData = [
                "email" => $email,
                "name" => $name,
                "google_id" => $googleId,
                "picture" => $picture,
            ];

            $this->session->set("google_data", $googleData);

            // Redirect to role-specific social registration
            return redirect()->to("auth/register/social/{$role}");
        } catch (\Exception $e) {
            log_message("error", "Google callback error: " . $e->getMessage());
            return redirect()
                ->to("auth/{$role}/login")
                ->with(
                    "error",
                    "Failed to sign in with Google: " . $e->getMessage()
                );
        }
    }

    /**
     * Social registration form
     */
    public function registerSocial($role = "patient")
    {
        $googleData = $this->session->get('google_data');
        // Prefer role from google_data, then from session, fallback to URL param
        $role = $googleData['intended_role'] ?? $this->session->get('intended_role') ?? $role;
    
        $data["google_data"] = $googleData;
        $data["role"] = $role;
        $data["title"] = "Complete Social Registration";
    
        return view("auth/complete_social_registration", $data);
    }

    /**
     * Complete doctor social registration
     */
    public function completeDoctorSocialRegistration()
    {
        // Validate CSRF token
        if (!$this->validateRequest()) {
            return redirect()
                ->back()
                ->with("error", "Invalid form submission.");
        }

        $googleData = $this->session->get("google_data");

        if (!$googleData) {
            return redirect()
                ->to("auth/doctor/login")
                ->with(
                    "error",
                    "No social registration data found. Please try again."
                );
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            "username" =>
                "required|alpha_numeric_space|min_length[3]|is_unique[Users.username]",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[Dokter.NO_LISENSI]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user
            $userData = [
                "username" => htmlspecialchars(
                    $this->request->getPost("username"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "email" => $googleData["email"],
                "google_id" => $googleData["google_id"],
                "password" => password_hash(
                    random_string("alnum", 16),
                    PASSWORD_DEFAULT
                ), // Generate a random password
                "role" => "doctor",
                "is_active" => 1,
                "created_at" => date("Y-m-d H:i:s"),
            ];

            $userId = $this->userModel->insert($userData);

            // Create doctor profile
            $doctorData = [
                "user_id" => $userId,
                "NAMA_DOKTER" => $googleData["name"],
                "id_spesialisasi" => (int) $this->request->getPost(
                    "id_spesialisasi"
                ),
                "NO_TELP_DOKTER" => htmlspecialchars(
                    $this->request->getPost("no_telp_dokter"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "NO_LISENSI" => htmlspecialchars(
                    $this->request->getPost("no_lisensi"),
                    ENT_QUOTES,
                    "UTF-8"
                ),
                "is_verified" => 0,
                "verification_status" => "pending",
                "created_by" => $userId,
                "updated_by" => $userId,
            ];

            $this->dokterModel->insert($doctorData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed");
            }

            // Clear the temporary data
            $this->session->remove("google_data");

            return redirect()
                ->to("auth/doctor/login")
                ->with(
                    "success",
                    "Registration successful! Your account is pending verification. We will contact you once your account has been verified."
                );
        } catch (\Exception $e) {
            $db->transRollback();
            log_message(
                "error",
                "Doctor social registration failed: " . $e->getMessage()
            );
            return redirect()
                ->back()
                ->withInput()
                ->with("error", "Registration failed: " . $e->getMessage());
        }
    }

    /**
     * Complete social patient registration
     */
    public function completeSocialRegistration()
    {
        // Validate form inputs
        $validation = \Config\Services::validation();
        $validation->setRules([
            "email" => "required|valid_email",
            "username" =>
                "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]",
            "password" => "required|min_length[8]",
            "confirm_password" => "required|matches[password]",
            "nama_pasien" => "required|min_length[3]",
            "jenis_kelamin" => "required|in_list[L,P]",
            "no_telp_pasien" => "required|numeric|min_length[10]",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required|valid_date",
            "alamat" => "required",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->with("error", implode("<br>", $validation->getErrors()))
                ->withInput();
        }

        // Get the form data
        $email = $this->request->getPost("email");
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user account
            $userModel = new \App\Models\UserModel();
            $userData = [
                "username" => $username,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT), // Hash the password
                "role" => "patient",
                "status" => "active",
            ];

            $userModel->insert($userData);
            $userId = $userModel->getInsertID();

            // Create patient profile
            $patientModel = new \App\Models\PatientModel();
            $patientData = [
                "user_id" => $userId,
                "nama_pasien" => $this->request->getPost("NAMA_PASIEN"),
                "jenis_kelamin" => $this->request->getPost("jenis_kelamin"),
                "no_telp_pasien" => $this->request->getPost("no_telp_pasien"),
                "tempat_lahir" => $this->request->getPost("tempat_lahir"),
                "tanggal_lahir" => $this->request->getPost("tanggal_lahir"),
                "alamat" => $this->request->getPost("alamat"),
                "email" => $email,
            ];

            $patientModel->insert($patientData);
            $patientId = $patientModel->getInsertID();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed.");
            }

            // Set up the patient session
            $session = session();
            $sessionData = [
                "user_id" => $userId,
                "username" => $username,
                "email" => $email,
                "role" => "patient",
                "patient_id" => $patientId,
                "is_logged_in" => true,
            ];
            $session->set($sessionData);

            return redirect()
                ->to("/patient/dashboard")
                ->with(
                    "success",
                    "Registration completed successfully. Welcome to our healthcare system!"
                );
        } catch (\Exception $e) {
            $db->transRollback();
            log_message("error", "Registration error: " . $e->getMessage());

            return redirect()
                ->back()
                ->with(
                    "error",
                    "Registration failed: " .
                        (ENVIRONMENT === "production"
                            ? "A system error occurred."
                            : $e->getMessage())
                )
                ->withInput();
        }
    }
    /**
     * User logout
     */
    public function logout($role = null)
    {
        // Validate role
        if (!in_array($role, ["patient", "doctor", "admin"])) {
            return redirect()
                ->to("/")
                ->with("error", "Invalid role specified.");
        }

        // Clear all session data
        $this->session->destroy();

        return redirect()
            ->to("auth/{$role}/login")
            ->with("success", "You have been successfully logged out.");
    }

    /**
     * Set user session data helper
     */
    private function setUserSession($user)
    {
        // Implementation based on user role
        $sessionData = [
            "user_id" => $user["user_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "isLoggedIn" => true,
        ];

        // Add role-specific session data
        if ($user["role"] === "patient") {
            $patient = $this->patientModel
                ->where("user_id", $user["user_id"])
                ->first();
            if ($patient) {
                $sessionData["patient_id"] = $patient["PASIEN_ID"];
                $sessionData["full_name"] =
                    $patient["NAMA_LENGKAP"] ?? $user["username"];
            }
        } elseif ($user["role"] === "doctor") {
            $doctor = $this->dokterModel
                ->where("user_id", $user["user_id"])
                ->first();
            if ($doctor) {
                $sessionData["doctor_id"] = $doctor["ID_DOKTER"];
                $sessionData["full_name"] =
                    $doctor["NAMA_DOKTER"] ?? $user["username"];
                $sessionData["specialization"] = $doctor["id_spesialisasi"];
            }
        } elseif ($user["role"] === "admin") {
            $admin = $this->adminModel
                ->where("user_id", $user["user_id"])
                ->first();
            if ($admin) {
                $sessionData["admin_id"] = $admin["admin_id"];
                $sessionData["name"] = $admin["name"] ?? $user["username"];
                $sessionData["role_type"] = $admin["role_type"];
            }
        }

        // Log session data for debugging (consider removing in production)
        log_message(
            "debug",
            "Setting session data for user: " . json_encode($sessionData)
        );

        // Set the session data
        $this->session->set($sessionData);
    }

    /**
     * Get redirect URL based on role
     */
    private function getRedirectUrl(string $role): string
    {
        switch ($role) {
            case "patient":
                return "/patient/dashboard";
            case "doctor":
                return "/doctor/dashboard";
            case "admin":
                return "/admin/dashboard";
            default:
                return "/";
        }
    }

    /**
     * Validate CSRF token and other request validations
     */
    private function validateRequest(): bool
    {
        // Check CSRF token
        return $this->request->hasValidCSRFToken();
    }

    /**
     * Generate unique username from a name
     */
    private function generateUsername(string $name): string
    {
        // Remove special characters and spaces
        $baseUsername = preg_replace("/[^a-zA-Z0-9]/", "", strtolower($name));

        // Ensure minimum length
        if (strlen($baseUsername) < 3) {
            $baseUsername .= random_string("alnum", 3);
        }

        // Check if username exists
        $existingUser = $this->userModel
            ->where("username", $baseUsername)
            ->first();

        if (!$existingUser) {
            return $baseUsername;
        }

        // Add a random number until unique
        $i = 1;
        $username = $baseUsername . $i;

        while ($this->userModel->where("username", $username)->first()) {
            $i++;
            $username = $baseUsername . $i;
        }

        return $username;
    }

    /**
     * Send verification email to doctors (placeholder for implementation)
     */
    private function sendDoctorVerificationEmail(
        string $email,
        string $name,
        string $token
    ): bool {
        // Implementation depends on your email service
        try {
            $email = \Config\Services::email();

            $email->setFrom(
                "no-reply@medicalcheckup.com",
                "Medical Checkup System"
            );
            $email->setTo($email);
            $email->setSubject("Doctor Account Verification");

            $message = "Dear Dr. {$name},\n\n";
            $message .=
                "Thank you for registering with our Medical Checkup System. ";
            $message .=
                "Your account is currently being reviewed. We will notify you once your account has been verified.\n\n";
            $message .= "Best regards,\nMedical Checkup Team";

            $email->setMessage($message);

            return $email->send();
        } catch (\Exception $e) {
            log_message("error", "Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
