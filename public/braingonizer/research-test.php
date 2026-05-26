<?php include '../includes/braingonizer/header.php'; ?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">

    <header class="text-center">
        <h1><i class="fas fa-flask"></i> Productivity Techniques <button class="edit-btn" onclick="editResearch()"><i class="fas fa-pen"></i></button></h1>

        <p class="lead">Exploring different productivity methodologies and their effectiveness in modern work environments. <button class="edit-btn" onclick="editResearch()"><i class="fas fa-pen"></i></button></p>

        <div class="mt-3">
            <span class="badge bg-info me-2">Personal</span>
            <span class="badge bg-secondary me-2">Productivity</span>
            <span class="badge bg-secondary me-2">Self-Improvement</span>
            <span><button class="edit-btn" onclick="editResearch()"><i class="fas fa-pen"></i></button></span>
        </div>
    </header>

    <main>
        <!-- Uploaded Files Section -->
        <section class="files-section">
            <h3><i class="fas fa-paperclip"></i> Uploaded Files</h3>
            <div class="row g-3">
                <!-- File Card 1: PDF -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="file-card" onclick="alert('View/Download: Research_Summary.pdf')">
                        <div class="file-icon pdf">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Research_Summary.pdf</div>
                            <div class="file-meta">2.4 MB • Jan 15, 2024</div>
                        </div>
                        <div class="file-actions">
                            <i class="fas fa-download file-action-icon" title="Download" onclick="event.stopPropagation(); alert('Download file');"></i>
                            <i class="fas fa-trash file-action-icon delete" title="Delete" onclick="event.stopPropagation(); deleteFile('Research_Summary.pdf');"></i>
                        </div>
                    </div>
                </div>

                <!-- File Card 2: Excel -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="file-card" onclick="alert('View/Download: Data_Analysis.xlsx')">
                        <div class="file-icon excel">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Data_Analysis.xlsx</div>
                            <div class="file-meta">1.8 MB • Jan 18, 2024</div>
                        </div>
                        <div class="file-actions">
                            <i class="fas fa-download file-action-icon" title="Download" onclick="event.stopPropagation(); alert('Download file');"></i>
                            <i class="fas fa-trash file-action-icon delete" title="Delete" onclick="event.stopPropagation(); deleteFile('Data_Analysis.xlsx');"></i>
                        </div>
                    </div>
                </div>

                <!-- File Card 3: Word Document -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="file-card" onclick="alert('View/Download: Interview_Notes.docx')">
                        <div class="file-icon doc">
                            <i class="fas fa-file-word"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Interview_Notes.docx</div>
                            <div class="file-meta">856 KB • Jan 20, 2024</div>
                        </div>
                        <div class="file-actions">
                            <i class="fas fa-download file-action-icon" title="Download" onclick="event.stopPropagation(); alert('Download file');"></i>
                            <i class="fas fa-trash file-action-icon delete" title="Delete" onclick="event.stopPropagation(); deleteFile('Interview_Notes.docx');"></i>
                        </div>
                    </div>
                </div>

                <!-- File Card 4: Image -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="file-card" onclick="alert('View/Download: Chart_Visualization.png')">
                        <div class="file-icon image">
                            <i class="fas fa-file-image"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Chart_Visualization.png</div>
                            <div class="file-meta">342 KB • Jan 22, 2024</div>
                        </div>
                        <div class="file-actions">
                            <i class="fas fa-download file-action-icon" title="Download" onclick="event.stopPropagation(); alert('Download file');"></i>
                            <i class="fas fa-trash file-action-icon delete" title="Delete" onclick="event.stopPropagation(); deleteFile('Chart_Visualization.png');"></i>
                        </div>
                    </div>
                </div>

                <!-- Upload File Card -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="file-upload-card" onclick="uploadFile()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Upload File</span>
                    </div>
                </div>
            </div>
        </section>

        <hr>

        <!-- Research Content Sections -->
        <section class="research-content">
            <!-- Existing Section Example -->
            <div class="row research-section mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h3>Introduction</h3>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="alert('Edit section clicked')">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSection()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
            </div>

            <div class="row add-section" onclick="alert('Add new section clicked')">
                <span>
                    <i class="fas fa-plus fa-sm"></i> Add New Section
                </span>
            </div>
        </section>

        <!-- Navigation Buttons -->
        <section class="mt-5 mb-4">
            <div class="d-flex justify-content-between">
                <a href="researches.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Researches
                </a>
                <div>
                    <button class="btn btn-danger" onclick="deleteResearch()">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-success me-2" onclick="alert('Save research clicked')">
                        <i class="fas fa-save"></i> Save Research
                    </button>
                </div>
            </div>
        </section>
    </main> 
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script src="/src/js/braingonizer/research-test.js"></script>
</body>
</html>
