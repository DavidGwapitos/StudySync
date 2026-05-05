<?php
/**
 * StudySync - Edit Task Page
 * Form for editing existing tasks
 * 
 * @package StudySync
 * @subpackage Views
 */

// Include configuration
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/TaskController.php';

// Create database connection
$database = new Database();
$db = $database->connect();

// Initialize auth controller and check login
$authController = new AuthController($db);
$authController->requireLogin();

// Get user ID from session
$user_id = getUserId();
$user_name = getUserName();

// Initialize task controller
$taskController = new TaskController($db, $user_id);

// Get task ID from URL
$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($task_id <= 0) {
    redirect(BASE_URL . 'tasks/view.php');
}

// Get task details
$task = $taskController->getTask($task_id);

if (!$task) {
    redirect(BASE_URL . 'tasks/view.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $taskController->update($task_id);
    
    if ($result['success']) {
        $success = $result['message'];
        // Refresh task data
        $task = $taskController->getTask($task_id);
        // Redirect after 1.5 seconds
        header("refresh:1.5;url=" . BASE_URL . "tasks/view.php");
    } else {
        $error = $result['message'];
    }
}

// Format due date for input field
$due_date_formatted = date('Y-m-d\TH:i', strtotime($task['due_date']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - StudySync</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>dashboard.php">
                <i class="bi bi-clipboard-check"></i> StudySync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard.php">
                            <i class="bi bi-graph-up me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>tasks/view.php">
                            <i class="bi bi-list-check me-1"></i> My Tasks
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        <div class="container-content">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4 mt-3">
                <a href="<?php echo BASE_URL; ?>tasks/view.php" class="me-3" title="Go back">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem; color: #0d6efd;"></i>
                </a>
                <h2 class="mb-0">
                    <i class="bi bi-pencil me-2"></i> Edit Task
                </h2>
            </div>
            
            <!-- Form Card -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-clipboard-check me-2"></i> Update Task Details
                        </div>
                        <div class="card-body">
                            <!-- Success Alert -->
                            <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Error Alert -->
                            <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Task Form -->
                            <form method="POST" action="" class="needs-validation" novalidate data-validate="true">
                                <!-- Title Field -->
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">
                                        <i class="bi bi-file-text me-1"></i> Task Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($task['title']); ?>" required autofocus>
                                    <div class="invalid-feedback">
                                        Please enter a task title.
                                    </div>
                                </div>
                                
                                <!-- Description Field -->
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-chat-left me-1"></i> Description
                                    </label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                                </div>
                                
                                <!-- Subject Field -->
                                <div class="form-group mb-3">
                                    <label for="subject" class="form-label">
                                        <i class="bi bi-bookmark-fill me-1"></i> Subject <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Select a subject</option>
                                        <?php foreach (SUBJECTS as $subj): ?>
                                        <option value="<?php echo htmlspecialchars($subj); ?>"
                                                <?php echo $task['subject'] === $subj ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subj); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a subject.
                                    </div>
                                </div>
                                
                                <!-- Priority Field -->
                                <div class="form-group mb-3">
                                    <label for="priority" class="form-label">
                                        <i class="bi bi-exclamation-diamond me-1"></i> Priority <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="">Select priority</option>
                                        <option value="Low" <?php echo $task['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                                        <option value="Medium" <?php echo $task['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="High" <?php echo $task['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a priority level.
                                    </div>
                                </div>
                                
                                <!-- Due Date Field -->
                                <div class="form-group mb-3">
                                    <label for="due_date" class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i> Due Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="due_date" name="due_date" 
                                           value="<?php echo $due_date_formatted; ?>" required>
                                    <div class="invalid-feedback">
                                        Please enter a due date.
                                    </div>
                                </div>
                                
                                <!-- Status Display -->
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-check-circle me-1"></i> Current Status
                                    </label>
                                    <div>
                                        <span class="badge badge-status-<?php echo strtolower($task['status']); ?>">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                        <small class="text-muted ms-2">Change status by marking as complete in the task list</small>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i> Save Changes
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>tasks/view.php" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Task Info Sidebar -->
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="bi bi-info-circle me-2"></i> Task Information
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Task ID</label>
                                <p class="text-muted"><?php echo $task['id']; ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created</label>
                                <p class="text-muted"><?php echo date('M d, Y H:i', strtotime($task['created_at'])); ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="text-muted"><?php echo date('M d, Y H:i', strtotime($task['updated_at'])); ?></p>
                            </div>
                            
                            <div>
                                <label class="form-label fw-bold">Current Priority</label>
                                <p>
                                    <span class="badge badge-priority-<?php echo strtolower($task['priority']); ?>">
                                        <?php echo htmlspecialchars($task['priority']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-clock me-2"></i> Quick Stats
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Days until due:</small>
                                <p class="mb-0">
                                    <?php 
                                    $days = ceil((strtotime($task['due_date']) - time()) / (60*60*24));
                                    if ($days < 0) {
                                        echo '<span class="badge bg-danger">Overdue by ' . abs($days) . ' days</span>';
                                    } elseif ($days === 0) {
                                        echo '<span class="badge bg-warning">Due today</span>';
                                    } elseif ($days === 1) {
                                        echo '<span class="badge bg-warning">Due tomorrow</span>';
                                    } else {
                                        echo '<span class="badge bg-info">' . $days . ' days left</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- App Base URL -->
    <script>
        var BASE_URL = '<?php echo BASE_URL; ?>';
    </script>

    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
    
    <script>
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    </script>
</body>
</html>
