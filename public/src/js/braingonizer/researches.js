/**
 * Braingonizer Researches Module - Frontend JavaScript
 * Handles all client-side logic for researches management
 */

document.addEventListener('DOMContentLoaded', function() {
    debugLog('Researches page loaded.', 'researches');

    const filterButtons = document.querySelectorAll('.filter-btn');
    const researchRows = document.querySelectorAll('.research-row');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the category filter value
            const category = this.getAttribute('data-category');
            debugLog(`Filtering researches by category: ${category}`, 'researches');
            
            // Filter research rows
            researchRows.forEach(row => {
                if (category === 'all') {
                    row.style.display = '';
                } else {
                    if (row.getAttribute('data-category') === category) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
});

// Add research category function
addResearchCategory = function() {
    Swal.fire({
        title: 'Add Research Category',
        html: `
            <input id="category-name" class="swal2-input" placeholder="Category Name">
            <select id="category-color" class="swal2-select">
                <option value="">Select Color</option>
                <option value="primary">Blue</option>
                <option value="success">Green</option>
                <option value="danger">Red</option>
                <option value="warning">Yellow</option>
                <option value="info">Cyan</option>
                <option value="secondary">Gray</option>
                <option value="pink">Pink</option>
                <option value="purple">Purple</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-success').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        preConfirm: () => {
            Swal.fire({
                icon: 'info',
                title: 'Feature Not Implemented',
                text: 'Save functionality will be added in the next phase',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return false;
        }
    });
};

// Create research function
createResearch = function() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Create New Research',
        html: `
            <input id="research-title" class="swal2-input" placeholder="Research Title">
            <textarea id="research-desc" class="swal2-textarea" placeholder="Description"></textarea>
            <select id="research-category" class="swal2-select">
                <option value="">Select Category</option>
                <option value="personal">Personal</option>
                <option value="work">Work</option>
                <option value="gym">Gym</option>
                <option value="financial">Financial</option>
            </select>
            <input id="research-tags" class="swal2-input" placeholder="Tags (comma separated)">
        `,
        background: '#1e1e1e',
        color: '#e0e0e0',
        width: '600px',
        padding: '2em',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Create',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-success').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        preConfirm: () => {
            Swal.fire({
                icon: 'info',
                title: 'Feature Not Implemented',
                text: 'Save functionality will be added in the next phase',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return false;
        }
    });
};

// Filter researches function
filterResearches = function() {
    Swal.fire({
        title: 'Filter Researches',
        html: `
            <div class="text-left">
                <p><strong>Category:</strong></p>
                <label><input type="checkbox" value="personal"> Personal</label><br>
                <label><input type="checkbox" value="work"> Work</label><br>
                <label><input type="checkbox" value="gym"> Gym</label><br>
                <label><input type="checkbox" value="financial"> Financial</label><br>
                
                <p class="mt-3"><strong>Tags:</strong></p>
                <label><input type="checkbox" value="productivity"> Productivity</label><br>
                <label><input type="checkbox" value="backend"> Backend</label><br>
                <label><input type="checkbox" value="training"> Training</label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Apply Filters',
        cancelButtonText: 'Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim(),
        preConfirm: () => {
            Swal.fire({
                icon: 'info',
                title: 'Feature Not Implemented',
                text: 'Filtering functionality will be added in the next phase',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return false;
        }
    });
};

// View research function
viewResearch = function() {
    Swal.fire({
        title: '<i class="fas fa-flask"></i> Productivity Techniques',
        html: `
            <div class="text-left">
                <p><strong>Description:</strong><br>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                
                <p><strong>Category:</strong> <span class="badge bg-info">Personal</span></p>
                <p><strong>Tags:</strong> 
                    <span class="badge bg-secondary">Productivity</span>
                    <span class="badge bg-secondary">Self-Improvement</span>
                </p>
            </div>
        `,
        iconHtml: '<i class="fas fa-flask" style="font-size: 1.8em; margin-bottom: 0.5em;"></i>',
        iconColor: '#28a745',
        width: '600px',
        padding: '2em',
        footer: '<small style="color: #888;"><i class="fas fa-calendar"></i> Created: 2024-01-15 | <i class="fas fa-folder"></i> 3 sections, 5 files</small>',
        confirmButtonText: '<i class="fas fa-times"></i> Close',
        confirmButtonColor: '#17a2b8',
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        customClass: {
            popup: 'research-view-modal',
            title: 'research-modal-title',
            icon: 'swal2-icon-no-border'
        }
    });
};

viewResearchWork = function() {
    viewResearch();
};

viewResearchGym = function() {
    viewResearch();
};

viewResearchFinancial = function() {
    viewResearch();
};

// Edit research function
editResearch = function() {
    Swal.fire({
        title: '<i class="fas fa-edit"></i> Edit Research',
        html: `
            <input id="edit-research-title" class="swal2-input" placeholder="Research Title" value="Productivity Techniques">
            <textarea id="edit-research-desc" class="swal2-textarea" placeholder="Description">Exploring different productivity methodologies and their effectiveness</textarea>
            <select id="edit-research-category" class="swal2-select">
                <option value="">Select Category</option>
                <option value="personal" selected>Personal</option>
                <option value="work">Work</option>
                <option value="gym">Gym</option>
                <option value="financial">Financial</option>
            </select>
            <input id="edit-research-tags" class="swal2-input" placeholder="Tags (comma separated)" value="Productivity, Self-Improvement">
        `,
        background: '#1e1e1e',
        color: '#e0e0e0',
        width: '600px',
        padding: '2em',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Save Changes',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-success').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        preConfirm: () => {
            Swal.fire({
                icon: 'info',
                title: 'Feature Not Implemented',
                text: 'Save functionality will be added in the next phase',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return false;
        }
    });
};

// Delete research function
deleteResearch = function(researchId) {
    Swal.fire({
    title: "Delete Research",
    text: "Are you sure you want to delete this research?",
    icon: "error",
    showCancelButton: true,
    confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-info').trim(),
    confirmButtonText: "Yes, delete"
    }).then((result) => {
    if (result.isConfirmed) {
        Swal.fire({
        title: "Deleted!",
        text: "Your research has been deleted.",
        icon: "success"
        });
    }
    });
};
