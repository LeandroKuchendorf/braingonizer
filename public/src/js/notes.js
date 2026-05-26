/**
 * Notes Module - Frontend JavaScript
 * Handles all client-side logic for notes management
 */

// Add reminder function
addReminder = function() {
        Swal.fire({
            title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Reminder',
            html: `
                <input id="reminder-title" class="swal2-input" placeholder="Reminder Title">
                <textarea id="reminder-desc" class="swal2-textarea" placeholder="Description"></textarea>
                <select id="reminder-color" class="swal2-select">
                    <option value="">Select Color</option>
                    <option value="yellow">Yellow</option>
                    <option value="blue">Blue</option>
                    <option value="red">Red</option>
                    <option value="lightblue">Light Blue</option>
                </select>
                <input id="reminder-tags" class="swal2-input" placeholder="Tags (comma separated)">
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

    // Filter reminders function
    filterReminders = function() {
        Swal.fire({
            title: 'Filter Reminders',
            html: `
                <div class="text-left">
                    <p><strong>Color:</strong></p>
                    <label><input type="checkbox" value="yellow"> Yellow</label><br>
                    <label><input type="checkbox" value="blue"> Blue</label><br>
                    <label><input type="checkbox" value="red"> Red</label><br>
                    <label><input type="checkbox" value="lightblue"> Light Blue</label><br>
                    
                    <p class="mt-3"><strong>Tags:</strong></p>
                    <label><input type="checkbox" value="tag1"> Tag 1</label><br>
                    <label><input type="checkbox" value="tag2"> Tag 2</label>
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

    // View reminder function
    viewReminder = function() {
        Swal.fire({
            title: '<i class="fas fa-bell"></i> Reminder Details',
            html: `
                <div class="text-left">
                    <h5>Title</h5>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                    
                    <p><strong>Tags:</strong> 
                        <span class="badge bg-primary">Tag 1</span>
                        <span class="badge bg-secondary">Tag 2</span>
                    </p>
                </div>
            `,
            iconHtml: '<i class="fas fa-bell" style="font-size: 1.8em; margin-bottom: 0.5em;"></i>',
            iconColor: '#ffc107',
            background: '#1e1e1e',
            color: '#e0e0e0',
            width: '600px',
            padding: '2em',
            footer: '<small style="color: #888;"><i class="fas fa-calendar"></i> Created: 2024-03-20</small>',
            confirmButtonText: '<i class="fas fa-times"></i> Close',
            confirmButtonColor: '#ffc107',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            customClass: {
                popup: 'reminder-view-modal',
                icon: 'swal2-icon-no-border'
            }
        });
    };

    // Add note function
    addNote = function() {
        Swal.fire({
            title: '<i class="fas fa-plus" style="color: var(--color-primary);"></i> Add New Note',
            html: `
                <input id="note-title" class="swal2-input" placeholder="Note Title" maxlength="255">
                <textarea id="note-content" class="swal2-textarea" placeholder="Note Content" rows="5"></textarea>
                <select id="note-tags" class="swal2-select" multiple>
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
                popup: 'note-modal-dark',
                title: 'swal2-title-dark',
                htmlContainer: 'swal2-html-dark'
            },
            didOpen: () => {
                $('#note-tags').select2({
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
                const title = document.getElementById('note-title').value.trim();
                const content = document.getElementById('note-content').value.trim();
                const tags = $('#note-tags').val() || [];
                
                if (!title) {
                    Swal.showValidationMessage('Please enter a title');
                    return false;
                }
                
                if (!content) {
                    Swal.showValidationMessage('Please enter content');
                    return false;
                }
                
                if (tags.length > 3) {
                    Swal.showValidationMessage('Maximum 3 tags allowed');
                    return false;
                }
                
                return { title, content, tags };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                createNoteAction(result.value);
            }
        });
    };

    // Filter notes function
    filterNotes = function() {
        Swal.fire({
            title: 'Filter Notes',
            html: `
                <div class="text-left">
                    <p><strong>Tags:</strong></p>
                    <label><input type="checkbox" value="tag1"> Tag 1</label><br>
                    <label><input type="checkbox" value="tag2"> Tag 2</label><br>
                    
                    <p class="mt-3"><strong>Date Range:</strong></p>
                    <input type="date" class="swal2-input" placeholder="From Date">
                    <input type="date" class="swal2-input" placeholder="To Date">
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

    // View note function
    viewNote = function(id) {
        axios.post('notes.php', new URLSearchParams({
            type: 'note',
            action: 'get_note',
            id: id
        }))
        .then(response => {
            const data = response.data;
            if (data.success) {
                const note = data.note;
                const tagBadges = note.tags.map(tag => 
                    `<span class="badge bg-${tag.color}">${tag.name}</span>`
                ).join(' ');
                
                Swal.fire({
                    title: `<i class="fas fa-sticky-note"></i> ${note.title}`,
                    html: `
                        <div class="text-left">
                            <p>${note.content}</p>
                            ${tagBadges ? `<p><strong>Tags:</strong> ${tagBadges}</p>` : ''}
                        </div>
                    `,
                    background: '#1e1e1e',
                    color: '#e0e0e0',
                    width: '600px',
                    padding: '2em',
                    footer: `<small style="color: #888;"><i class="fas fa-calendar"></i> Created: ${new Date(note.created_at).toLocaleDateString()} | <i class="fas fa-clock"></i> Updated: ${new Date(note.updated_at).toLocaleDateString()}</small>`,
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
        });
    };

    // Edit note function
    editNote = function(id) {
        axios.post('notes.php', new URLSearchParams({
            type: 'note',
            action: 'get_note',
            id: id
        }))
        .then(response => {
            const data = response.data;
            if (data.success) {
                const note = data.note;
                const tagNames = note.tags.map(t => t.name);
                
                Swal.fire({
                    title: '<i class="fas fa-edit"></i> Edit Note',
                    html: `
                        <input id="edit-note-title" class="swal2-input" placeholder="Note Title" value="${note.title}" maxlength="255">
                        <textarea id="edit-note-content" class="swal2-textarea" placeholder="Note Content" rows="5">${note.content}</textarea>
                        <select id="edit-note-tags" class="swal2-select" multiple>
                            ${allTags.map(tag => `<option value="${tag.name}" ${tagNames.includes(tag.name) ? 'selected' : ''}>${tag.name}</option>`).join('')}
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
                        popup: 'note-modal-dark',
                        title: 'swal2-title-dark',
                        htmlContainer: 'swal2-html-dark'
                    },
                    didOpen: () => {
                        $('#edit-note-tags').select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select or create tags (max 3)',
                            tags: true,
                            maximumSelectionLength: 3,
                            tokenSeparators: [','],
                            dropdownParent: $('.swal2-container')
                        });
                        
                        $('#edit-note-tags').val(tagNames).trigger('change');
                        
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
                        const title = document.getElementById('edit-note-title').value.trim();
                        const content = document.getElementById('edit-note-content').value.trim();
                        const tags = $('#edit-note-tags').val() || [];
                        
                        if (!title) {
                            Swal.showValidationMessage('Please enter a title');
                            return false;
                        }
                        
                        if (!content) {
                            Swal.showValidationMessage('Please enter content');
                            return false;
                        }
                        
                        if (tags.length > 3) {
                            Swal.showValidationMessage('Maximum 3 tags allowed');
                            return false;
                        }
                        
                        return { id, title, content, tags };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateNoteAction(result.value);
                    }
                });
            }
        });
    };

    // Delete reminder function
    deleteReminder = function(reminderId) {
        Swal.fire({
        title: "Delete Reminder",
        text: "Are you sure you want to delete this reminder?",
        icon: "error",
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-info').trim(),
        confirmButtonText: "Yes, delete"
        }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
            title: "Deleted!",
            text: "Your reminder has been deleted.",
            icon: "success"
            });
        }
        });
    };

    // Delete note confirmation
    deleteNoteConfirm = function(id) {
        Swal.fire({
            title: 'Delete Note',
            text: 'Are you sure you want to delete this note?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
            cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-secondary').trim(),
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteNoteAction(id);
            }
        });
    };

    // Create note AJAX
    createNoteAction = function(data) {
        axios.post('notes.php', new URLSearchParams({
            type: 'note',
            action: 'create',
            title: data.title,
            content: data.content,
            tags: JSON.stringify(data.tags)
        }))
        .then(response => {
            const result = response.data;
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
        });
    };

    // Update note AJAX
    updateNoteAction = function(data) {
        axios.post('notes.php', new URLSearchParams({
            type: 'note',
            action: 'update',
            id: data.id,
            title: data.title,
            content: data.content,
            tags: JSON.stringify(data.tags)
        }))
        .then(response => {
            const result = response.data;
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
        });
    };

    // Delete note AJAX
    deleteNoteAction = function(id) {
        axios.post('notes.php', new URLSearchParams({
            type: 'note',
            action: 'delete',
            id: id
        }))
        .then(response => {
            const result = response.data;
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
