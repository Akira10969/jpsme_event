<?php
// submit_registration.php

// Include security configurations
include 'security.php';
include 'db.php';

// Ensure database connection is available
if ($conn->connect_error) {
    header("Location: 404.php");
    exit();
}

function upload_file($file, $folder) {
    // Security: Check if file was actually uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return '';
    }
    
    // Security: Validate file size (5MB max)
    $max_file_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_file_size) {
        return '';
    }
    
    // Security: Validate file extension
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        return '';
    }
    
    // Security: Generate unique filename to prevent overwriting
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
    
    $target_dir = $folder . "/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // More secure permissions
    }
    
    $target_file = $target_dir . $unique_filename;
    
    // Security: Double-check the final path is within intended directory
    $real_target_dir = realpath($target_dir);
    $real_target_file = realpath(dirname($target_file)) . '/' . basename($target_file);
    
    if (strpos($real_target_file, $real_target_dir) !== 0) {
        return ''; // Path traversal attempt detected
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Security: Validate and sanitize input data
    $competition_type = isset($_POST['competition_type']) ? trim(htmlspecialchars($_POST['competition_type'], ENT_QUOTES, 'UTF-8')) : '';
    $university = isset($_POST['university']) ? trim(htmlspecialchars($_POST['university'], ENT_QUOTES, 'UTF-8')) : '';
    $coach_name = isset($_POST['coach_name']) ? trim(htmlspecialchars($_POST['coach_name'], ENT_QUOTES, 'UTF-8')) : '';
    $prc_license = isset($_POST['prc_license']) ? trim(htmlspecialchars($_POST['prc_license'], ENT_QUOTES, 'UTF-8')) : '';
    $prc_reg_date = isset($_POST['prc_reg_date']) ? trim($_POST['prc_reg_date']) : '';
    $prc_exp_date = isset($_POST['prc_exp_date']) ? trim($_POST['prc_exp_date']) : '';
    $payment_reference = isset($_POST['payment_reference']) ? trim(htmlspecialchars($_POST['payment_reference'], ENT_QUOTES, 'UTF-8')) : '';

    // Security: Validate required fields
    if (empty($competition_type) || empty($university) || empty($coach_name) || empty($prc_license)) {
        header("Location: 404.php");
        exit();
    }

    // Security: Validate competition type against allowed values
    $allowed_competitions = ['machine_design', 'quizbee'];
    if (!in_array($competition_type, $allowed_competitions)) {
        header("Location: 404.php");
        exit();
    }

    // Security: Validate date formats
    if (!empty($prc_reg_date) && !DateTime::createFromFormat('Y-m-d', $prc_reg_date)) {
        header("Location: 404.php");
        exit();
    }
    if (!empty($prc_exp_date) && !DateTime::createFromFormat('Y-m-d', $prc_exp_date)) {
        header("Location: 404.php");
        exit();
    }

    // Handle team members with validation
    $member_names = isset($_POST['member_names']) ? $_POST['member_names'] : [];
    $sanitized_members = [];
    foreach ($member_names as $name) {
        $clean_name = trim(htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
        if (!empty($clean_name)) {
            $sanitized_members[] = $clean_name;
        }
    }
    $team_members = implode(', ', $sanitized_members);

    $proof_natcon = upload_file($_FILES['proof_natcon'], 'uploads/proof_natcon');
    $proof_payment = upload_file($_FILES['proof_payment'], 'uploads/proof_payment');
    
    // Handle multiple member enrollment files
    $member_enrollment_files = [];
    if (isset($_FILES['member_enrollments'])) {
        foreach ($_FILES['member_enrollments']['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $file = [
                    'name' => $_FILES['member_enrollments']['name'][$key],
                    'tmp_name' => $tmp_name
                ];
                $uploaded_file = upload_file($file, 'uploads/member_enrollments');
                if ($uploaded_file) {
                    $member_enrollment_files[] = $uploaded_file;
                }
            }
        }
    }
    $proof_enrollment = implode(', ', $member_enrollment_files);

    // Prepare and execute database insert with error handling
    if (isset($conn) && !$conn->connect_error) {
        $stmt = $conn->prepare("INSERT INTO registrations (competition_type, university, proof_natcon, team_members, proof_enrollment, coach_name, prc_license, prc_reg_date, prc_exp_date, proof_payment, payment_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssssssssss", $competition_type, $university, $proof_natcon, $team_members, $proof_enrollment, $coach_name, $prc_license, $prc_reg_date, $prc_exp_date, $proof_payment, $payment_reference);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: success.php");
                exit();
            } else {
                $stmt->close();
                $conn->close();
                header("Location: 404.php");
                exit();
            }
        } else {
            $conn->close();
            header("Location: 404.php");
            exit();
        }
    } else {
        header("Location: 404.php");
        exit();
    }
}
?>
