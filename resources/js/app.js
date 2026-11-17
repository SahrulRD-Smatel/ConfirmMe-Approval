import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';

Alpine.plugin(collapse);

const BRAND_COLOR = '#82181a';
const MUTED_COLOR = '#6b7280';
const STYLE_ID = 'confirmme-swal-theme';

const sweetAlertMotion = {
    popup: 'cm-swal-popup-enter',
};

const sweetAlertExitMotion = {
    popup: 'cm-swal-popup-exit',
};

const sweetAlertClasses = {
    container: 'z-[9999]',
    popup: 'rounded-3xl border border-gray-200/80 bg-white p-6 shadow-soft-xl dark:border-gray-700 dark:bg-gray-900',
    title: 'text-lg font-bold text-gray-900 dark:text-white',
    htmlContainer: 'text-sm text-gray-600 dark:text-gray-400',
    confirmButton: 'inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:shadow-md focus:outline-none focus:ring-4',
    cancelButton: 'inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 transition-all hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700',
    actions: 'mt-6 gap-3',
};

const ensureSweetAlertStyles = () => {
    // Relying on Tailwind classes and customClass configuration
};

window.Alpine = Alpine;
window.Swal = Swal;

// Global Alert Helper
window.showAlert = (message, type = 'error') => {
    ensureSweetAlertStyles();

    return Swal.fire({
        text: message,
        icon: type,
        buttonsStyling: false,
        backdrop: 'rgba(3, 7, 18, 0.75)',
        showClass: sweetAlertMotion,
        hideClass: sweetAlertExitMotion,
        customClass: {
            ...sweetAlertClasses,
            confirmButton: sweetAlertClasses.confirmButton + ' bg-primary-600 hover:bg-primary-700 focus:ring-primary-500/30'
        },
    });
};

// Global Confirm Helper (Universal Enterprise Dialog)
window.confirmAction = (event, message, options = {}) => {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;

    ensureSweetAlertStyles();

    const isDanger = options.isDanger ?? (typeof message === 'string' && /hapus|delete|tolak|reset/i.test(message));
    const confirmButtonClass = isDanger
        ? sweetAlertClasses.confirmButton + ' bg-danger-600 hover:bg-danger-700 focus:ring-danger-500/30'
        : sweetAlertClasses.confirmButton + ' bg-primary-600 hover:bg-primary-700 focus:ring-primary-500/30';

    return Swal.fire({
        title: options.title || (isDanger ? 'Konfirmasi Tindakan' : 'Konfirmasi'),
        html: message,
        icon: options.icon || (isDanger ? 'warning' : 'question'),
        showCancelButton: true,
        focusCancel: true,
        confirmButtonText: options.confirmButtonText || options.confirmText || (isDanger ? 'Ya, Lanjutkan' : 'Ya'),
        cancelButtonText: options.cancelButtonText || options.cancelText || 'Batal',
        buttonsStyling: false,
        backdrop: 'rgba(3, 7, 18, 0.75)',
        showClass: sweetAlertMotion,
        hideClass: sweetAlertExitMotion,
        customClass: {
            ...sweetAlertClasses,
            confirmButton: confirmButtonClass,
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }

        return result;
    });
};

Alpine.start();