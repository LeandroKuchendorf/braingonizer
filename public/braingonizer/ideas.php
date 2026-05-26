<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/ideas_helpers.php';

// Handle AJAX requests BEFORE including header
handleIdeaRequest();

// Get all ideas and categories from database
$allIdeas = getAllIdeas();
$allCategories = getIdeaCategories();

// Now include header for page display
$pageTitle = 'Ideas'; 
include '../includes/braingonizer/header.php';
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-lightbulb"></i> Ideas</h1>
    </header>

    <main>
        <section id="section-ideas-navigation">
            <div class="d-flex justify-content-center align-items-center">
                <button class="filter-btn btn-sm me-2 active" data-category="all">All Ideas</button>
                <?php foreach ($allCategories as $category): ?>
                <button class="filter-btn btn-sm me-2" data-category="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></button>
                <?php endforeach; ?>
                <button class="filter-btn btn-sm" id="add-idea-btn" onclick="addIdeaCategory()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add category">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </section>
        <hr>
        <section id="section-ideas">
            <div class="row">
                <div class="col-12">
                    <h2>
                        Ideas 
                        <i class="fas fa-plus-circle" id="add-idea-header-btn" onclick="addIdea()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new idea"></i> 
                    </h2>
                </div>

                <div class="col-12">
                    <?php if (!empty($allIdeas)): ?>
                    <table class="pink-notes-table">
                        <thead>
                            <tr>
                                <th>Idea</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ideas-tbody">
                            <?php foreach ($allIdeas as $idea): ?>
                            <tr data-idea-id="<?= $idea['id'] ?>">
                                <td><strong><?= htmlspecialchars($idea['title']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($idea['description'], 0, 100)) ?><?= strlen($idea['description']) > 100 ? '...' : '' ?></td>
                                <td>
                                    <?php if (!empty($idea['category_name'])): ?>
                                        <span class="badge bg-<?= htmlspecialchars($idea['category_color'] ?? 'secondary') ?>">
                                            <?= htmlspecialchars($idea['category_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $priorityClass = [
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        'low' => 'info',
                                        'very-low' => 'secondary'
                                    ][$idea['priority']] ?? 'secondary';
                                    $priorityIcon = [
                                        'high' => 'exclamation-triangle',
                                        'medium' => 'exclamation-circle',
                                        'low' => 'info-circle',
                                        'very-low' => 'minus-circle'
                                    ][$idea['priority']] ?? 'minus-circle';
                                    ?>
                                    <span class="badge bg-<?= $priorityClass ?>"><i class="fas fa-<?= $priorityIcon ?>"></i> <?= ucfirst(str_replace('-', ' ', $idea['priority'])) ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = [
                                        'concept' => 'secondary',
                                        'in-progress' => 'warning',
                                        'completed' => 'success'
                                    ][$idea['status']] ?? 'secondary';
                                    $statusIcon = [
                                        'concept' => 'lightbulb',
                                        'in-progress' => 'spinner fa-spin',
                                        'completed' => 'check-circle'
                                    ][$idea['status']] ?? 'lightbulb';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><i class="fas fa-<?= $statusIcon ?>"></i> <?= ucfirst(str_replace('-', ' ', $idea['status'])) ?></span>
                                </td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View idea" onclick="viewIdea(<?= $idea['id'] ?>)"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit idea" onclick="editIdea(<?= $idea['id'] ?>)"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete idea" onclick="deleteIdeaConfirm(<?= $idea['id'] ?>)"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-5 no-results-placeholder">
                        <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
                        <h4 class="text-secondary mb-2">No Results Found</h4>
                        <p class="text-secondary">No ideas available. Click the <i class="fas fa-plus"></i> icon to create your first idea!</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script>
    window.allCategories = <?php echo json_encode($allCategories); ?>;
</script>
<script src="/src/js/ideas.js"></script>
