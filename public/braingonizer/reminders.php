<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/reminder_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';

// Handle AJAX requests BEFORE including header
handleReminderRequest();

// Now include header for page display
$pageTitle = 'Reminders'; 
include '../includes/braingonizer/header.php';

// Get all reminders for display
$allReminders = getAllReminders();
$allTags = getAllTags();
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster" style="display: inline-block; vertical-align: middle;"><i class="fas fa-sticky-note"></i> Notes & Reminders</h1>
    </header>

    <main>
        <section id="section-reminders">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="d-flex-center">
                        <a href="javascript:history.back()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go back" class="d-inline-flex-center cursor-pointer text-decoration-none">
                            <i class="fas fa-arrow-left text-pink" style="font-size: 1.5rem;"></i>
                        </a>
                        Reminders 
                        <i class="fas fa-plus" id="add-reminder-btn" onclick="addReminderNew()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new reminder"></i>
                        <i class="fas fa-filter" id="filter-reminders-btn" onclick="filterRemindersNew()" data-bs-toggle="tooltip" data-bs-placement="top" title="Filter reminders"></i>
                    </h2>
                </div>
            </div>

            <!-- Reminders Grid -->
            <div class="row" id="reminders-grid">
                <?php foreach ($allReminders as $reminder): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card <?= htmlspecialchars($reminder['color']) ?>-reminder-card">
                        <div class="card-body">
                            <div class="reminder-actions">
                                <i class="fas fa-eye reminder-view-icon" onclick="viewReminder(<?= $reminder['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="View reminder"></i>
                                <i class="fas fa-edit reminder-edit-icon" onclick="editReminder(<?= $reminder['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit reminder"></i>
                                <i class="fas fa-times reminder-delete-icon" onclick="deleteReminderConfirm(<?= $reminder['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete reminder"></i>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($reminder['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($reminder['description'], 0, 100)) ?><?= strlen($reminder['description']) > 100 ? '...' : '' ?></p>
                        </div>
                        <div class="card-footer">
                            <?php foreach ($reminder['tags'] as $tag): ?>
                                <span class="badge bg-<?= htmlspecialchars($tag['color']) ?>"><?= htmlspecialchars($tag['name']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($allReminders)): ?>
                <div class="col-12 text-center py-5 no-results-placeholder">
                    <i class="fas fa-bell-slash fa-4x text-secondary mb-3"></i>
                    <h4 class="text-secondary mb-2">No Results Found</h4>
                    <p class="text-secondary">No reminders available. Click the <i class="fas fa-plus"></i> icon to create your first reminder!</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Global tags data
const allTags = <?= json_encode($allTags) ?>;
</script>

<script src="/src/js/reminders.js"></script>
