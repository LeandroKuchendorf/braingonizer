/**
 * Files Module - Frontend JavaScript
 * Handles all client-side logic for files management
 */

// Initialize tooltips on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const filterButtons = document.querySelectorAll('.file-filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the category filter value
            const category = this.getAttribute('data-category');
            debugLog(`Filtering by category: ${category}`, 'files');
            
            // TODO: Implement actual filtering logic here
            // For now, just log the selection
        });
    });
});

// Upload file modal function
uploadFileModal = function() {
    const categoryOptions = window.allCategories && window.allCategories.length > 0
        ? window.allCategories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')
        : '<option value="">No categories created</option>';
    
    Swal.fire({
        title: '<i class="fas fa-upload" style="color: var(--color-primary);"></i> Upload File',
        html: `
            <input type="file" id="file-upload" class="swal2-file" style="display:block; margin:10px auto;">
            <input id="file-name" class="swal2-input" placeholder="File Name (optional)">
            <textarea id="file-desc" class="swal2-textarea" placeholder="Description"></textarea>
            <select id="file-category" class="swal2-select">
                <option value="">Select Category</option>
                ${categoryOptions}
            </select>
            <input id="file-tags" class="swal2-input" placeholder="Tags (comma separated)">
        `,
        background: '#1e1e1e',
        color: '#e0e0e0',
        width: '600px',
        padding: '2em',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-upload"></i> Upload',
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
            const fileInput = document.getElementById('file-upload');
            const fileName = document.getElementById('file-name').value.trim();
            const description = document.getElementById('file-desc').value.trim();
            const category = document.getElementById('file-category').value;
            const tagsInput = document.getElementById('file-tags').value.trim();
            
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.showValidationMessage('Please select a file');
                return false;
            }
            
            if (!category) {
                Swal.showValidationMessage('Please select a category');
                return false;
            }
            
            const tags = tagsInput ? tagsInput.split(',').map(t => t.trim()) : [];
            
            return {
                file: fileInput.files[0],
                name: fileName || fileInput.files[0].name,
                description,
                category,
                tags
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            uploadFileAction(result.value);
        }
    });
};

// Upload file AJAX
uploadFileAction = function(data) {
    debugLog(`Uploading file: ${data.name}`, 'files');
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('file', data.file);
    formData.append('name', data.name);
    formData.append('description', data.description);
    formData.append('category_id', data.category);
    formData.append('tags', JSON.stringify(data.tags));
    
    axios.post('files.php', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        const result = response.data;
        debugLog(`Upload file response: ${JSON.stringify(result)}`, 'files');
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
        debugLog(`Upload file error: ${error}`, 'files');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Edit file function
editFile = function(id) {
    debugLog(`Editing file id: ${id}`, 'files');
    axios.post('files.php', new URLSearchParams({
        action: 'get_file',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get file for edit response: ${JSON.stringify(data)}`, 'files');
        if (data.success) {
            const file = data.file;
            const tagNames = file.tags.map(t => t.name).join(', ');
            
            const categoryOptions = window.allCategories && window.allCategories.length > 0
                ? window.allCategories.map(cat => `<option value="${cat.id}" ${file.category_id == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('')
                : '<option value="">No categories created</option>';
            
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Edit File Details',
                html: `
                    <input id="edit-file-name" class="swal2-input" placeholder="File Name" value="${file.name}" maxlength="255">
                    <textarea id="edit-file-desc" class="swal2-textarea" placeholder="Description" rows="4">${file.description}</textarea>
                    <select id="edit-file-category" class="swal2-select">
                        <option value="">Select Category</option>
                        ${categoryOptions}
                    </select>
                    <input id="edit-file-tags" class="swal2-input" placeholder="Tags (comma separated)" value="${tagNames}">
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
                    const name = document.getElementById('edit-file-name').value.trim();
                    const description = document.getElementById('edit-file-desc').value.trim();
                    const category = document.getElementById('edit-file-category').value;
                    const tagsInput = document.getElementById('edit-file-tags').value.trim();
                    
                    if (!name) {
                        Swal.showValidationMessage('Please enter a file name');
                        return false;
                    }
                    
                    const tags = tagsInput ? tagsInput.split(',').map(t => t.trim()) : [];
                    
                    return { id, name, description, category, tags };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateFileAction(result.value);
                }
            });
        }
    })
    .catch(error => {
        debugLog(`Edit file fetch error: ${error}`, 'files');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Update file AJAX
updateFileAction = function(data) {
    debugLog(`Updating file id: ${data.id}, name: ${data.name}`, 'files');
    axios.post('files.php', new URLSearchParams({
        action: 'update',
        id: data.id,
        name: data.name,
        description: data.description,
        category_id: data.category,
        tags: JSON.stringify(data.tags)
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Update file response: ${JSON.stringify(result)}`, 'files');
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
        debugLog(`Update file error: ${error}`, 'files');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Delete file confirmation
deleteFileConfirm = function(id) {
    Swal.fire({
        title: 'Delete File',
        text: 'Are you sure you want to delete this file?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFileAction(id);
        }
    });
};

// Delete file AJAX
deleteFileAction = function(id) {
    debugLog(`Deleting file id: ${id}`, 'files');
    axios.post('files.php', new URLSearchParams({
        action: 'delete',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete file response: ${JSON.stringify(result)}`, 'files');
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
    })
    .catch(error => {
        debugLog(`Delete file error: ${error}`, 'files');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Download file function
downloadFile = function(filePath, fileName) {
    const link = document.createElement('a');
    link.href = filePath;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
