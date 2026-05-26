/**
 * Tags Module - Frontend JavaScript
 * Handles all client-side logic for tags management
 */

// Add Tag Modal
function addTagModal() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Tag',
        html: `
            <input id="tag-name" class="swal2-input" placeholder="Tag Name" maxlength="100">
            <select id="tag-color" class="swal2-select">
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
        width: '500px',
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
            const name = document.getElementById('tag-name').value.trim();
            const color = document.getElementById('tag-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a tag name');
                return false;
            }
            
            if (!color) {
                Swal.showValidationMessage('Please select a color');
                return false;
            }
            
            return { name, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createTag(result.value.name, result.value.color);
        }
    });
}

// Edit Tag Modal
function editTagModal(id, name, color) {
    Swal.fire({
        title: '<i class="fas fa-edit"></i> Edit Tag',
        html: `
            <input id="tag-name" class="swal2-input" placeholder="Tag Name" value="${name}" maxlength="100">
            <select id="tag-color" class="swal2-select">
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
        width: '500px',
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
            const name = document.getElementById('tag-name').value.trim();
            const color = document.getElementById('tag-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a tag name');
                return false;
            }
            
            return { id, name, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateTag(result.value.id, result.value.name, result.value.color);
        }
    });
}

// Delete Tag Confirmation
function deleteTagConfirm(id) {
    Swal.fire({
        title: 'Delete Tag',
        text: 'Are you sure you want to delete this tag?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteTag(id);
        }
    });
}

// Create Tag AJAX
function createTag(name, color) {
    debugLog(`Creating tag: ${name}, color: ${color}`, 'tags');
    axios.post('tags.php', new URLSearchParams({
        action: 'create',
        name: name,
        color: color
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Create tag response: ${JSON.stringify(data)}`, 'tags');
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
        debugLog(`Create tag error: ${error}`, 'tags');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Update Tag AJAX
function updateTag(id, name, color) {
    debugLog(`Updating tag id: ${id}, name: ${name}, color: ${color}`, 'tags');
    axios.post('tags.php', new URLSearchParams({
        action: 'update',
        id: id,
        name: name,
        color: color
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Update tag response: ${JSON.stringify(data)}`, 'tags');
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
        debugLog(`Update tag error: ${error}`, 'tags');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Delete Tag AJAX
function deleteTag(id) {
    debugLog(`Deleting tag id: ${id}`, 'tags');
    axios.post('tags.php', new URLSearchParams({
        action: 'delete',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Delete tag response: ${JSON.stringify(data)}`, 'tags');
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
        debugLog(`Delete tag error: ${error}`, 'tags');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-tags');
    const tableBody = document.getElementById('tags-table-body');
    const allRows = Array.from(tableBody.querySelectorAll('tr'));
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        allRows.forEach(row => {
            const tagName = row.querySelector('td:first-child').textContent.toLowerCase();
            if (tagName.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
