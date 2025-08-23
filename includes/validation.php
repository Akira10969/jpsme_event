<?php
/**
 * Validation functions for registration form
 */

function validateRegistrationForm($data) {
    $errors = [];
    
    // Validate required fields
    $required_fields = ['institution', 'coach_name', 'prc_license', 'prc_registration_date', 'prc_expiration_date'];
    
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    
    // Validate email format
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    // Validate phone number
    if (!empty($data['phone']) && !preg_match('/^[\d\s\+\-\(\)]+$/', $data['phone'])) {
        $errors[] = "Please enter a valid phone number.";
    }
    
    // Validate PRC license format (assuming it should be alphanumeric)
    if (!empty($data['prc_license']) && !preg_match('/^[A-Za-z0-9\-]+$/', $data['prc_license'])) {
        $errors[] = "PRC license should contain only letters, numbers, and hyphens.";
    }
    
    // Validate dates
    if (!empty($data['prc_registration_date']) && !validateDate($data['prc_registration_date'])) {
        $errors[] = "Please enter a valid PRC registration date.";
    }
    
    if (!empty($data['prc_expiration_date']) && !validateDate($data['prc_expiration_date'])) {
        $errors[] = "Please enter a valid PRC expiration date.";
    }
    
    // Check if PRC license is not expired
    if (!empty($data['prc_expiration_date']) && strtotime($data['prc_expiration_date']) < time()) {
        $errors[] = "PRC license appears to be expired.";
    }
    
    return $errors;
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function processRegistration($post_data, $files_data) {
    global $conn;
    
    $errors = [];
    $uploaded_files = [];
    
    // Validate required fields
    $required_fields = ['institution', 'coach_name', 'prc_license', 'prc_registration_date', 'prc_expiration_date'];
    
    foreach ($required_fields as $field) {
        if (empty($post_data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    
    // Validate institution
    if (!empty($post_data['institution'])) {
        if (strlen($post_data['institution']) > 255) {
            $errors[] = "Institution name is too long.";
        }
        if (!preg_match('/^[a-zA-Z0-9\s\-\.&,()]+$/', $post_data['institution'])) {
            $errors[] = "Institution name contains invalid characters.";
        }
    }
    
    // Validate coach name
    if (!empty($post_data['coach_name'])) {
        if (strlen($post_data['coach_name']) > 255) {
            $errors[] = "Coach name is too long.";
        }
        if (!preg_match('/^[a-zA-Z\s\-\.]+$/', $post_data['coach_name'])) {
            $errors[] = "Coach name contains invalid characters.";
        }
    }
    
    // Validate PRC license
    if (!empty($post_data['prc_license'])) {
        if (!validatePRCLicense($post_data['prc_license'])) {
            $errors[] = "Invalid PRC license number format.";
        }
    }
    
    // Validate dates
    if (!empty($post_data['prc_registration_date']) && !empty($post_data['prc_expiration_date'])) {
        $date_errors = validateDates($post_data['prc_registration_date'], $post_data['prc_expiration_date']);
        $errors = array_merge($errors, $date_errors);
    }
    
    // Validate team members
    if (empty($post_data['members']) || !is_array($post_data['members'])) {
        $errors[] = "At least one team member is required.";
    } else {
        foreach ($post_data['members'] as $index => $member) {
            if (empty($member['name'])) {
                $errors[] = "Team member #$index name is required.";
            } elseif (strlen($member['name']) > 255) {
                $errors[] = "Team member #$index name is too long.";
            } elseif (!preg_match('/^[a-zA-Z\s\-\.]+$/', $member['name'])) {
                $errors[] = "Team member #$index name contains invalid characters.";
            }
        }
    }
    
    // Validate captcha
    if (empty($post_data['captcha']) || !validateCaptcha($post_data['captcha'])) {
        $errors[] = "Invalid security code.";
    }
    
    // Validate file uploads
    $required_files = ['natcon_proof', 'payment_proof'];
    
    foreach ($required_files as $file_field) {
        if (isset($files_data[$file_field])) {
            $file_errors = validateFileUpload($files_data[$file_field], $file_field);
            $errors = array_merge($errors, $file_errors);
        } else {
            $errors[] = ucfirst(str_replace('_', ' ', $file_field)) . " is required.";
        }
    }
    
    // Validate team member proof files
    if (isset($post_data['members']) && is_array($post_data['members'])) {
        foreach ($post_data['members'] as $index => $member) {
            $proof_field = "members_{$index}_proof";
            if (isset($files_data['members'])) {
                if (isset($files_data['members']['tmp_name'][$index]['proof'])) {
                    $member_file = [
                        'tmp_name' => $files_data['members']['tmp_name'][$index]['proof'],
                        'size' => $files_data['members']['size'][$index]['proof'],
                        'error' => $files_data['members']['error'][$index]['proof'],
                        'name' => $files_data['members']['name'][$index]['proof']
                    ];
                    $file_errors = validateFileUpload($member_file, "team_member_{$index}_proof");
                    $errors = array_merge($errors, $file_errors);
                } else {
                    $errors[] = "Team member #$index proof of enrollment is required.";
                }
            }
        }
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Begin transaction
    $conn->autocommit(FALSE);
    
    try {
        // Generate registration ID
        $registration_id = 'REG' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if registration ID already exists
        $stmt = $conn->prepare("SELECT id FROM registrations WHERE registration_id = ?");
        $stmt->bind_param("s", $registration_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($result->num_rows > 0) {
            $registration_id = 'REG' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt->bind_param("s", $registration_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
        // Upload and save files
        $uploaded_files = [];
        
        // Upload main files
        foreach ($required_files as $file_field) {
            if (isset($files_data[$file_field])) {
                $secure_filename = generateSecureFilename($files_data[$file_field]['name']);
                $upload_path = UPLOAD_PATH . $registration_id . '/' . $secure_filename;
                
                if (moveUploadedFile($files_data[$file_field], $upload_path)) {
                    $uploaded_files[$file_field] = $secure_filename;
                } else {
                    throw new Exception("Failed to upload " . str_replace('_', ' ', $file_field));
                }
            }
        }
        
        // Upload team member proof files
        if (isset($files_data['members'])) {
            foreach ($post_data['members'] as $index => $member) {
                if (isset($files_data['members']['tmp_name'][$index]['proof'])) {
                    $member_file = [
                        'tmp_name' => $files_data['members']['tmp_name'][$index]['proof'],
                        'size' => $files_data['members']['size'][$index]['proof'],
                        'error' => $files_data['members']['error'][$index]['proof'],
                        'name' => $files_data['members']['name'][$index]['proof']
                    ];
                    
                    $secure_filename = generateSecureFilename($member_file['name']);
                    $upload_path = UPLOAD_PATH . $registration_id . '/members/' . $secure_filename;
                    
                    if (moveUploadedFile($member_file, $upload_path)) {
                        $uploaded_files["member_{$index}_proof"] = $secure_filename;
                    } else {
                        throw new Exception("Failed to upload team member #$index proof");
                    }
                }
            }
        }
        
        // Insert main registration record
        $stmt = $conn->prepare("
            INSERT INTO registrations (
                registration_id, institution, coach_name, prc_license, 
                prc_registration_date, prc_expiration_date, payment_reference,
                natcon_proof_file, payment_proof_file, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->bind_param("sssssssss",
            $registration_id,
            $post_data['institution'],
            $post_data['coach_name'],
            $post_data['prc_license'],
            $post_data['prc_registration_date'],
            $post_data['prc_expiration_date'],
            $post_data['payment_reference'] ?? '',
            $uploaded_files['natcon_proof'],
            $uploaded_files['payment_proof']
        );
        $stmt->execute();
        
        $registration_db_id = $conn->insert_id;
        
        // Insert team members
        foreach ($post_data['members'] as $index => $member) {
            $stmt = $conn->prepare("
                INSERT INTO team_members (registration_id, name, proof_file, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->bind_param("iss",
                $registration_db_id,
                $member['name'],
                $uploaded_files["member_{$index}_proof"] ?? ''
            );
            $stmt->execute();
        }
        
        // Log the registration
        logSecurityEvent('registration', "New registration submitted: $registration_id");
        
        // Clean old rate limits
        cleanRateLimits();
        
        $conn->commit();
        
        return ['success' => true, 'registration_id' => $registration_id];
        
    } catch (Exception $e) {
        $conn->rollback();
        
        // Clean up uploaded files on error
        foreach ($uploaded_files as $file) {
            $file_path = UPLOAD_PATH . $registration_id . '/' . $file;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'errors' => ['An error occurred while processing your registration. Please try again.']];
    }
}

/**
 * Validate captcha
 */
function validateCaptcha($user_input) {
    if (!isset($_SESSION['captcha'])) {
        return false;
    }
    
    $is_valid = hash_equals(strtolower($_SESSION['captcha']), strtolower($user_input));
    
    // Clear captcha after validation
    unset($_SESSION['captcha']);
    
    return $is_valid;
}
?>
