<?php
/**
 * User Model Class
 * Handles all user-related database operations
 * Includes authentication, registration, and user data management
 * 
 * @package StudySync
 * @subpackage Models
 */

class User {
    private $db;
    private $table = 'users';

    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Register a new user
     * Validates input and creates a new user account with hashed password
     * 
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password User's password (plain text)
     * @return array Result array with 'success' and 'message' keys
     */
    public function register($name, $email, $password) {
        // Sanitize inputs
        $name = sanitize($name);
        $email = sanitize($email);

        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        // Validate email format
        if (!validateEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Validate password length
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters long'];
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_ALGORITHM, PASSWORD_OPTIONS);

        // Prepare SQL query with prepared statement to prevent SQL injection
        $sql = "INSERT INTO " . $this->table . " (name, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->db->error];
        }

        // Bind parameters
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        // Execute query
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Registration successful. Please log in.'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Registration failed: ' . $this->db->error];
        }
    }

    /**
     * Authenticate user login
     * Verifies email and password against database records
     * 
     * @param string $email User's email address
     * @param string $password User's password (plain text)
     * @return array Result array with 'success', 'message', and user data if successful
     */
    public function login($email, $password) {
        // Sanitize email input
        $email = sanitize($email);

        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        // Prepare SQL query with prepared statement to prevent SQL injection
        $sql = "SELECT id, name, email, password FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->db->error];
        }

        // Bind parameter
        $stmt->bind_param("s", $email);

        // Execute query
        if (!$stmt->execute()) {
            $stmt->close();
            return ['success' => false, 'message' => 'Login failed: ' . $this->db->error];
        }

        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows !== 1) {
            $stmt->close();
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Return user data without password
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ];
    }

    /**
     * Check if email already exists in database
     * 
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email) {
        $sql = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data array or null if not found
     */
    public function getUserById($id) {
        $sql = "SELECT id, name, email, created_at FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Update user profile information
     * 
     * @param int $id User ID
     * @param string $name New name
     * @param string $email New email
     * @return array Result array with success status and message
     */
    public function updateProfile($id, $name, $email) {
        // Sanitize inputs
        $name = sanitize($name);
        $email = sanitize($email);

        // Validate input
        if (empty($name) || empty($email)) {
            return ['success' => false, 'message' => 'Name and email are required'];
        }

        // Validate email format
        if (!validateEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Prepare SQL query
        $sql = "UPDATE " . $this->table . " SET name = ?, email = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    /**
     * Change user password
     * 
     * @param int $id User ID
     * @param string $old_password Current password
     * @param string $new_password New password
     * @return array Result array with success status and message
     */
    public function changePassword($id, $old_password, $new_password) {
        // Validate input
        if (empty($old_password) || empty($new_password)) {
            return ['success' => false, 'message' => 'Both passwords are required'];
        }

        // Validate new password length
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }

        // Get user's current password hash
        $sql = "SELECT password FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'User not found'];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify old password
        if (!password_verify($old_password, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_ALGORITHM, PASSWORD_OPTIONS);

        // Update password in database
        $sql = "UPDATE " . $this->table . " SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Password changed successfully'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }
}
?>
