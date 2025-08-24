<?php
// submit_registration.php

include 'db.php';

function upload_file($file, $folder) {
    $target_dir = $folder . "/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($file["name"]);
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_type = $_POST['competition_type'];
    $university = $_POST['university'];
    $coach_name = $_POST['coach_name'];
    $prc_license = $_POST['prc_license'];
    $prc_reg_date = $_POST['prc_reg_date'];
    $prc_exp_date = $_POST['prc_exp_date'];
    $payment_reference = $_POST['payment_reference'] ?? '';

    // Handle team members
    $member_names = $_POST['member_names'];
    $team_members = implode(', ', $member_names);

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

    $stmt = $conn->prepare("INSERT INTO registrations (competition_type, university, proof_natcon, team_members, proof_enrollment, coach_name, prc_license, prc_reg_date, prc_exp_date, proof_payment, payment_reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $competition_type, $university, $proof_natcon, $team_members, $proof_enrollment, $coach_name, $prc_license, $prc_reg_date, $prc_exp_date, $proof_payment, $payment_reference);
    if ($stmt->execute()) {
        header("Location: success.php");
        exit();
    } else {
        header("Location: 404.php");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>
