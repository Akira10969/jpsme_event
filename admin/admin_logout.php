<?php
session_start();

require_once '../config/database.php';
require_once '../includes/security.php';

// Log the logout event
if (isset($_SESSION['admin_user'])) {
    logSecurityEvent('logout', 'Admin logout: ' . $_SESSION['admin_user']);
}

// Store logout info for animation
$logout_user = $_SESSION['admin_name'] ?? $_SESSION['admin_user'] ?? 'Admin';

// Clear all session data
session_unset();
session_destroy();

// Start a new session for the redirect message
session_start();
$_SESSION['logout_message'] = 'You have been successfully logged out.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - JPSME Event Registration</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .logout-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.5s ease-out;
        }
        
        .logout-content {
            text-align: center;
            color: white;
            animation: slideInUp 0.8s ease-out;
        }
        
        .logout-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            animation: rotateAndPulse 2s ease-in-out;
        }
        
        .logout-text h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }
        
        .logout-text p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }
        
        .logout-progress {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            margin: 0 auto;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.7s both;
        }
        
        .logout-progress-bar {
            width: 0%;
            height: 100%;
            background: #ffd700;
            border-radius: 2px;
            animation: progressBar 2s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes rotateAndPulse {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.1);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }
        
        @keyframes progressBar {
            from { width: 0%; }
            to { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="logout-animation">
        <div class="logout-content">
            <div class="logout-icon">
                <i data-feather="log-out"></i>
            </div>
            <div class="logout-text">
                <h1>Goodbye, <?php echo htmlspecialchars($logout_user); ?>!</h1>
                <p>You have been successfully logged out.</p>
                <div class="logout-progress">
                    <div class="logout-progress-bar"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Feather icons
        feather.replace();
        
        // Redirect after animation completes
        setTimeout(function() {
            window.location.href = 'admin_login.php';
        }, 3000);
        
        // Add some interactive effects
        document.addEventListener('click', function() {
            window.location.href = 'admin_login.php';
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Escape') {
                window.location.href = 'admin_login.php';
            }
        });
    </script>
</body>
</html>
