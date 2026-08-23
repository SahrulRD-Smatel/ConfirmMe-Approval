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
    popup: 'cm-swal-popup',
    title: 'cm-swal-title',
    htmlContainer: 'cm-swal-message',
    confirmButton: 'cm-swal-confirm',
    cancelButton: 'cm-swal-cancel',
};

const guardianIcon = `
    <svg class="cm-guardian-svg" viewBox="0 0 96 96" role="img" aria-label="Guardian">
        <g class="cm-guardian-float">
            <path class="cm-guardian-shadow" d="M18 82c8-5 52-5 60 0-7 7-53 7-60 0Z" />
            <path class="cm-guardian-horn" d="M28 32 16 18l4 23 11-5Zm40 0 12-14-4 23-11-5Z" />
            <path class="cm-guardian-body" d="M25 37c0-11 10-19 23-19s23 8 23 19v25c0 10-10 17-23 17s-23-7-23-17V37Z" />
            <path class="cm-guardian-face" d="M29 43c0-8 8-14 19-14s19 6 19 14v14c0 8-8 14-19 14s-19-6-19-14V43Z" />
            <ellipse class="cm-guardian-eye" cx="40" cy="48" rx="4" ry="6" />
            <ellipse class="cm-guardian-eye" cx="56" cy="48" rx="4" ry="6" />
            <circle class="cm-guardian-glint" cx="41" cy="46" r="1.5" />
            <circle class="cm-guardian-glint" cx="57" cy="46" r="1.5" />
            <path class="cm-guardian-mouth" d="M41 59c4 3 10 3 14 0" />
            <path class="cm-guardian-shield" d="M48 64 39 59v9c0 7 5 11 9 13 4-2 9-6 9-13v-9l-9 5Z" />
            <path class="cm-guardian-shield-mark" d="m44 67 3 3 6-7" />
        </g>
    </svg>
`;

const themeStyles = `
    #${STYLE_ID} + .swal2-container .cm-swal-popup,
    .cm-swal-popup {
        border: 1px solid rgba(130, 24, 26, .12) !important;
        border-radius: 28px !important;
        padding: 1.65rem !important;
        background: linear-gradient(145deg, #ffffff 0%, #fffafa 100%) !important;
        box-shadow: 0 24px 70px rgba(45, 12, 13, .18), 0 4px 14px rgba(45, 12, 13, .08) !important;
    }
    .dark .cm-swal-popup {
        border-color: rgba(248, 113, 113, .18) !important;
        background: linear-gradient(145deg, #1f2937 0%, #111827 100%) !important;
    }
    .cm-swal-popup-enter { animation: cmSwalIn .38s cubic-bezier(.22, 1, .36, 1) both !important; }
    .cm-swal-popup-exit { animation: cmSwalOut .2s ease-in both !important; }
    .cm-swal-popup .swal2-icon {
        width: 88px !important;
        height: 88px !important;
        margin: .25rem auto 1rem !important;
        border: 0 !important;
    }
    .cm-swal-popup .swal2-icon.swal2-warning {
        color: transparent !important;
        border-color: transparent !important;
    }
    .cm-swal-popup .cm-swal-icon {
        width: 92px !important;
        height: 92px !important;
        margin: .15rem auto 1rem !important;
        border-radius: 30px !important;
        background: linear-gradient(145deg, #fff2e5 0%, #ffe4e6 100%) !important;
        box-shadow: inset 0 0 0 1px rgba(130, 24, 26, .08), 0 12px 24px rgba(130, 24, 26, .12) !important;
    }
    .cm-guardian-svg { width: 100%; height: 100%; overflow: visible; }
    .cm-guardian-float { transform-origin: 48px 52px; animation: cmGuardianFloat 2.8s ease-in-out infinite; }
    .cm-guardian-eye { transform-box: fill-box; transform-origin: center; animation: cmGuardianBlink 4.6s ease-in-out infinite; }
    .cm-guardian-shadow { fill: rgba(130, 24, 26, .12); }
    .cm-guardian-horn { fill: #f59e0b; stroke: #b45309; stroke-width: 1.5; stroke-linejoin: round; }
    .cm-guardian-body { fill: #82181a; stroke: #5f1012; stroke-width: 2; }
    .cm-guardian-face { fill: #fff7ed; stroke: #fecaca; stroke-width: 1.5; }
    .cm-guardian-eye { fill: #1f2937; }
    .cm-guardian-glint { fill: #ffffff; }
    .cm-guardian-mouth { fill: none; stroke: #82181a; stroke-width: 2; stroke-linecap: round; }
    .cm-guardian-shield { fill: #f59e0b; stroke: #b45309; stroke-width: 1.2; }
    .cm-guardian-shield-mark { fill: none; stroke: #ffffff; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .cm-swal-title { margin-top: .25rem !important; color: #1f2937 !important; font-size: 1.45rem !important; font-weight: 800 !important; letter-spacing: -.025em !important; }
    .dark .cm-swal-title { color: #f9fafb !important; }
    .cm-swal-message { color: #6b7280 !important; font-size: .95rem !important; line-height: 1.55 !important; }
    .dark .cm-swal-message { color: #9ca3af !important; }
    .cm-swal-popup .swal2-actions { gap: 14px !important; margin-top: 1.25rem !important; }
    .cm-swal-confirm, .cm-swal-cancel { border-radius: 12px !important; padding: .72rem 1.4rem !important; font-size: .875rem !important; font-weight: 800 !important; transition: transform .2s ease, box-shadow .2s ease, filter .2s ease !important; }
    .cm-swal-confirm { border: 0 !important; color: #ffffff !important; background: #82181a !important; box-shadow: 0 8px 16px rgba(130, 24, 26, .22) !important; }
    .cm-swal-cancel { border: 1px solid #d1d5db !important; color: #374151 !important; background: #ffffff !important; box-shadow: 0 2px 4px rgba(0,0,0, .05) !important; }
    .dark .cm-swal-cancel { border-color: #4b5563 !important; color: #e5e7eb !important; background: #374151 !important; }
    .cm-swal-confirm:hover, .cm-swal-cancel:hover { transform: translateY(-2px); filter: brightness(1.04); }
    .cm-swal-cancel:hover { background: #f3f4f6 !important; }
    .cm-swal-confirm:focus-visible, .cm-swal-cancel:focus-visible { outline: 3px solid rgba(130, 24, 26, .25) !important; outline-offset: 2px; }
    @keyframes cmSwalIn { from { opacity: 0; transform: translateY(12px) scale(.94); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @keyframes cmSwalOut { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(.97); } }
    @keyframes cmGuardianFloat { 0%, 100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-4px) rotate(1deg); } }
    @keyframes cmGuardianBlink { 0%, 43%, 47%, 100% { transform: scaleY(1); } 45% { transform: scaleY(.12); } }
    @media (prefers-reduced-motion: reduce) { .cm-swal-popup-enter, .cm-swal-popup-exit, .cm-guardian-float, .cm-guardian-eye { animation: none !important; } .cm-swal-confirm, .cm-swal-cancel { transition: none !important; } }
`;

const ensureSweetAlertStyles = () => {
    if (document.getElementById(STYLE_ID)) return;

    const style = document.createElement('style');
    style.id = STYLE_ID;
    style.textContent = themeStyles;
    document.head.appendChild(style);
};

window.Alpine = Alpine;
window.Swal = Swal;

// Global Alert Helper
window.showAlert = (message, type = 'error') => {
    ensureSweetAlertStyles();

    return Swal.fire({
        text: message,
        icon: type,
        confirmButtonColor: BRAND_COLOR,
        buttonsStyling: false,
        showClass: sweetAlertMotion,
        hideClass: sweetAlertExitMotion,
        customClass: sweetAlertClasses,
    });
};

// Global Confirm Helper
window.confirmAction = (event, message) => {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;

    ensureSweetAlertStyles();

    return Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        iconHtml: guardianIcon,
        showCancelButton: true,
        focusCancel: true,
        confirmButtonColor: BRAND_COLOR,
        cancelButtonColor: MUTED_COLOR,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        showClass: sweetAlertMotion,
        hideClass: sweetAlertExitMotion,
        customClass: {
            ...sweetAlertClasses,
            icon: 'cm-swal-icon',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }

        return result;
    });
};

Alpine.start();