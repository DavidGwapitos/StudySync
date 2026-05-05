<?php
/**
 * StudySync - Login Page
 * Handles user authentication
 * 
 * @package StudySync
 * @subpackage Views
 */

// Include configuration
require_once __DIR__ . '/config/Config.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect(BASE_URL . 'dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/Database.php';
    require_once __DIR__ . '/controllers/AuthController.php';

    // Create database connection
    $database = new Database();
    $db = $database->connect();

    if ($db) {
        // Create auth controller and handle login
        $authController = new AuthController($db);
        $result = $authController->login();

        if ($result['success']) {
            redirect(BASE_URL . 'dashboard.php');
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Database connection failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudySync</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 1rem 1rem 0 0;
        }
        
        .login-header-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            outline: none;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            padding: 0 2rem 2rem 2rem;
        }
        
        .login-footer p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-footer a:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        
        .remember-me input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        
        .forgot-password {
            font-size: 0.9rem;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Header -->
        <div class="login-header">
            <div class="login-header-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <h2>StudySync</h2>
            <p>Welcome back! Please login to continue</p>
        </div>
        
        <!-- Login Body -->
        <div class="login-body">
            <!-- Error Alert -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="" class="needs-validation" novalidate>
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i> Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" required autofocus>
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter your password" required>
                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>
                </div>
                
                <!-- Remember Me Checkbox -->
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember" class="mb-0" style="cursor: pointer;">
                        Remember me
                    </label>
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
            </form>
        </div>
        
        <!-- Login Footer -->
        <div class="login-footer">
            <p>Don't have an account? 
                <a href="<?php echo BASE_URL; ?>register.php">
                    Sign up here
                </a>
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    </script>
</body>
</html>
