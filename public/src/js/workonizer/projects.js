/**
 * Workonizer Projects Module - Frontend JavaScript
 * Handles all client-side logic for work projects management
 */

function addProject() {
    Swal.fire({
        title: 'New Project',
        html: `
            <input type="text" id="proj_name" class="swal2-input" placeholder="Project Name" required>
            <textarea id="proj_description" class="swal2-textarea" placeholder="Project Description"></textarea>
            <select id="proj_status" class="swal2-select">
                <option value="active" selected>Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        preConfirm: () => {
            const name = document.getElementById('proj_name').value;
            const description = document.getElementById('proj_description').value;
            const status = document.getElementById('proj_status').value;
            
            if (!name) {
                Swal.showValidationMessage('Project name is required');
                return false;
            }
            
            return { name, description, status };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            debugLog(`Creating workonizer project: ${result.value.name}`, 'projects');
            axios.post('projects.php', new URLSearchParams({
                action: 'create',
                name: result.value.name,
                description: result.value.description,
                status: result.value.status
            })).then(response => {
                debugLog(`Create workonizer project response: ${JSON.stringify(response.data)}`, 'projects');
                if (response.data.success) {
                    Swal.fire('Success!', 'Project created successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            }).catch(error => {
                debugLog(`Create workonizer project error: ${error}`, 'projects');
                Swal.fire('Error', 'Failed to create project', 'error');
            });
        }
    });
}

function viewProject(id) {
    debugLog(`Viewing workonizer project id: ${id}`, 'projects');
    axios.post('projects.php', new URLSearchParams({
        action: 'get_project',
        id: id
    })).then(response => {
        debugLog(`Get workonizer project response: ${JSON.stringify(response.data)}`, 'projects');
        if (response.data.success) {
            const proj = response.data.project;
            Swal.fire({
                title: `<i class="fas fa-project-diagram"></i> ${proj.name}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- Description Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">${proj.description || 'N/A'}</p>
                        </div>
                        
                        <!-- Footer-like Section: Status -->
                        <div class="modal-footer-section" style="border-top: 1px solid #444; padding-top: 1rem; margin-top: 1.5rem; font-size: 0.75em;">
                            <p style="margin-bottom: 0;">
                                <strong>Status:</strong> <span class="badge bg-${proj.status === 'completed' ? 'success' : (proj.status === 'active' ? 'info' : 'secondary')}">${proj.status}</span>
                            </p>
                        </div>
                    </div>
                `,
                background: '#1e1e1e',
                color: '#e0e0e0',
                width: '600px',
                padding: '2em',
                confirmButtonText: '<i class="fas fa-times"></i> Close',
                confirmButtonColor: '#ff6b9d',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        }
    }).catch(error => {
        debugLog(`View workonizer project error: ${error}`, 'projects');
    });
}

function editProject(id) {
    debugLog(`Editing workonizer project id: ${id}`, 'projects');
    axios.post('projects.php', new URLSearchParams({
        action: 'get_project',
        id: id
    })).then(response => {
        debugLog(`Get workonizer project for edit response: ${JSON.stringify(response.data)}`, 'projects');
        if (response.data.success) {
            const proj = response.data.project;
            Swal.fire({
                title: 'Edit Project',
                html: `
                    <input type="text" id="proj_name" class="swal2-input" value="${proj.name}" required>
                    <textarea id="proj_description" class="swal2-textarea">${proj.description || ''}</textarea>
                    <select id="proj_status" class="swal2-select">
                        <option value="active" ${proj.status === 'active' ? 'selected' : ''}>Active</option>
                        <option value="on_hold" ${proj.status === 'on_hold' ? 'selected' : ''}>On Hold</option>
                        <option value="completed" ${proj.status === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const name = document.getElementById('proj_name').value;
                    const description = document.getElementById('proj_description').value;
                    const status = document.getElementById('proj_status').value;
                    
                    if (!name) {
                        Swal.showValidationMessage('Project name is required');
                        return false;
                    }
                    
                    return { name, description, status };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('projects.php', new URLSearchParams({
                        action: 'update',
                        id: id,
                        name: result.value.name,
                        description: result.value.description,
                        status: result.value.status
                    })).then(response => {
                        debugLog(`Update workonizer project response: ${JSON.stringify(response.data)}`, 'projects');
                        if (response.data.success) {
                            Swal.fire('Success!', 'Project updated successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    }).catch(error => {
                        debugLog(`Update workonizer project error: ${error}`, 'projects');
                        Swal.fire('Error', 'Failed to update project', 'error');
                    });
                }
            });
        }
    }).catch(error => {
        debugLog(`Edit workonizer project fetch error: ${error}`, 'projects');
    });
}

function deleteProject(id) {
    Swal.fire({
        title: 'Delete Project?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            debugLog(`Deleting workonizer project id: ${id}`, 'projects');
            axios.post('projects.php', new URLSearchParams({
                action: 'delete',
                id: id
            })).then(response => {
                debugLog(`Delete workonizer project response: ${JSON.stringify(response.data)}`, 'projects');
                if (response.data.success) {
                    Swal.fire('Deleted!', 'Project deleted successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            }).catch(error => {
                debugLog(`Delete workonizer project error: ${error}`, 'projects');
                Swal.fire('Error', 'Failed to delete project', 'error');
            });
        }
    });
}
