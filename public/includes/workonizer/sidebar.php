<!-- Sidebar -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.05s;">
                <a class="nav-link text-white" href="/workonizer/dashboard.php">
                    <i class="fas fa-home me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.1s;">
                <a class="nav-link text-white" href="/workonizer/documents.php">
                    <i class="fas fa-file-alt me-2"></i>
                    Documents
                </a>
            </li>
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.15s;">
                <a class="nav-link text-white" href="/workonizer/tasks.php">
                    <i class="fas fa-tasks me-2"></i>
                    Tasks
                </a>
            </li>
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.2s;">
                <a class="nav-link text-white" href="/workonizer/projects.php">
                    <i class="fas fa-folder me-2"></i>
                    Projects
                </a>
            </li>
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.25s;">
                <a class="nav-link text-white" data-bs-toggle="collapse" href="#settingsCollapse" role="button" aria-expanded="false" aria-controls="settingsCollapse">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <div class="collapse" id="settingsCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/braingonizer/tags.php">
                                <i class="fas fa-tags me-2"></i>
                                Tags
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="/braingonizer/categories.php">
                                <i class="fas fa-folder-open me-2"></i>
                                Categories
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item animate__animated animate__fadeInLeft animate__faster" style="animation-delay: 0.3s;">
                <a class="nav-link text-white" href="/index.php">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Main
                </a>
            </li>
        </ul>
    </div>
</div>
