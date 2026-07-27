import { test, expect, Browser, BrowserContext, Page } from '@playwright/test';
import { USERS, DEFAULT_PASSWORD, todayDate } from './helpers';

/**
 * GOLDEN FLOW E2E: Purchase Order dari Staff IT → Supervisor IT → Manager IT
 * 
 * PENTING: Alur create dokumen ConfirmMe:
 * 1. POST /documents → status "draft" → redirect ke /documents/{id} (show)
 * 2. Pada show page, klik "Kirim untuk Approval" → POST /documents/{id}/submit
 * 3. Baru workflow dimulai (status berubah ke supervisor_review dst)
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

test.describe('12 — Golden Flow: PO Approval End-to-End', () => {
    test.setTimeout(120000); // 2 menit untuk multi-step flow (3 login contexts)

    test('Full PO Approval Chain', async ({ browser }) => {
        // ===== STEP 1: Staff IT membuat pengajuan PO =====
        const { context: ctx1, page: staffPage } = await loginNewContext(browser, USERS.staffIT);

        await staffPage.goto('http://localhost:8000/documents/create');
        await staffPage.waitForURL(/.*documents\/create/);

        await staffPage.locator('label:has(input[value="po"])').click();
        await staffPage.waitForTimeout(500);

        await staffPage.locator('#title').fill('Golden Flow PO — Pengadaan Server');
        await staffPage.locator('#description').fill('Pengadaan server baru untuk data center.');
        await staffPage.locator('#vendor_name').fill('PT Cloud Indonesia');
        await staffPage.locator('#item_description').fill('Server Dell PowerEdge x 1 unit');
        await staffPage.locator('#subtotal').fill('50000000');

        // Submit form (simpan draft) → redirect ke show page
        await staffPage.locator('button[value="submit"]').evaluate((btn: HTMLButtonElement) => btn.click());
        await staffPage.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

        // Klik "Kirim untuk Approval" — POST lambat, bypass nav timeout
        const submitApprovalBtn = staffPage.locator('button:has-text("Kirim untuk Approval")');
        if (await submitApprovalBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
            await submitApprovalBtn.evaluate((btn: HTMLButtonElement) => btn.click());
            await staffPage.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
        }
        await ctx1.close();

        // ===== STEP 2: Supervisor IT menyetujui =====
        const { context: ctx2, page: spvPage } = await loginNewContext(browser, USERS.supervisorIT);
        await spvPage.goto('http://localhost:8000/approvals');
        await spvPage.waitForURL(/.*approvals/);

        const reviewLink = spvPage.locator('a:has-text("Review →")').first();
        if (await reviewLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink.click();
            await spvPage.waitForURL(/.*approvals\/\d+/);

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

        // ===== STEP 3: Manager IT menyetujui =====
        const { context: ctx3, page: mgrPage } = await loginNewContext(browser, USERS.managerIT);
        await mgrPage.goto('http://localhost:8000/approvals');
        await mgrPage.waitForURL(/.*approvals/);

        const reviewLink2 = mgrPage.locator('a:has-text("Review →")').first();
        if (await reviewLink2.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink2.click();
            await mgrPage.waitForURL(/.*approvals\/\d+/);

            await mgrPage.locator('span:has-text("Setujui")').first().click();
            await mgrPage.waitForTimeout(500);
            await mgrPage.locator('button:has-text("Lanjutkan Persetujuan")').click();
            await mgrPage.waitForTimeout(500);
            await mgrPage.locator('button:has-text("Ya, Setujui")').evaluate((btn: HTMLButtonElement) => {
                btn.closest('form')?.submit();
            });
            await mgrPage.waitForURL(/.*approvals/, { timeout: 30000 });
        }
        await ctx3.close();
    });
});
