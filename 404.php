<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | JPSME Event</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="fav/favicon.ico">
    <link rel="shortcut icon" href="fav/favicon.ico">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .error-container {
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1746a2 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .error-title {
            font-size: 2rem;
            color: #1746a2;
            margin-bottom: 16px;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1746a2 0%, #3b82f6 30%, #6366f1 60%, #ffd700 100%);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(23, 70, 162, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 70, 162, 0.3);
        }
        
        .btn-secondary {
            background: rgba(23, 70, 162, 0.1);
            color: #1746a2;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: 2px solid rgba(23, 70, 162, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(23, 70, 162, 0.15);
            border-color: rgba(23, 70, 162, 0.3);
            transform: translateY(-1px);
        }
        
        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
            stroke: #1746a2;
            opacity: 0.3;
        }
        
        @media (max-width: 600px) {
            .error-container {
                padding: 40px 20px;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="error-icon" data-feather="alert-triangle"></div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            Sorry, the page you are looking for doesn't exist or has been moved. 
            You can go back to the homepage or try one of our registration forms.
        </p>
        
        <div class="error-actions">
            <a href="machine_design_registration.php" class="btn-primary">
                <span data-feather="settings"></span>
                Machine Design
            </a>
            <a href="quizbee_registration.php" class="btn-primary">
                <span data-feather="help-circle"></span>
                Quizbee
            </a>
            <a href="javascript:history.back()" class="btn-secondary">
                <span data-feather="arrow-left"></span>
                Go Back
            </a>
        </div>
    </div>
    
    <script>feather.replace()</script>
</body>
</html>
