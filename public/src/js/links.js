/**
 * Links Module - Frontend JavaScript
 * Handles all client-side logic for links and categories management
 */

// Initialize tooltips on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Add category function
addCategory = function() {
    Swal.fire({
        title: '<i class="fas fa-folder-plus" style="color: var(--color-primary);"></i> Add New Category',
        html: `
            <input id="category-name" class="swal2-input" placeholder="Category Name">
            <input id="category-icon" class="swal2-input" placeholder="Icon (e.g., fas fa-star)">
            <select id="category-color" class="swal2-select">
                <option value="">Select Color</option>
                <option value="work">Blue (Work)</option>
                <option value="university">Green (University)</option>
                <option value="generic">Gray (Generic)</option>
                <option value="personal">Pink (Personal)</option>
                <option value="resources">Yellow (Resources)</option>
                <option value="entertainment">Cyan (Entertainment)</option>
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
            const icon = document.getElementById('category-icon').value.trim();
            const color = document.getElementById('category-color').value;
            
            if (!name) {
                Swal.showValidationMessage('Please enter a category name');
                return false;
            }
            
            if (!icon) {
                Swal.showValidationMessage('Please enter an icon class');
                return false;
            }
            
            if (!color) {
                Swal.showValidationMessage('Please select a color');
                return false;
            }
            
            return { name, icon, color };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createCategoryAction(result.value);
        }
    });
};

// Create category AJAX
createCategoryAction = function(data) {
    debugLog(`Creating link category: ${data.name}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'create_category',
        name: data.name,
        icon: data.icon,
        color: data.color
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create link category response: ${JSON.stringify(result)}`, 'links');
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
        debugLog(`Create link category error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Add link function
addLink = function(categoryId) {
    Swal.fire({
        title: '<i class="fas fa-link" style="color: var(--color-primary);"></i> Add New Link',
        html: `
            <input id="link-title" class="swal2-input" placeholder="Link Title">
            <input id="link-url" class="swal2-input" placeholder="https://example.com" type="url">
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
            const title = document.getElementById('link-title').value.trim();
            const url = document.getElementById('link-url').value.trim();
            
            if (!title) {
                Swal.showValidationMessage('Please enter a link title');
                return false;
            }
            
            if (!url) {
                Swal.showValidationMessage('Please enter a URL');
                return false;
            }
            
            // Basic URL validation
            try {
                new URL(url);
            } catch (e) {
                Swal.showValidationMessage('Please enter a valid URL');
                return false;
            }
            
            return { title, url, categoryId };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createLinkAction(result.value);
        }
    });
};

// Create link AJAX
createLinkAction = function(data) {
    debugLog(`Creating link: ${data.title}, url: ${data.url}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'create_link',
        title: data.title,
        url: data.url,
        category_id: data.categoryId
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create link response: ${JSON.stringify(result)}`, 'links');
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
        debugLog(`Create link error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// View link function
viewLink = function(id) {
    debugLog(`Viewing link id: ${id}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'get_link',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get link response: ${JSON.stringify(data)}`, 'links');
        if (data.success) {
            const link = data.link;
            Swal.fire({
                title: `<i class="fas fa-link"></i> ${link.title}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- URL Section -->
                        <div class="modal-section" style="margin-bottom: 0;">
                            <p style="margin-bottom: 0.5rem;"><strong>URL:</strong></p>
                            <p style="margin-bottom: 0;"><a href="${link.url}" target="_blank" class="text-primary">${link.url}</a></p>
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
        debugLog(`View link error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Edit link function
editLink = function(id) {
    debugLog(`Editing link id: ${id}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'get_link',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get link for edit response: ${JSON.stringify(data)}`, 'links');
        if (data.success) {
            const link = data.link;
            
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Edit Link',
                html: `
                    <input id="edit-link-title" class="swal2-input" placeholder="Link Title" value="${link.title}" maxlength="255">
                    <input id="edit-link-url" class="swal2-input" placeholder="https://example.com" type="url" value="${link.url}">
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
                    const title = document.getElementById('edit-link-title').value.trim();
                    const url = document.getElementById('edit-link-url').value.trim();
                    
                    if (!title) {
                        Swal.showValidationMessage('Please enter a link title');
                        return false;
                    }
                    
                    if (!url) {
                        Swal.showValidationMessage('Please enter a URL');
                        return false;
                    }
                    
                    try {
                        new URL(url);
                    } catch (e) {
                        Swal.showValidationMessage('Please enter a valid URL');
                        return false;
                    }
                    
                    return { id, title, url, categoryId: link.category_id };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateLinkAction(result.value);
                }
            });
        }
    })
    .catch(error => {
        debugLog(`Edit link fetch error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Update link AJAX
updateLinkAction = function(data) {
    debugLog(`Updating link id: ${data.id}, title: ${data.title}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'update_link',
        id: data.id,
        title: data.title,
        url: data.url,
        category_id: data.categoryId
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Update link response: ${JSON.stringify(result)}`, 'links');
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
        debugLog(`Update link error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Delete link confirmation
deleteLinkConfirm = function(id) {
    Swal.fire({
        title: 'Delete Link',
        text: 'Are you sure you want to delete this link?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteLinkAction(id);
        }
    });
};

// Delete link AJAX
deleteLinkAction = function(id) {
    debugLog(`Deleting link id: ${id}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'delete_link',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete link response: ${JSON.stringify(result)}`, 'links');
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
        debugLog(`Delete link error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};

// Delete category confirmation
deleteCategoryConfirm = function(id) {
    Swal.fire({
        title: 'Delete Category',
        text: 'Are you sure you want to delete this category and all its links?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCategoryAction(id);
        }
    });
};

// Delete category AJAX
deleteCategoryAction = function(id) {
    debugLog(`Deleting link category id: ${id}`, 'links');
    axios.post('links.php', new URLSearchParams({
        action: 'delete_category',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete link category response: ${JSON.stringify(result)}`, 'links');
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
        debugLog(`Delete link category error: ${error}`, 'links');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
};
