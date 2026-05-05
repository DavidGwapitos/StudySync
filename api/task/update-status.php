<?php
/**
 * StudySync - Update Task Status API
 * AJAX endpoint for updating task status
 * 
 * @package StudySync
 * @subpackage API
 */

header('Content-Type: application/json');

// Include configuration
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/TaskController.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
$status = isset($_POST['status']) ? sanitize($_POST['status']) : '';

if ($task_id <= 0 || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Create database connection
$database = new Database();
$db = $database->connect();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Initialize task controller
$user_id = getUserId();
$taskController = new TaskController($db, $user_id);

// Update task status
$result = $taskController->updateStatus($task_id, $status);

// Send response
http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
?>
