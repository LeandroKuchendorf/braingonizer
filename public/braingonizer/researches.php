<?php $pageTitle = 'Researches'; include '../includes/braingonizer/header.php'; ?>
<?php include '../includes/braingonizer/sidebar.php'; ?>

<div class="site-wrapper">
<div class="container">
    <header class="text-center">
        <h1 class="animate__animated animate__fadeInDown animate__faster"><i class="fas fa-flask"></i> Researches</h1>
    </header>

    <main>
        <section id="section-researches-navigation">
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <button class="filter-btn btn-sm me-2 mb-2 active" data-category="all">All Researches</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-category="personal">Personal</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-category="work">Work</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-category="gym">Gym</button>
                <button class="filter-btn btn-sm me-2 mb-2" data-category="financial">Financial</button>
                <button class="filter-btn btn-sm" id="add-category-btn" onclick="addResearchCategory()" data-bs-toggle="tooltip" data-bs-placement="top" title="Add category">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </section>
        <hr>
        <section id="section-researches">
            <div class="row">
                <div class="col-12">
                    <h2>
                        Researches 
                        <i class="fas fa-plus-circle" id="add-research-header-btn" onclick="createResearch()" data-bs-toggle="tooltip" data-bs-placement="top" title="Create new research"></i> 
                        <i class="fas fa-filter" id="filter-research-btn" onclick="filterResearches()" data-bs-toggle="tooltip" data-bs-placement="top" title="Filter researches"></i>                   
                    </h2>
                </div>

                <div class="col-12">
                    <table class="pink-notes-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Tags</th>
                                <th>Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="research-row" data-category="personal">
                                <td><a href="research.php?id=1" class="text-decoration-none text-reset"><i class="fas fa-flask text-info"></i> Productivity Techniques</a></td>
                                <td>Exploring different productivity methodologies and their effectiveness</td>
                                <td><span class="badge bg-info">Personal</span></td>
                                <td><span class="badge bg-secondary">Productivity</span> <span class="badge bg-secondary">Self-Improvement</span></td>
                                <td>2024-01-15</td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View research" onclick="viewResearch()"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit research" onclick="editResearch()"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete research" onclick="deleteResearch()"></i>
                                </td>
                            </tr>

                            <tr class="research-row" data-category="work">
                                <td><a href="research.php?id=2" class="text-decoration-none text-reset"><i class="fas fa-flask text-success"></i> API Design Patterns</a></td>
                                <td>Best practices and patterns for designing scalable REST APIs</td>
                                <td><span class="badge bg-success">Work</span></td>
                                <td><span class="badge bg-secondary">Backend</span> <span class="badge bg-secondary">Architecture</span></td>
                                <td>2024-01-10</td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View research" onclick="viewResearchWork()"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit research"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete research"></i>
                                </td>
                            </tr>

                            <tr class="research-row" data-category="gym">
                                <td><a href="research.php?id=3" class="text-decoration-none text-reset"><i class="fas fa-flask text-danger"></i> Strength Training Recovery</a></td>
                                <td>Analysis of recovery methods and their impact on muscle growth</td>
                                <td><span class="badge bg-danger">Gym</span></td>
                                <td><span class="badge bg-secondary">Training</span> <span class="badge bg-secondary">Recovery</span></td>
                                <td>2024-01-08</td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View research" onclick="viewResearchGym()"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit research"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete research"></i>
                                </td>
                            </tr>

                            <tr class="research-row" data-category="financial">
                                <td><a href="research.php?id=4" class="text-decoration-none text-reset"><i class="fas fa-flask text-warning"></i> Investment Strategies</a></td>
                                <td>Comparison of different investment approaches and risk management</td>
                                <td><span class="badge bg-warning text-dark">Financial</span></td>
                                <td><span class="badge bg-secondary">Investing</span> <span class="badge bg-secondary">Analysis</span></td>
                                <td>2024-01-05</td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View research" onclick="viewResearchFinancial()"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit research"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete research"></i>
                                </td>
                            </tr>

                            <tr class="research-row" data-category="personal">
                                <td><a href="research.php?id=5" class="text-decoration-none text-reset"><i class="fas fa-flask text-info"></i> Sleep Optimization</a></td>
                                <td>Research on sleep cycles and techniques for better sleep quality</td>
                                <td><span class="badge bg-info">Personal</span></td>
                                <td><span class="badge bg-secondary">Health</span> <span class="badge bg-secondary">Sleep</span></td>
                                <td>2023-12-28</td>
                                <td class="text-right">
                                    <i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="View research"></i>
                                    <i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit research"></i>
                                    <i class="fas fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete research"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>
    </main> 
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/global_footer.php'; ?>

<script src="/src/js/braingonizer/researches.js"></script>
</body>
</html>
