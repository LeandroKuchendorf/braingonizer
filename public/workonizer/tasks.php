<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/workonizer/tasks_helpers.php';

// Handle AJAX requests BEFORE including header
handleWorkTaskRequest();

// Get all work tasks from database BEFORE header
$allWorkTasks = getAllWorkTasks();
$allTags = getAllTags();

// Now include header for page display
$pageTitle = 'Tasks'; 
include '../includes/workonizer/header.php';
?>
<?php include '../includes/workonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-tasks"></i> Work Tasks</h1>
    </header>

    <main>
        <section id="section-tasks-navigation">
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <button class="filter-btn btn-sm me-2 mb-2 active" data-status="all">All Tasks</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-status="pending">Pending</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-status="in_progress">In Progress</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-status="completed">Completed</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-status="cancelled">Cancelled</button>
                <button class="filter-btn btn-sm" id="add-task-btn" onclick="addTask()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new task">
                    <i class="fas fas fa-plus"></i>
                </button>
            </div>
        </section>

        <section id="section-tasks" class="mt-5">
            <?php if (!empty($allWorkTasks)): ?>
            <div class="table-responsive">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allWorkTasks as $task): ?>
                        <tr class="task-row" data-status="<?php echo $task['status']; ?>">
                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst($task['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'info' : 'secondary'); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : 'No due date'; ?></td>
                            <td class="text-right">
                                <i class="fas fa-eye cursor-pointer" onclick="viewTask(<?php echo $task['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="View"></i>
                                <i class="fas fa-edit cursor-pointer" onclick="editTask(<?php echo $task['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></i>
                                <i class="fas fa-trash cursor-pointer" onclick="deleteTask(<?php echo $task['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-results-placeholder">
                <i class="fas fa-tasks fa-3x"></i>
                <h4>No tasks yet</h4>
                <p>Create your first work task to get started!</p>
                <a href="#" onclick="addTask()">Create Task</a>
            </div>
            <?php endif; ?>
        </section>

    </main>
</div>
</div>

<?php include '../includes/workonizer/footer.php'; ?>

<script src="/src/js/workonizer/tasks.js"></script>
