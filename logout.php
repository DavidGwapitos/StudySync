<?php
/**
 * StudySync - Logout Page
 * Handles user logout and session destruction
 * 
 * @package StudySync
 */

// Include configuration
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Create database connection
$database = new Database();
$db = $database->connect();

// Initialize auth controller and handle logout
$authController = new AuthController($db);
$authController->logout();

// Note: logout() will redirect, so this line shouldn't be reached
?>
