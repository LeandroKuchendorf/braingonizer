<?php
require_once __DIR__ . '/../global_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/settings_helpers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Workonizer' : 'Workonizer'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.min.css">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS - Global Base + Workonizer Theme -->
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/workonizer.css">
    
    <!-- Debug Settings Global Variable -->
    <script>
        window.DEBUG_SETTINGS = <?php echo json_encode(getAllSettings()); ?>;
    </script>
</head>
<body>
    <!-- Top Bar -->
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- <span class="navbar-brand mb-0 h1">Workonizer</span> -->
            
            <!-- Task Tracker Widget -->
            <div class="task-tracker-widget">
                <div class="task-status-icon on-hold" data-status="on_hold" data-bs-toggle="tooltip" data-bs-placement="bottom" title="On Hold Tasks">
                    <i class="fas fa-pause-circle"></i>
                    <span class="task-count">3</span>
                </div>
                <div class="task-status-icon in-progress" data-status="in_progress" data-bs-toggle="tooltip" data-bs-placement="bottom" title="In Progress Tasks">
                    <i class="fas fa-spinner"></i>
                    <span class="task-count">5</span>
                </div>
                <div class="task-status-icon completed" data-status="completed" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Completed Tasks">
                    <i class="fas fa-check-circle"></i>
                    <span class="task-count">8</span>
                </div>
                <div class="task-status-icon expiring" data-status="expiring" data-bs-toggle="tooltip" data-bs-placement="bottom" title="About to Expire">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="task-count">2</span>
                </div>
            </div>
        </div>
    </nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for task tracker icons
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add click handlers to task status icons
    document.querySelectorAll('.task-status-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const count = this.querySelector('.task-count').textContent;
            const statusLabels = {
                'on_hold': 'On Hold',
                'in_progress': 'In Progress',
                'completed': 'Completed',
                'expiring': 'About to Expire'
            };
            
            Swal.fire({
                title: statusLabels[status] + ' Tasks',
                html: `<p>You have <strong>${count}</strong> ${statusLabels[status].toLowerCase()} tasks.</p>`,
                icon: status === 'expiring' ? 'warning' : 'info',
                confirmButtonText: 'Go to Tasks',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/workonizer/tasks.php';
                }
            });
        });
    });
});
</script>
