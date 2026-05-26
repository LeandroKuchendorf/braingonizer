<?php require_once __DIR__ . '/includes/global_functions.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Selector - Braingonizer & Workonizer</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/global.css">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #241822 0%, #1a2332 100%);
        }
        
        .app-selector-container {
            text-align: center;
            width: 100%;
            max-width: 900px;
            padding: 2rem;
        }
        
        .app-selector-header {
            margin-bottom: 3rem;
        }
        
        .app-selector-header h1 {
            font-size: 3rem;
            color: #f0f0f0;
            margin-bottom: 0.5rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        
        .app-selector-header p {
            color: #b8b8b8;
            font-size: 1.1rem;
            font-style: italic;
        }
        
        .app-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .app-card {
            background: linear-gradient(135deg, #2d1f2a 0%, #241822 100%);
            border: 2px solid rgba(230, 92, 154, 0.2);
            border-radius: 12px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 250px;
        }
        
        .app-card:hover {
            transform: translateY(-5px);
            border-color: rgba(230, 92, 154, 0.5);
            box-shadow: 0 8px 24px rgba(230, 92, 154, 0.15);
            background: linear-gradient(135deg, #342030 0%, #2d1f2a 100%);
        }
        
        .app-card.workonizer {
            background: linear-gradient(135deg, #1e293b 0%, #1a2332 100%);
            border-color: rgba(59, 130, 246, 0.2);
        }
        
        .app-card.workonizer:hover {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
            background: linear-gradient(135deg, #243447 0%, #1e293b 100%);
        }
        
        .app-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #E65C9A;
        }
        
        .app-card.workonizer .app-icon {
            color: #3B82F6;
        }
        
        .app-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #f0f0f0;
        }
        
        .app-description {
            color: #b8b8b8;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .app-card:hover .app-icon {
            animation: pulse 0.5s ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>
<body>
    <div class="app-selector-container">
        <div class="app-selector-header animate__animated animate__fadeInDown animate__faster">
            <h1>Welcome</h1>
            <p>Choose an app to get started</p>
        </div>
        
        <div class="app-cards">
            <a href="/braingonizer/dashboard.php" class="app-card animate__animated animate__fadeInUp animate__faster" style="animation-delay: 0.1s;">
                <div class="app-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="app-name">Braingonizer</div>
                <div class="app-description">
                    Organize your thoughts, ideas, and personal projects. Store notes, reminders, and research all in one place.
                </div>
            </a>
            
            <a href="/workonizer/dashboard.php" class="app-card workonizer animate__animated animate__fadeInUp animate__faster" style="animation-delay: 0.2s;">
                <div class="app-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="app-name">Workonizer</div>
                <div class="app-description">
                    Manage your work documents and tasks. Keep your professional projects organized and on track.
                </div>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
