<?php
/**
 * StudySync - User Profile Page
 * Displays and allows editing of user profile information
 * 
 * @package StudySync
 * @subpackage Views
 */

// Include configuration
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Create database connection
$database = new Database();
$db = $database->connect();

// Initialize auth controller and check login
$authController = new AuthController($db);
$authController->requireLogin();

// Get user ID from session
$user_id = getUserId();
$user_name = getUserName();
$user_email = getUserEmail();

$message = '';
$message_type = '';

// Note: Profile editing functionality can be extended here
// This is a placeholder for future enhancement
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - StudySync</title>
    
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
                        <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="<?php echo BASE_URL; ?>profile.php">
                                <i class="bi bi-person me-1"></i> Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
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
                <a href="<?php echo BASE_URL; ?>dashboard.php" class="me-3" title="Go back">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem; color: #0d6efd;"></i>
                </a>
                <h2 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i> My Profile
                </h2>
            </div>
            
            <!-- Profile Card -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-person me-2"></i> Account Information
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="profile-info">
                                <div class="mb-4 text-center">
                                    <div style="width: 100px; height: 100px; margin: 0 auto; background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($user_name); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($user_email); ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account Status</label>
                                    <p>
                                        <span class="badge bg-success">Active</span>
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Member Since</label>
                                    <p class="form-control-plaintext"><?php echo date('F d, Y'); ?></p>
                                </div>
                                
                                <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-graph-up me-2"></i> Account Statistics
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Profile Completion</span>
                                    <span class="badge bg-success">100%</span>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-shield-check me-2"></i> Security Recommendations
                                </h6>
                                <ul class="text-muted" style="font-size: 0.9rem; padding-left: 1.25rem;">
                                    <li>Keep your password secure</li>
                                    <li>Use a unique password</li>
                                    <li>Avoid sharing your account</li>
                                </ul>
                            </div>
                            
                            <hr>
                            
                            <div>
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-question-circle me-2"></i> Need Help?
                                </h6>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                    Check out our documentation or contact support for assistance.
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
</body>
</html>
