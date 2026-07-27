/**
 * Global Form Loading Spinner
 * 
 * Automatically prevents double-submit on ALL forms by:
 * 1. Disabling the submit button after first click
 * 2. Showing a spinning animation
 * 3. Changing button text to "Memproses..."
 * 
 * To exclude a form, add the class "no-loading" to the form element.
 * To customize the loading text, add data-loading-text="Custom text..." to the submit button.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        const form = e.target;

        // Skip forms with "no-loading" class
        if (form.classList.contains('no-loading')) return;

        // Skip if already submitting (prevent double submit)
        if (form.dataset.submitting === 'true') {
            e.preventDefault();
            return;
        }

        // Mark form as submitting
        form.dataset.submitting = 'true';

        // Find all submit buttons in the form
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

        submitButtons.forEach(function (btn) {
            // Get custom loading text or use default
            const loadingText = btn.dataset.loadingText || 'Memproses...';

            // Disable button
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');

            // Store original content
            btn.dataset.originalContent = btn.innerHTML;

            // Replace content with spinner + loading text
            btn.innerHTML = `
                <span class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    ${loadingText}
                </span>
            `;
        });

        // Also disable other buttons (like "Batal") inside the form
        const otherButtons = form.querySelectorAll('button:not([type="submit"])');
        otherButtons.forEach(function (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });

        // Safety: re-enable after 15 seconds in case of network issues
        setTimeout(function () {
            form.dataset.submitting = 'false';
            submitButtons.forEach(function (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                if (btn.dataset.originalContent) {
                    btn.innerHTML = btn.dataset.originalContent;
                }
            });
            otherButtons.forEach(function (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }, 15000);
    });
});
