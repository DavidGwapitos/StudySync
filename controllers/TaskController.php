<?php
/**
 * Task Controller
 * Handles all task-related operations (CRUD)
 * Manages task creation, reading, updating, and deletion
 * 
 * @package StudySync
 * @subpackage Controllers
 */

class TaskController {
    private $taskModel;
    private $db;
    private $user_id;

    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     * @param int $user_id Current user's ID
     */
    public function __construct($db, $user_id) {
        $this->db = $db;
        $this->user_id = $user_id;
        require_once __DIR__ . '/../models/Task.php';
        $this->taskModel = new Task($db);
    }

    /**
     * Create a new task
     * Processes POST request to create a new task
     * 
     * @return array Result array with success status and message
     */
    public function create() {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $priority = $_POST['priority'] ?? 'Medium';
            $due_date = $_POST['due_date'] ?? '';

            // Convert date input to datetime format if provided
            if (!empty($due_date)) {
                // HTML5 datetime-local sends values like "YYYY-MM-DDTHH:MM" or "YYYY-MM-DDTHH:MM:SS"
                $due_date = str_replace('T', ' ', $due_date);
                if (strlen($due_date) === 16) {
                    $due_date .= ':00';
                }
                if (strlen($due_date) === 10) {
                    $due_date .= ' 23:59:59';
                }
            }

            // Call model to create task
            $result = $this->taskModel->create($this->user_id, $title, $description, $subject, $priority, $due_date);
            return $result;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Get a specific task
     * Retrieves task details for viewing or editing
     * 
     * @param int $task_id Task ID to retrieve
     * @return array|null Task data array or null if not found
     */
    public function getTask($task_id) {
        return $this->taskModel->getTaskById($task_id, $this->user_id);
    }

    /**
     * Get all tasks for current user with optional filtering
     * 
     * @param array $filters Optional filters (subject, priority, status, search)
     * @param int $page Current page number (for pagination)
     * @return array Array containing tasks and pagination info
     */
    public function getTasks($filters = [], $page = 1) {
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        // Get total count
        $total = $this->taskModel->countTasks($this->user_id, $filters);

        // Get tasks for current page
        $tasks = $this->taskModel->getTasksByUser($this->user_id, $filters, $limit, $offset);

        // Calculate pagination info
        $total_pages = ceil($total / $limit);

        return [
            'tasks' => $tasks,
            'total' => $total,
            'page' => $page,
            'per_page' => $limit,
            'total_pages' => $total_pages,
            'has_next' => $page < $total_pages,
            'has_prev' => $page > 1
        ];
    }

    /**
     * Update an existing task
     * Processes POST request to update task details
     * 
     * @param int $task_id Task ID to update
     * @return array Result array with success status and message
     */
    public function update($task_id) {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $priority = $_POST['priority'] ?? 'Medium';
            $due_date = $_POST['due_date'] ?? '';

            // Convert date input to datetime format if provided
            if (!empty($due_date)) {
                $due_date = str_replace('T', ' ', $due_date);
                if (strlen($due_date) === 16) {
                    $due_date .= ':00';
                }
                if (strlen($due_date) === 10) {
                    $due_date .= ' 23:59:59';
                }
            }

            // Call model to update task
            $result = $this->taskModel->update($task_id, $this->user_id, $title, $description, $subject, $priority, $due_date);
            return $result;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Update task status (mark as completed or pending)
     * 
     * @param int $task_id Task ID to update
     * @param string $status New status (Pending or Completed)
     * @return array Result array with success status and message
     */
    public function updateStatus($task_id, $status) {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get status from POST if not provided as parameter
            if (empty($status)) {
                $status = $_POST['status'] ?? '';
            }

            // Call model to update status
            $result = $this->taskModel->updateStatus($task_id, $this->user_id, $status);
            return $result;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Delete a task
     * 
     * @param int $task_id Task ID to delete
     * @return array Result array with success status and message
     */
    public function delete($task_id) {
        $response = ['success' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Call model to delete task
            $result = $this->taskModel->delete($task_id, $this->user_id);
            return $result;
        }

        $response['message'] = 'Invalid request method';
        return $response;
    }

    /**
     * Get task statistics for dashboard
     * 
     * @return array Statistics data
     */
    public function getStatistics() {
        return $this->taskModel->getStatistics($this->user_id);
    }

    /**
     * Get upcoming tasks (due soon)
     * 
     * @param int $days Number of days to look ahead
     * @return array Array of upcoming tasks
     */
    public function getUpcomingTasks($days = 7) {
        return $this->taskModel->getUpcomingTasks($this->user_id, $days);
    }

    /**
     * Get available subjects
     * 
     * @return array Array of subject names
     */
    public function getSubjects() {
        return $this->taskModel->getSubjects($this->user_id);
    }
}
?>
