<?php
/**
 * StudySync - Registration Page
 * Handles new user registration
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
        // Create auth controller and handle registration
        $authController = new AuthController($db);
        $result = $authController->register();

        if ($result['success']) {
            $success = $result['message'];
            // Redirect to login after 2 seconds
            header("refresh:2;url=" . BASE_URL . "login.php");
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
    <title>Sign Up - StudySync</title>
    
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
            padding: 2rem 0;
        }
        
        .register-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
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
        
        .register-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 1rem 1rem 0 0;
        }
        
        .register-header-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        
        .register-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }
        
        .register-body {
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
        
        .btn-register {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .register-footer {
            text-align: center;
            padding: 0 2rem 2rem 2rem;
        }
        
        .register-footer p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .register-footer a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .register-footer a:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin: 0.25rem 0;
        }
        
        .requirement i {
            margin-right: 0.5rem;
            font-size: 0.8rem;
        }
        
        .requirement.valid {
            color: #28a745;
        }
        
        .requirement.invalid {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Register Header -->
        <div class="register-header">
            <div class="register-header-icon">
                <i class="bi bi-person-plus"></i>
            </div>
            <h2>Create Account</h2>
            <p>Join StudySync and start managing your tasks</p>
        </div>
        
        <!-- Register Body -->
        <div class="register-body">
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
            
            <!-- Register Form -->
            <form method="POST" action="" class="needs-validation" novalidate id="registerForm">
                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bi bi-person me-1"></i> Full Name
                    </label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Enter your full name" required autofocus>
                    <div class="invalid-feedback">
                        Please enter your full name.
                    </div>
                </div>
                
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i> Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" required>
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
                           placeholder="Enter a password (min 6 characters)" required 
                           minlength="6">
                    <div class="invalid-feedback">
                        Password must be at least 6 characters.
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="lengthReq">
                            <i class="bi bi-dash-circle"></i>
                            At least 6 characters
                        </div>
                    </div>
                </div>
                
                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Confirm Password
                    </label>
                    <input type="password" class="form-control" id="confirm_password" 
                           name="confirm_password" placeholder="Confirm your password" required>
                    <div class="invalid-feedback">
                        Passwords do not match.
                    </div>
                </div>
                
                <!-- Register Button -->
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-person-plus me-2"></i> Create Account
                </button>
            </form>
        </div>
        
        <!-- Register Footer -->
        <div class="register-footer">
            <p>Already have an account? 
                <a href="<?php echo BASE_URL; ?>login.php">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const form = document.getElementById('registerForm');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const lengthReq = document.getElementById('lengthReq');
        
        // Password strength indicator
        passwordField.addEventListener('input', function() {
            if (this.value.length >= 6) {
                lengthReq.classList.remove('invalid');
                lengthReq.classList.add('valid');
                lengthReq.querySelector('i').className = 'bi bi-check-circle';
            } else {
                lengthReq.classList.remove('valid');
                lengthReq.classList.add('invalid');
                lengthReq.querySelector('i').className = 'bi bi-dash-circle';
            }
        });
        
        // Password match validation
        confirmPasswordField.addEventListener('blur', function() {
            if (this.value && this.value !== passwordField.value) {
                this.classList.add('is-invalid');
                this.setCustomValidity('Passwords do not match');
            } else {
                this.classList.remove('is-invalid');
                this.setCustomValidity('');
            }
        });
        
        // Form validation
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Check if passwords match
            if (passwordField.value !== confirmPasswordField.value) {
                e.preventDefault();
                e.stopPropagation();
                confirmPasswordField.classList.add('is-invalid');
                confirmPasswordField.setCustomValidity('Passwords do not match');
                return false;
            }
            
            this.classList.add('was-validated');
        });
    </script>
</body>
</html>
