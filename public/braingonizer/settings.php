<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/settings_helpers.php';

handleSettingsRequest();

$pageTitle = 'General Settings'; 
include '../includes/braingonizer/header.php';

$allSettings = getAllSettings();
?>

<style>
    .form-check-input:checked {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
    }
    .form-check-input:focus {
        border-color: var(--color-primary-light) !important;
        box-shadow: 0 0 0 0.25rem rgba(230, 92, 154, 0.25) !important;
    }
</style>

<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster">
            <i class="fas fa-wrench"></i> General Settings
        </h1>
    </header>

    <main>

        <!-- Page-Specific Settings Links -->
        <section>
            <h2 class="mb-3" style="color: var(--color-primary-light); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 0.08em;">
                <i class="fas fa-cog me-2"></i>Page Settings
            </h2>
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <a href="tags.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-tags"></i> Tags</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <a href="categories.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-folder-open"></i> Categories</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <a href="projects.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-project-diagram"></i> Projects</h3>
                    </a>
                </div>
            </div>
        </section>

        <hr style="border-color: var(--color-border);">

        <!-- Global Debug Control Section -->
        <section class="mb-4">
            <h2 class="mb-3" style="color: var(--color-primary-light); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 0.08em;">
                <i class="fas fa-bug me-2"></i>Debugging
            </h2>
            <div style="background-color: var(--color-surface); border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; transition: border-color 0.2s ease;"
                 onmouseenter="this.style.borderColor='var(--color-primary)'"
                 onmouseleave="this.style.borderColor='var(--color-border)'">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--color-text);">Enable All Debugging</h5>
                        <p style="color: var(--color-text-muted); margin-bottom: 0; font-size: 0.9rem;">Master switch — turns on debugging for all pages at once.</p>
                    </div>
                    <div class="form-check form-switch ms-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="debug-global-toggle"
                               <?php echo ($allSettings['debug_global'] === '1') ? 'checked' : ''; ?>
                               style="width: 3rem; height: 1.5rem; cursor: pointer;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Per-Page Debug Controls Section -->
        <section>
            <h2 class="mb-3" style="color: var(--color-primary-light); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 0.08em;">
                <i class="fas fa-sliders-h me-2"></i>Per-Page Debugging
            </h2>
            <p style="color: var(--color-text-muted); margin-bottom: 1.25rem; font-size: 0.9rem;">Enable debugging for specific pages individually. Works independently when Global Debug is OFF.</p>

            <div class="row g-3">
                <?php
                $pages = [
                    ['key' => 'dashboard',  'label' => 'Dashboard',  'icon' => 'fa-home'],
                    ['key' => 'notes',      'label' => 'Notes',      'icon' => 'fa-sticky-note'],
                    ['key' => 'reminders',  'label' => 'Reminders',  'icon' => 'fa-bell'],
                    ['key' => 'tasks',      'label' => 'Tasks',      'icon' => 'fa-tasks'],
                    ['key' => 'ideas',      'label' => 'Ideas',      'icon' => 'fa-lightbulb'],
                    ['key' => 'files',      'label' => 'Files',      'icon' => 'fa-folder-open'],
                    ['key' => 'researches', 'label' => 'Researches', 'icon' => 'fa-flask'],
                    ['key' => 'links',      'label' => 'Links',      'icon' => 'fa-link'],
                    ['key' => 'tags',       'label' => 'Tags',       'icon' => 'fa-tags'],
                    ['key' => 'categories', 'label' => 'Categories', 'icon' => 'fa-folder'],
                    ['key' => 'projects',   'label' => 'Projects',   'icon' => 'fa-project-diagram'],
                ];
                foreach ($pages as $page):
                    $isOn = ($allSettings['debug_' . $page['key']] ?? '0') === '1';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex justify-content-between align-items-center p-3 animate__animated animate__fadeIn"
                         style="background-color: var(--color-surface); border: 1px solid var(--color-border); border-radius: 10px; transition: border-color 0.2s ease;"
                         onmouseenter="this.style.borderColor='var(--color-primary)'" 
                         onmouseleave="this.style.borderColor='var(--color-border)'">
                        <div style="color: var(--color-text);">
                            <i class="fas <?= $page['icon'] ?> me-2" style="color: var(--color-primary); width: 18px; text-align: center;"></i>
                            <strong><?= $page['label'] ?></strong>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input page-debug-toggle" type="checkbox" role="switch"
                                   data-page="<?= $page['key'] ?>" id="debug-<?= $page['key'] ?>-toggle"
                                   <?= $isOn ? 'checked' : '' ?>
                                   style="cursor: pointer;">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>
</div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script src="/src/js/settings.js"></script>
