<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/workonizer/documents_helpers.php';

// Handle AJAX requests BEFORE including header
handleDocumentRequest();

// Now include header for page display
$pageTitle = 'Documents'; 
include '../includes/workonizer/header.php';

// Get all documents from database
$allDocuments = getAllDocuments();
$allTags = getAllTags();
?>
<?php include '../includes/workonizer/sidebar.php'; ?>

<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-file-alt"></i> Documents</h1>
    </header>

    <main>
        <section id="section-documents">
            <div class="row mb-4">
                <div class="col-12">
                    <h2>
                        Work Documents
                        <i class="fas fa-plus-circle" id="add-document-btn" onclick="addDocument()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new document"></i>
                    </h2>
                </div>
            </div>

            <?php if (!empty($allDocuments)): ?>
            <div class="table-responsive">
                <table class="pink-notes-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allDocuments as $document): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($document['title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($document['created_at'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($document['updated_at'])); ?></td>
                            <td class="text-right">
                                <i class="fas fa-eye cursor-pointer" onclick="viewDocument(<?php echo $document['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="View"></i>
                                <i class="fas fa-edit cursor-pointer" onclick="editDocument(<?php echo $document['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"></i>
                                <i class="fas fa-trash cursor-pointer" onclick="deleteDocument(<?php echo $document['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-results-placeholder">
                <i class="fas fa-file fa-3x"></i>
                <h4>No documents yet</h4>
                <p>Create your first work document to get started!</p>
                <a href="#" onclick="addDocument()">Create Document</a>
            </div>
            <?php endif; ?>
        </section>

    </main>
</div>

<?php include '../includes/workonizer/footer.php'; ?>

<script src="/src/js/workonizer/documents.js"></script>
