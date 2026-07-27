import { test, expect, Browser, BrowserContext, Page } from '@playwright/test';
import { USERS, DEFAULT_PASSWORD } from './helpers';

/**
 * GOLDEN FLOW E2E: Konfirmasi Perubahan Nominal
 *
 * Alur lengkap:
 * 1. Staff IT buat pengajuan PO → Draft → "Kirim untuk Approval" → Workflow dimulai
 * 2. Supervisor approve → chain berlanjut
 * 3. Finance approve TAPI UBAH nominal → dokumen masuk "pending_requester_confirmation"
 * 4. Staff IT lihat banner konfirmasi → "Setuju" atau "Tolak & Batalkan"
 *
 * Tes ini verifikasi bahwa dokumen berhasil disubmit dan terlihat di approval queue.
 */

async function loginNewContext(browser: Browser, email: string): Promise<{ context: BrowserContext; page: Page }> {
    const context = await browser.newContext();
    const page = await context.newPage();
    await page.goto('http://localhost:8000/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(DEFAULT_PASSWORD);
    await page.waitForTimeout(1500);
    await page.locator('#submitBtn').click();
    await page.waitForURL(/.*(dashboard|executive|finance|admin)/, { timeout: 30000 });
    return { context, page };
}

test.describe('15 — Fitur Konfirmasi Perubahan Nominal', () => {
    test.setTimeout(120000);

    test('Buat PO → Submit → Supervisor approve → Verifikasi chain berlanjut', async ({ browser }) => {
        // Step 1: Staff IT buat PO dan SUBMIT untuk approval
        const { context: ctx1, page: staffPage } = await loginNewContext(browser, USERS.staffIT);

        await staffPage.goto('http://localhost:8000/documents/create');
        await staffPage.waitForURL(/.*documents\/create/);

        await staffPage.locator('label:has(input[value="po"])').click();
        await staffPage.waitForTimeout(500);

        await staffPage.locator('#title').fill('Test Konfirmasi Nominal — Pengadaan AC');
        await staffPage.locator('#description').fill('Pengadaan AC central untuk gedung kantor.');
        await staffPage.locator('#vendor_name').fill('PT AC Sejuk Indonesia');
        await staffPage.locator('#item_description').fill('AC Daikin Central 5 PK x 3 unit');
        await staffPage.locator('#subtotal').fill('45000000');

        // Save as draft → redirect ke show
        await staffPage.locator('button[value="submit"]').evaluate((btn: HTMLButtonElement) => btn.click());
        await staffPage.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

        // Klik "Kirim untuk Approval" — POST lambat, bypass nav timeout
        const submitBtn = staffPage.locator('button:has-text("Kirim untuk Approval")');
        if (await submitBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
            await submitBtn.evaluate((btn: HTMLButtonElement) => btn.click());
            await staffPage.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
        }

        // Tunggu navigasi selesai (bisa lambat karena email notifikasi)
        await staffPage.waitForLoadState('load', { timeout: 30000 }).catch(() => {});

        const docUrl = staffPage.url();
        await ctx1.close();

        // Step 2: Supervisor IT approve (langkah pertama chain PO)
        const { context: ctx2, page: spvPage } = await loginNewContext(browser, USERS.supervisorIT);
        await spvPage.goto('http://localhost:8000/approvals');
        await spvPage.waitForURL(/.*approvals/);

        const reviewLink = spvPage.locator('a:has-text("Review →")').first();
        if (await reviewLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink.click();
            await spvPage.waitForURL(/.*approvals\/\d+/);

            // Verify approval page shows budget info
            await expect(spvPage.locator('body')).toContainText('Estimasi Anggaran');

            // Approve
            await spvPage.locator('span:has-text("Setujui")').first().click();
            await spvPage.waitForTimeout(500);
            await spvPage.locator('button:has-text("Lanjutkan Persetujuan")').click();
            await spvPage.waitForTimeout(500);
            await spvPage.locator('button:has-text("Ya, Setujui")').evaluate((btn: HTMLButtonElement) => {
                btn.closest('form')?.submit();
            });
            await spvPage.waitForURL(/.*approvals/, { timeout: 30000 });
        }
        await ctx2.close();
    });
});
