import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

// Global Alert Helper
window.showAlert = (message, type = 'error') => {
    Swal.fire({
        text: message,
        icon: type,
        confirmButtonColor: '#82181a',
        customClass: {
            confirmButton: 'rounded-lg px-4 py-2 font-semibold',
        }
    });
};

// Global Confirm Helper
window.confirmAction = (event, message) => {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    
    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#82181a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'rounded-lg px-4 py-2 font-semibold mr-2',
            cancelButton: 'rounded-lg px-4 py-2 font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

Alpine.start();
