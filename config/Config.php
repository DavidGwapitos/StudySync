<?php
/**
 * Application Configuration File
 * Contains global constants and configuration settings for StudySync
 * 
 * @package StudySync
 * @subpackage Config
 */

// Application Settings
define('APP_NAME', 'StudySync');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:8000/');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_PREFIX', 'studysync_');

// Password Hashing
define('PASSWORD_ALGORITHM', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 10]);

// Security Settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 900); // 15 minutes in seconds

// Pagination Settings
define('ITEMS_PER_PAGE', 10);

// Priority Levels
define('PRIORITIES', ['Low', 'Medium', 'High']);

// Task Statuses
define('STATUSES', ['Pending', 'Completed']);

// Subjects (Common Academic Subjects)
define('SUBJECTS', [
    'Mathematics',
    'English',
    'Science',
    'History',
    'Geography',
    'Physics',
    'Chemistry',
    'Biology',
    'Computer Science',
    'Economics',
    'Business Studies',
    'Literature',
    'Art',
    'Physical Education',
    'Other'
]);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION[SESSION_PREFIX . 'user_id']);
}

/**
 * Get current logged-in user ID
 * 
 * @return int|null User ID if logged in, null otherwise
 */
function getUserId() {
    return $_SESSION[SESSION_PREFIX . 'user_id'] ?? null;
}

/**
 * Get current logged-in user name
 * 
 * @return string|null User name if logged in, null otherwise
 */
function getUserName() {
    return $_SESSION[SESSION_PREFIX . 'user_name'] ?? null;
}

/**
 * Get current logged-in user email
 * 
 * @return string|null User email if logged in, null otherwise
 */
function getUserEmail() {
    return $_SESSION[SESSION_PREFIX . 'user_email'] ?? null;
}

/**
 * Sanitize user input
 * 
 * @param string $input User input to sanitize
 * @return string Sanitized input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * 
 * @param string $email Email to validate
 * @return bool True if valid email, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
