<?php
// registration_form.php

// Include security configurations
include_once 'security.php';
include 'db.php';

function render_registration_form($competition_type, $competition_title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/style.css">
        <link rel="icon" type="image/x-icon" href="fav/favicon.ico">
        <link rel="shortcut icon" href="fav/favicon.ico">
        <script src="https://unpkg.com/feather-icons"></script>
        <title><?php echo htmlspecialchars($competition_type); ?> Registration | JPSME Event</title>
    </head>
    <body>
    <div class="container">
        <h2><span class="icon" data-feather="edit-3"></span><?php echo htmlspecialchars($competition_type); ?> Registration</h2>
        <form action="submit_registration.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="competition_type" value="<?php echo htmlspecialchars($competition_type); ?>">

            <div class="form-group">
                <label><span class="icon" data-feather="home"></span>University / Institution:</label>
                <input type="text" name="university" required>
            </div>

            <div class="form-group">
                <label><span class="icon" data-feather="file-text"></span>Proof of Registration to NatCon:</label>
                <input type="file" name="proof_natcon" required>
            </div>

            <!-- Team Members Section -->
            <section class="form-section">
                <h2><span class="icon" data-feather="users"></span>Team Members</h2>
                <div id="team-members">
                    <div class="team-member">
                        <div class="team-member-header">
                            <span class="team-member-title">Member 1</span>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Name of Member:</label>
                                <input type="text" name="member_names[]" required>
                            </div>
                            <div class="form-group">
                                <label>Proof of Enrollment:</label>
                                <input type="file" name="member_enrollments[]" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-member" onclick="addTeamMember()">
                    <span class="icon" data-feather="plus"></span>Add Another Member
                </button>
            </section>

            <div class="form-row">
                <div class="form-group">
                    <label><span class="icon" data-feather="user"></span>Name of Coach:</label>
                    <input type="text" name="coach_name" required>
                </div>
                <div class="form-group">
                    <label><span class="icon" data-feather="award"></span>PRC License Number:</label>
                    <input type="text" name="prc_license" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><span class="icon" data-feather="calendar"></span>Date of Registration:</label>
                    <input type="date" name="prc_reg_date" required>
                </div>
                <div class="form-group">
                    <label><span class="icon" data-feather="calendar"></span>Date of Expiration:</label>
                    <input type="date" name="prc_exp_date" required>
                </div>
            </div>

            <!-- Payment Information -->
            <section class="form-section">
                <h2><span class="icon" data-feather="credit-card"></span>Payment Information</h2>
                <div class="payment-info">
                    <div class="payment-instructions">
                        <h3>Payment Instructions</h3>
                        <p>Please make your payment through the following methods and upload proof of payment:</p>
                        <ul>
                            <li><strong>Bank Transfer:</strong> Account Name: JPSME Event Committee | Account Number: 1234567890 | Bank: BPI</li>
                            <li><strong>GCash:</strong> 09123456789</li>
                            <li><strong>PayMaya:</strong> 09123456789</li>
                        </ul>
                        <p><strong>Registration Fee:</strong> ₱500.00</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="payment_proof" class="required"><span class="icon" data-feather="credit-card"></span>Proof of Payment for Competition Registration</label>
                    <input type="file" id="payment_proof" name="proof_payment" required accept=".pdf,.jpg,.jpeg,.png">
                    <small>Upload receipt, screenshot, or bank transfer confirmation (PDF, JPG, PNG - Max: 5MB)</small>
                    <div class="error-message" id="payment_proof-error"></div>
                </div>
                <div class="form-group">
                    <label for="payment_reference"><span class="icon" data-feather="hash"></span>Payment Reference Number (Optional)</label>
                    <input type="text" id="payment_reference" name="payment_reference" maxlength="100">
                    <small>Reference number from your payment method (if applicable)</small>
                </div>
            </section>

            <button type="submit"><span class="icon" data-feather="check-circle"></span>Register</button>
        </form>
    </div>
    <script>
        let memberCount = 1;
        
        function updateMemberNumbers() {
            const teamMembers = document.querySelectorAll('.team-member');
            teamMembers.forEach((member, index) => {
                const title = member.querySelector('.team-member-title');
                title.textContent = `Member ${index + 1}`;
            });
            memberCount = teamMembers.length;
        }
        
        function addTeamMember() {
            memberCount++;
            const teamMembersDiv = document.getElementById('team-members');
            const newMember = document.createElement('div');
            newMember.className = 'team-member';
            newMember.innerHTML = `
                <div class="team-member-header">
                    <span class="team-member-title">Member ${memberCount}</span>
                    <button type="button" class="remove-member" onclick="removeTeamMember(this)">Remove</button>
                </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Name of Member:</label>
                                <input type="text" name="member_names[]" required>
                            </div>
                            <div class="form-group">
                                <label>Proof of Enrollment:</label>
                                <input type="file" name="member_enrollments[]" required>
                            </div>
                        </div>
            `;
            teamMembersDiv.appendChild(newMember);
            updateMemberNumbers();
            feather.replace();
        }
        
        function removeTeamMember(button) {
            const teamMembersDiv = document.getElementById('team-members');
            const members = teamMembersDiv.querySelectorAll('.team-member');
            
            // Don't allow removing the last member
            if (members.length > 1) {
                button.closest('.team-member').remove();
                updateMemberNumbers();
            } else {
                alert('At least one team member is required.');
            }
        }
        
        feather.replace();
    </script>
    </body>
    </html>
    <?php
}
?>
