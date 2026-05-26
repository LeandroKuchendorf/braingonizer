/**
 * Workonizer Tasks Module - Frontend JavaScript
 * Handles all client-side logic for work tasks management
 */

function addTask() {
    Swal.fire({
        title: 'New Task',
        html: `
            <input type="text" id="task_title" class="swal2-input" placeholder="Task Title" required>
            <textarea id="task_description" class="swal2-textarea" placeholder="Task Description"></textarea>
            <select id="task_priority" class="swal2-select">
                <option value="low">Low Priority</option>
                <option value="medium" selected>Medium Priority</option>
                <option value="high">High Priority</option>
            </select>
            <select id="task_status" class="swal2-select">
                <option value="pending" selected>Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="date" id="task_due_date" class="swal2-input" placeholder="Due Date">
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        preConfirm: () => {
            const title = document.getElementById('task_title').value;
            const description = document.getElementById('task_description').value;
            const priority = document.getElementById('task_priority').value;
            const status = document.getElementById('task_status').value;
            const dueDate = document.getElementById('task_due_date').value;
            
            if (!title) {
                Swal.showValidationMessage('Title is required');
                return false;
            }
            
            return { title, description, priority, status, dueDate };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            debugLog(`Creating workonizer task: ${result.value.title}`, 'tasks');
            axios.post('tasks.php', new URLSearchParams({
                action: 'create',
                title: result.value.title,
                description: result.value.description,
                priority: result.value.priority,
                status: result.value.status,
                due_date: result.value.dueDate
            })).then(response => {
                debugLog(`Create workonizer task response: ${JSON.stringify(response.data)}`, 'tasks');
                if (response.data.success) {
                    Swal.fire('Success!', 'Task created successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            }).catch(error => {
                debugLog(`Create workonizer task error: ${error}`, 'tasks');
                Swal.fire('Error', 'Failed to create task', 'error');
            });
        }
    });
}

function viewTask(id) {
    debugLog(`Viewing workonizer task id: ${id}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'get_task',
        id: id
    })).then(response => {
        debugLog(`Get workonizer task response: ${JSON.stringify(response.data)}`, 'tasks');
        if (response.data.success) {
            const task = response.data.task;
            Swal.fire({
                title: `<i class="fas fa-tasks"></i> ${task.title}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- Description Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">${task.description || 'N/A'}</p>
                        </div>
                        
                        <!-- Due Date Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">
                                <strong>Due Date:</strong> ${task.due_date && task.due_date !== '0000-00-00' && !isNaN(new Date(task.due_date)) ? new Date(task.due_date).toLocaleDateString() : 'No date set'}
                            </p>
                        </div>
                        
                        <!-- Footer-like Section: Priority, Status -->
                        <div class="modal-footer-section" style="border-top: 1px solid #444; padding-top: 1rem; margin-top: 1.5rem; font-size: 0.75em;">
                            <p style="margin-bottom: 0.75rem;">
                                <strong>Priority:</strong> <span class="badge bg-${task.priority === 'high' ? 'danger' : (task.priority === 'medium' ? 'warning' : 'secondary')}">${task.priority}</span>
                            </p>
                            <p style="margin-bottom: 0;">
                                <strong>Status:</strong> <span class="badge bg-${task.status === 'completed' ? 'success' : (task.status === 'in_progress' ? 'info' : 'secondary')}">${task.status}</span>
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
        debugLog(`View workonizer task error: ${error}`, 'tasks');
    });
}

function editTask(id) {
    debugLog(`Editing workonizer task id: ${id}`, 'tasks');
    axios.post('tasks.php', new URLSearchParams({
        action: 'get_task',
        id: id
    })).then(response => {
        debugLog(`Get workonizer task for edit response: ${JSON.stringify(response.data)}`, 'tasks');
        if (response.data.success) {
            const task = response.data.task;
            Swal.fire({
                title: 'Edit Task',
                html: `
                    <input type="text" id="task_title" class="swal2-input" value="${task.title}" required>
                    <textarea id="task_description" class="swal2-textarea">${task.description || ''}</textarea>
                    <select id="task_priority" class="swal2-select">
                        <option value="low" ${task.priority === 'low' ? 'selected' : ''}>Low Priority</option>
                        <option value="medium" ${task.priority === 'medium' ? 'selected' : ''}>Medium Priority</option>
                        <option value="high" ${task.priority === 'high' ? 'selected' : ''}>High Priority</option>
                    </select>
                    <select id="task_status" class="swal2-select">
                        <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="in_progress" ${task.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                        <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="cancelled" ${task.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                    <input type="date" id="task_due_date" class="swal2-input" value="${task.due_date || ''}">
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const title = document.getElementById('task_title').value;
                    const description = document.getElementById('task_description').value;
                    const priority = document.getElementById('task_priority').value;
                    const status = document.getElementById('task_status').value;
                    const dueDate = document.getElementById('task_due_date').value;
                    
                    if (!title) {
                        Swal.showValidationMessage('Title is required');
                        return false;
                    }
                    
                    return { title, description, priority, status, dueDate };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('tasks.php', new URLSearchParams({
                        action: 'update',
                        id: id,
                        title: result.value.title,
                        description: result.value.description,
                        priority: result.value.priority,
                        status: result.value.status,
                        due_date: result.value.dueDate
                    })).then(response => {
                        debugLog(`Update workonizer task response: ${JSON.stringify(response.data)}`, 'tasks');
                        if (response.data.success) {
                            Swal.fire('Success!', 'Task updated successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    }).catch(error => {
                        debugLog(`Update workonizer task error: ${error}`, 'tasks');
                        Swal.fire('Error', 'Failed to update task', 'error');
                    });
                }
            });
        }
    }).catch(error => {
        debugLog(`Edit workonizer task fetch error: ${error}`, 'tasks');
    });
}

function deleteTask(id) {
    Swal.fire({
        title: 'Delete Task?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            debugLog(`Deleting workonizer task id: ${id}`, 'tasks');
            axios.post('tasks.php', new URLSearchParams({
                action: 'delete',
                id: id
            })).then(response => {
                debugLog(`Delete workonizer task response: ${JSON.stringify(response.data)}`, 'tasks');
                if (response.data.success) {
                    Swal.fire('Deleted!', 'Task deleted successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            }).catch(error => {
                debugLog(`Delete workonizer task error: ${error}`, 'tasks');
                Swal.fire('Error', 'Failed to delete task', 'error');
            });
        }
    });
}

// Filter tasks by status
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.filter-btn[data-status]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn[data-status]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const status = this.dataset.status;
            debugLog(`Filtering workonizer tasks by status: ${status}`, 'tasks');
            document.querySelectorAll('.task-row').forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
