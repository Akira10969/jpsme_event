<?php
// index.php - Landing page for JPSME Event Registration
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JPSME Event Registration System</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/x-icon" href="fav/favicon.ico">
    <link rel="shortcut icon" href="fav/favicon.ico">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .landing-container {
            text-align: center;
            padding: 60px 40px;
            max-width: 700px;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, #1746a2 0%, #ffd700 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 8px 24px rgba(23, 70, 162, 0.2);
        }
        
        .main-title {
            font-size: 2.5rem;
            color: #1746a2;
            margin-bottom: 16px;
            font-weight: 700;
            background: linear-gradient(135deg, #1746a2 0%, #ffd700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            font-size: 1.2rem;
            color: #475569;
            margin-bottom: 48px;
            line-height: 1.6;
        }
        
        .competition-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 48px;
        }
        
        .competition-card {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(23, 70, 162, 0.1);
            border-radius: 20px;
            padding: 32px 24px;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .competition-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1746a2 0%, #ffd700 100%);
        }
        
        .competition-card:hover {
            transform: translateY(-4px);
            border-color: rgba(23, 70, 162, 0.2);
            box-shadow: 0 12px 32px rgba(23, 70, 162, 0.15);
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            stroke: #1746a2;
        }
        
        .card-title {
            font-size: 1.3rem;
            color: #1746a2;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .card-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .info-section {
            background: rgba(248, 250, 252, 0.8);
            border-radius: 16px;
            padding: 24px;
            margin-top: 32px;
            text-align: left;
        }
        
        .info-title {
            color: #1746a2;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        .info-list {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .competition-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .main-title {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1.1rem;
            }
            
            .landing-container {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container landing-container">
        <div class="logo">J</div>
        <h1 class="main-title">JPSME Event Registration</h1>
        <p class="subtitle">Register your team for Machine Design Competition or Quizbee events</p>
        
        <div class="competition-grid">
            <a href="machine_design_registration.php" class="competition-card">
                <div class="card-icon" data-feather="settings"></div>
                <h2 class="card-title">Machine Design</h2>
                <p class="card-description">Engineering design competition focusing on mechanical innovation and creativity</p>
            </a>
            
            <a href="quizbee_registration.php" class="competition-card">
                <div class="card-icon" data-feather="help-circle"></div>
                <h2 class="card-title">Quizbee</h2>
                <p class="card-description">Knowledge competition testing academic excellence and quick thinking</p>
            </a>
        </div>
        
        <div class="info-section">
            <h3 class="info-title">Registration Requirements</h3>
            <ul class="info-list">
                <li>University/Institution verification</li>
                <li>Proof of Registration to NatCon</li>
                <li>Team member enrollment documents</li>
                <li>Coach PRC license information</li>
                <li>Payment confirmation (₱500.00)</li>
            </ul>
        </div>
    </div>
    
    <script>feather.replace()</script>
</body>
</html>
