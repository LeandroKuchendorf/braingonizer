<?php 
$pageTitle = 'Dashboard'; 
include '../includes/workonizer/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/workonizer/tasks_helpers.php';

// Get all work tasks from database
$allWorkTasks = getAllWorkTasks();
?>
<?php include '../includes/workonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster">Workonizer</h1>
    </header>

    <main>

        <section class="menu-section">             
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <a href="documents.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-file-alt"></i> Documents</h3>
                    </a>
                </div>
                <div class="col-sm-12 col-md-3 menu-item animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <a href="tasks.php" class="text-decoration-none text-reset">
                        <h3><i class="fas fa-tasks"></i> Tasks</h3>
                    </a>
                </div>
            </div>
        </section>

        <section id="section-recent-tasks" class="mt-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>
                        Recent Tasks
                        <a href="tasks.php"><i class="fas fa-expand text-grey" data-bs-toggle="tooltip" data-bs-placement="top" title="Fullscreen view"></i></a>
                    </h2>
                </div>
            </div>

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
                        <?php foreach (array_slice($allWorkTasks, 0, 5) as $task): ?>
                        <tr>
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
                                <i class="fas fa-trash cursor-pointer" onclick="deleteTask(<?php echo $task['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-results-placeholder">
                <i class="fas fa-inbox fa-3x"></i>
                <h4>No tasks yet</h4>
                <p>Create your first work task to get started!</p>
                <a href="tasks.php">Go to Tasks</a>
            </div>
            <?php endif; ?>
        </section>

    </main>
</div>

<?php include '../includes/workonizer/footer.php'; ?>
