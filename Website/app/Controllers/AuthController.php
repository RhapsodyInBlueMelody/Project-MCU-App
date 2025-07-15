<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasienModel;
use App\Models\DokterModel;
use App\Models\AdminModel;
use App\Models\PetugasLabModel;
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
    protected $petugasLabModel;
    protected $adminModel;
    protected $googleConfig;
    protected $client;

    public function __construct()
    {
        helper(["form", "url", "text"]);

        $this->session = session();
        $this->userModel = new UserModel();
        $this->pasienModel = new PasienModel();
        $this->dokterModel = new DokterModel();
        $this->adminModel = new AdminModel();
        $this->petugasLabModel = new PetugasLabModel();

        try {
            $this->googleConfig = new GoogleConfig();
            $this->client = new Client();
            $this->client->setClientId($this->googleConfig->clientId);
            $this->client->setClientSecret($this->googleConfig->clientSecret);
            $this->client->addScope("email");
            $this->client->addScope("profile");
        } catch (\Exception $e) {
            log_message("error", "Google client initialization error: " . $e->getMessage());
        }
    }

    // --- LOGIN PAGES ---
    public function pasienLogin()
    {
        if ($this->session->get("role") === "pasien" && $this->session->get("isLoggedIn")) {
            return redirect()->to("/pasien/dashboard");
        }
        $this->session->set("intended_role", "pasien");
        return view("auth/login/pasien", ["title" => "Pasien Login"]);
    }

    public function doctorLogin()
    {
        if ($this->session->get("role") === "dokter" && $this->session->get("isLoggedIn")) {
            return redirect()->to("/dokter/dashboard");
        }
        $this->session->set("intended_role", "dokter");
        return view("auth/login/dokter", ["title" => "Doctor Login"]);
    }

    public function adminLogin()
    {
        if ($this->session->get("role") === "admin" && $this->session->get("isLoggedIn")) {
            return redirect()->to("/admin/dashboard");
        }
        return view("auth/login/admin", ["title" => "Admin Login"]);
    }

    public function labLogin()
    {
        if ($this->session->get("role") === "petugas_lab" && $this->session->get("isLoggedIn")) {
            return redirect()->to("/petugas_lab/dashboard");
        }
        return view("auth/login/lab", ["title" => "Laboratorium Login"]);
    }

    // --- AUTHENTICATION (ALL ROLES) ---
    public function authenticate(): RedirectResponse
    {
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $role = $this->request->getPost("role");

        if (empty($username) || empty($password) || empty($role)) {
            return redirect()->to("auth/{$role}/login")->with("error", "Username and Password are required.");
        }
        $username = trim(htmlspecialchars($username));
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
            case "petugas_lab":
                $authenticated = $this->authenticateLab($username, $password);
                $redirectUrl = "/petugas_lab/dashboard";
                break;
            default:
                return redirect()->to("/")->with("error", "Invalid role specified.");
        }

        if ($authenticated) {
            log_message("info", "User '{$username}' successfully logged in as {$role}");
            return redirect()->to($redirectUrl);
        } else {
            if (!session()->getFlashdata("error")) {
                session()->setFlashdata("error", "Invalid username or password.");
            }
            log_message("warning", "Failed login attempt for user '{$username}' as {$role}");
            return redirect()->to("auth/{$role}/login");
        }
    }

    private function authenticatePasien(string $login, string $password): bool
    {
        $user = $this->userModel->getUserByEmailOrUsername($login, 'pasien');
        if (!$user) {
            session()->setFlashdata('msg', 'Username or email not found.');
            return false;
        }
        if (empty($user["password"])) {
            session()->setFlashdata('msg', 'Password is not set for this account.');
            return false;
        }
        if (!password_verify($password, $user["password"])) {
            session()->setFlashdata('msg', 'Incorrect password for this account.');
            return false;
        }
        $pasien = $this->pasienModel->where("user_id", $user["user_id"])->first();
        if (!$pasien) {
            session()->setFlashdata('msg', 'pasien profile not found.');
            return false;
        }
        $this->session->set([
            "user_id"    => $user["user_id"],
            "username"   => $user["username"],
            "email"      => $user["email"],
            "role"       => "pasien",
            "id_pasien"  => $pasien["id_pasien"],
            "nama_pasien" => $pasien["nama_pasien"],
            "isLoggedIn" => true,
        ]);
        return true;
    }

    private function authenticateDoctor(string $username, string $password): bool
    {
        $user = $this->userModel->authenticateDoctor($username, $password);
        if (!$user) {
            if (!session()->getFlashdata('error')) {
                session()->setFlashdata("error", "Password atau Username Salah.");
            }
            return false;
        }
        $doctorProfile = $this->dokterModel->getVerifiedDoctorProfile($user["user_id"]);
        if (isset($doctorProfile["error"])) {
            $statusMessage = "Akun Tidak Ditemukan.";
            if ($doctorProfile["error"] === "not_verified") {
                $statusMessage = "Akunmu Sedang Menunggu Verifikasi.";
            } elseif ($doctorProfile["error"] === "rejected") {
                $statusMessage = "Verifikasi Akun Anda Tertolak. Alasan: " . ($doctorProfile["verification_notes"] ?? "No reason provided.");
            }
            session()->setFlashdata("error", $statusMessage);
            return false;
        }
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

    private function authenticateLab(string $username, string $password): bool
    {
        $user = $this->userModel->authenticateLab($username, $password);
        if (!$user) {
            if (!session()->getFlashdata('error')) {
                session()->setFlashdata("error", "Password atau Username Salah.");
            }
            return false;
        }
        $petugasLabProfile = $this->petugasLabModel->getVerifiedLabProfile($user["user_id"]);
        if (isset($petugasLabProfile["error"])) {
            $statusMessage = "Akun Tidak Ditemukan.";
            if ($petugasLabProfile["error"] === "not_verified") {
                $statusMessage = "Akunmu Sedang Menunggu Verifikasi.";
            } elseif ($petugasLabProfile["error"] === "rejected") {
                $statusMessage = "Verifikasi Akun Anda Tertolak. Alasan: " . ($petugasLabProfile["verification_notes"] ?? "No reason provided.");
            }
            session()->setFlashdata("error", $statusMessage);
            return false;
        }
        $this->session->set([
            "user_id" => $user["user_id"],
            "id_petugas_lab" => $petugasLabProfile["id_petugas_lab"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => "petugas_lab",
            "nama_petugas_lab" => $petugasLabProfile["nama_petugas_lab"],
            "id_spesialisasi_lab" => $petugasLabProfile["id_spesialisasi_lab"],
            "isLoggedIn" => true,
        ]);
        return true;
    }

    private function authenticateAdmin(string $username, string $password): bool
    {
        $user = $this->userModel
            ->where("role", "admin")
            ->groupStart()
            ->where("username", $username)
            ->orWhere("email", $username)
            ->groupEnd()
            ->first();

        if (!$user || !password_verify($password, $user["password"])) {
            return false;
        }

        $admin = $this->adminModel->where("user_id", $user["user_id"])->first();
        if (!$admin) return false;
        $this->session->set([
            "user_id" => $user["user_id"],
            "id_admin" => $admin["id_admin"],
            "username" => $user["username"],
            "email" => $user["email"],
            "nama_admin" => $admin["nama_admin"],
            "role" => "admin",
            "role_type" => $admin["role_type"],
            "isLoggedIn" => true,
        ]);
        return true;
    }

    // ---- MANUAL REGISTRATION (ALL ROLES, DRY) ----
    public function register($role)
    {
        $request = $this->request;
        $isPost = $request->getMethod() === 'post';

        $spesialisasiList = [];
        if ($role === 'dokter') {
            $spesialisasiList = model('SpesialisasiModel')->findAll();
        } elseif ($role === 'petugas_lab') {
            $spesialisasiList = model('SpesialisasiLabModel')->findAll();
        }

        if ($isPost) {
            $validation = \Config\Services::validation();
            if ($role === 'dokter') {
                $validation->setRules([
                    "email" => "required|valid_email|is_unique[users.email]",
                    "username" => "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]",
                    "password" => "required|min_length[8]",
                    "confirm_password" => "required|matches[password]",
                    "nama_dokter" => "required|min_length[3]",
                    "jenis_kelamin" => "required|in_list[L,P]",
                    "tanggal_lahir" => "required|valid_date",
                    "telepon_dokter" => "required|numeric|min_length[10]",
                    "id_spesialisasi" => "required",
                    "lokasi_kerja" => "required|in_list[Jakarta,Bandung,Surabaya]",
                    "no_lisensi" => "required|is_unique[dokter.no_lisensi]",
                    "alamat_dokter" => "required|min_length[5]",
                ]);
            } elseif ($role === 'petugas_lab') {
                $validation->setRules([
                    "email" => "required|valid_email|is_unique[users.email]",
                    "username" => "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]",
                    "password" => "required|min_length[8]",
                    "confirm_password" => "required|matches[password]",
                    "nama_petugas_lab" => "required|min_length[3]",
                    "jenis_kelamin" => "required|in_list[L,P]",
                    "tanggal_lahir" => "required|valid_date",
                    "telepon_petugas_lab" => "required|numeric|min_length[10]",
                    "id_spesialisasi_lab" => "required",
                    "lokasi_kerja" => "required|in_list[Jakarta,Bandung,Surabaya]",
                    "no_lisensi" => "required|is_unique[petugas_lab.no_lisensi]",
                    "alamat_petugas_lab" => "required|min_length[5]",
                ]);
            } else { // pasien
                $validation->setRules([
                    "email" => "required|valid_email|is_unique[users.email]",
                    "username" => "required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]",
                    "password" => "required|min_length[8]",
                    "confirm_password" => "required|matches[password]",
                    "nama_pasien" => "required|min_length[3]",
                    "jenis_kelamin" => "required|in_list[L,P]",
                    "tanggal_lahir" => "required|valid_date",
                    "no_telp_pasien" => "required|numeric|min_length[10]",
                    "no_identitas" => "required|min_length[10]",
                    "tempat_lahir" => "required",
                    "lokasi" => "required|in_list[JKT,BDG,SBY]",
                    "alamat" => "required|min_length[5]",
                ]);
            }
            if (!$validation->withRequest($request)->run()) {
                return view('auth/register', [
                    'role' => $role,
                    'spesialisasiList' => $spesialisasiList,
                    'title' => 'Registrasi ' . ucfirst(str_replace('_', ' ', $role)),
                    'errors' => $validation->getErrors(),
                ]);
            }

            $db = \Config\Database::connect();
            $db->transStart();
            try {
                $userModel = new UserModel();
                $userId = $userModel->generateUserId();
                $userData = [
                    "user_id" => $userId,
                    "username" => htmlspecialchars($request->getPost("username"), ENT_QUOTES, "UTF-8"),
                    "email" => $request->getPost("email"),
                    "password" => password_hash($request->getPost("password"), PASSWORD_DEFAULT),
                    "role" => $role,
                    "status" => "active",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $userModel->insert($userData);

                if ($role === 'dokter') {
                    $dokterModel = new DokterModel();
                    $id_dokter = $dokterModel->generateDokterId($request->getPost("id_spesialisasi"));
                    $dokterModel->insert([
                        "id_dokter" => $id_dokter,
                        "user_id" => $userId,
                        "nama_dokter" => $request->getPost("nama_dokter"),
                        "id_spesialisasi" => $request->getPost("id_spesialisasi"),
                        "telepon_dokter" => $request->getPost("telepon_dokter"),
                        "no_lisensi" => $request->getPost("no_lisensi"),
                        "lokasi_kerja" => $request->getPost("lokasi_kerja"),
                        "alamat_dokter" => $request->getPost("alamat_dokter"),
                        "jenis_kelamin" => $request->getPost("jenis_kelamin"),
                        "tanggal_lahir" => $request->getPost("tanggal_lahir"),
                        "is_verified" => 0,
                        "verification_status" => "pending",
                        "created_by" => $userId,
                        "updated_by" => $userId,
                    ]);
                } elseif ($role === 'petugas_lab') {
                    $petugasLabModel = new PetugasLabModel();
                    $id_petugas_lab = $petugasLabModel->generatePetugasLabId($request->getPost("id_spesialisasi_lab"));
                    $petugasLabModel->insert([
                        "id_petugas_lab" => $id_petugas_lab,
                        "user_id" => $userId,
                        "nama_petugas_lab" => $request->getPost("nama_petugas_lab"),
                        "id_spesialisasi_lab" => $request->getPost("id_spesialisasi_lab"),
                        "telepon_petugas_lab" => $request->getPost("telepon_petugas_lab"),
                        "no_lisensi" => $request->getPost("no_lisensi"),
                        "lokasi_kerja" => $request->getPost("lokasi_kerja"),
                        "alamat_petugas_lab" => $request->getPost("alamat_petugas_lab"),
                        "jenis_kelamin" => $request->getPost("jenis_kelamin"),
                        "tanggal_lahir" => $request->getPost("tanggal_lahir"),
                        "is_verified" => 0,
                        "verification_status" => "pending",
                        "created_by" => $userId,
                        "updated_by" => $userId,
                    ]);
                } else { // pasien
                    $pasienModel = new PasienModel();
                    $pasienModel->createPasienProfile($userId, [
                        "no_identitas" => $request->getPost("no_identitas"),
                        "nama_pasien" => $request->getPost("nama_pasien"),
                        "jenis_kelamin" => $request->getPost("jenis_kelamin"),
                        "no_telp_pasien" => $request->getPost("no_telp_pasien"),
                        "tempat_lahir" => $request->getPost("tempat_lahir"),
                        "tanggal_lahir" => $request->getPost("tanggal_lahir"),
                        "alamat" => $request->getPost("alamat"),
                        "email" => $request->getPost("email"),
                        "lokasi" => $request->getPost("lokasi"),
                    ]);
                }
                $db->transComplete();
                if ($db->transStatus() === false) throw new \Exception("Database transaction failed");
                return redirect()->to("auth/{$role}/login")->with('success', 'Registrasi berhasil!');
            } catch (\Exception $e) {
                $db->transRollback();
                log_message("error", "Registration error: " . $e->getMessage());
                return view('auth/register', [
                    'role' => $role,
                    'spesialisasiList' => $spesialisasiList,
                    'title' => 'Registrasi ' . ucfirst(str_replace('_', ' ', $role)),
                    'errors' => [$e->getMessage()],
                ]);
            }
        }
        return view('auth/register', [
            'role' => $role,
            'spesialisasiList' => $spesialisasiList,
            'title' => 'Registrasi ' . ucfirst(str_replace('_', ' ', $role)),
            'errors' => [],
        ]);
    }

    // ---- SOCIAL (GOOGLE) REGISTRATION ----
    public function googleLogin($role = "pasien")
    {
        $roleRoutes = [
            "pasien" => "auth/pasien/login",
            "dokter" => "auth/dokter/login",
            "admin" => "auth/admin/login",
            "petugas_lab" => "auth/lab/login"
        ];

        if (!array_key_exists($role, $roleRoutes)) {
            return redirect()->to("auth/pasien/login")->with("error", "Invalid role specified for Google login.");
        }

        try {
            $this->session->set("intended_role", $role);
            $redirectUri = base_url("auth/google/callback/{$role}");
            $this->client->setRedirectUri($redirectUri);
            $authUrl = $this->client->createAuthUrl();
            return redirect()->to($authUrl);
        } catch (\Exception $e) {
            log_message("error", "Google OAuth error: " . $e->getMessage());
            return redirect()->to($roleRoutes[$role])->with("error", "Unable to connect to Google. Please try again or use regular login.");
        }
    }

    public function googleCallback($role = "pasien")
    {
        if (!in_array($role, ["pasien", "dokter", "admin", "petugas_lab"])) {
            return redirect()->to("auth/login")->with("error", "Invalid role specified for Google login.");
        }
        $redirectUri = base_url("auth/google/callback/{$role}");
        $this->client->setRedirectUri($redirectUri);

        $code = $this->request->getGet("code");
        if (!$code) {
            log_message("error", "No code received from Google");
            return redirect()->to("auth/{$role}/login")->with("error", "Google Sign-In failed: No authorization code received.");
        }

        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            if (isset($token["error"])) {
                log_message("error", "Google token error: " . $token["error"]);
                return redirect()->to("auth/{$role}/login")->with("error", "Google authentication error: " . $token["error"]);
            }
            $this->client->setAccessToken($token);
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
                    $user['google_id'] = $googleId;
                }
            }

            if ($user) {
                if ($user["role"] !== $role) {
                    return redirect()->to("auth/{$role}/login")->with("error", "This account is associated with a different role.");
                }
                if ($role === "dokter") {
                    $dokterStatus = $this->dokterModel->checkVerification($user["user_id"]);
                    if (!$dokterStatus['ok']) {
                        return redirect()->to("auth/dokter/login")->with("error", $dokterStatus['msg']);
                    }
                }
                $this->setUserSession($user);
                return redirect()->to($this->getRedirectUrl($role));
            }

            $googleData = [
                "email" => $email,
                "name" => $name,
                "google_id" => $googleId,
                "picture" => $picture,
                "intended_role" => $role,
            ];
            $this->session->set("google_data", $googleData);

            return redirect()->to("auth/register/social/{$role}");
        } catch (\Exception $e) {
            log_message("error", "Google callback error: " . $e->getMessage());
            return redirect()->to("auth/{$role}/login")->with("error", "Failed to sign in with Google: " . $e->getMessage());
        }
    }

    public function registerSocial($role = "pasien")
    {
        $googleData = $this->session->get('google_data');
        // Always prefer fresh role param if present
        if (!empty($role)) {
            $role = $role;
        } elseif (!empty($googleData['intended_role'])) {
            $role = $googleData['intended_role'];
        } elseif ($this->session->get('intended_role')) {
            $role = $this->session->get('intended_role');
        } else {
            $role = 'pasien';
        }

        $spesialisasiList = [];
        if ($role === 'dokter') {
            $spesialisasiModel = new \App\Models\SpesialisasiModel();
            $spesialisasiList = $spesialisasiModel->findAll();
        } elseif ($role === 'petugas_lab') {
            $spesialisasiLabModel = new \App\Models\SpesialisasiLabModel();
            $spesialisasiList = $spesialisasiLabModel->findAll();
        }

        $data = [
            'google_data' => $googleData,
            'role' => $role,
            'spesialisasiList' => $spesialisasiList,
            'title' => 'Lengkapi Registrasi',
        ];

        return view('auth/complete_registration', $data);
    }

    public function completeSocialRegistration()
    {
        $session = session();
        $role = $this->request->getPost('role');
        $googleData = $session->get("google_data");
        $validation = \Config\Services::validation();

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
                "password" => "required|min_length[8]",
                "confirm_password" => "required|matches[password]",
            ]);
        } elseif ($role === 'petugas_lab') {
            $validation->setRules([
                "username" => "required|alpha_numeric_space|min_length[3]|is_unique[users.username]",
                "telepon_petugas_lab" => "required|numeric|min_length[10]",
                "id_spesialisasi_lab" => "required",
                "no_lisensi" => "required|is_unique[petugas_lab.no_lisensi]",
                "alamat_petugas_lab" => "required",
                "jenis_kelamin" => "required|in_list[L,P]",
                "lokasi_kerja" => "required|in_list[Jakarta,Bandung,Surabaya]",
                "tanggal_lahir" => "required|valid_date",
                "nama_petugas_lab" => "required|min_length[3]",
                "password" => "required|min_length[8]",
                "confirm_password" => "required|matches[password]",
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
            return redirect()->back()->withInput()->with("error", implode("<br>", $validation->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($role === 'dokter') {
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

                $session->remove(["google_data", "intended_role"]);
                return redirect()->to("auth/dokter/login")->with("success", "Registration successful! Your account is pending verification. We will contact you once your account has been verified.");
            } elseif ($role === 'petugas_lab') {
                $userModel = new UserModel();
                $petugasLabModel = new PetugasLabModel();

                $userId = $userModel->generateUserId();
                $id_petugas_lab = $petugasLabModel->generatePetugasLabId($this->request->getPost("id_spesialisasi_lab"));

                $userData = [
                    "user_id" => $userId,
                    "username" => htmlspecialchars($this->request->getPost("username"), ENT_QUOTES, "UTF-8"),
                    "email" => $googleData["email"] ?? null,
                    "google_id" => $googleData["google_id"] ?? null,
                    "password" => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT),
                    "role" => "petugas_lab",
                    "status" => "active",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $userModel->insert($userData);

                $labData = [
                    "id_petugas_lab" => $id_petugas_lab,
                    "user_id" => $userId,
                    "nama_petugas_lab" => $this->request->getPost("nama_petugas_lab"),
                    "id_spesialisasi_lab" => $this->request->getPost("id_spesialisasi_lab"),
                    "telepon_petugas_lab" => $this->request->getPost("telepon_petugas_lab"),
                    "no_lisensi" => $this->request->getPost("no_lisensi"),
                    "lokasi_kerja" => $this->request->getPost("lokasi_kerja"),
                    "alamat_petugas_lab" => $this->request->getPost("alamat_petugas_lab"),
                    "jenis_kelamin" => $this->request->getPost("jenis_kelamin"),
                    "tanggal_lahir" => $this->request->getPost("tanggal_lahir"),
                    "is_verified" => 0,
                    "verification_status" => "pending",
                    "created_by" => $userId,
                    "updated_by" => $userId,
                ];
                $petugasLabModel->insert($labData);

                $db->transComplete();
                if ($db->transStatus() === false) throw new \Exception("Database transaction failed");

                $session->remove(["google_data", "intended_role"]);
                return redirect()->to("auth/petugas_lab/login")->with("success", "Registrasi Berhasil! Akun Anda sedang menunggu verifikasi. Kami akan menghubungi Anda setelah verifikasi selesai.");
            } else {
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

                $session->set([
                    "user_id" => $userId,
                    "username" => $this->request->getPost("username"),
                    "email" => $this->request->getPost("email"),
                    "role" => "pasien",
                    "pasien_id" => $pasienResult['pasien_id'],
                    "is_logged_in" => true,
                ]);
                $session->remove(["google_data", "intended_role"]);
                return redirect()->to("/pasien/dashboard")->with("success", "Registration completed successfully. Welcome!");
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message("error", "Registration error: " . $e->getMessage());
            return redirect()->back()->withInput()->with("error", "Registration failed: " . (ENVIRONMENT === "production" ? "A system error occurred." : $e->getMessage()));
        }
    }

    // --- LOGOUT ---
    public function logout($role = null)
    {
        $this->session->destroy();
        if (in_array($role, ["pasien", "dokter", "admin", "petugas_lab"])) {
            return redirect()->to("auth/{$role}/login")->with("success", "You have been successfully logged out.");
        }
        return redirect()->to("/")->with("success", "You have been successfully logged out.");
    }

    // --- SESSION HELPERS ---
    private function setUserSession($user)
    {
        $sessionData = [
            "user_id" => $user["user_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "isLoggedIn" => true,
        ];
        if ($user["role"] === "pasien") {
            $pasien = $this->pasienModel->where("user_id", $user["user_id"])->first();
            if ($pasien) {
                $sessionData["pasien_id"] = $pasien["id_pasien"];
                $sessionData["full_name"] = $pasien["nama_pasien"] ?? $user["username"];
            }
        } elseif ($user["role"] === "dokter") {
            $doctor = $this->dokterModel->where("user_id", $user["user_id"])->first();
            if ($doctor) {
                $sessionData["id_dokter"] = $doctor["id_dokter"];
                $sessionData["full_name"] = $doctor["nama_dokter"] ?? $user["username"];
                $sessionData["specialization"] = $doctor["id_spesialisasi"];
            }
        } elseif ($user["role"] === "admin") {
            $admin = $this->adminModel->where("user_id", $user["user_id"])->first();
            if ($admin) {
                $sessionData["id_admin"] = $admin["id_admin"];
                $sessionData["nama_admin"] = $admin["nama_admin"] ?? $user["username"];
                $sessionData["role_type"] = $admin["role_type"];
            }
        }
        $this->session->set($sessionData);
    }

    private function getRedirectUrl(string $role): string
    {
        switch ($role) {
            case "pasien":
                return "/pasien/dashboard";
            case "dokter":
                return "/dokter/dashboard";
            case "admin":
                return "/admin/dashboard";
            case "petugas_lab":
                return "/petugas_lab/dashboard";
            default:
                return "/";
        }
    }
}
