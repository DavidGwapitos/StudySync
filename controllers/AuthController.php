<?php
/**
 * Authentication Controller
 * Handles user registration, login, and logout operations
 * 
 * @package StudySync
 * @subpackage Controllers
 */

class AuthController {
    private $userModel;
    private $db;

    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User($db);
    }

    /**
     * Handle user registration
     * Processes POST request to register a new user
     * 
     * @return void
     */
    public function register() {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate input
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Client-side validation checks
            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                $response['message'] = 'All fields are required';
                return $response;
            }

            if ($password !== $confirm_password) {
                $response['message'] = 'Passwords do not match';
                return $response;
            }

            // Call model to register user
            $result = $this->userModel->register($name, $email, $password);
            return $result;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Handle user login
     * Processes POST request to authenticate user
     * Sets session variables on successful login
     * 
     * @return array Result array with success status and message
     */
    public function login() {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get credentials from POST request
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate input
            if (empty($email) || empty($password)) {
                $response['message'] = 'Email and password are required';
                return $response;
            }

            // Call model to authenticate user
            $result = $this->userModel->login($email, $password);

            if ($result['success']) {
                // Set session variables on successful login
                $_SESSION[SESSION_PREFIX . 'user_id'] = $result['user']['id'];
                $_SESSION[SESSION_PREFIX . 'user_name'] = $result['user']['name'];
                $_SESSION[SESSION_PREFIX . 'user_email'] = $result['user']['email'];
                $_SESSION[SESSION_PREFIX . 'login_time'] = time();

                $response['success'] = true;
                $response['message'] = 'Login successful';
            } else {
                $response['message'] = $result['message'];
            }

            return $response;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Handle user logout
     * Clears session variables and destroys session
     * 
     * @return void
     */
    public function logout() {
        // Clear session variables
        unset($_SESSION[SESSION_PREFIX . 'user_id']);
        unset($_SESSION[SESSION_PREFIX . 'user_name']);
        unset($_SESSION[SESSION_PREFIX . 'user_email']);
        unset($_SESSION[SESSION_PREFIX . 'login_time']);

        // Destroy session
        session_destroy();

        // Redirect to login page
        redirect(BASE_URL . 'login.php');
    }

    /**
     * Check if session is still valid (not timed out)
     * 
     * @return bool True if session is valid, false if timed out
     */
    public function isSessionValid() {
        if (!isLoggedIn()) {
            return false;
        }

        $login_time = $_SESSION[SESSION_PREFIX . 'login_time'] ?? 0;
        $current_time = time();

        // Check if session has exceeded timeout duration
        if ($current_time - $login_time > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }

        // Update login time to extend session
        $_SESSION[SESSION_PREFIX . 'login_time'] = $current_time;
        return true;
    }

    /**
     * Require user to be logged in
     * Redirects to login if user is not authenticated
     * 
     * @return void
     */
    public function requireLogin() {
        if (!$this->isSessionValid()) {
            redirect(BASE_URL . 'login.php');
        }
    }
}
?>
