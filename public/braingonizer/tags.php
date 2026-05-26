<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';

// Handle AJAX requests BEFORE including header
handleTagRequest();

$pageTitle = 'Manage Tags'; 
include '../includes/braingonizer/header.php';

// Get all tags for display
$allTags = getAllTags();

$colorNames = [
    'primary'   => 'Blue',
    'secondary' => 'Gray',
    'success'   => 'Green',
    'danger'    => 'Red',
    'warning'   => 'Yellow',
    'info'      => 'Cyan',
    'pink'      => 'Pink',
    'purple'    => 'Purple',
];
?>

<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster">
            <i class="fas fa-tags"></i> Manage Tags
        </h1>
    </header>

    <main>
        <!-- Search Bar and Create Tag Button -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white border-secondary">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search-tags" class="form-control bg-dark text-white border-secondary" placeholder="Search tags...">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button id="add-tag-btn" class="btn btn-success" onclick="addTagModal()">
                    <i class="fas fa-plus"></i> Create Tag
                </button>
            </div>
        </div>

        <!-- Tags Table -->
        <div class="row">
            <div class="col-12">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Tag Name</th>
                            <th>Color</th>
                            <th>Preview</th>
                            <th>Usage Count</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tags-table-body">
                        <?php foreach ($allTags as $tag): ?>
                            <?php $usageCount = getTagUsageCount($tag['id']); ?>
                            <tr data-tag-id="<?= $tag['id'] ?>">
                                <td><?= htmlspecialchars($tag['name']) ?></td>
                                <td><?= htmlspecialchars($colorNames[$tag['color']] ?? ucfirst($tag['color'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= htmlspecialchars($tag['color']) ?>">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </span>
                                </td>
                                <td><?= $usageCount ?></td>
                                <td class="text-right">
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit tag" onclick="editTagModal(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($tag['color']) ?>')"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete tag" onclick="deleteTagConfirm(<?= $tag['id'] ?>)"></i>
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

<script src="/src/js/tags.js"></script>
