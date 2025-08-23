# JPSME Event Registration System

A modern, secure, and feature-rich registration system for the JPSME National Conference with stunning animations, comprehensive security features, and an intuitive admin panel.

## ✨ **Latest Updates**

- 🎨 **Beautiful Animations**: Smooth login/logout animations with loading states
- 🎯 **Modern UI**: Royal blue and gold color scheme with Feather icons
- 🔒 **Enhanced Security**: mysqli database connections with improved error handling
- 🚀 **Performance Optimized**: Fast loading with optimized code structure
- 📱 **Responsive Design**: Works perfectly on all devices

## 🌟 **Key Features**

### 🔐 **Advanced Security**
- **CSRF Protection**: Cross-Site Request Forgery protection on all forms
- **Rate Limiting**: Intelligent spam and brute force attack prevention
- **File Upload Security**: Multi-layer validation (type, size, content signatures)
- **Input Sanitization**: Comprehensive validation and XSS protection
- **SQL Injection Prevention**: mysqli prepared statements throughout
- **Session Security**: Secure session management with regeneration
- **Admin Account Lockout**: Smart protection against brute force attempts
- **Security Logging**: Comprehensive audit trail for all activities

### 🎨 **Modern User Experience**
- **Smooth Animations**: Login/logout animations with loading states
- **Interactive Elements**: Hover effects, button ripples, and transitions
- **Royal Blue & Gold Theme**: Professional color scheme throughout
- **Feather Icons**: Clean, modern iconography
- **Responsive Design**: Perfect experience on desktop, tablet, and mobile
- **Loading Indicators**: Visual feedback for all user actions
- **Form Validation**: Real-time validation with animated feedback

### 📋 **Registration Management**
- **Institution Information**: Complete university/institution details
- **NatCon Proof Upload**: Secure file upload for registration verification
- **Dynamic Team Management**: Add up to 10 team members with individual proofs
- **Coach Validation**: PRC license verification with expiration checking
- **Payment Processing**: Manual payment proof upload and verification
- **File Security**: Multi-format support (PDF, JPG, PNG) with content validation
- **Registration Tracking**: Unique ID generation and status tracking

### �‍💼 **Powerful Admin Panel**
- **Animated Dashboard**: Beautiful statistics and overview with smooth transitions
- **Registration Management**: Approve, reject, and manage registrations
- **Advanced Filtering**: Search and filter registrations by status, date, institution
- **Security Monitoring**: Real-time login attempts and security event logs
- **User Management**: Admin account creation and management
- **Export Features**: Generate reports and export registration data
- **Audit Trail**: Complete activity logging for compliance

## 🛠 **Technical Specifications**

### **System Requirements**
- **XAMPP** (or similar LAMP/WAMP stack)
- **PHP 8.0+** with GD extension and mysqli support
- **MySQL 5.7+** or **MariaDB 10.2+**
- **Apache 2.4+** with mod_rewrite enabled
- **4GB RAM** minimum (8GB recommended)
- **500MB disk space** minimum

### **Technologies Used**
- **Backend**: PHP 8.2 with mysqli database connections
- **Database**: MySQL/MariaDB with optimized schema
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Icons**: Feather Icons library
- **Security**: Custom security framework with rate limiting
- **File Handling**: Secure upload system with content validation

## 🚀 **Installation Guide**

### **Quick Setup**

1. **Download & Extract**
   ```bash
   # Clone the repository
   git clone https://github.com/Akira10969/jpsme_event.git
   
   # OR extract to: d:\xampp\htdocs\jpsme_event
   ```

2. **Database Configuration**
   ```sql
   -- Start XAMPP and open phpMyAdmin
   -- Create database and run setup script:
   SOURCE d:\xampp\htdocs\jpsme_event\database\setup.sql;
   ```

3. **Configure Database Connection**
   Edit `config/database.php` (if needed):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'jpsme_event');
   define('DB_USER', 'root');        // Default XAMPP user
   define('DB_PASS', '');            // Default XAMPP password (empty)
   ```

4. **Set Directory Permissions**
   ```bash
   # Ensure uploads directory exists and is writable
   mkdir d:\xampp\htdocs\jpsme_event\uploads
   # Set appropriate permissions for web server access
   ```

5. **Access the Application**
   ```
   Registration Form: http://localhost/jpsme_event/
   Admin Panel:       http://localhost/jpsme_event/admin/admin_login.php
   ```

### **Default Admin Credentials**
```
Username: admin
Password: admin123
Email:    admin@jpsme-event.com
```
> ⚠️ **Important**: Change the default password immediately after first login!

## 🎯 **Usage Guide**

### **For Users (Registration)**

1. **Access Registration Form**
   - Navigate to `http://localhost/jpsme_event/`
   - Complete all required fields with accurate information

2. **Institution Information**
   - Enter complete university/institution details
   - Provide valid coach information with PRC license

3. **Upload Required Documents**
   - **NatCon Proof**: Registration verification document
   - **Payment Proof**: Payment receipt or verification
   - **Team Member Proofs**: Individual enrollment proofs

4. **Submit Registration**
   - Review all information before submission
   - Receive unique registration ID for tracking
   - Wait for admin approval/rejection

### **For Administrators**

1. **Login to Admin Panel**
   - Access: `http://localhost/jpsme_event/admin/admin_login.php`
   - Use provided credentials (change default password)

2. **Dashboard Overview**
   - View registration statistics and recent activity
   - Monitor system security and performance metrics

3. **Manage Registrations**
   - Review submitted registrations
   - Approve or reject applications with notes
   - Download uploaded documents for verification

4. **Security Monitoring**
   - View security logs and login attempts
   - Monitor rate limiting and suspicious activities
   - Manage admin user accounts

## 📁 **Project Structure**

```
jpsme_event/
├── 📂 admin/                    # Admin panel files
│   ├── admin_login.php         # Admin login with animations
│   ├── admin_logout.php        # Animated logout page
│   └── admin_dashboard.php     # Main admin dashboard
├── 📂 assets/                   # Static assets
│   ├── css/                    # Stylesheets
│   │   ├── style.css          # Main registration form styles
│   │   └── admin.css          # Admin panel styles with animations
│   └── js/                     # JavaScript files
│       └── registration.js     # Form validation and interactions
├── 📂 config/                   # Configuration files
│   └── database.php           # Database connection (mysqli)
├── 📂 database/                 # Database setup
│   └── setup.sql              # Complete database schema
├── 📂 includes/                 # PHP include files
│   ├── security.php           # Security functions with error handling
│   └── validation.php         # Form validation and processing
├── 📂 uploads/                  # File upload directory
│   └── [registration_files]   # Organized by registration ID
├── 📄 index.php                # Main registration form
├── 📄 captcha.php              # CAPTCHA generation (registration only)
├── 📄 .htaccess                # Apache configuration
└── 📄 README.md                # This documentation
```

## 🔧 **Configuration Options**

### **Security Settings**
```php
// includes/security.php - Customize these constants:
define('RATE_LIMIT_REQUESTS', 10);    // Max requests per time window
define('RATE_LIMIT_WINDOW', 300);     // Time window in seconds (5 minutes)
define('MAX_FILE_SIZE', 5242880);     // Max upload size (5MB)
define('UPLOAD_PATH', 'uploads/');    // Upload directory path
```

### **Database Settings**
```php
// config/database.php - Production recommendations:
define('DB_HOST', 'localhost');
define('DB_NAME', 'jpsme_event');
define('DB_USER', 'jpsme_user');      // Create dedicated user
define('DB_PASS', 'strong_password'); // Use strong password
```

### **Admin Panel Customization**
- Update event date and registration deadline in admin settings
- Customize email templates for registration confirmations
- Modify payment instructions and account details
- Configure automatic backup settings

## 🛡️ **Security Features Details**

### **File Upload Security**
- **Magic Number Validation**: Checks actual file signatures
- **Extension Filtering**: Only allows PDF, JPG, PNG files
- **Size Limitations**: Configurable maximum file sizes
- **Secure Storage**: Files stored outside web directory when possible
- **Virus Scanning**: Ready for antivirus integration

### **Database Security**
- **mysqli Prepared Statements**: Prevents SQL injection
- **Input Validation**: Multiple layers of data sanitization
- **Error Handling**: Secure error messages (no data leakage)
- **Connection Security**: Encrypted connections supported

### **Session Management**
- **Session Regeneration**: Prevents session fixation
- **Secure Cookies**: HTTPOnly and Secure flags
- **CSRF Tokens**: Unique tokens for all forms
- **Timeout Handling**: Automatic session expiration

## 🎨 **Animation Features**

### **Login/Logout Animations**
- **Page Load**: Smooth fade-in transitions
- **Form Interactions**: Scale and glow effects on focus
- **Button States**: Loading spinners and ripple effects
- **Logout Flow**: Full-screen animation with progress indicator

### **Admin Panel Animations**
- **Dashboard**: Animated statistics and data visualization
- **Navigation**: Smooth hover effects and transitions
- **Form Handling**: Real-time validation feedback
- **Status Updates**: Animated success/error messages

## 🚨 **Troubleshooting**

### **Common Issues**

1. **Database Connection Error**
   ```
   Solution: Check XAMPP MySQL service and database credentials
   ```

2. **File Upload Errors**
   ```
   Solution: Check uploads directory permissions and PHP upload limits
   ```

3. **CSRF Token Errors**
   ```
   Solution: Clear browser cache and ensure session handling is working
   ```

4. **Admin Account Locked**
   ```php
   // Run this SQL to unlock admin account:
   UPDATE admin_users SET failed_login_attempts = 0, locked_until = NULL WHERE username = 'admin';
   ```

### **Performance Optimization**

1. **Enable Apache mod_rewrite** for clean URLs
2. **Configure PHP OPcache** for better performance
3. **Set up database indexing** for large datasets
4. **Enable gzip compression** in Apache
5. **Optimize images** and static assets

## 🔄 **Update Instructions**

### **Updating the System**
1. **Backup Database**: Export current data
2. **Backup Files**: Copy uploads directory
3. **Download Updates**: Get latest version
4. **Apply Changes**: Overwrite files (keep config)
5. **Test System**: Verify all functions work

### **Version History**
- **v2.0.0** (Current): Added animations, mysqli support, enhanced security
- **v1.5.0**: Improved admin panel, better file handling
- **v1.0.0**: Initial release with basic functionality

## 📞 **Support & Contact**

### **For Technical Support**
- **GitHub Issues**: Report bugs and feature requests
- **Documentation**: Refer to inline code comments
- **Community**: Join discussions for help and updates

### **For Event-Related Queries**
- **JPSME Office**: Contact official JPSME channels
- **Event Coordinators**: Reach out to designated coordinators
- **Technical Team**: For system-specific issues

## 📜 **License & Credits**

### **License**
This project is licensed under the MIT License - see the LICENSE file for details.

### **Credits**
- **Icons**: Feather Icons (https://feathericons.com/)
- **Animations**: Custom CSS3 animations
- **Security**: Best practices from OWASP guidelines
- **UI/UX**: Modern responsive design principles

### **Acknowledgments**
- JPSME organization for requirements and feedback
- Open source community for tools and libraries
- Security researchers for vulnerability testing

---

**© 2025 JPSME Event Registration System. Built with ❤️ for secure and efficient event management.**

## Usage

### For Users (Registration)
1. Visit: `http://localhost/jpsme_event/`
2. Fill out the registration form:
   - Institution information
   - Upload NatCon registration proof
   - Add team members (with enrollment proofs)
   - Enter coach information and PRC license
   - Upload payment proof
**© 2025 JPSME Event Registration System. Built with ❤️ for secure and efficient event management.**
