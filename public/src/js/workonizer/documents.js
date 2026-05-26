/**
 * Workonizer Documents Module - Frontend JavaScript
 * Handles all client-side logic for work documents management
 */

function addDocument() {
    Swal.fire({
        title: 'New Document',
        html: `
            <input type="text" id="doc_title" class="swal2-input" placeholder="Document Title" required>
            <textarea id="doc_content" class="swal2-textarea" placeholder="Document Content"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        preConfirm: () => {
            const title = document.getElementById('doc_title').value;
            const content = document.getElementById('doc_content').value;
            
            if (!title) {
                Swal.showValidationMessage('Title is required');
                return false;
            }
            
            return { title, content };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('documents.php', new URLSearchParams({
                action: 'create',
                title: result.value.title,
                content: result.value.content
            })).then(response => {
                if (response.data.success) {
                    Swal.fire('Success!', 'Document created successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            }).catch(error => {
                Swal.fire('Error', 'Failed to create document', 'error');
            });
        }
    });
}

function viewDocument(id) {
    axios.post('documents.php', new URLSearchParams({
        action: 'get_document',
        id: id
    })).then(response => {
        if (response.data.success) {
            const doc = response.data.document;
            Swal.fire({
                title: `<i class="fas fa-file-alt"></i> ${doc.title}`,
                html: `
                    <div class="modal-content-wrapper" style="text-align: left;">
                        <!-- Content Section -->
                        <div class="modal-section" style="margin-bottom: 0; max-height: 400px; overflow-y: auto;">
                            ${doc.content}
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
    });
}

function editDocument(id) {
    axios.post('documents.php', new URLSearchParams({
        action: 'get_document',
        id: id
    })).then(response => {
        if (response.data.success) {
            const doc = response.data.document;
            Swal.fire({
                title: 'Edit Document',
                html: `
                    <input type="text" id="doc_title" class="swal2-input" value="${doc.title}" required>
                    <textarea id="doc_content" class="swal2-textarea">${doc.content}</textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const title = document.getElementById('doc_title').value;
                    const content = document.getElementById('doc_content').value;
                    
                    if (!title) {
                        Swal.showValidationMessage('Title is required');
                        return false;
                    }
                    
                    return { title, content };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('documents.php', new URLSearchParams({
                        action: 'update',
                        id: id,
                        title: result.value.title,
                        content: result.value.content
                    })).then(response => {
                        if (response.data.success) {
                            Swal.fire('Success!', 'Document updated successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    });
                }
            });
        }
    });
}

function deleteDocument(id) {
    Swal.fire({
        title: 'Delete Document?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post('documents.php', new URLSearchParams({
                action: 'delete',
                id: id
            })).then(response => {
                if (response.data.success) {
                    Swal.fire('Deleted!', 'Document deleted successfully', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            });
        }
    });
}
