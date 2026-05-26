<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/files_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/braingonizer/tag_helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/categories_helpers.php';

// Handle AJAX requests BEFORE including header
handleFileRequest();

// Get all files and categories from database
$allFiles = getAllFiles();
$allCategories = getAllCategories();

// Now include header for page display
$pageTitle = 'Files'; 
include '../includes/braingonizer/header.php';
?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-folder-open"></i> Files</h1>
    </header>

    <main>
        <section id="section-files-navigation">
            <div class="d-flex justify-content-center align-items-center">
                <button class="btn btn-dark btn-sm me-2 file-filter-btn active" data-category="all">All Files</button>
                <?php foreach ($allCategories as $category): ?>
                    <button class="btn btn-dark btn-sm me-2 file-filter-btn" data-category="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></button>
                <?php endforeach; ?>
            </div>
        </section>
        <hr>
        <section id="section-files">
            <div class="row">
                <div class="col-12">
                    <h2>
                        Files 
                        <i class="fas fa-plus-circle" id="add-file-header-btn" onclick="uploadFileModal()" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload new file"></i> 
                    </h2>
                </div>

                <div class="col-12">
                    <?php if (!empty($allFiles)): ?>
                    <table class="pink-notes-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Tags</th>
                                <th>Size</th>
                                <th>Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="files-tbody">
                            <?php foreach ($allFiles as $file): ?>
                            <tr data-file-id="<?= $file['id'] ?>">
                                <td>
                                    <?php
                                    $mimeType = $file['mime_type'];
                                    $icon = 'fa-file';
                                    $iconColor = 'text-secondary';
                                    
                                    if (strpos($mimeType, 'pdf') !== false) {
                                        $icon = 'fa-file-pdf';
                                        $iconColor = 'text-danger';
                                    } elseif (strpos($mimeType, 'word') !== false || strpos($mimeType, 'document') !== false) {
                                        $icon = 'fa-file-word';
                                        $iconColor = 'text-primary';
                                    } elseif (strpos($mimeType, 'image') !== false) {
                                        $icon = 'fa-file-image';
                                        $iconColor = 'text-warning';
                                    } elseif (strpos($mimeType, 'zip') !== false || strpos($mimeType, 'archive') !== false) {
                                        $icon = 'fa-file-archive';
                                        $iconColor = 'text-secondary';
                                    } elseif (strpos($mimeType, 'sheet') !== false) {
                                        $icon = 'fa-file-excel';
                                        $iconColor = 'text-success';
                                    }
                                    ?>
                                    <i class="fas <?= $icon ?> <?= $iconColor ?>"></i> <?= htmlspecialchars($file['name']) ?>
                                </td>
                                <td><?= htmlspecialchars(substr($file['description'], 0, 100)) ?><?= strlen($file['description']) > 100 ? '...' : '' ?></td>
                                <td>
                                    <?php if (!empty($file['category_name'])): ?>
                                        <span class="badge bg-<?= htmlspecialchars($file['category_color'] ?? 'secondary') ?>">
                                            <?= htmlspecialchars($file['category_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($file['tags'])): ?>
                                        <?php foreach ($file['tags'] as $tag): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($tag['name']) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($file['file_size'] / 1024 / 1024, 1) ?> MB</td>
                                <td><?= date('Y-m-d', strtotime($file['created_at'])) ?></td>
                                <td class="text-right">
                                    <i class="fas fa-download" data-bs-toggle="tooltip" data-bs-placement="top" title="Download file" onclick="downloadFile('<?= htmlspecialchars($file['file_path']) ?>', '<?= htmlspecialchars($file['name']) ?>')"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit file details" onclick="editFile(<?= $file['id'] ?>)"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete file" onclick="deleteFileConfirm(<?= $file['id'] ?>)"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-5 no-results-placeholder">
                        <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
                        <h4 class="text-secondary mb-2">No Results Found</h4>
                        <p class="text-secondary">No files available. Click the <i class="fas fa-upload"></i> icon to upload your first file!</p>
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
    // Pass categories to JavaScript
    window.allCategories = <?= json_encode($allCategories) ?>;
</script>
<script src="/src/js/files.js"></script>
