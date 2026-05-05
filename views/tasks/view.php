<?php
/**
 * StudySync - View All Tasks Page
 * Displays all user tasks with filtering and search capabilities
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

// Get filter parameters from URL
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$subject_filter = isset($_GET['subject']) ? $_GET['subject'] : '';
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build filters array
$filters = [];
if (!empty($subject_filter)) {
    $filters['subject'] = $subject_filter;
}
if (!empty($priority_filter)) {
    $filters['priority'] = $priority_filter;
}
if (!empty($status_filter)) {
    $filters['status'] = $status_filter;
}
if (!empty($search)) {
    $filters['search'] = $search;
}

// Get tasks
$result = $taskController->getTasks($filters, $page);
$tasks = $result['tasks'];
$total_pages = $result['total_pages'];
$total = $result['total'];

// Get available subjects
$subjects = $taskController->getSubjects();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - StudySync</title>
    
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
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>tasks/view.php">
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
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h2>
                    <i class="bi bi-list-check me-2"></i> My Tasks
                </h2>
                <a href="<?php echo BASE_URL; ?>tasks/create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> New Task
                </a>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" id="filterForm">
                    <div class="filter-group">
                        <!-- Search Input -->
                        <div>
                            <label class="filter-label">Search</label>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search tasks..." value="<?php echo htmlspecialchars($search); ?>"
                                   data-action="search">
                        </div>
                        
                        <!-- Subject Filter -->
                        <div>
                            <label class="filter-label">Subject</label>
                            <select class="form-select" name="subject">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subj): ?>
                                <option value="<?php echo htmlspecialchars($subj); ?>"
                                        <?php echo $subject_filter === $subj ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subj); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Priority Filter -->
                        <div>
                            <label class="filter-label">Priority</label>
                            <select class="form-select" name="priority">
                                <option value="">All Priorities</option>
                                <option value="Low" <?php echo $priority_filter === 'Low' ? 'selected' : ''; ?>>Low</option>
                                <option value="Medium" <?php echo $priority_filter === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="High" <?php echo $priority_filter === 'High' ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="filter-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Filter Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> Apply Filters
                        </button>
                        <a href="<?php echo BASE_URL; ?>tasks/view.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Tasks Count -->
            <div class="mb-3">
                <p class="text-muted">
                    Showing <strong><?php echo count($tasks); ?></strong> of <strong><?php echo $total; ?></strong> tasks
                </p>
            </div>
            
            <!-- Tasks List -->
            <?php if (!empty($tasks)): ?>
                <div class="tasks-container">
                    <?php foreach ($tasks as $task): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <!-- Checkbox -->
                                <div>
                                    <input type="checkbox" class="form-check-input task-checkbox" 
                                           data-task-id="<?php echo $task['id']; ?>"
                                           <?php echo $task['status'] === 'Completed' ? 'checked' : ''; ?>
                                           style="width: 1.5rem; height: 1.5rem; cursor: pointer;">
                                </div>
                                
                                <!-- Task Content -->
                                <div style="flex: 1;">
                                    <h5 class="card-title <?php echo $task['status'] === 'Completed' ? 'completed' : ''; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h5>
                                    
                                    <?php if (!empty($task['description'])): ?>
                                    <p class="card-text text-muted" style="margin-bottom: 0.5rem; font-size: 0.95rem;">
                                        <?php echo htmlspecialchars(substr($task['description'], 0, 100)); 
                                              if (strlen($task['description']) > 100) echo '...'; ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <!-- Task Metadata -->
                                    <div class="task-meta mb-2">
                                        <span class="task-meta-item">
                                            <i class="bi bi-bookmark-fill"></i>
                                            <?php echo htmlspecialchars($task['subject']); ?>
                                        </span>
                                        <span class="task-meta-item">
                                            <i class="bi bi-calendar3"></i>
                                            <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Badges -->
                                    <div>
                                        <span class="badge badge-priority-<?php echo strtolower($task['priority']); ?>">
                                            <?php echo htmlspecialchars($task['priority']); ?>
                                        </span>
                                        <span class="badge badge-status-<?php echo strtolower($task['status']); ?>">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="task-actions">
                                    <a href="<?php echo BASE_URL; ?>tasks/edit.php?id=<?php echo $task['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit task">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-action="delete"
                                            data-task-id="<?php echo $task['id']; ?>"
                                            data-task-title="<?php echo htmlspecialchars($task['title']); ?>"
                                            title="Delete task">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&subject=<?php echo urlencode($subject_filter); ?>&priority=<?php echo urlencode($priority_filter); ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                Previous
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&subject=<?php echo urlencode($subject_filter); ?>&priority=<?php echo urlencode($priority_filter); ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <!-- Next Page -->
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&subject=<?php echo urlencode($subject_filter); ?>&priority=<?php echo urlencode($priority_filter); ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <div class="empty-state-title">No Tasks Found</div>
                            <div class="empty-state-text">
                                <?php if (!empty($search) || !empty($subject_filter) || !empty($priority_filter) || !empty($status_filter)): ?>
                                    Try adjusting your filters or 
                                <?php else: ?>
                                    You don't have any tasks yet.
                                <?php endif; ?>
                                <a href="<?php echo BASE_URL; ?>tasks/create.php" style="color: #0d6efd;">
                                    Create a new task
                                </a> to get started!
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        var BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
