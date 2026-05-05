<?php
/**
 * Task Model Class
 * Handles all task-related database operations
 * Includes CRUD operations, filtering, and task statistics
 * 
 * @package StudySync
 * @subpackage Models
 */

class Task {
    private $db;
    private $table = 'tasks';

    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new task
     * Validates input and inserts new task into database
     * 
     * @param int $user_id User ID
     * @param string $title Task title
     * @param string $description Task description
     * @param string $subject Task subject
     * @param string $priority Task priority (Low, Medium, High)
     * @param string $due_date Task due date (YYYY-MM-DD HH:MM:SS)
     * @return array Result array with success status and message
     */
    public function create($user_id, $title, $description, $subject, $priority, $due_date) {
        // Sanitize inputs
        $title = sanitize($title);
        $description = sanitize($description);
        $subject = sanitize($subject);
        $priority = sanitize($priority);

        // Validate required fields
        if (empty($title) || empty($subject) || empty($priority)) {
            return ['success' => false, 'message' => 'Title, subject, and priority are required'];
        }

        // Validate priority value
        if (!in_array($priority, PRIORITIES)) {
            return ['success' => false, 'message' => 'Invalid priority level'];
        }

        // Validate due date format if provided
        if (!empty($due_date)) {
            if (!$this->validateDateTime($due_date)) {
                return ['success' => false, 'message' => 'Invalid date/time format'];
            }
        } else {
            $due_date = null;
        }

        // Prepare SQL query
        $sql = "INSERT INTO " . $this->table . " (user_id, title, description, subject, priority, due_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error: ' . $this->db->error];
        }

        // Bind parameters
        $stmt->bind_param("isssss", $user_id, $title, $description, $subject, $priority, $due_date);

        // Execute query
        if ($stmt->execute()) {
            $task_id = $this->db->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Task created successfully', 'task_id' => $task_id];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to create task: ' . $this->db->error];
        }
    }

    /**
     * Get task by ID
     * 
     * @param int $task_id Task ID
     * @param int $user_id User ID (for security verification)
     * @return array|null Task data array or null if not found
     */
    public function getTaskById($task_id, $user_id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
        $stmt->close();

        return $task;
    }

    /**
     * Get all tasks for a user with optional filtering
     * 
     * @param int $user_id User ID
     * @param array $filters Optional filters (subject, priority, status, search)
     * @param int $limit Number of tasks to return
     * @param int $offset Offset for pagination
     * @return array Array of tasks
     */
    public function getTasksByUser($user_id, $filters = [], $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $params = [$user_id];
        $types = "i";

        // Apply filters
        if (!empty($filters['subject'])) {
            $subject = sanitize($filters['subject']);
            $sql .= " AND subject = ?";
            $params[] = $subject;
            $types .= "s";
        }

        if (!empty($filters['priority'])) {
            $priority = sanitize($filters['priority']);
            $sql .= " AND priority = ?";
            $params[] = $priority;
            $types .= "s";
        }

        if (!empty($filters['status'])) {
            $status = sanitize($filters['status']);
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $search = '%' . sanitize($filters['search']) . '%';
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }

        // Add ordering and pagination
        $sql .= " ORDER BY due_date ASC, created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = [];

        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        $stmt->close();
        return $tasks;
    }

    /**
     * Count total tasks for a user with optional filtering
     * 
     * @param int $user_id User ID
     * @param array $filters Optional filters
     * @return int Total task count
     */
    public function countTasks($user_id, $filters = []) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = ?";
        $params = [$user_id];
        $types = "i";

        // Apply same filters as getTasksByUser
        if (!empty($filters['subject'])) {
            $subject = sanitize($filters['subject']);
            $sql .= " AND subject = ?";
            $params[] = $subject;
            $types .= "s";
        }

        if (!empty($filters['priority'])) {
            $priority = sanitize($filters['priority']);
            $sql .= " AND priority = ?";
            $params[] = $priority;
            $types .= "s";
        }

        if (!empty($filters['status'])) {
            $status = sanitize($filters['status']);
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ?? 0;
    }

    /**
     * Update an existing task
     * 
     * @param int $task_id Task ID
     * @param int $user_id User ID (for security verification)
     * @param string $title Task title
     * @param string $description Task description
     * @param string $subject Task subject
     * @param string $priority Task priority
     * @param string $due_date Task due date
     * @return array Result array with success status and message
     */
    public function update($task_id, $user_id, $title, $description, $subject, $priority, $due_date) {
        // Sanitize inputs
        $title = sanitize($title);
        $description = sanitize($description);
        $subject = sanitize($subject);
        $priority = sanitize($priority);

        // Validate required fields
        if (empty($title) || empty($subject) || empty($priority)) {
            return ['success' => false, 'message' => 'Title, subject, and priority are required'];
        }

        // Validate priority value
        if (!in_array($priority, PRIORITIES)) {
            return ['success' => false, 'message' => 'Invalid priority level'];
        }

        // Validate due date format if provided
        if (!empty($due_date)) {
            if (!$this->validateDateTime($due_date)) {
                return ['success' => false, 'message' => 'Invalid date/time format'];
            }
        } else {
            $due_date = null;
        }

        // Prepare SQL query
        $sql = "UPDATE " . $this->table . " SET title = ?, description = ?, subject = ?, priority = ?, due_date = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("sssssii", $title, $description, $subject, $priority, $due_date, $task_id, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Task updated successfully'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to update task'];
        }
    }

    /**
     * Update task status (mark as completed or pending)
     * 
     * @param int $task_id Task ID
     * @param int $user_id User ID (for security verification)
     * @param string $status New status (Pending or Completed)
     * @return array Result array with success status and message
     */
    public function updateStatus($task_id, $user_id, $status) {
        // Validate status value
        if (!in_array($status, STATUSES)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        // Prepare SQL query
        $sql = "UPDATE " . $this->table . " SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("sii", $status, $task_id, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Task status updated successfully'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to update task status'];
        }
    }

    /**
     * Delete a task
     * 
     * @param int $task_id Task ID
     * @param int $user_id User ID (for security verification)
     * @return array Result array with success status and message
     */
    public function delete($task_id, $user_id) {
        // Prepare SQL query
        $sql = "DELETE FROM " . $this->table . " WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("ii", $task_id, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Task deleted successfully'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to delete task'];
        }
    }

    /**
     * Get task statistics for a user
     * 
     * @param int $user_id User ID
     * @return array Statistics including total, completed, pending, and overdue tasks
     */
    public function getStatistics($user_id) {
        $sql = "SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN status = 'Pending' AND due_date < NOW() THEN 1 ELSE 0 END) as overdue_tasks
                FROM " . $this->table . " WHERE user_id = ?";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['total_tasks' => 0, 'completed_tasks' => 0, 'pending_tasks' => 0, 'overdue_tasks' => 0];
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $stmt->close();

        // Ensure all values are integers
        return [
            'total_tasks' => (int)($stats['total_tasks'] ?? 0),
            'completed_tasks' => (int)($stats['completed_tasks'] ?? 0),
            'pending_tasks' => (int)($stats['pending_tasks'] ?? 0),
            'overdue_tasks' => (int)($stats['overdue_tasks'] ?? 0)
        ];
    }

    /**
     * Validate date/time format
     * 
     * @param string $dateTime Date/time string to validate
     * @return bool True if valid datetime, false otherwise
     */
    private function validateDateTime($dateTime) {
        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $dateTime);
        return $d && $d->format($format) === $dateTime;
    }

    /**
     * Get tasks due soon (within next 7 days)
     * 
     * @param int $user_id User ID
     * @param int $days Number of days to look ahead
     * @return array Array of upcoming tasks
     */
    public function getUpcomingTasks($user_id, $days = 7) {
        $sql = "SELECT * FROM " . $this->table . " 
                WHERE user_id = ? AND status = 'Pending' 
                AND due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)
                ORDER BY due_date ASC";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $user_id, $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = [];

        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        $stmt->close();
        return $tasks;
    }

    /**
     * Get all unique subjects for a user
     * 
     * @param int $user_id User ID
     * @return array Array of subjects
     */
    public function getSubjects($user_id) {
        $sql = "SELECT DISTINCT subject FROM " . $this->table . " 
                WHERE user_id = ? ORDER BY subject ASC";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $subjects = [];

        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row['subject'];
        }

        $stmt->close();
        return $subjects;
    }
}
?>
