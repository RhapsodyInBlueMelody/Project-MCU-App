<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasienModel;
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
    protected $pasienModel;
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
        $this->pasienModel = new PasienModel();
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
     * pasien login page
     */
    public function pasienLogin()
    {
        // If already logged in as pasien, redirect to dashboard
        if (
            $this->session->get("role") === "pasien" &&
            $this->session->get("isLoggedIn")
        ) {
            return redirect()->to("/pasien/dashboard");
        }

        $this->session->set("intended_role", "pasien");
        $data = ["title" => "pasien Login"];
        return view("auth/login/pasien", $data);
    }

    /**
     * Doctor login page
     */
    public function doctorLogin()
    {
        // If already logged in as doctor, redirect to dashboard
        if (
            $this->session->get("role") === "dokter" &&
            $this->session->get("isLoggedIn")
        ) {
            return redirect()->to("/dokter/dashboard");
        }

        $this->session->set("intended_role", "dokter");
        $data["title"] = "Doctor Login";
        return view("auth/login/dokter", $data);
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
            case "pasien":
                $authenticated = $this->authenticatePasien($username, $password);
                $redirectUrl = "/pasien/dashboard";
                break;
            case "dokter":
                $authenticated = $this->authenticateDoctor($username, $password);
                $redirectUrl = "/dokter/dashboard";
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
            log_message("info", "User '{$username}' successfully logged in as {$role}");
            return redirect()->to($redirectUrl);
        } else {
            // Only set a generic error if a specific error wasn't already set
            if (!session()->getFlashdata("error")) {
                session()->setFlashdata("error", "Invalid username or password.");
            }
            // Log failed login attempt
            log_message(
                "warning",
                "Failed login attempt for user '{$username}' as {$role}"
            );
            return redirect()
                ->to("auth/{$role}/login");
        }
    }

    /**
     * pasien authentication method
     */
    private function authenticatePasien(string $login, string $password): bool
    {
        $user = $this->userModel->getUserByEmailOrUsername($login, 'pasien');
        // Debug log: see what you get from the DB
        log_message('debug', 'Login query for [' . $login . '] returned: ' . print_r($user, true));

        if (!$user) {
            // Set a session flash message for user not found
            session()->setFlashdata('msg', 'Username or email not found.');
            return false;
        }
        if (empty($user["password"])) {
            session()->setFlashdata('msg', 'Password is not set for this account.');
            return false;
        }
        if (!password_verify($password, $user["password"])) {
            // Set a session flash message for wrong password
            session()->setFlashdata('msg', 'Incorrect password for this account.');
            return false;
        }
        // Get pasien profile
        $pasien = $this->pasienModel
            ->where("user_id", $user["user_id"])
            ->first();
        if (!$pasien) {
            session()->setFlashdata('msg', 'pasien profile not found.');
            return false;
        }
        // Set session data
        $this->session->set([
            "user_id"    => $user["user_id"],
            "username"   => $user["username"],
            "email"      => $user["email"],
            "role"       => "pasien",
            "id_pasien" => $pasien["id_pasien"],
            "nama_pasien"  => $pasien["nama_pasien"],
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
        $user = $this->userModel->authenticateDoctor($username, $password);

        if (!$user) {
            // Only set error if not already set (e.g., by password check inside model)
            if (!session()->getFlashdata('error')) {
                session()->setFlashdata("error", "Paswword atau Username Salah.");
            }
            return false;
        }

        $doctorProfile = $this->dokterModel->getVerifiedDoctorProfile($user["user_id"]);

        if (isset($doctorProfile["error"])) {
            if ($doctorProfile["error"] === "not_found") {
                $statusMessage = "Akun Tidak Ditemukan.";
            } elseif ($doctorProfile["error"] === "not_verified") {
                $statusMessage = "Akunmu Sedang Menunggu Verifikasi.";
            } elseif ($doctorProfile["error"] === "rejected") {
                $statusMessage = "Verifikasi Akun Anda Tertolak. Alasan: " . ($doctorProfile["verification_notes"] ?? "No reason provided.");
            } else {
                $statusMessage = "An unexpected error occurred while fetching your profile.";
            }
            session()->setFlashdata("error", $statusMessage);
            return false;
        }

        // Set session data
        $this->session->set([
            "user_id" => $user["user_id"],
            "doctor_id" => $doctorProfile["id_dokter"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => "dokter",
            "nama_dokter" => $doctorProfile["nama_dokter"],
            "id_spesialisasi" => $doctorProfile["id_spesialisasi"],
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
     * Default register method redirects to pasien registration
     */
    public function register(): RedirectResponse
    {
        return redirect()->to("/auth/register/pasien");
    }

    /**
     * pasien registration form
     */
    public function registerpasien()
    {
        $data = [
            "title" => "pasien Register",
            "google_data" => [
                "intended_role" => "pasien"
            ]
        ];
        return view("auth/register/pasien", $data);
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
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            "nama_dokter" => "required|min_length[3]|max_length[255]",
            "email" => "required|valid_email|is_unique[users.email]",
            "username" => "required|alpha_numeric_space|min_length[3]|is_unique[users.username]",
            "password" => "required|min_length[8]|matches[password_confirm]",
            "password_confirm" => "required",
            "no_telp_dokter" => "required|numeric|min_length[10]",
            "id_spesialisasi" => "required|numeric",
            "no_lisensi" => "required|is_unique[dokter.no_lisensi]",
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        // Collect registration data
        $registrationData = [
            'username' => $this->request->getPost("username"),
            'email' => $this->request->getPost("email"),
            'password' => $this->request->getPost("password"),
            'nama_dokter' => $this->request->getPost("nama_dokter"),
            'id_spesialisasi' => $this->request->getPost("id_spesialisasi"),
            'no_telp_dokter' => $this->request->getPost("no_telp_dokter"),
            'no_lisensi' => $this->request->getPost("no_lisensi"),
        ];

        try {
            // Use DokterModel to handle the complete registration process
            $result = $this->dokterModel->createDoctorWithProfile($registrationData);

            // Optional: Send verification email
            // $this->sendVerificationEmail($registrationData['email']);

            return redirect()
                ->to("auth/dokter/login")
                ->with(
                    "success",
                    "Registration successful! Your account is pending verification. We will contact you via email once your account has been verified."
                );
        } catch (\Exception $e) {
            log_message("error", "Doctor registration failed: " . $e->getMessage());

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
    public function googleLogin($role = "pasien")
    {
        // Map roles to actual routes
        $roleRoutes = [
            "pasien" => "auth/pasien/login",
            "dokter" => "auth/dokter/login",
            "admin" => "auth/admin/login"
        ];

        // Validate role
        if (!array_key_exists($role, $roleRoutes)) {
            return redirect()
                ->to("auth/pasien/login") // Default fallback
                ->with("error", "Invalid role specified for Google login.");
        }

        // Store the login route for potential use later (optional)
        $loginRoute = $roleRoutes[$role];

        try {
            // Store intended role in session
            $this->session->set("intended_role", $role);

            // Set the redirect URI based on role
            $redirectUri = base_url("auth/google/callback/{$role}");
            $this->client->setRedirectUri($redirectUri);

            // Generate the authorization URL and redirect
            $authUrl = $this->client->createAuthUrl();
            return redirect()->to($authUrl);
        } catch (\Exception $e) {
            log_message("error", "Google OAuth error: " . $e->getMessage());

            return redirect()
                ->to($loginRoute)
                ->with("error", "Unable to connect to Google. Please try again or use regular login.");
        }
    }

    /**
     * Google callback handler
     */
    public function googleCallback($role = "pasien")
    {
        // Validate role
        if (!in_array($role, ["pasien", "dokter", "admin"])) {
            return redirect()->to("auth/login")
                ->with("error", "Invalid role specified for Google login.");
        }

        // Set the correct redirect URI
        $redirectUri = base_url("auth/google/callback/{$role}");
        $this->client->setRedirectUri($redirectUri);

        $code = $this->request->getGet("code");

        if (!$code) {
            log_message("error", "No code received from Google");
            return redirect()->to("auth/{$role}/login")
                ->with("error", "Google Sign-In failed: No authorization code received.");
        }

        try {
            // Exchange authorization code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token["error"])) {
                log_message("error", "Google token error: " . $token["error"]);
                return redirect()->to("auth/{$role}/login")
                    ->with("error", "Google authentication error: " . $token["error"]);
            }

            $this->client->setAccessToken($token);

            // Get user profile information
            $googleService = new \Google_Service_Oauth2($this->client);
            $userInfo = $googleService->userinfo->get();

            $email = $userInfo->getEmail();
            $name = $userInfo->getName();
            $googleId = $userInfo->getId();
            $picture = $userInfo->getPicture();

            // 1. Try get user by Google ID
            $user = $this->userModel->getUserByGoogleId($googleId);

            // 2. If not found, try get user by email and update google_id
            if (!$user && $email) {
                $user = $this->userModel->getUserByEmailOrUsername($email, $role);
                if ($user && empty($user['google_id'])) {
                    $this->userModel->updateGoogleId($user['user_id'], $googleId);
                    $user['google_id'] = $googleId; // update in $user for session
                }
            }

            // 3. If user exists, check role (and doctor verification)
            if ($user) {
                if ($user["role"] !== $role) {
                    return redirect()->to("auth/{$role}/login")
                        ->with("error", "This account is associated with a different role.");
                }

                if ($role === "dokter") {
                    // Verification check (delegated to model)
                    $dokterStatus = $this->dokterModel->checkVerification($user["user_id"]);
                    if (!$dokterStatus['ok']) {
                        return redirect()->to("auth/dokter/login")
                            ->with("error", $dokterStatus['msg']);
                    }
                }

                // Set session and redirect
                $this->setUserSession($user);
                return redirect()->to($this->getRedirectUrl($role));
            }

            // 4. If user not found, save data to session for social registration
            $googleData = [
                "email" => $email,
                "name" => $name,
                "google_id" => $googleId,
                "picture" => $picture,
            ];
            $this->session->set("google_data", $googleData);

            // Redirect to registration
            return redirect()->to("auth/register/social/{$role}");
        } catch (\Exception $e) {
            log_message("error", "Google callback error: " . $e->getMessage());
            return redirect()->to("auth/{$role}/login")
                ->with("error", "Failed to sign in with Google: " . $e->getMessage());
        }
    }

    /**
     * Social registration form
     */
    public function registerSocial($role = "pasien")
    {
        $googleData = $this->session->get('google_data');
        // Prefer role from google_data, then from session, fallback to URL param
        $role = $googleData['intended_role'] ?? $this->session->get('intended_role') ?? $role;
        $spesialisasiModel = new \App\Models\SpesialisasiModel();
        $data['spesialisasiList'] = $spesialisasiModel->findAll();

        $data["google_data"] = $googleData;
        $data["role"] = $role;
        $data["title"] = "Complete Social Registration";

        return view("auth/complete_social_registration", $data);
    }

    /**
     * Complete social pasien registration
     */
    public function completeSocialRegistration()
    {
        $session = session();
        $role = $this->request->getPost('role'); // 'dokter' or 'pasien'
        $googleData = $session->get("google_data");

        $validation = \Config\Services::validation();

        // Validation rules
        if ($role === 'dokter') {
            $validation->setRules([
                "username" => "required|alpha_numeric_space|min_length[3]|is_unique[users.username]",
                "telepon_dokter" => "required|numeric|min_length[10]",
                "id_spesialisasi" => "required",
                "no_lisensi" => "required|is_unique[dokter.no_lisensi]",
                "alamat_dokter" => "required",
                "jenis_kelamin" => "required|in_list[L,P]",
                "lokasi_kerja" => "required|in_list[Jakarta,Bandung,Surabaya]",
                "tanggal_lahir" => "required|valid_date",
            ]);
        } else {
            $validation->setRules([
                "email" => "required|valid_email",
                "username" => "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]",
                "password" => "required|min_length[8]",
                "confirm_password" => "required|matches[password]",
                "nama_pasien" => "required|min_length[3]",
                "jenis_kelamin" => "required|in_list[L,P]",
                "no_telp_pasien" => "required|numeric|min_length[10]",
                "tempat_lahir" => "required",
                "lokasi" => "required|in_list[JKT,BDG,SBY]",
                "tanggal_lahir" => "required|valid_date",
                "alamat" => "required",
            ]);
        }

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()
                ->back()
                ->withInput()
                ->with("error", implode("<br>", $validation->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($role === 'dokter') {
                // --- DOCTOR REGISTRATION ---
                $userModel = new UserModel();
                $dokterModel = new DokterModel();

                $userId = $userModel->generateUserId();
                $id_dokter = $dokterModel->generateDokterId($this->request->getPost("id_spesialisasi"));

                $userData = [
                    "user_id" => $userId,
                    "username" => htmlspecialchars($this->request->getPost("username"), ENT_QUOTES, "UTF-8"),
                    "email" => $googleData["email"] ?? null,
                    "google_id" => $googleData["google_id"] ?? null,
                    "password" => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT),
                    "role" => "dokter",
                    "status" => "active",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $userModel->insert($userData);

                $rawNamaDokter = $googleData["name"] ?? $this->request->getPost("nama_dokter");
                $namaDokterWithPrefix = preg_match('/^Dr\. /i', $rawNamaDokter) ? $rawNamaDokter : 'Dr. ' . $rawNamaDokter;

                $doctorData = [
                    "id_dokter" => $id_dokter,
                    "user_id" => $userId,
                    "nama_dokter" => $namaDokterWithPrefix,
                    "id_spesialisasi" => $this->request->getPost("id_spesialisasi"),
                    "telepon_dokter" => htmlspecialchars($this->request->getPost("telepon_dokter"), ENT_QUOTES, "UTF-8"),
                    "no_lisensi" => htmlspecialchars($this->request->getPost("no_lisensi"), ENT_QUOTES, "UTF-8"),
                    "lokasi_kerja" => $this->request->getPost("lokasi_kerja"),
                    "alamat_dokter" => $this->request->getPost("alamat_dokter"),
                    "jenis_kelamin" => $this->request->getPost("jenis_kelamin"),
                    "tanggal_lahir" => $this->request->getPost("tanggal_lahir"),
                    "is_verified" => 0,
                    "verification_status" => "pending",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $dokterModel->insert($doctorData);

                $db->transComplete();
                if ($db->transStatus() === false) throw new \Exception("Database transaction failed");

                $session->remove("google_data");

                return redirect()
                    ->to("auth/dokter/login")
                    ->with("success", "Registration successful! Your account is pending verification. We will contact you once your account has been verified.");
            } else {
                // --- PATIENT REGISTRATION ---
                $userModel = new UserModel();
                $pasienModel = new PasienModel();

                $userId = $userModel->generateUserId();

                $userData = [
                    "user_id" => $userId,
                    "username" => htmlspecialchars($this->request->getPost("username"), ENT_QUOTES, "UTF-8"),
                    "email" => $this->request->getPost("email"),
                    "password" => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT),
                    "role" => "pasien",
                    "status" => "active",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $userModel->insert($userData);

                $pasienData = [
                    "no_identitas" => $this->request->getPost("no_identitas"),
                    "nama_pasien" => $this->request->getPost("nama_pasien"),
                    "jenis_kelamin" => $this->request->getPost("jenis_kelamin"),
                    "no_telp_pasien" => $this->request->getPost("no_telp_pasien"),
                    "tempat_lahir" => $this->request->getPost("tempat_lahir"),
                    "tanggal_lahir" => $this->request->getPost("tanggal_lahir"),
                    "alamat" => $this->request->getPost("alamat"),
                    "email" => $this->request->getPost("email"),
                    "lokasi" => $this->request->getPost("lokasi"),
                ];
                $pasienResult = $pasienModel->createPasienProfile($userId, $pasienData);

                $db->transComplete();
                if ($db->transStatus() === false) throw new \Exception("Database transaction failed");

                // Set up the session
                $session->set([
                    "user_id" => $userId,
                    "username" => $this->request->getPost("username"),
                    "email" => $this->request->getPost("email"),
                    "role" => "pasien",
                    "pasien_id" => $pasienResult['pasien_id'],
                    "is_logged_in" => true,
                ]);

                return redirect()
                    ->to("/pasien/dashboard")
                    ->with("success", "Registration completed successfully. Welcome!");
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message("error", "Registration error: " . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with("error", "Registration failed: " . (ENVIRONMENT === "production" ? "A system error occurred." : $e->getMessage()));
        }
    }

    /**
     * User logout
     */
    public function logout($role = null)
    {
        // Validate role
        if (!in_array($role, ["pasien", "doctor", "admin"])) {
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
        if ($user["role"] === "pasien") {
            $pasien = $this->pasienModel
                ->where("user_id", $user["user_id"])
                ->first();
            if ($pasien) {
                $sessionData["pasien_id"] = $pasien["id_pasien"];
                $sessionData["full_name"] =
                    $pasien["nama_pasien"] ?? $user["username"];
            }
        } elseif ($user["role"] === "doctor") {
            $doctor = $this->dokterModel
                ->where("user_id", $user["user_id"])
                ->first();
            if ($doctor) {
                $sessionData["id_dokter"] = $doctor["id_dokter"];
                $sessionData["full_name"] =
                    $doctor["nama_dokter"] ?? $user["username"];
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
            case "pasien":
                return "/pasien/dashboard";
            case "doctor":
                return "/doctor/dashboard";
            case "admin":
                return "/admin/dashboard";
            default:
                return "/";
        }
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
