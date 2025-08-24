<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | JPSME Event</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="fav/favicon.ico">
    <link rel="shortcut icon" href="fav/favicon.ico">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .success-container {
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            stroke: #10b981;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            padding: 20px;
        }
        
        .success-title {
            font-size: 2rem;
            color: #1746a2;
            margin-bottom: 16px;
            font-weight: 600;
        }
        
        .success-message {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #1746a2 0%, #3b82f6 30%, #6366f1 60%, #ffd700 100%);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(23, 70, 162, 0.2);
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 70, 162, 0.3);
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <div class="success-icon" data-feather="check-circle"></div>
        <h1 class="success-title">Registration Successful!</h1>
        <p class="success-message">
            Thank you for your registration. Your submission has been received and will be processed shortly.
            You will receive a confirmation email with further details.
        </p>
        
        <a href="machine_design_registration.php" class="btn-home">
            <span data-feather="home"></span>
            Register Another Team
        </a>
    </div>
    
    <script>feather.replace()</script>
</body>
</html>
