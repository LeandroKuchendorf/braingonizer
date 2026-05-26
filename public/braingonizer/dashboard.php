<?php 
$pageTitle = 'Dashboard'; 
include '../includes/braingonizer/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/reminder_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';

// Get all reminders from database
$allReminders = getAllReminders();
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster">Braingonizer</h1>
    </header>

    <main>

        <section class="menu-section">             
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <a href="notes.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-sticky-note"></i> Notes</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <a href="tasks.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-tasks"></i> Tasks</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <a href="ideas.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-lightbulb"></i> Ideas</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <a href="files.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-folder-open"></i> Files</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                    <a href="researches.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-flask"></i> Researches</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
                    <a href="links.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-link"></i> Links</h3>
                    </a>
                </div>
            </div>
        </section>

        <hr>

        <section id="section-reminders">
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

    </main>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script src="/src/js/braingonizer/dashboard.js"></script>
