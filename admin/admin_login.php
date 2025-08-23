<?php
// Start session with proper configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/security.php';

$error = '';
$max_attempts = 5;
$lockout_time = 1800; // 30 minutes

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Verify CSRF token
    $csrf_valid = false;
    if (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token'])) {
        $csrf_valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    if (!$csrf_valid) {
        $error = 'Invalid request. Please refresh the page and try again.';
        logSecurityEvent('login_csrf_fail', 'CSRF token validation failed for admin login');
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Check for existing admin user
        $stmt = $conn->prepare("SELECT id, username, password_hash, full_name, role, failed_login_attempts, locked_until FROM admin_users WHERE username = ? AND is_active = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if ($admin) {
            // Check if account is locked
            if ($admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
                $remaining = ceil((strtotime($admin['locked_until']) - time()) / 60);
                $error = "Account is locked. Please try again in $remaining minutes.";
                logSecurityEvent('login_locked', "Login attempt on locked account: $username");
            } elseif (password_verify($password, $admin['password_hash'])) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_user'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Reset failed attempts
                $stmt = $conn->prepare("UPDATE admin_users SET failed_login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?");
                $stmt->bind_param("i", $admin['id']);
                $stmt->execute();
                
                logSecurityEvent('login_success', "Admin login successful: $username");
                
                header('Location: admin_dashboard.php');
                exit;
            } else {
                // Failed login
                $failed_attempts = $admin['failed_login_attempts'] + 1;
                
                if ($failed_attempts >= $max_attempts) {
                    $locked_until = date('Y-m-d H:i:s', time() + $lockout_time);
                    $stmt = $conn->prepare("UPDATE admin_users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?");
                    $stmt->bind_param("isi", $failed_attempts, $locked_until, $admin['id']);
                    $stmt->execute();
                    $error = "Too many failed attempts. Account locked for 30 minutes.";
                    logSecurityEvent('login_locked_due_to_attempts', "Account locked due to failed attempts: $username");
                } else {
                    $stmt = $conn->prepare("UPDATE admin_users SET failed_login_attempts = ? WHERE id = ?");
                    $stmt->bind_param("ii", $failed_attempts, $admin['id']);
                    $stmt->execute();
                    $remaining = $max_attempts - $failed_attempts;
                    $error = "Invalid credentials. $remaining attempts remaining.";
                    logSecurityEvent('login_fail', "Failed login attempt for: $username");
                }
            }
        } else {
            $error = 'Invalid credentials.';
            logSecurityEvent('login_fail_unknown_user', "Login attempt with unknown username: $username");
        }
    }
}

// Generate or regenerate CSRF token
if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Regenerate CSRF token after failed attempt to prevent replay attacks
if (isset($error) && !empty($error)) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - JPSME Event Registration</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <h1><i data-feather="shield"></i> Admin Login</h1>
                <p>JPSME Event Registration System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i data-feather="alert-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['logout_message'])): ?>
                <div class="alert alert-success">
                    <i data-feather="check-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['logout_message']); ?>
                </div>
                <?php unset($_SESSION['logout_message']); ?>
            <?php endif; ?>
            
            <form method="POST" id="loginForm" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <label for="username">
                        <i data-feather="user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i data-feather="lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required 
                           autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-feather="log-in"></i> Login
                </button>
            </form>
            
            <div class="login-footer">
                <p><a href="../index.php"><i data-feather="arrow-left"></i> Back to Registration</a></p>
                <p class="text-muted">
                    <small>For security purposes, all login attempts are logged.</small>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const submitBtnText = submitBtn.innerHTML;
            
            // Add page transition class
            document.body.classList.add('page-transition');
            
            // Form submission with loading animation
            loginForm.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                
                if (!username || !password) {
                    e.preventDefault();
                    showAlert('Please fill in all fields.', 'error');
                    return false;
                }
                
                // Add loading state
                submitBtn.classList.add('btn-loading');
                submitBtn.innerHTML = '<span class="btn-text">' + submitBtnText + '</span>';
                
                // Disable form elements
                const inputs = loginForm.querySelectorAll('input');
                inputs.forEach(input => input.disabled = true);
            });
            
            // Auto-focus first empty field with animation
            setTimeout(() => {
                if (!document.getElementById('username').value) {
                    document.getElementById('username').focus();
                } else {
                    document.getElementById('password').focus();
                }
            }, 500);
            
            // Security: Clear form on page unload
            window.addEventListener('beforeunload', function() {
                document.getElementById('password').value = '';
            });
            
            // Security: Disable form if in iframe
            if (window.top !== window.self) {
                document.body.innerHTML = '<h1>Access Denied</h1><p>This page cannot be loaded in a frame.</p>';
            }
            
            // Show alert function
            function showAlert(message, type) {
                const existingAlert = document.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                const alert = document.createElement('div');
                alert.className = `alert alert-${type}`;
                alert.innerHTML = `
                    <i data-feather="${type === 'error' ? 'alert-circle' : 'check-circle'}"></i>
                    ${message}
                `;
                
                const form = document.querySelector('.login-form');
                form.insertBefore(alert, form.firstChild);
                
                // Replace feather icons
                feather.replace();
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.style.animation = 'slideOutRight 0.5s ease-out';
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 5000);
            }
        });
    </script>
    <script>
        // Initialize Feather icons
        feather.replace();
    </script>
</body>
</html>