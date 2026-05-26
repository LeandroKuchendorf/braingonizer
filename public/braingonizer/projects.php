<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/tasks_helpers.php';

// Handle AJAX requests BEFORE including header
handleTaskRequest();

$pageTitle = 'Manage Projects'; 
include '../includes/braingonizer/header.php';

// Get all projects for display
$allProjects = getAllProjects();

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
            <i class="fas fa-project-diagram"></i> Manage Projects
        </h1>
    </header>

    <main>
        <!-- Search Bar and Create Project Button -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white border-secondary">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search-projects" class="form-control bg-dark text-white border-secondary" placeholder="Search projects...">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button id="add-project-btn" class="btn btn-success" onclick="addProjectModal()">
                    <i class="fas fa-plus"></i> Create Project
                </button>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="row">
            <div class="col-12">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th>Color</th>
                            <th>Preview</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-table-body">
                        <?php foreach ($allProjects as $project): ?>
                            <tr data-project-id="<?= $project['id'] ?>">
                                <td><strong><?= htmlspecialchars($project['name']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($project['description'] ?? '', 0, 100)) ?><?= strlen($project['description'] ?? '') > 100 ? '...' : '' ?></td>
                                <td><?= htmlspecialchars($colorNames[$project['color']] ?? ucfirst($project['color'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= htmlspecialchars($project['color']) ?>">
                                        <?= htmlspecialchars($project['name']) ?>
                                    </span>
                                </td>
                                <td><?= date('Y-m-d', strtotime($project['created_at'])) ?></td>
                                <td class="text-right">
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit project" onclick="editProjectModal(<?= $project['id'] ?>, '<?= htmlspecialchars($project['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($project['color']) ?>')"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete project" onclick="deleteProjectConfirm(<?= $project['id'] ?>)"></i>
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

<script src="/src/js/projects.js"></script>
