/**
 * Ideas Module - Frontend JavaScript
 * Handles all client-side logic for ideas management
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
            
            // Get the category filter value
            const category = this.getAttribute('data-category');
            debugLog(`Filtering by category: ${category}`, 'ideas');
            
            // TODO: Implement actual filtering logic here
            // For now, just log the selection
        });
    });
});

// Add idea category function
addIdeaCategory = function() {
    Swal.fire({
        title: '<i class="fas fa-folder-plus" style="color: var(--color-primary);"></i> Add Idea Category',
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
            const name = document.getElementById('category-name').value.trim();
            const color = document.getElementById('category-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a category name');
                return false;
            }
            
            return { name, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createIdeaCategoryAction(result.value);
        }
    });
};

// Create idea category AJAX
createIdeaCategoryAction = function(data) {
    debugLog(`Creating idea category: ${data.name}, color: ${data.color}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'create_category',
        name: data.name,
        color: data.color
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create idea category response: ${JSON.stringify(result)}`, 'ideas');
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
        debugLog(`Create idea category error: ${error}`, 'ideas');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Add idea function
addIdea = function() {
    const categoryOptions = window.allCategories && window.allCategories.length > 0
        ? window.allCategories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')
        : '<option value="">No categories created</option>';
    
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Idea',
        html: `
            <input id="idea-title" class="swal2-input" placeholder="Idea Title">
            <textarea id="idea-desc" class="swal2-textarea" placeholder="Description"></textarea>
            <select id="idea-category" class="swal2-select">
                <option value="">Select Category</option>
                ${categoryOptions}
            </select>
            <select id="idea-priority" class="swal2-select">
                <option value="">Select Priority</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
                <option value="very-low">Very Low</option>
            </select>
            <select id="idea-status" class="swal2-select">
                <option value="">Select Status</option>
                <option value="concept">Concept</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
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
        preConfirm: () => {
            const title = document.getElementById('idea-title').value.trim();
            const description = document.getElementById('idea-desc').value.trim();
            const category = document.getElementById('idea-category').value;
            const priority = document.getElementById('idea-priority').value;
            const status = document.getElementById('idea-status').value;
            
            if (!title) {
                Swal.showValidationMessage('Please enter a title');
                return false;
            }
            
            if (!category) {
                Swal.showValidationMessage('Please select a category');
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
            
            return { title, description, category, priority, status };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createIdeaAction(result.value);
        }
    });
};

// Create idea AJAX
createIdeaAction = function(data) {
    debugLog(`Creating idea: ${data.title}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'create',
        title: data.title,
        description: data.description,
        category_id: data.category,
        priority: data.priority,
        status: data.status
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create idea response: ${JSON.stringify(result)}`, 'ideas');
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
        debugLog(`Create idea error: ${error}`, 'ideas');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// View idea function
viewIdea = function(id) {
    debugLog(`Viewing idea id: ${id}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'get_idea',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get idea response: ${JSON.stringify(data)}`, 'ideas');
        if (data.success) {
            const idea = data.idea;
            
            const categoryClass = {
                'personal': 'info',
                'work': 'primary',
                'projects': 'success',
                'research': 'warning'
            }[idea.category] || 'secondary';
            
            const priorityClass = {
                'high': 'danger',
                'medium': 'warning',
                'low': 'info',
                'very-low': 'secondary'
            }[idea.priority] || 'secondary';
            
            const priorityIcon = {
                'high': 'exclamation-triangle',
                'medium': 'exclamation-circle',
                'low': 'info-circle',
                'very-low': 'minus-circle'
            }[idea.priority] || 'minus-circle';
            
            const statusClass = {
                'concept': 'secondary',
                'in-progress': 'warning',
                'completed': 'success'
            }[idea.status] || 'secondary';
            
            const statusIcon = {
                'concept': 'lightbulb',
                'in-progress': 'spinner fa-spin',
                'completed': 'check-circle'
            }[idea.status] || 'lightbulb';
            
            Swal.fire({
                title: `<i class="fas fa-lightbulb"></i> ${idea.title}`,
                html: `
                    <div class="text-left">
                        <p><strong>Description:</strong><br>
                        ${idea.description}</p>
                        
                        <p><strong>Category:</strong> <span class="badge bg-${categoryClass}">${idea.category_name || 'Uncategorized'}</span></p>
                        <p><strong>Priority:</strong> <span class="badge bg-${priorityClass}"><i class="fas fa-${priorityIcon}"></i> ${idea.priority}</span></p>
                        <p><strong>Status:</strong> <span class="badge bg-${statusClass}"><i class="fas fa-${statusIcon}"></i> ${idea.status}</span></p>
                    </div>
                `,
                background: '#1e1e1e',
                color: '#e0e0e0',
                width: '600px',
                padding: '2em',
                footer: `<small style="color: #888;"><i class="fas fa-calendar"></i> Created: ${new Date(idea.created_at).toLocaleDateString()} | <i class="fas fa-clock"></i> Updated: ${new Date(idea.updated_at).toLocaleDateString()}</small>`,
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
        debugLog(`View idea error: ${error}`, 'ideas');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Edit idea function
editIdea = function(id) {
    debugLog(`Editing idea id: ${id}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'get_idea',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get idea for edit response: ${JSON.stringify(data)}`, 'ideas');
        if (data.success) {
            const idea = data.idea;
            
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Edit Idea',
                html: `
                    <input id="edit-idea-title" class="swal2-input" placeholder="Idea Title" value="${idea.title}" maxlength="255">
                    <textarea id="edit-idea-desc" class="swal2-textarea" placeholder="Description" rows="4">${idea.description}</textarea>
                    <select id="edit-idea-category" class="swal2-select">
                        ${window.allCategories && window.allCategories.length > 0
                            ? window.allCategories.map(cat => `<option value="${cat.id}" ${idea.category_id == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('')
                            : '<option value="">No categories created</option>'}
                    </select>
                    <select id="edit-idea-priority" class="swal2-select">
                        <option value="high" ${idea.priority === 'high' ? 'selected' : ''}>High</option>
                        <option value="medium" ${idea.priority === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="low" ${idea.priority === 'low' ? 'selected' : ''}>Low</option>
                        <option value="very-low" ${idea.priority === 'very-low' ? 'selected' : ''}>Very Low</option>
                    </select>
                    <select id="edit-idea-status" class="swal2-select">
                        <option value="concept" ${idea.status === 'concept' ? 'selected' : ''}>Concept</option>
                        <option value="in-progress" ${idea.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                        <option value="completed" ${idea.status === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
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
                    const title = document.getElementById('edit-idea-title').value.trim();
                    const description = document.getElementById('edit-idea-desc').value.trim();
                    const category = document.getElementById('edit-idea-category').value;
                    const priority = document.getElementById('edit-idea-priority').value;
                    const status = document.getElementById('edit-idea-status').value;
                    
                    if (!title) {
                        Swal.showValidationMessage('Please enter a title');
                        return false;
                    }
                    
                    return { id, title, description, category, priority, status };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateIdeaAction(result.value);
                }
            });
        }
    })
    .catch(error => {
        debugLog(`Edit idea fetch error: ${error}`, 'ideas');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Update idea AJAX
updateIdeaAction = function(data) {
    debugLog(`Updating idea id: ${data.id}, title: ${data.title}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'update',
        id: data.id,
        title: data.title,
        description: data.description,
        category_id: data.category,
        priority: data.priority,
        status: data.status
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Update idea response: ${JSON.stringify(result)}`, 'ideas');
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
        debugLog(`Update idea error: ${error}`, 'ideas');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Delete idea confirmation
deleteIdeaConfirm = function(id) {
    Swal.fire({
        title: 'Delete Idea',
        text: 'Are you sure you want to delete this idea?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteIdeaAction(id);
        }
    });
};

// Delete idea AJAX
deleteIdeaAction = function(id) {
    debugLog(`Deleting idea id: ${id}`, 'ideas');
    axios.post('ideas.php', new URLSearchParams({
        action: 'delete',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete idea response: ${JSON.stringify(result)}`, 'ideas');
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
