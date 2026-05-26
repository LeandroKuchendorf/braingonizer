<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/categories_helpers.php';

// Handle AJAX requests BEFORE including header
handleCategoryRequest();

$pageTitle = 'Manage Categories'; 
include '../includes/braingonizer/header.php';

// Get all categories for display
$allCategories = getAllCategories();
?>

<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster">
            <i class="fas fa-folder-open"></i> Manage Categories
        </h1>
    </header>

    <main>
        <!-- Search Bar and Create Category Button -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white border-secondary">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search-categories" class="form-control bg-dark text-white border-secondary" placeholder="Search categories...">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button id="add-category-btn" class="btn btn-success" onclick="addCategoryModal()">
                    <i class="fas fa-plus"></i> Create Category
                </button>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="row">
            <div class="col-12">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Color</th>
                            <th>Preview</th>
                            <th>In Use</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categories-table-body">
                        <?php foreach ($allCategories as $category): ?>
                            <?php $inUse = isCategoryInUse($category['id']); ?>
                            <tr data-category-id="<?= $category['id'] ?>">
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['color']) ?></td>
                                <td>
                                    <span class="badge bg-<?= htmlspecialchars($category['color']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </span>
                                </td>
                                <td><?= $inUse ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-secondary"></i>' ?></td>
                                <td class="text-right">
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit category" onclick="editCategoryModal(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($category['color']) ?>')"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete category" onclick="deleteCategoryConfirm(<?= $category['id'] ?>)"></i>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script src="/src/js/categories.js"></script>
