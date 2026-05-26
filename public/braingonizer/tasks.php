<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tasks_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';

// Handle AJAX requests BEFORE including header
handleTaskRequest();

// Get all tasks and projects from database BEFORE header
$allTasks = getAllTasks();
$allProjects = getAllProjects();
$allTags = getAllTags();

// Now include header for page display
$pageTitle = 'Tasks'; 
include '../includes/braingonizer/header.php';
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-tasks"></i> Tasks</h1>
    </header>

    <?php
        $projectColorMap = [
            'primary'   => '#3085d6',
            'success'   => '#28a745',
            'danger'    => '#d33',
            'warning'   => '#f0ad4e',
            'info'      => '#17a2b8',
            'secondary' => '#6c757d',
            'pink'      => '#E65C9A',
            'purple'    => '#7952b3',
        ];
        ?>
        <main>
        <section id="section-tasks-navigation">
            <div class="d-flex justify-content-center align-items-center">
                <button class="filter-btn btn-sm me-2 active" data-project="all">All Tasks</button>
                <?php foreach ($allProjects as $project): ?>
                <button class="filter-btn btn-sm me-2" data-project="<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?> Tasks</button>
                <?php endforeach; ?>
                <button class="filter-btn btn-sm" id="add-project-btn" onclick="addProject()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add project">
                    <i class="fas fa-plus-circle"></i>
                </button>
            </div>
        </section>
        <hr>
        <section id="section-tasks">
            <div class="row">
                <div class="col-12">
                    <h2>
                        Tasks 
                        <i class="fas fa-plus-circle" id="add-tasks-btn" onclick="addTask()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new task"></i> 
                        <!-- <i class="fas fa-filter" id="filter-notes-btn" onclick="alert('Filter notes clicked')"></i> -->
                    </h2>
                </div>

                <div class="col-12">
                    <?php if (!empty($allTasks)): ?>
                    <table class="pink-notes-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Content</th>
                                <th>Project</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tasks-tbody">
                            <?php foreach ($allTasks as $task): ?>
                            <tr data-task-id="<?= $task['id'] ?>">
                                <td><strong><?= htmlspecialchars($task['title']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($task['description'], 0, 100)) ?><?= strlen($task['description']) > 100 ? '...' : '' ?></td>
                                <td>
                                    <?php if (!empty($task['project_name'])): ?>
                                    <?php $badgeColor = $projectColorMap[$task['project_color']] ?? '#6c757d'; ?>
                                    <span class="badge" style="background-color: <?= $badgeColor ?>;"><?= htmlspecialchars($task['project_name']) ?></span>
                                    <?php else: ?>
                                    <span class="text-secondary">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $priorityClass = [
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        'low' => 'info',
                                        'very-low' => 'secondary'
                                    ][$task['priority']] ?? 'secondary';
                                    $priorityIcon = [
                                        'high' => 'exclamation-triangle',
                                        'medium' => 'exclamation-circle',
                                        'low' => 'info-circle',
                                        'very-low' => 'minus-circle'
                                    ][$task['priority']] ?? 'minus-circle';
                                    ?>
                                    <span class="badge bg-<?= $priorityClass ?>"><i class="fas fa-<?= $priorityIcon ?>"></i> <?= ucfirst(str_replace('-', ' ', $task['priority'])) ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = [
                                        'completed' => 'success',
                                        'in-progress' => 'warning',
                                        'on-hold' => 'danger',
                                        'not-started' => 'secondary'
                                    ][$task['status']] ?? 'secondary';
                                    $statusIcon = [
                                        'completed' => 'check-circle',
                                        'in-progress' => 'spinner fa-spin',
                                        'on-hold' => 'hand-paper',
                                        'not-started' => 'clock'
                                    ][$task['status']] ?? 'clock';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><i class="fas fa-<?= $statusIcon ?>"></i> <?= ucfirst(str_replace('-', ' ', $task['status'])) ?></span>
                                </td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View task" onclick="viewTask(<?= $task['id'] ?>)"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit task" onclick="editTask(<?= $task['id'] ?>)"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete task" onclick="deleteTaskConfirm(<?= $task['id'] ?>)"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-5 no-results-placeholder">
                        <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
                        <h4 class="text-secondary mb-2">No Results Found</h4>
                        <p class="text-secondary">No tasks available. Click the <i class="fas fa-plus"></i> icon to create your first task!</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </main>
    </div>
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
// Global data
const allProjects = <?= json_encode($allProjects) ?>;
const allTags = <?= json_encode($allTags) ?>;
</script>

<script src="/src/js/tasks.js"></script>
