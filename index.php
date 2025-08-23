<?php
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database configuration
require_once 'config/database.php';
require_once 'includes/security.php';
require_once 'includes/validation.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
    
    // Rate limiting check
    if (!checkRateLimit($_SERVER['REMOTE_ADDR'])) {
        $errors[] = 'Too many submission attempts. Please try again later.';
    } else {
        // Process form submission
        $result = processRegistration($_POST, $_FILES);
        if ($result['success']) {
            $success = true;
            $_SESSION['registration_id'] = $result['registration_id'];
        } else {
            $errors = $result['errors'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NatCon Event Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1><i data-feather="graduation-cap"></i> NatCon Event Registration</h1>
            <p>Secure Registration Portal</p>
        </header>

        <?php if ($success): ?>
            <div class="success-message">
                <i data-feather="check-circle"></i>
                <h2>Registration Submitted Successfully!</h2>
                <p>Your registration ID is: <strong><?php echo htmlspecialchars($_SESSION['registration_id']); ?></strong></p>
                <p>Please save this ID for future reference. Your registration is pending manual verification.</p>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <h3><i data-feather="alert-triangle"></i> Please correct the following errors:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="registrationForm" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <!-- Institution Information -->
                <section class="form-section">
                    <h2><i data-feather="building"></i> Institution Information</h2>
                    <div class="form-group">
                        <label for="institution" class="required">University / Institution</label>
                        <input type="text" id="institution" name="institution" required maxlength="255" 
                               value="<?php echo htmlspecialchars($_POST['institution'] ?? ''); ?>">
                        <div class="error-message" id="institution-error"></div>
                    </div>
                </section>

                <!-- NatCon Registration Proof -->
                <section class="form-section">
                    <h2><i data-feather="file-text"></i> NatCon Registration</h2>
                    <div class="form-group">
                        <label for="natcon_proof" class="required">Proof of Registration to NatCon</label>
                        <input type="file" id="natcon_proof" name="natcon_proof" required 
                               accept=".pdf,.jpg,.jpeg,.png" data-max-size="5242880">
                        <small>Accepted formats: PDF, JPG, PNG (Max: 5MB)</small>
                        <div class="error-message" id="natcon_proof-error"></div>
                    </div>
                </section>

                <!-- Team Members -->
                <section class="form-section">
                    <h2><i data-feather="users"></i> Team Members</h2>
                    <div id="team-members">
                        <div class="team-member" data-member="1">
                            <h3>Team Member 1</h3>
                            <div class="form-group">
                                <label for="member_1_name" class="required">Full Name</label>
                                <input type="text" id="member_1_name" name="members[1][name]" required maxlength="255"
                                       value="<?php echo htmlspecialchars($_POST['members'][1]['name'] ?? ''); ?>">
                                <div class="error-message" id="member_1_name-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="member_1_proof" class="required">Proof of Enrollment</label>
                                <input type="file" id="member_1_proof" name="members[1][proof]" required 
                                       accept=".pdf,.jpg,.jpeg,.png" data-max-size="5242880">
                                <small>Accepted formats: PDF, JPG, PNG (Max: 5MB)</small>
                                <div class="error-message" id="member_1_proof-error"></div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-member" class="btn-secondary">
                        <i data-feather="plus"></i> Add Team Member
                    </button>
                </section>

                <!-- Coach Information -->
                <section class="form-section">
                    <h2><i data-feather="user-check"></i> Coach Information</h2>
                    <div class="form-group">
                        <label for="coach_name" class="required">Name of Coach</label>
                        <input type="text" id="coach_name" name="coach_name" required maxlength="255"
                               value="<?php echo htmlspecialchars($_POST['coach_name'] ?? ''); ?>">
                        <div class="error-message" id="coach_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="prc_license" class="required">PRC License Number</label>
                        <input type="text" id="prc_license" name="prc_license" required maxlength="50"
                               value="<?php echo htmlspecialchars($_POST['prc_license'] ?? ''); ?>">
                        <div class="error-message" id="prc_license-error"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prc_registration_date" class="required">Date of Registration</label>
                            <input type="date" id="prc_registration_date" name="prc_registration_date" required
                                   value="<?php echo htmlspecialchars($_POST['prc_registration_date'] ?? ''); ?>">
                            <div class="error-message" id="prc_registration_date-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="prc_expiration_date" class="required">Date of Expiration</label>
                            <input type="date" id="prc_expiration_date" name="prc_expiration_date" required
                                   value="<?php echo htmlspecialchars($_POST['prc_expiration_date'] ?? ''); ?>">
                            <div class="error-message" id="prc_expiration_date-error"></div>
                        </div>
                    </div>
                </section>

                <!-- Payment Information -->
                <section class="form-section">
                    <h2><i data-feather="credit-card"></i> Payment Information</h2>
                    <div class="payment-info">
                        <div class="payment-instructions">
                            <h3>Payment Instructions</h3>
                            <p>Please make your payment through the following methods and upload proof of payment:</p>
                            <ul>
                                <li><strong>Bank Transfer:</strong> Account Name: [Account Name] | Account Number: [Number] | Bank: [Bank Name]</li>
                                <li><strong>GCash:</strong> [GCash Number]</li>
                                <li><strong>PayMaya:</strong> [PayMaya Number]</li>
                            </ul>
                            <p><strong>Registration Fee:</strong> ₱[Amount]</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="payment_proof" class="required">Proof of Payment for Competition Registration</label>
                        <input type="file" id="payment_proof" name="payment_proof" required 
                               accept=".pdf,.jpg,.jpeg,.png" data-max-size="5242880">
                        <small>Upload receipt, screenshot, or bank transfer confirmation (PDF, JPG, PNG - Max: 5MB)</small>
                        <div class="error-message" id="payment_proof-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="payment_reference">Payment Reference Number (Optional)</label>
                        <input type="text" id="payment_reference" name="payment_reference" maxlength="100"
                               value="<?php echo htmlspecialchars($_POST['payment_reference'] ?? ''); ?>">
                        <small>Reference number from your payment method (if applicable)</small>
                    </div>
                </section>

                <!-- Security Verification -->
                <section class="form-section">
                    <h2><i data-feather="shield"></i> Security Verification</h2>
                    <div class="form-group">
                        <label for="captcha" class="required">Security Code</label>
                        <div class="captcha-container">
                            <img src="captcha.php" alt="Security Code" id="captcha-image">
                            <button type="button" id="refresh-captcha" title="Refresh Code">
                                <i data-feather="refresh-cw"></i>
                            </button>
                        </div>
                        <input type="text" id="captcha" name="captcha" required maxlength="6" 
                               placeholder="Enter the code shown above">
                        <div class="error-message" id="captcha-error"></div>
                    </div>
                </section>

                <div class="form-actions">
                    <button type="submit" class="btn-primary" id="submit-btn">
                        <i data-feather="send"></i> Submit Registration
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="assets/js/registration.js"></script>
    <script>
        // Initialize Feather icons
        feather.replace();
    </script>
</body>
</html>
