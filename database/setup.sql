-- Database setup for JPSME Event Registration System
-- Run this script to create the necessary tables

CREATE DATABASE IF NOT EXISTS jpsme_event;
USE jpsme_event;

-- Main registrations table
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(20) UNIQUE NOT NULL,
    institution VARCHAR(255) NOT NULL,
    coach_name VARCHAR(255) NOT NULL,
    prc_license VARCHAR(50) NOT NULL,
    prc_registration_date DATE NOT NULL,
    prc_expiration_date DATE NOT NULL,
    payment_reference VARCHAR(100),
    natcon_proof_file VARCHAR(255) NOT NULL,
    payment_proof_file VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'incomplete') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_registration_id (registration_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Team members table
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    proof_file VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    INDEX idx_registration_id (registration_id)
);

-- Rate limiting table
CREATE TABLE rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_ip_created (ip_address, created_at)
);

-- Security logs table
CREATE TABLE security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address)
);

-- Admin users table (for future admin panel)
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'viewer') DEFAULT 'viewer',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- File uploads tracking table
CREATE TABLE file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    upload_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    INDEX idx_registration_id (registration_id),
    INDEX idx_file_type (file_type)
);

-- Settings table for system configuration
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('registration_fee', '500', 'Registration fee amount in PHP'),
('max_team_members', '10', 'Maximum number of team members allowed'),
('registration_open', '1', 'Whether registration is currently open (1=open, 0=closed)'),
('payment_instructions', 'Bank Transfer: BDO - Account Name: JPSME Event | Account Number: 123456789\nGCash: 09123456789\nPayMaya: 09123456789', 'Payment instructions displayed to users'),
('contact_email', 'admin@jpsme-event.com', 'Contact email for support'),
('event_name', 'JPSME National Conference 2025', 'Name of the event'),
('event_date', '2025-12-01', 'Date of the event'),
('registration_deadline', '2025-11-15', 'Registration deadline');

-- Create default admin user (password: admin123)
-- Note: Change this password immediately after setup
INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@jpsme-event.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqyT4VyFkjn8ahATkUMsTAm', 'System Administrator', 'admin');

-- Create views for reporting
CREATE VIEW registration_summary AS
SELECT 
    r.registration_id,
    r.institution,
    r.coach_name,
    r.status,
    COUNT(tm.id) as team_member_count,
    r.created_at
FROM registrations r
LEFT JOIN team_members tm ON r.id = tm.registration_id
GROUP BY r.id;

CREATE VIEW pending_registrations AS
SELECT 
    r.registration_id,
    r.institution,
    r.coach_name,
    r.prc_license,
    r.prc_expiration_date,
    COUNT(tm.id) as team_member_count,
    r.created_at
FROM registrations r
LEFT JOIN team_members tm ON r.id = tm.registration_id
WHERE r.status = 'pending'
GROUP BY r.id
ORDER BY r.created_at DESC;

-- Create indexes for better performance
CREATE INDEX idx_registrations_coach_prc ON registrations(coach_name, prc_license);
CREATE INDEX idx_team_members_name ON team_members(name);
CREATE INDEX idx_security_logs_event_ip ON security_logs(event_type, ip_address);

-- Create triggers for audit trail
DELIMITER //

CREATE TRIGGER registration_status_audit 
AFTER UPDATE ON registrations
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO security_logs (event_type, description, created_at)
        VALUES ('status_change', 
                CONCAT('Registration ', NEW.registration_id, ' status changed from ', OLD.status, ' to ', NEW.status),
                NOW());
    END IF;
END//

DELIMITER ;

-- Sample procedure for cleanup old data
DELIMITER //

CREATE PROCEDURE CleanupOldData()
BEGIN
    -- Clean old rate limits (older than 7 days)
    DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
    
    -- Clean old security logs (older than 90 days)
    DELETE FROM security_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    
    -- Reset failed login attempts for unlocked accounts
    UPDATE admin_users 
    SET failed_login_attempts = 0, locked_until = NULL 
    WHERE locked_until IS NOT NULL AND locked_until < NOW();
END//

DELIMITER ;

-- Create event scheduler to run cleanup daily
-- SET GLOBAL event_scheduler = ON;
-- CREATE EVENT daily_cleanup
-- ON SCHEDULE EVERY 1 DAY
-- STARTS CURRENT_TIMESTAMP
-- DO CALL CleanupOldData();

-- Grant permissions (adjust as needed for your setup)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON jpsme_event.* TO 'jpsme_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Display completion message
SELECT 'Database setup completed successfully!' as message;
