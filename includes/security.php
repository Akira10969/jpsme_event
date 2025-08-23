<?php
/**
 * Security functions for the registration system
 */

/**
 * Rate limiting to prevent spam and brute force attacks
 */
function checkRateLimit($ip) {
    global $conn;
    
    // If no database connection, allow the request but log the issue
    if (!$conn) {
        error_log("Rate limit check failed: No database connection");
        return true;
    }
    
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip_address = ? AND created_at > ?");
        $time_limit = date('Y-m-d H:i:s', time() - RATE_LIMIT_WINDOW);
        $stmt->bind_param("ss", $ip, $time_limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        
        if ($count >= RATE_LIMIT_REQUESTS) {
            return false;
        }
        
        // Log this request
        $stmt = $conn->prepare("INSERT INTO rate_limits (ip_address, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        
        return true;
    } catch (Exception $e) {
        error_log("Rate limit error: " . $e->getMessage());
        return true; // Allow request if there's a database error
    }
}

/**
 * Sanitize filename for secure file uploads
 */
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    $filename = preg_replace('/\.+/', '.', $filename);
    return substr($filename, 0, 200);
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $field_name) {
    $errors = [];
    
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field_name)) . " is required.";
        return $errors;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error for " . str_replace('_', ' ', $field_name) . ".";
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $errors[] = str_replace('_', ' ', ucfirst($field_name)) . " file size must be less than 5MB.";
    }
    
    // Check file type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ALLOWED_FILE_TYPES)) {
        $errors[] = str_replace('_', ' ', ucfirst($field_name)) . " must be a PDF, JPG, or PNG file.";
    }
    
    // Validate file content (basic MIME type check)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'application/pdf',
        'image/jpeg',
        'image/png'
    ];
    
    if (!in_array($mime_type, $allowed_mimes)) {
        $errors[] = str_replace('_', ' ', ucfirst($field_name)) . " file type is not allowed.";
    }
    
    return $errors;
}

/**
 * Generate secure random filename
 */
function generateSecureFilename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    $secure_name = bin2hex(random_bytes(16)) . '.' . $extension;
    return $secure_name;
}

/**
 * Move uploaded file securely
 */
function moveUploadedFile($file, $destination_path) {
    if (!is_dir(dirname($destination_path))) {
        if (!mkdir(dirname($destination_path), 0755, true)) {
            return false;
        }
    }
    
    return move_uploaded_file($file['tmp_name'], $destination_path);
}

/**
 * Log security events
 */
function logSecurityEvent($event_type, $description, $ip_address = null) {
    global $conn;
    
    // If no database connection, fail silently
    if (!$conn) {
        return;
    }
    
    if (!$ip_address) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO security_logs (event_type, description, ip_address, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $event_type, $description, $ip_address);
        $stmt->execute();
    } catch (Exception $e) {
        // Fail silently on database errors to prevent breaking functionality
        error_log("Security log error: " . $e->getMessage());
    }
}

/**
 * Clean old rate limit entries
 */
function cleanRateLimits() {
    global $conn;
    
    if (!$conn) {
        return;
    }
    
    try {
        $time_limit = date('Y-m-d H:i:s', time() - (RATE_LIMIT_WINDOW * 2));
        $stmt = $conn->prepare("DELETE FROM rate_limits WHERE created_at < ?");
        $stmt->bind_param("s", $time_limit);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Clean rate limits error: " . $e->getMessage());
    }
}

/**
 * Validate PRC License format
 */
function validatePRCLicense($license) {
    // Basic validation for PRC license format
    // Adjust pattern based on actual PRC license format
    return preg_match('/^[0-9]{7,10}$/', $license);
}

/**
 * Validate dates
 */
function validateDates($registration_date, $expiration_date) {
    $errors = [];
    
    $reg_date = DateTime::createFromFormat('Y-m-d', $registration_date);
    $exp_date = DateTime::createFromFormat('Y-m-d', $expiration_date);
    $today = new DateTime();
    
    if (!$reg_date || !$exp_date) {
        $errors[] = "Invalid date format.";
        return $errors;
    }
    
    if ($reg_date > $today) {
        $errors[] = "PRC registration date cannot be in the future.";
    }
    
    if ($exp_date <= $today) {
        $errors[] = "PRC license has expired. Please provide a valid license.";
    }
    
    if ($exp_date <= $reg_date) {
        $errors[] = "PRC expiration date must be after registration date.";
    }
    
    return $errors;
}
?>
