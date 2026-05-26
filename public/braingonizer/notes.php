<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/reminder_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/notes_helpers.php';

// Handle AJAX requests BEFORE including header
handleReminderRequest();
handleNoteRequest();

// Now include header for page display
$pageTitle = 'Notes'; 
include '../includes/braingonizer/header.php';

// Get all reminders and notes from database
$allReminders = getAllReminders();
$allNotes = getAllNotes();
$allTags = getAllTags();
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-sticky-note"></i> Notes & Reminders</h1>
    </header>

    <main>
        <section id="section-reminders">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>
                        Reminders 
                        <a href="reminders.php"><i class="fas fa-expand text-grey" data-bs-toggle="tooltip" data-bs-placement="top" title="Fullscreen view"></i></a>
                        <i class="fas fa-filter" id="filter-reminders-btn" onclick="filterRemindersNew()" data-bs-toggle="tooltip" data-bs-placement="top" title="Filter reminders"></i>
                        <i class="fas fa-plus" id="add-reminder-btn" onclick="addReminderNew()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new reminder"></i>
                    </h2>
                </div>
            </div>

            <?php if (!empty($allReminders)): ?>
            <!-- Carousel Wrapper -->
            <div id="remindersCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <?php 
                    $slideIndex = 0;
                    $remindersPerSlide = 3;
                    $totalSlides = ceil(count($allReminders) / $remindersPerSlide);
                    
                    for ($slide = 0; $slide < $totalSlides; $slide++): 
                        $slideReminders = array_slice($allReminders, $slide * $remindersPerSlide, $remindersPerSlide);
                    ?>
                    <div class="carousel-item <?= $slide === 0 ? 'active' : '' ?>">
                        <div class="row">
                            <?php foreach ($slideReminders as $reminder): ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                                <div class="card <?= htmlspecialchars($reminder['color']) ?>-reminder-card">
                                    <div class="card-body">
                                        <div class="reminder-actions">
                                            <i class="fas fa-eye reminder-view-icon" onclick="viewReminder(<?= $reminder['id'] ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="View reminder"></i>
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
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#remindersCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#remindersCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators">
                    <?php for ($i = 0; $i < $totalSlides; $i++): ?>
                    <button type="button" data-bs-target="#remindersCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endfor; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5 no-results-placeholder">
                <i class="fas fa-bell-slash fa-4x text-secondary mb-3"></i>
                <h4 class="text-secondary mb-2">No Results Found</h4>
                <p class="text-secondary">No reminders available. <a href="reminders.php">Create one now!</a></p>
            </div>
            <?php endif; ?>
        </section>
        <hr>
        <section id="section-notes">
            <div class="row">
                <div class="col-12">
                    <h2>
                        Notes 
                        <i class="fas fa-plus" id="add-notes-btn" onclick="addNote()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new note"></i> 
                        <i class="fas fa-filter" id="filter-notes-btn" onclick="filterNotes()" data-bs-toggle="tooltip" data-bs-placement="top" title="Filter notes"></i>
                    </h2>
                </div>

                <div class="col-12">
                    <?php if (!empty($allNotes)): ?>
                    <table class="pink-notes-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Tags</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="notes-tbody">
                            <?php foreach ($allNotes as $note): ?>
                            <tr data-note-id="<?= $note['id'] ?>">
                                <td><strong><?= htmlspecialchars($note['title']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($note['content'], 0, 100)) ?><?= strlen($note['content']) > 100 ? '...' : '' ?></td>
                                <td><?= date('Y-m-d', strtotime($note['created_at'])) ?></td>
                                <td><?= date('Y-m-d', strtotime($note['updated_at'])) ?></td>
                                <td>
                                    <?php foreach ($note['tags'] as $tag): ?>
                                        <span class="badge bg-<?= htmlspecialchars($tag['color']) ?>"><?= htmlspecialchars($tag['name']) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View note" onclick="viewNote(<?= $note['id'] ?>)"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit note" onclick="editNote(<?= $note['id'] ?>)"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete note" onclick="deleteNoteConfirm(<?= $note['id'] ?>)"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-5 no-results-placeholder">
                        <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
                        <h4 class="text-secondary mb-2">No Results Found</h4>
                        <p class="text-secondary">No notes available. Click the <i class="fas fa-plus"></i> icon to create your first note!</p>
                    </div>
                    <?php endif; ?>
                </div>

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
<script src="/src/js/notes.js"></script>
