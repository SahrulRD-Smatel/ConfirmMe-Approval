/**
 * Currency Input Formatter
 * 
 * Auto-formats number inputs with Indonesian thousand separators (titik).
 * 
 * Usage: Add class "currency-input" to any input element.
 * The input will display "10.000.000" but submit the raw numeric value.
 * 
 * Works by:
 * 1. Changing type="number" to type="text" (number inputs can't show dots)
 * 2. Creating a hidden input with the original name for form submission
 * 3. Formatting the display value on input/focus/blur
 */
document.addEventListener('DOMContentLoaded', function () {
    initCurrencyInputs();
});

function initCurrencyInputs() {
    // Find all inputs that should be formatted
    // Target: inputs with class "currency-input" OR type="number" inputs inside a .relative with Rp label
    const currencyInputs = document.querySelectorAll('.currency-input');
    const rpInputs = document.querySelectorAll('.relative > input[type="number"]');

    currencyInputs.forEach(setupCurrencyInput);

    // Auto-detect Rp-prefixed number inputs
    rpInputs.forEach(function (input) {
        const parent = input.parentElement;
        if (parent && parent.querySelector('span') &&
            parent.querySelector('span').textContent.trim() === 'Rp') {
            setupCurrencyInput(input);
        }
    });
}

function setupCurrencyInput(input) {
    // Skip if already initialized
    if (input.dataset.currencyInit) return;
    input.dataset.currencyInit = 'true';

    const originalName = input.name;
    const originalValue = input.value;

    // Create hidden input to hold raw numeric value for form submission
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = originalName;
    hidden.value = originalValue ? String(originalValue).split('.')[0].split(',')[0].replace(/[^\d]/g, '') : '';

    // Modify visible input
    input.type = 'text';
    input.name = ''; // Remove name so it doesn't submit
    input.removeAttribute('min');
    input.removeAttribute('max');
    input.removeAttribute('step');
    input.inputMode = 'numeric';

    // Insert hidden input after the visible one
    input.parentNode.insertBefore(hidden, input.nextSibling);

    // Format initial value
    if (originalValue) {
        input.value = formatRupiah(originalValue);
    }

    // Handle input - format as user types
    input.addEventListener('input', function (e) {
        const cursorPos = this.selectionStart;
        const prevLength = this.value.length;

        // Strip everything except digits
        const raw = this.value.replace(/[^\d]/g, '');
        hidden.value = raw;

        // Format with dots
        this.value = formatRupiah(raw);

        // Restore cursor position intelligently
        const newLength = this.value.length;
        const diff = newLength - prevLength;
        const newPos = Math.max(0, cursorPos + diff);
        this.setSelectionRange(newPos, newPos);

        // Trigger change event for Alpine.js / other listeners
        this.dispatchEvent(new Event('change', { bubbles: true }));

        // Also dispatch a custom event with the raw value
        this.dispatchEvent(new CustomEvent('currency-change', {
            detail: { value: parseInt(raw) || 0 },
            bubbles: true
        }));
    });

    // On focus, select all for easy replacement
    input.addEventListener('focus', function () {
        setTimeout(() => this.select(), 0);
    });

    // On blur, ensure formatting is clean
    input.addEventListener('blur', function () {
        const raw = this.value.replace(/[^\d]/g, '');
        hidden.value = raw;
        this.value = raw ? formatRupiah(raw) : '';
    });

    // Handle paste
    input.addEventListener('paste', function (e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const raw = paste.replace(/[^\d]/g, '');
        hidden.value = raw;
        this.value = formatRupiah(raw);
    });

    // Prevent non-numeric keypress (except navigation keys)
    input.addEventListener('keypress', function (e) {
        if (!/[\d]/.test(e.key) && e.key !== 'Enter') {
            e.preventDefault();
        }
    });
}

/**
 * Format a number string with Indonesian thousand separator (titik/dot)
 * Example: "10000000" → "10.000.000"
 */
function formatRupiah(value) {
    // Take only integer part (handles "100000000.00" from PHP decimal cast)
    const intPart = String(value).split('.')[0].split(',')[0];
    const num = intPart.replace(/[^\d]/g, '');
    if (!num) return '';
    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Parse formatted rupiah string back to number
 * Example: "10.000.000" → 10000000
 */
function parseRupiah(formatted) {
    return parseInt(String(formatted).replace(/[^\d]/g, '')) || 0;
}

// Export for use in inline scripts
window.formatRupiah = formatRupiah;
window.parseRupiah = parseRupiah;
window.initCurrencyInputs = initCurrencyInputs;
