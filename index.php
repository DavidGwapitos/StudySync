<?php
/**
 * StudySync - Landing/Home Page
 * Entry point for the application
 * Shows login/register options or redirects to dashboard if already logged in
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudySync - Student Task Manager</title>
    
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
        
        .hero-container {
            text-align: center;
            color: white;
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .hero-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .feature-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .btn-action {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 0.5rem;
            margin: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login {
            background-color: white;
            color: #667eea;
        }
        
        .btn-login:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-register {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-register:hover {
            background-color: white;
            color: #667eea;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="hero-container">
        <!-- App Icon -->
        <div class="hero-icon">
            <i class="bi bi-clipboard-check"></i>
        </div>
        
        <!-- Main Title -->
        <h1 class="hero-title">StudySync</h1>
        <p class="hero-subtitle">Your Personal Student Task Manager</p>
        
        <!-- Features -->
        <div class="hero-features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="feature-title">Organize Tasks</div>
                <div class="feature-text">Keep all your academic tasks organized and never miss a deadline</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="feature-title">Track Progress</div>
                <div class="feature-text">Monitor your completion status and stay on top of your work</div>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div class="feature-title">Secure & Private</div>
                <div class="feature-text">Your data is encrypted and kept private with secure authentication</div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-5">
            <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-action btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login
            </a>
            <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-action btn-register">
                <i class="bi bi-person-plus me-2"></i> Sign Up
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
