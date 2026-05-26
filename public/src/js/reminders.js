/**
 * Reminders Module - Frontend JavaScript
 * Handles all client-side logic for reminders management
 */

// Initialize tooltips on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Add reminder function
addReminderNew = function() {
    Swal.fire({
        title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Reminder',
        html: `
            <input id="reminder-title" class="swal2-input" placeholder="Reminder Title" maxlength="255">
            <textarea id="reminder-desc" class="swal2-textarea" placeholder="Description" rows="4"></textarea>
            <select id="reminder-color" class="swal2-select">
                <option value="">Select Color</option>
                <option value="yellow">Yellow</option>
                <option value="blue">Blue</option>
                <option value="red">Red</option>
                <option value="green">Green</option>
                <option value="lightblue">Light Blue</option>
            </select>
            <select id="reminder-tags" class="swal2-select" multiple>
                ${allTags.map(tag => `<option value="${tag.name}">${tag.name}</option>`).join('')}
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
        customClass: {
            popup: 'reminder-modal-dark',
            title: 'swal2-title-dark',
            htmlContainer: 'swal2-html-dark'
        },
        didOpen: () => {
            // Initialize Select2 for tags
            $('#reminder-tags').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select or create tags (max 3)',
                tags: true,
                maximumSelectionLength: 3,
                tokenSeparators: [','],
                dropdownParent: $('.swal2-container')
            });
            
            // Force dark theme on Select2 elements
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
            const title = document.getElementById('reminder-title').value.trim();
            const description = document.getElementById('reminder-desc').value.trim();
            const color = document.getElementById('reminder-color').value;
            const tags = $('#reminder-tags').val() || [];
            
            if (!title) {
                Swal.showValidationMessage('Please enter a title');
                return false;
            }
            
            if (!color) {
                Swal.showValidationMessage('Please select a color');
                return false;
            }
            
            if (tags.length > 3) {
                Swal.showValidationMessage('Maximum 3 tags allowed');
                return false;
            }
            
            return { title, description, color, tags };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createReminder(result.value);
        }
    });
}

// View reminder function
viewReminder = function(id) {
    debugLog(`Viewing reminder id: ${id}`, 'reminders');
    axios.post('reminders.php', new URLSearchParams({
        action: 'get_reminder',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get reminder response: ${JSON.stringify(data)}`, 'reminders');
        if (data.success) {
            const reminder = data.reminder;
            const tagBadges = reminder.tags.map(tag => 
                `<span class="badge bg-${tag.color}">${tag.name}</span>`
            ).join(' ');
            
            Swal.fire({
                title: `<i class="fas fa-bell"></i> ${reminder.title}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- Description Section -->
                        <div class="modal-section" style="margin-bottom: 1.5rem;">
                            <p style="margin-bottom: 0;">${reminder.description}</p>
                        </div>
                        
                        <!-- Footer-like Section: Tags -->
                        ${tagBadges ? `
                        <div class="modal-footer-section" style="border-top: 1px solid #444; padding-top: 1rem; margin-top: 1.5rem; font-size: 0.75em;">
                            <div>
                                <strong>Tags:</strong> ${tagBadges}
                            </div>
                        </div>
                        ` : ''}
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
        debugLog(`View reminder error: ${error}`, 'reminders');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Edit reminder function
editReminder = function(id) {
    debugLog(`Editing reminder id: ${id}`, 'reminders');
    axios.post('reminders.php', new URLSearchParams({
        action: 'get_reminder',
        id: id
    }))
    .then(response => {
        const data = response.data;
        debugLog(`Get reminder for edit response: ${JSON.stringify(data)}`, 'reminders');
        if (data.success) {
            const reminder = data.reminder;
            const tagNames = reminder.tags.map(t => t.name);
            
            Swal.fire({
                title: 'Edit Reminder',
                html: `
                    <input id="reminder-title" class="swal2-input" placeholder="Reminder Title" value="${reminder.title}" maxlength="255">
                    <textarea id="reminder-desc" class="swal2-textarea" placeholder="Description" rows="4">${reminder.description}</textarea>
                    <select id="reminder-color" class="swal2-select">
                        <option value="yellow" ${reminder.color === 'yellow' ? 'selected' : ''}>Yellow</option>
                        <option value="blue" ${reminder.color === 'blue' ? 'selected' : ''}>Blue</option>
                        <option value="red" ${reminder.color === 'red' ? 'selected' : ''}>Red</option>
                        <option value="green" ${reminder.color === 'green' ? 'selected' : ''}>Green</option>
                        <option value="lightblue" ${reminder.color === 'lightblue' ? 'selected' : ''}>Light Blue</option>
                    </select>
                    <select id="reminder-tags" class="swal2-select" multiple>
                        ${allTags.map(tag => `<option value="${tag.name}" ${tagNames.includes(tag.name) ? 'selected' : ''}>${tag.name}</option>`).join('')}
                    </select>
                `,
                background: '#1e1e1e',
                color: '#e0e0e0',
                width: '600px',
                padding: '2em',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-edit"></i> Update',
                cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim(),
                cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                customClass: {
                    popup: 'reminder-modal-dark',
                    title: 'swal2-title-dark',
                    htmlContainer: 'swal2-html-dark'
                },
                didOpen: () => {
                    $('#reminder-tags').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select or create tags (max 3)',
                        tags: true,
                        maximumSelectionLength: 3,
                        tokenSeparators: [','],
                        dropdownParent: $('.swal2-container')
                    });
                    
                    // Force dark theme on Select2 elements
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
                    const title = document.getElementById('reminder-title').value.trim();
                    const description = document.getElementById('reminder-desc').value.trim();
                    const color = document.getElementById('reminder-color').value;
                    const tags = $('#reminder-tags').val() || [];
                    
                    if (!title) {
                        Swal.showValidationMessage('Please enter a title');
                        return false;
                    }
                    
                    if (tags.length > 3) {
                        Swal.showValidationMessage('Maximum 3 tags allowed');
                        return false;
                    }
                    
                    return { id, title, description, color, tags };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateReminder(result.value);
                }
            });
        }
    });
}

// Create reminder AJAX
createReminder = function(data) {
    debugLog(`Creating reminder: ${data.title}`, 'reminders');
    axios.post('reminders.php', new URLSearchParams({
        action: 'create',
        title: data.title,
        description: data.description,
        color: data.color,
        tags: JSON.stringify(data.tags)
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Create reminder response: ${JSON.stringify(result)}`, 'reminders');
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
        debugLog(`Create reminder error: ${error}`, 'reminders');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Update reminder AJAX
updateReminder = function(data) {
    debugLog(`Updating reminder id: ${data.id}, title: ${data.title}`, 'reminders');
    axios.post('reminders.php', new URLSearchParams({
        action: 'update',
        id: data.id,
        title: data.title,
        description: data.description,
        color: data.color,
        tags: JSON.stringify(data.tags)
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Update reminder response: ${JSON.stringify(result)}`, 'reminders');
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
        debugLog(`Update reminder error: ${error}`, 'reminders');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Delete reminder confirmation
deleteReminderConfirm = function(id) {
    Swal.fire({
        title: 'Delete Reminder',
        text: 'Are you sure you want to delete this reminder?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteReminderAction(id);
        }
    });
}

// Delete reminder AJAX
deleteReminderAction = function(id) {
    debugLog(`Deleting reminder id: ${id}`, 'reminders');
    axios.post('reminders.php', new URLSearchParams({
        action: 'delete',
        id: id
    }))
    .then(response => {
        const result = response.data;
        debugLog(`Delete reminder response: ${JSON.stringify(result)}`, 'reminders');
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
        debugLog(`Delete reminder error: ${error}`, 'reminders');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + error.message });
    });
}

// Filter reminders function
filterRemindersNew = function() {
    Swal.fire({
        icon: 'info',
        title: 'Filter Feature',
        text: 'Filtering functionality will be added in a future update',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}
