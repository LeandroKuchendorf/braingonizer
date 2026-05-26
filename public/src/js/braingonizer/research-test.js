/**
 * Braingonizer Research Test Module - Frontend JavaScript
 * Handles all client-side logic for individual research page
 */

// Upload file function
uploadFile = function() {
    Swal.fire({
        title: "Upload File",
        text: "File upload functionality will be implemented soon.",
        icon: "info",
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim(),
        confirmButtonText: "OK"
    });
};

// Delete file function
deleteFile = function(fileName) {
    Swal.fire({
        title: "Delete File",
        text: `Are you sure you want to delete ${fileName}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
        cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-info').trim(),
        confirmButtonText: "Yes, delete"
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Deleted!",
                text: "File has been deleted.",
                icon: "success"
            });
        }
    });
};

// Delete section function
deleteSection = function(sectionId) {
    Swal.fire({
    title: "Delete Section",
    text: "Are you sure you want to delete this section?",
    icon: "error",
    showCancelButton: true,
    confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-info').trim(),
    confirmButtonText: "Yes, delete"
    }).then((result) => {
    if (result.isConfirmed) {
        Swal.fire({
        title: "Deleted!",
        text: "Your section has been deleted.",
        icon: "success"
        });
    }
    });
};

// Delete research function
deleteResearch = function() {
    Swal.fire({
    title: "Delete Research",
    text: "Are you sure you want to delete this entire research? This action cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-danger').trim(),
    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--color-info').trim(),
    confirmButtonText: "Yes, delete research"
    }).then((result) => {
    if (result.isConfirmed) {
        Swal.fire({
        title: "Deleted!",
        text: "Your research has been deleted.",
        icon: "success"
        }).then(() => {
            window.location.href = "researches.php";
        });
    }
    });
};

// Edit research function (placeholder)
editResearch = function() {
    Swal.fire({
        icon: 'info',
        title: 'Edit Mode',
        text: 'Edit functionality will be implemented in the next phase',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    });
};
