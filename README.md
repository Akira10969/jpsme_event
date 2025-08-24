# JPSME Event Registration System

A modern, responsive web application for managing competition registrations for Machine Design Competition and Quizbee events. Built with PHP, MySQL, and modern CSS with a beautiful glass morphism design.

## ✨ Features

### 🏆 **Dual Competition Support**
- **Machine Design Competition** registration
- **Quizbee Competition** registration
- Same form structure for both competitions

### 👥 **Dynamic Team Management**
- Add unlimited team members
- Individual member name and proof of enrollment
- Smart sequential numbering (auto-renumbers when members are removed)
- Minimum 1 member requirement

### 💳 **Comprehensive Registration Fields**
- University/Institution information
- Proof of Registration to NatCon
- Team member details with file uploads
- Coach information with PRC license details
- Payment information with multiple payment methods
- Optional payment reference number

### 🎨 **Modern Design**
- Glass morphism effects with backdrop blur
- Royal blue and gold gradient color scheme
- Responsive two-column layout (desktop) / single column (mobile)
- Feather icons integration
- Smooth animations and hover effects
- Mobile-first responsive design

### 📱 **Mobile Optimized**
- Touch-friendly interface (44px minimum touch targets)
- iOS zoom prevention (16px minimum font size)
- Progressive enhancement for touch devices
- Optimized for all screen sizes (320px+)

## 🚀 Installation

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP)
- **Web browser** (Chrome, Firefox, Safari, Edge)

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   git clone https://github.com/Akira10969/jpsme_event.git
   # OR download ZIP and extract to xampp/htdocs/
   ```

2. **Database Setup**
   - Start XAMPP (Apache + MySQL)
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `jpsme_event`
   - Import SQL file: `registration.sql`

3. **Configure Database Connection**
   - Edit `db.php` if needed:
   ```php
   $host = 'localhost';
   $db = 'jpsme_event';
   $user = 'root';      // Change if different
   $pass = '';          // Change if you have password
   ```

4. **Set Permissions**
   - Ensure `uploads/` folder has write permissions
   - Create upload directories if they don't exist:
     - `uploads/proof_natcon/`
     - `uploads/member_enrollments/`
     - `uploads/proof_payment/`

## 📋 Usage

### Access the Forms
- **Machine Design**: `http://localhost/jpsme_event/machine_design_registration.php`
- **Quizbee**: `http://localhost/jpsme_event/quizbee_registration.php`

### Registration Process
1. **University Information**: Enter institution details
2. **Upload NatCon Proof**: Upload registration document
3. **Team Members**: Add team members with enrollment proofs
4. **Coach Details**: Enter coach info with PRC license
5. **Payment**: Follow payment instructions and upload proof
6. **Submit**: Complete registration

## 💳 Payment Methods

### Supported Payment Options
- **Bank Transfer**: BPI Account
- **GCash**: Mobile payment
- **PayMaya**: Digital wallet

### Registration Fee
- **₱500.00** per team

*Note: Update payment details in `registration_form.php` as needed*

## 🗂️ File Structure

```
jpsme_event/
├── assets/
│   └── style.css                    # Main stylesheet with responsive design
├── uploads/                         # File upload directories
│   ├── proof_natcon/
│   ├── member_enrollments/
│   └── proof_payment/
├── db.php                          # Database connection
├── registration_form.php           # Shared form template
├── machine_design_registration.php # Machine Design form
├── quizbee_registration.php        # Quizbee form
├── submit_registration.php         # Form submission handler
├── registration.sql                # Database schema
└── README.md                       # This file
```

## 🗄️ Database Schema

### `registrations` Table
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- competition_type (VARCHAR 50)
- university (VARCHAR 255)
- proof_natcon (VARCHAR 255)
- team_members (TEXT)
- proof_enrollment (TEXT)
- coach_name (VARCHAR 255)
- prc_license (VARCHAR 100)
- prc_reg_date (DATE)
- prc_exp_date (DATE)
- proof_payment (VARCHAR 255)
- payment_reference (VARCHAR 100)
- created_at (TIMESTAMP)
```

## 🎨 Design Features

### Color Scheme
- **Primary**: Royal Blue (#1746a2)
- **Secondary**: Gold (#ffd700)
- **Accent**: Blue variations (#3b82f6, #6366f1)
- **Background**: White with subtle gradients

### Responsive Breakpoints
- **Desktop**: 768px+ (two-column layout)
- **Tablet**: 600px-768px (single column)
- **Mobile**: 480px-600px (compact)
- **Small**: <480px (ultra-compact)

### Typography
- **Font**: Inter (Google Fonts)
- **Weights**: 300, 400, 500, 600, 700
- **Mobile-safe**: 16px minimum (prevents iOS zoom)

## 🔧 Customization

### Update Payment Information
Edit `registration_form.php` (lines ~85-95):
```php
<li><strong>Bank Transfer:</strong> Account Name: [Your Account] | Account Number: [Your Number] | Bank: [Your Bank]</li>
<li><strong>GCash:</strong> [Your GCash Number]</li>
<li><strong>PayMaya:</strong> [Your PayMaya Number]</li>
```

### Modify Registration Fee
Edit `registration_form.php`:
```php
<p><strong>Registration Fee:</strong> ₱[Your Amount]</p>
```

### Add New Competition
1. Create new PHP file (e.g., `robotics_registration.php`)
2. Include the form template:
```php
<?php
include 'db.php';
include 'registration_form.php';
render_registration_form('Robotics Competition');
?>
```

## 🛡️ Security Features

- **File Upload Validation**: Accepts only PDF, JPG, PNG files
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: HTML escaping
- **File Size Limits**: 5MB maximum per file
- **Input Validation**: Required field checks

## 📱 Browser Support

### Desktop
- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+

### Mobile
- ✅ iOS Safari 13+
- ✅ Chrome Mobile 80+
- ✅ Samsung Internet 12+
- ✅ Firefox Mobile 75+

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Developer

**Akira10969**
- GitHub: [@Akira10969](https://github.com/Akira10969)

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Contact the development team
- Check the documentation

---

**Made with ❤️ for JPSME Events**
