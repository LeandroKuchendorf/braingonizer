/**
 * Projects Module - Frontend JavaScript
 * Handles all client-side logic for projects management
 */

// Add Project Modal
function addProjectModal() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Project',
        html: `
            <input id="project-name" class="swal2-input" placeholder="Project Name" maxlength="255">
            <textarea id="project-description" class="swal2-textarea" placeholder="Project Description"></textarea>
            <select id="project-color" class="swal2-select">
                <option value="">Select Color</option>
                <option value="primary">Blue</option>
                <option value="secondary">Gray</option>
                <option value="success">Green</option>
                <option value="danger">Red</option>
                <option value="warning">Yellow</option>
                <option value="info">Cyan</option>
                <option value="pink">Pink</option>
                <option value="purple">Purple</option>
            </select>
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
            const name = document.getElementById('project-name').value.trim();
            const description = document.getElementById('project-description').value.trim();
            const color = document.getElementById('project-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a project name');
                return false;
            }
            
            if (!color) {
                Swal.showValidationMessage('Please select a color');
                return false;
            }
            
            return { name, description, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createProject(result.value.name, result.value.description, result.value.color);
        }
    });
}

// Edit Project Modal
function editProjectModal(id, name, description, color) {
    Swal.fire({
        title: '<i class="fas fa-edit"></i> Edit Project',
        html: `
            <input id="project-name" class="swal2-input" placeholder="Project Name" value="${name}" maxlength="255">
            <textarea id="project-description" class="swal2-textarea" placeholder="Project Description">${description}</textarea>
            <select id="project-color" class="swal2-select">
                <option value="primary" ${color === 'primary' ? 'selected' : ''}>Blue</option>
                <option value="secondary" ${color === 'secondary' ? 'selected' : ''}>Gray</option>
                <option value="success" ${color === 'success' ? 'selected' : ''}>Green</option>
                <option value="danger" ${color === 'danger' ? 'selected' : ''}>Red</option>
                <option value="warning" ${color === 'warning' ? 'selected' : ''}>Yellow</option>
                <option value="info" ${color === 'info' ? 'selected' : ''}>Cyan</option>
                <option value="pink" ${color === 'pink' ? 'selected' : ''}>Pink</option>
                <option value="purple" ${color === 'purple' ? 'selected' : ''}>Purple</option>
            </select>
        `,
        background: '#1e1e1e',
        color: '#e0e0e0',
        width: '600px',
        padding: '2em',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Update',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        preConfirm: () => {
            const name = document.getElementById('project-name').value.trim();
            const description = document.getElementById('project-description').value.trim();
            const color = document.getElementById('project-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a project name');
                return false;
            }
            
            return { id, name, description, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateProject(result.value.id, result.value.name, result.value.description, result.value.color);
        }
    });
}

// Delete Project Confirmation
function deleteProjectConfirm(id) {
    Swal.fire({
        title: 'Delete Project',
        text: 'Are you sure you want to delete this project? All associated tasks will also be deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteProject(id);
        }
    });
}

// Create Project AJAX
function createProject(name, description, color) {
    debugLog(`Creating project: ${name}, color: ${color}`, 'projects');
    axios.post('projects.php', new URLSearchParams({
        action: 'create_project',
        name: name,
        description: description,
        color: color
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Create project response: ${JSON.stringify(data)}`, 'projects');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        debugLog(`Create project error: ${error}`, 'projects');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Update Project AJAX
function updateProject(id, name, description, color) {
    debugLog(`Updating project id: ${id}, name: ${name}, color: ${color}`, 'projects');
    axios.post('projects.php', new URLSearchParams({
        action: 'update_project',
        id: id,
        name: name,
        description: description,
        color: color
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Update project response: ${JSON.stringify(data)}`, 'projects');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        debugLog(`Update project error: ${error}`, 'projects');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Delete Project AJAX
function deleteProject(id) {
    debugLog(`Deleting project id: ${id}`, 'projects');
    axios.post('projects.php', new URLSearchParams({
        action: 'delete_project',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Delete project response: ${JSON.stringify(data)}`, 'projects');
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Cannot Delete',
                text: data.message
            });
        }
    })
    .catch(error => {
        debugLog(`Delete project error: ${error}`, 'projects');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-projects');
    const tableBody = document.getElementById('projects-table-body');
    const allRows = Array.from(tableBody.querySelectorAll('tr'));
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        allRows.forEach(row => {
            const projectName = row.querySelector('td:first-child').textContent.toLowerCase();
            const projectDesc = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (projectName.includes(query) || projectDesc.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
