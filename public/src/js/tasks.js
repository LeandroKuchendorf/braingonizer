/**
 * Tasks Module - Frontend JavaScript
 * Handles all client-side logic for tasks management
 */

// Initialize tooltips on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the project filter value
            const project = this.getAttribute('data-project');
            debugLog(`Filtering by project: ${project}`, 'tasks');
            
            // TODO: Implement actual filtering logic here
            // For now, just log the selection
        });
    });
});

// Add project function
addProject = function() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add Project',
        html: `
            <input id="project-name" class="swal2-input" placeholder="Project Name">
            <textarea id="project-desc" class="swal2-textarea" placeholder="Description"></textarea>
            <select id="project-color" class="swal2-select">
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
            const description = document.getElementById('project-desc').value.trim();
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
            createProjectAction(result.value);
        }
    });
};

// Create project AJAX
createProjectAction = function(data) {
    debugLog(`Creating project: ${data.name}, color: ${data.color}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'create_project',
        name: data.name,
        description: data.description,
        color: data.color
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create project response: ${JSON.stringify(result)}`, 'tasks');
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    })
    .catch(error => {
        debugLog(`Create project error: ${error}`, 'tasks');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Add task function
addTask = function() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Task',
        html: `
            <input id="task-title" class="swal2-input" placeholder="Task Title">
            <textarea id="task-desc" class="swal2-textarea" placeholder="Description"></textarea>
            <select id="task-project" class="swal2-select">
                <option value="">Select Project</option>
                ${allProjects.map(project => `<option value="${project.id}">${project.name}</option>`).join('')}
            </select>
            <select id="task-priority" class="swal2-select">
                <option value="">Select Priority</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
                <option value="very-low">Very Low</option>
            </select>
            <select id="task-status" class="swal2-select">
                <option value="">Select Status</option>
                <option value="completed">Completed</option>
                <option value="in-progress">In Progress</option>
                <option value="on-hold">On Hold</option>
                <option value="not-started">Not Started</option>
            </select>
            <select id="task-tags" class="swal2-select" multiple>
                ${allTags.map(tag => `<option value="${tag.name}">${tag.name}</option>`).join('')}
            </select>
            <input type="date" id="task-due-date" class="swal2-input" placeholder="Due Date">
        `,
        background: '#1e1e1e',
        color: '#e0e0e0',
        width: '600px',
        padding: '2em',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Save',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-success').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        customClass: {
            popup: 'task-modal-dark',
            title: 'swal2-title-dark',
            htmlContainer: 'swal2-html-dark'
        },
        didOpen: () => {
            $('#task-tags').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select or create tags (max 3)',
                tags: true,
                maximumSelectionLength: 3,
                tokenSeparators: [','],
                dropdownParent: $('.swal2-container')
            });
            
            setTimeout(() => {
                $('.select2-selection').css({
                    'background-color': '#2a2a2a',
                    'border-color': '#444',
                    'color': '#e0e0e0'
                });
                $('.select2-search__field').css({
                    'background-color': 'transparent',
                    'color': '#e0e0e0'
                });
            }, 10);
        },
        preConfirm: () => {
            const title = document.getElementById('task-title').value.trim();
            const description = document.getElementById('task-desc').value.trim();
            const projectId = document.getElementById('task-project').value;
            const priority = document.getElementById('task-priority').value;
            const status = document.getElementById('task-status').value;
            const dueDate = document.getElementById('task-due-date').value;
            const tags = $('#task-tags').val() || [];
            
            if (!title) {
                Swal.showValidationMessage('Please enter a title');
                return false;
            }
            
            if (!projectId) {
                Swal.showValidationMessage('Please select a project');
                return false;
            }
            
            if (!priority) {
                Swal.showValidationMessage('Please select a priority');
                return false;
            }
            
            if (!status) {
                Swal.showValidationMessage('Please select a status');
                return false;
            }
            
            if (tags.length > 3) {
                Swal.showValidationMessage('Maximum 3 tags allowed');
                return false;
            }
            
            return { title, description, projectId, priority, status, dueDate, tags };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createTaskAction(result.value);
        }
    });
};

// Create task AJAX
createTaskAction = function(data) {
    debugLog(`Creating task: ${data.title}, project: ${data.projectId}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'create',
        title: data.title,
        description: data.description,
        project_id: data.projectId,
        priority: data.priority,
        status: data.status,
        due_date: data.dueDate,
        tags: JSON.stringify(data.tags)
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create task response: ${JSON.stringify(result)}`, 'tasks');
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    })
    .catch(error => {
        debugLog(`Create task error: ${error}`, 'tasks');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// View task function
viewTask = function(id) {
    debugLog(`Viewing task id: ${id}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'get_task',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get task response: ${JSON.stringify(data)}`, 'tasks');
        if (data.success) {
            const task = data.task;
            const tagBadges = task.tags.map(tag => 
                `<span class="badge bg-${tag.color}">${tag.name}</span>`
            ).join(' ');
            
            Swal.fire({
                title: `<i class="fas fa-tasks"></i> ${task.title}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- Description Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">${task.description}</p>
                        </div>
                        
                        <!-- Due Date Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">
                                <strong>Due Date:</strong> ${task.due_date && task.due_date !== '0000-00-00' && !isNaN(new Date(task.due_date)) ? new Date(task.due_date).toLocaleDateString() : 'No date set'}
                            </p>
                        </div>
                        
                        <!-- Footer-like Section: Priority, Status, Tags -->
                        <div class="modal-footer-section" style="border-top: 1px solid #444; padding-top: 1rem; margin-top: 1.5rem; font-size: 0.75em;">
                            <p style="margin-bottom: 0.75rem;">
                                <strong>Priority:</strong> <span class="badge bg-${ {high:'danger',medium:'warning',low:'info','very-low':'secondary'}[task.priority] ?? 'secondary' }">${task.priority}</span>
                            </p>
                            <p style="margin-bottom: 0.75rem;">
                                <strong>Status:</strong> <span class="badge bg-${ {completed:'success','in-progress':'warning','on-hold':'danger','not-started':'secondary'}[task.status] ?? 'secondary' }">${task.status}</span>
                            </p>
                            ${task.project ? `<p style="margin-bottom: 0.75rem;"><strong>Project:</strong> ${task.project}</p>` : ''}
                            ${tagBadges ? `
                            <div style="margin-top: 0.75rem;">
                                <strong>Tags:</strong> ${tagBadges}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `,
                background: '#1e1e1e',
                color: '#e0e0e0',
                width: '600px',
                padding: '2em',
                confirmButtonText: '<i class="fas fa-times"></i> Close',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim(),
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        }
    })
    .catch(error => {
        debugLog(`View task error: ${error}`, 'tasks');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Edit task function
editTask = function(id) {
    debugLog(`Editing task id: ${id}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'get_task',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get task for edit response: ${JSON.stringify(data)}`, 'tasks');
        if (data.success) {
            const task = data.task;
            const tagNames = task.tags.map(t => t.name);
            
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Edit Task',
                html: `
                    <input id="edit-task-title" class="swal2-input" placeholder="Task Title" value="${task.title}" maxlength="255">
                    <textarea id="edit-task-desc" class="swal2-textarea" placeholder="Description" rows="4">${task.description}</textarea>
                    <select id="edit-task-project" class="swal2-select">
                        <option value="">Select Project</option>
                        ${allProjects.map(project => `<option value="${project.id}" ${project.id === task.project_id ? 'selected' : ''}>${project.name}</option>`).join('')}
                    </select>
                    <select id="edit-task-priority" class="swal2-select">
                        <option value="high" ${task.priority === 'high' ? 'selected' : ''}>High</option>
                        <option value="medium" ${task.priority === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="low" ${task.priority === 'low' ? 'selected' : ''}>Low</option>
                        <option value="very-low" ${task.priority === 'very-low' ? 'selected' : ''}>Very Low</option>
                    </select>
                    <select id="edit-task-status" class="swal2-select">
                        <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="in-progress" ${task.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                        <option value="on-hold" ${task.status === 'on-hold' ? 'selected' : ''}>On Hold</option>
                        <option value="not-started" ${task.status === 'not-started' ? 'selected' : ''}>Not Started</option>
                    </select>
                    <select id="edit-task-tags" class="swal2-select" multiple>
                        ${allTags.map(tag => `<option value="${tag.name}" ${tagNames.includes(tag.name) ? 'selected' : ''}>${tag.name}</option>`).join('')}
                    </select>
                    <input type="date" id="edit-task-due-date" class="swal2-input" value="${task.due_date || ''}">
                `,
                background: '#1e1e1e',
                color: '#e0e0e0',
                width: '600px',
                padding: '2em',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save"></i> Save',
                cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-success').trim(),
                cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                customClass: {
                    popup: 'task-modal-dark',
                    title: 'swal2-title-dark',
                    htmlContainer: 'swal2-html-dark'
                },
                didOpen: () => {
                    $('#edit-task-tags').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select or create tags (max 3)',
                        tags: true,
                        maximumSelectionLength: 3,
                        tokenSeparators: [','],
                        dropdownParent: $('.swal2-container')
                    });
                    
                    setTimeout(() => {
                        $('.select2-selection').css({
                            'background-color': '#2a2a2a',
                            'border-color': '#444',
                            'color': '#e0e0e0'
                        });
                        $('.select2-search__field').css({
                            'background-color': 'transparent',
                            'color': '#e0e0e0'
                        });
                    }, 10);
                },
                preConfirm: () => {
                    const title = document.getElementById('edit-task-title').value.trim();
                    const description = document.getElementById('edit-task-desc').value.trim();
                    const projectId = document.getElementById('edit-task-project').value;
                    const priority = document.getElementById('edit-task-priority').value;
                    const status = document.getElementById('edit-task-status').value;
                    const dueDate = document.getElementById('edit-task-due-date').value;
                    const tags = $('#edit-task-tags').val() || [];
                    
                    if (!title) {
                        Swal.showValidationMessage('Please enter a title');
                        return false;
                    }
                    
                    if (!projectId) {
                        Swal.showValidationMessage('Please select a project');
                        return false;
                    }
                    
                    if (tags.length > 3) {
                        Swal.showValidationMessage('Maximum 3 tags allowed');
                        return false;
                    }
                    
                    return { id, title, description, projectId, priority, status, dueDate, tags };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTaskAction(result.value);
                }
            });
        }
    })
    .catch(error => {
        debugLog(`Edit task fetch error: ${error}`, 'tasks');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Update task AJAX
updateTaskAction = function(data) {
    debugLog(`Updating task id: ${data.id}, title: ${data.title}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'update',
        id: data.id,
        title: data.title,
        description: data.description,
        project_id: data.projectId,
        priority: data.priority,
        status: data.status,
        due_date: data.dueDate,
        tags: JSON.stringify(data.tags)
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Update task response: ${JSON.stringify(result)}`, 'tasks');
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    })
    .catch(error => {
        debugLog(`Update task error: ${error}`, 'tasks');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Delete task confirmation
deleteTaskConfirm = function(id) {
    Swal.fire({
        title: 'Delete Task',
        text: 'Are you sure you want to delete this task?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteTaskAction(id);
        }
    });
};

// Delete task AJAX
deleteTaskAction = function(id) {
    debugLog(`Deleting task id: ${id}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'delete',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete task response: ${JSON.stringify(result)}`, 'tasks');
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: result.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            setTimeout(() => location.reload(), 1000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    });
};
