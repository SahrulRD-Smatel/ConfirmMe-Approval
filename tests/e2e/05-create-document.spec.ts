import { test, expect } from '@playwright/test';
import { login, USERS, todayDate } from './helpers';

/**
 * Helper: Buat dokumen dan SUBMIT untuk approval.
 * Alur: Create (draft) → Show → Klik "Kirim untuk Approval" → Workflow dimulai
 */
async function createAndSubmitDocument(page: import('@playwright/test').Page, type: string, fields: Record<string, string>) {
    await page.goto('/documents/create');
    await page.waitForURL(/.*documents\/create/);

    // Pilih tipe approval
    if (type !== 'only') {
        await page.locator(`label:has(input[value="${type}"])`).click();
        await page.waitForTimeout(500);
    }

    // Isi semua field
    for (const [selector, value] of Object.entries(fields)) {
        if (selector.startsWith('select:')) {
            await page.locator(selector.replace('select:', '')).selectOption(value);
        } else {
            await page.locator(selector).fill(value);
        }
    }

    // Submit form (simpan sebagai Draft → redirect ke show page)
    await page.locator('button[value="submit"]').evaluate((btn: HTMLButtonElement) => btn.click());
    await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

    // Klik "Kirim untuk Approval" di show page — POST lambat (kirim email notifikasi)
    // Gunakan evaluate(btn.click()) untuk bypass Playwright navigation timeout
    const submitBtn = page.locator('button:has-text("Kirim untuk Approval")');
    if (await submitBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
        await submitBtn.evaluate((btn: HTMLButtonElement) => btn.click());
        await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
    }
}

test.describe('05 — Buat Pengajuan Dokumen (Approval Only)', () => {

    test('Staff IT: Form Buat Pengajuan — Approval Only (default)', async ({ page }) => {
        await login(page, USERS.staffIT);
        await createAndSubmitDocument(page, 'only', {
            '#title': 'Memo Internal Pengujian Otomatis',
            '#description': 'Ini adalah pengajuan memo internal via Playwright test.',
        });
        // Setelah submit, tunggu navigasi selesai (bisa lambat karena email notifikasi)
        await page.waitForLoadState('load', { timeout: 30000 }).catch(() => {});
    });
});

test.describe('05 — Buat Pengajuan Dokumen (Approval to PO)', () => {

    test('Staff IT: Form Buat Pengajuan — Approval to PO', async ({ page }) => {
        await login(page, USERS.staffIT);
        await createAndSubmitDocument(page, 'po', {
            '#title': 'Pengadaan Laptop Tim IT — Test PW',
            '#description': 'Dibutuhkan 2 unit laptop baru untuk developer.',
            '#vendor_name': 'PT Komputer Indonesia',
            '#item_description': 'Laptop Lenovo ThinkPad x 2 unit',
            '#subtotal': '30000000',
        });
    });
});

test.describe('05 — Buat Pengajuan Dokumen (Approval to Invoice)', () => {

    test('Staff IT: Form Buat Pengajuan — Approval to Invoice', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/documents/create');
        await page.waitForURL(/.*documents\/create/);

        const invoiceLabel = page.locator('label:has(input[value="invoice"])');
        if (await invoiceLabel.count() === 0) {
            test.skip();
            return;
        }

        await invoiceLabel.click();
        await page.waitForTimeout(500);

        await page.locator('#title').fill('Reimbursement Tiket Pesawat — Test PW');
        await page.locator('#description').fill('Penggantian biaya tiket perjalanan dinas.');
        await page.locator('#invoice_number').fill('INV-TEST-001');
        await page.locator('#invoice_date').fill(todayDate());
        await page.locator('#vendor_name_invoice').fill('PT Garuda Indonesia');
        await page.locator('#invoice_amount').fill('2500000');
        await page.locator('#payment_due_date').fill(todayDate());

        await page.locator('button[value="submit"]').evaluate((btn: HTMLButtonElement) => btn.click());
        await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

        // Submit untuk approval (jika ada tombolnya)
        const submitBtn = page.locator('button:has-text("Kirim untuk Approval")');
        if (await submitBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
            await submitBtn.evaluate((btn: HTMLButtonElement) => btn.closest('form')?.submit());
            await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
        }
    });
});

test.describe('05 — Buat Pengajuan Dokumen (Employee Loan)', () => {

    test('Staff IT: Form Buat Pengajuan — Employee Loan', async ({ page }) => {
        await login(page, USERS.staffIT);
        await createAndSubmitDocument(page, 'loan', {
            '#title': 'Pinjaman Biaya Rumah Sakit — Test PW',
            '#description': 'Pinjaman darurat untuk biaya operasi keluarga.',
            'select:#loan_type': 'darurat',
            '#loan_amount': '10000000',
            'select:#installment_count': '6',
            '#loan_reason': 'Biaya operasi keluarga yang mendesak.',
        });
    });
});
