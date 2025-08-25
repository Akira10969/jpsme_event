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
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #1746a2 0%, #3b82f6 40%, #ffd700 100%);
            background-size: 200% 200%;
            animation: gradientMove 8s ease-in-out infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .error-container {
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255,255,255,0.85);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(23,70,162,0.10);
            position: relative;
            top: 8vh;
            opacity: 0;
            animation: fadeIn 1.2s ease 0.2s forwards;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
        .error-icon {
            width: 150px;
            height: 150px;
            margin: 0 auto 32px;
            stroke: #1746a2;
            opacity: 0.5;
            filter: drop-shadow(0 4px 16px #1746a2aa);
            animation: iconPop 1.2s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes iconPop {
            0% { transform: scale(0.7) rotate(-10deg); opacity: 0; }
            60% { transform: scale(1.1) rotate(3deg); opacity: 0.7; }
            100% { transform: scale(1) rotate(0); opacity: 0.5; }
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1746a2 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
            letter-spacing: -4px;
            text-shadow: 0 2px 8px #1746a222;
        }
        .error-title {
            font-size: 2.2rem;
            color: #1746a2;
            margin-bottom: 16px;
            font-weight: 700;
            letter-spacing: -1px;
        }
        .error-message {
            font-size: 1.15rem;
            color: #475569;
            margin-bottom: 32px;
            line-height: 1.7;
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
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(.68,-0.55,.27,1.55);
            box-shadow: 0 4px 16px rgba(23, 70, 162, 0.18);
            font-size: 1.1rem;
        }
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.04);
            box-shadow: 0 8px 28px rgba(23, 70, 162, 0.28);
        }
        .btn-secondary {
            background: rgba(23, 70, 162, 0.08);
            color: #1746a2;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(.68,-0.55,.27,1.55);
            border: 2px solid rgba(23, 70, 162, 0.18);
            font-size: 1.1rem;
        }
        .btn-secondary:hover {
            background: rgba(23, 70, 162, 0.15);
            border-color: rgba(23, 70, 162, 0.28);
            transform: translateY(-2px) scale(1.02);
        }
        @media (max-width: 600px) {
            .error-container {
                padding: 40px 10px;
            }
            .error-code {
                font-size: 5rem;
            }
            .error-title {
                font-size: 1.3rem;
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
                max-width: 260px;
            }
            .error-icon {
                width: 90px;
                height: 90px;
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
