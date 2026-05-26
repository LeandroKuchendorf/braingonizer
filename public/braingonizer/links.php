<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/links_helpers.php';

// Handle AJAX requests BEFORE including header
handleLinkRequest();

// Get all categories and links from database
$allCategories = getAllCategories();

// Now include header for page display
$pageTitle = 'Links'; 
include '../includes/braingonizer/header.php';
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-link"></i> Links</h1>
    </header>

    <main>
        <section id="section-links-navigation">
            <div class="d-flex justify-content-center align-items-center">
                <button class="filter-btn btn-sm me-2 active" data-category="all">All Categories</button>
                <?php foreach ($allCategories as $category): ?>
                <button class="filter-btn btn-sm me-2" data-category="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></button>
                <?php endforeach; ?>
                <button class="filter-btn btn-sm" id="add-category-btn" onclick="addCategory()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add category">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </section>
        <hr>
        <section id="section-links">
            <div class="row">
            <?php if (!empty($allCategories)): ?>
                <?php foreach ($allCategories as $category): ?>
                <div class="col-md-4 mb-4">
                    <div class="card links-category-card category-<?= htmlspecialchars($category['color']) ?> h-100">
                        <div class="card-header text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title h5 mb-0"><i class="fas <?= htmlspecialchars($category['icon']) ?>"></i> <?= htmlspecialchars($category['name']) ?></h3>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end bg-dark">
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteCategoryConfirm(<?= $category['id'] ?>)"><i class="fas fa-trash me-2"></i>Delete Category</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="links-list">
                                <?php if (!empty($category['links'])): ?>
                                    <?php foreach ($category['links'] as $link): ?>
                                    <li class="links-list-item d-flex justify-content-between align-items-center">
                                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"><i class="fas fa-external-link-alt me-2"></i><?= htmlspecialchars($link['title']) ?></a>
                                        <div class="link-actions">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="editLink(<?= $link['id'] ?>)"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLinkConfirm(<?= $link['id'] ?>)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="links-list-item text-center text-secondary">
                                        <small>No links yet</small>
                                    </li>
                                <?php endif; ?>
                                <li class="links-list-item text-center">
                                    <button class="btn btn-sm btn-outline-primary w-100" onclick="addLink(<?= $category['id'] ?>)">
                                        <i class="fas fa-plus me-2"></i>Add Link
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5 no-results-placeholder">
                    <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
                    <h4 class="text-secondary mb-2">No Results Found</h4>
                    <p class="text-secondary">No categories available. Click the <i class="fas fa-plus"></i> button to create your first category!</p>
                </div>
            </div>
            <?php endif; ?>
            </div>
        </section>
    </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script>
    window.allCategories = <?php echo json_encode($allCategories); ?>;
</script>
<script src="/src/js/links.js"></script>
