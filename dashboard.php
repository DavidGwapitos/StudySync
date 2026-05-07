<?php
/**
 * StudySync - Dashboard Page
 * Main dashboard showing task statistics and overview
 *
 * @package StudySync
 * @subpackage Views
 */

// Include configuration
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TaskController.php';
require_once __DIR__ . '/controllers/DashboardController.php';

// Create database connection
$database = new Database();
$db = $database->connect();

// Initialize auth controller and check login
$authController = new AuthController($db);
$authController->requireLogin();

// Get user ID from session
$user_id = getUserId();
$user_name = getUserName();

// Initialize controllers
$taskController = new TaskController($db, $user_id);
$dashboardController = new DashboardController($taskController, $user_id);

// Get dashboard data
$dashboard_data = $dashboardController->getDashboardData();
$stats = $dashboard_data['stats'];
$upcoming_tasks = $dashboard_data['upcoming_tasks'];
$recent_tasks = $dashboard_data['recent_tasks'];

// Calculate completion percentage
$completion_percentage = $dashboardController->getCompletionPercentage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudySync</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>dashboard.php">
                <i class="bi bi-clipboard-check"></i>
                StudySync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>tasks/view.php">My Tasks</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($user_name); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="dashboard-main" style="padding-top: 100px;">
        <div class="container-content">
            <div class="mb-4">
                <h1 class="welcome-message">Welcome back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h1>
                <p class="welcome-subtitle text-muted">Here's your task overview for today</p>
            </div>

            <section class="stats-grid mb-4">
                <div class="stat-card total">
                    <div class="stat-icon"><i class="bi bi-list-check"></i></div>
                    <div class="stat-number"><?php echo $stats['total_tasks']; ?></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat-card completed">
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-number"><?php echo $stats['completed_tasks']; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-number"><?php echo $stats['pending_tasks']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card overdue">
                    <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="stat-number"><?php echo $stats['overdue_tasks']; ?></div>
                    <div class="stat-label">Overdue</div>
                </div>
            </section>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Overall Progress</h5>
                        <small class="text-muted">Track your completed tasks this week.</small>
                    </div>
                    <span class="badge bg-success"><?php echo round($completion_percentage, 0); ?>%</span>
                </div>
                <div class="card-body">
                    <div class="progress" style="height: 18px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $completion_percentage; ?>%;" aria-valuenow="<?php echo (int) $completion_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 text-secondary">
                        <span><strong><?php echo $stats['completed_tasks']; ?></strong> completed</span>
                        <span><strong><?php echo $stats['total_tasks']; ?></strong> total</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="bi bi-calendar-event me-2"></i> Upcoming Tasks (Next 7 Days)
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcoming_tasks)): ?>
                                <div class="task-list">
                                    <?php foreach ($upcoming_tasks as $task): ?>
                                        <div class="task-item">
                                            <div class="task-content">
                                                <div class="task-title <?php echo $task['status'] === 'Completed' ? 'completed' : ''; ?>">
                                                    <?php echo htmlspecialchars($task['title']); ?>
                                                </div>
                                                <div class="task-meta">
                                                    <span class="task-meta-item"><i class="bi bi-bookmark"></i> <?php echo htmlspecialchars($task['subject']); ?></span>
                                                    <span class="task-meta-item"><i class="bi bi-calendar3"></i> <?php echo date('M d', strtotime($task['due_date'])); ?></span>
                                                </div>
                                                <span class="badge badge-priority-<?php echo strtolower($task['priority']); ?>">
                                                    <?php echo htmlspecialchars($task['priority']); ?> Priority
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-calendar-check"></i></div>
                                    <div class="empty-state-title">No Upcoming Tasks</div>
                                    <div class="empty-state-text">Great! You don't have any tasks due in the next 7 days.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="bi bi-clock-history me-2"></i> Recent Tasks
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_tasks)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Task</th>
                                                <th>Status</th>
                                                <th>Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_tasks as $task): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo BASE_URL; ?>tasks/edit.php?id=<?php echo $task['id']; ?>" class="task-link">
                                                            <?php echo htmlspecialchars($task['title']); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-status-<?php echo strtolower($task['status']); ?>" data-status="<?php echo htmlspecialchars($task['status']); ?>">
                                                            <?php echo htmlspecialchars($task['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d', strtotime($task['due_date'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                    <div class="empty-state-title">No Tasks Yet</div>
                                    <div class="empty-state-text"><a href="<?php echo BASE_URL; ?>tasks/create.php" class="link-primary">Create your first task</a> to get started!</div>
                                </div>
                            <?php endif; ?>
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
</body>
</html>
