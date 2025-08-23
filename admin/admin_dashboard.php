<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/security.php';

// Get registration statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_registrations,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_registrations,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_registrations,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_registrations
    FROM registrations
";
$result = $conn->query($stats_query);
$stats = $result->fetch_assoc();

// Get recent registrations
$recent_query = "
    SELECT r.*, COUNT(tm.id) as team_count
    FROM registrations r
    LEFT JOIN team_members tm ON r.id = tm.registration_id
    GROUP BY r.id
    ORDER BY r.created_at DESC
    LIMIT 10
";
$result = $conn->query($recent_query);
$recent_registrations = [];
while ($row = $result->fetch_assoc()) {
    $recent_registrations[] = $row;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $registration_id = $_POST['registration_id'];
    $new_status = $_POST['status'];
    $admin_notes = $_POST['admin_notes'] ?? '';
    
    $update_query = "UPDATE registrations SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $new_status, $admin_notes, $registration_id);
    $stmt->execute();
    
    logSecurityEvent('admin_action', "Registration status updated to $new_status by admin", $_SESSION['admin_user']);
    
    header('Location: admin_dashboard.php?success=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JPSME Event Registration</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="admin-logo">
                <h2><i data-feather="graduation-cap"></i> JPSME Admin</h2>
            </div>
            <ul class="admin-menu">
                <li><a href="admin_dashboard.php" class="active"><i data-feather="home"></i> Dashboard</a></li>
                <li><a href="registrations.php"><i data-feather="users"></i> Registrations</a></li>
                <li><a href="reports.php"><i data-feather="bar-chart-2"></i> Reports</a></li>
                <li><a href="settings.php"><i data-feather="settings"></i> Settings</a></li>
                <li><a href="security_logs.php"><i data-feather="shield"></i> Security Logs</a></li>
                <li><a href="admin_logout.php"><i data-feather="log-out"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                </div>
            </header>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i data-feather="check-circle"></i> Operation completed successfully!
                </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i data-feather="users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_registrations']; ?></h3>
                        <p>Total Registrations</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i data-feather="clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_registrations']; ?></h3>
                        <p>Pending Review</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i data-feather="check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['approved_registrations']; ?></h3>
                        <p>Approved</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon rejected">
                        <i data-feather="x"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['rejected_registrations']; ?></h3>
                        <p>Rejected</p>
                    </div>
                </div>
            </div>
            
            <div class="content-section">
                <h2>Recent Registrations</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Registration ID</th>
                                <th>Institution</th>
                                <th>Coach</th>
                                <th>Team Size</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_registrations as $registration): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($registration['registration_id']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($registration['institution']); ?></td>
                                    <td><?php echo htmlspecialchars($registration['coach_name']); ?></td>
                                    <td>
                                        <span class="badge"><?php echo $registration['team_count']; ?> members</span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $registration['status']; ?>">
                                            <?php echo ucfirst($registration['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($registration['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_registration.php?id=<?php echo $registration['id']; ?>" 
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <button onclick="updateStatus(<?php echo $registration['id']; ?>, '<?php echo $registration['status']; ?>')" 
                                                    class="btn btn-sm btn-secondary" title="Update Status">
                                                <i data-feather="edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center" style="margin-top: 20px;">
                    <a href="registrations.php" class="btn btn-primary">
                        <i data-feather="list"></i> View All Registrations
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Registration Status</h3>
                <span class="close">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="registration_id" id="modal_registration_id">
                <input type="hidden" name="update_status" value="1">
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="modal_status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="incomplete">Incomplete</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="admin_notes">Admin Notes:</label>
                    <textarea name="admin_notes" id="modal_admin_notes" rows="4" 
                              placeholder="Add notes about this status change..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Status
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateStatus(registrationId, currentStatus) {
            document.getElementById('modal_registration_id').value = registrationId;
            document.getElementById('modal_status').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        
        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
        
        // Logout animation
        const logoutLink = document.querySelector('a[href="admin_logout.php"]');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Create logout overlay
                const overlay = document.createElement('div');
                overlay.className = 'logout-overlay';
                overlay.innerHTML = `
                    <div class="logout-content">
                        <div class="logout-icon">
                            <i data-feather="log-out"></i>
                        </div>
                        <h2>Logging Out...</h2>
                        <p>Please wait while we securely log you out.</p>
                    </div>
                `;
                
                document.body.appendChild(overlay);
                
                // Replace feather icons in overlay
                feather.replace();
                
                // Show overlay
                setTimeout(() => {
                    overlay.classList.add('active');
                }, 10);
                
                // Redirect after animation
                setTimeout(() => {
                    window.location.href = 'admin_logout.php';
                }, 1500);
            });
        }
        
        // Page load animation
        document.body.classList.add('page-transition');
    </script>
    <script>
        // Initialize Feather icons
        feather.replace();
    </script>
</body>
</html>
