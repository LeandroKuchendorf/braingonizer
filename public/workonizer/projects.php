<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/workonizer/projects_helpers.php';

// Handle AJAX requests BEFORE including header
handleWorkProjectRequest();

// Now include header for page display
$pageTitle = 'Projects'; 
include '../includes/workonizer/header.php';

// Get all work projects from database
$allWorkProjects = getAllWorkProjects();
?>
<?php include '../includes/workonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-folder"></i> Projects</h1>
    </header>

    <main>
        <section id="section-projects">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>
                        Work Projects
                        <i class="fas fa-plus-circle" id="add-project-btn" onclick="addProject()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new project"></i>
                    </h2>
                </div>
            </div>

            <?php if (!empty($allWorkProjects)): ?>
            <div class="table-responsive">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allWorkProjects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $project['status'] === 'active' ? 'success' : ($project['status'] === 'on_hold' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($project['created_at'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($project['updated_at'])); ?></td>
                            <td class="text-right">
                                <i class="fas fa-eye cursor-pointer" onclick="viewProject(<?php echo $project['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="View"></i>
                                <i class="fas fa-edit cursor-pointer" onclick="editProject(<?php echo $project['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></i>
                                <i class="fas fa-trash cursor-pointer" onclick="deleteProject(<?php echo $project['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-results-placeholder">
                <i class="fas fa-folder fa-3x"></i>
                <h4>No projects yet</h4>
                <p>Create your first work project to get started!</p>
                <a href="#" onclick="addProject()">Create Project</a>
            </div>
            <?php endif; ?>
        </section>

    </main>
</div>

<?php include '../includes/workonizer/footer.php'; ?>

<script src="/src/js/workonizer/projects.js"></script>
