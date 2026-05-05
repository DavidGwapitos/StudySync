<?php
/**
 * StudySync - Create Task Page
 * Form for creating new tasks
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

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $taskController->create();
    
    if ($result['success']) {
        $success = $result['message'];
        // Redirect after 1.5 seconds
        header("refresh:1.5;url=" . BASE_URL . "tasks/view.php");
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task - StudySync</title>
    
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
                    <i class="bi bi-plus-circle me-2"></i> Create New Task
                </h2>
            </div>
            
            <!-- Form Card -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-clipboard-check me-2"></i> Task Details
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
                                           placeholder="Enter task title" required autofocus>
                                    <div class="invalid-feedback">
                                        Please enter a task title.
                                    </div>
                                </div>
                                
                                <!-- Description Field -->
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">
                                        <i class="bi bi-chat-left me-1"></i> Description
                                    </label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" placeholder="Enter task description (optional)"></textarea>
                                </div>
                                
                                <!-- Subject Field -->
                                <div class="form-group mb-3">
                                    <label for="subject" class="form-label">
                                        <i class="bi bi-bookmark-fill me-1"></i> Subject <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Select a subject</option>
                                        <?php foreach (SUBJECTS as $subj): ?>
                                        <option value="<?php echo htmlspecialchars($subj); ?>">
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
                                        <option value="Low">Low</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
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
                                    <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                                    <div class="invalid-feedback">
                                        Please enter a due date.
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i> Create Task
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>tasks/view.php" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Help Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-info-circle me-2"></i> Tips
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="card-subtitle mb-2 fw-bold">
                                    <i class="bi bi-star me-1" style="color: #ffc107;"></i> Priority Levels
                                </h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    <strong>Low:</strong> Can be completed anytime<br>
                                    <strong>Medium:</strong> Should be completed soon<br>
                                    <strong>High:</strong> Urgent, needs immediate attention
                                </p>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6 class="card-subtitle mb-2 fw-bold">
                                    <i class="bi bi-lightning me-1" style="color: #0d6efd;"></i> Best Practices
                                </h6>
                                <ul class="text-muted mb-0" style="font-size: 0.9rem; padding-left: 1.25rem;">
                                    <li>Be specific with your task title</li>
                                    <li>Set realistic due dates</li>
                                    <li>Add details in description</li>
                                    <li>Assign correct priority level</li>
                                </ul>
                            </div>
                            
                            <hr>
                            
                            <div>
                                <h6 class="card-subtitle mb-2 fw-bold">
                                    <i class="bi bi-check2 me-1" style="color: #28a745;"></i> Get Started
                                </h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Fill in all required fields (marked with *) and click "Create Task" to save.
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
        // Set minimum due date to today
        const today = new Date().toISOString().slice(0, 16);
        document.getElementById('due_date').setAttribute('min', today);
        
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
