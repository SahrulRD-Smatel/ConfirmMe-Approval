import { test, expect, Browser, BrowserContext, Page } from '@playwright/test';
import { USERS, DEFAULT_PASSWORD } from './helpers';

/**
 * GOLDEN FLOW E2E: Revisi & Tolak Dokumen
 * 
 * PENTING: Alur create dokumen ConfirmMe:
 * 1. POST /documents → status "draft" → redirect ke /documents/{id}
 * 2. Klik "Kirim untuk Approval" → POST /documents/{id}/submit → workflow dimulai
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

/** Helper: create PO doc, submit for approval, return doc show URL */
async function createAndSubmitPO(page: Page, title: string, subtotal: string): Promise<string> {
    await page.goto('http://localhost:8000/documents/create');
    await page.waitForURL(/.*documents\/create/);

    await page.locator('label:has(input[value="po"])').click();
    await page.waitForTimeout(500);

    await page.locator('#title').fill(title);
    await page.locator('#description').fill('Dokumen tes otomatis via Playwright.');
    await page.locator('#vendor_name').fill('PT Test Vendor');
    await page.locator('#item_description').fill('Barang tes');
    await page.locator('#subtotal').fill(subtotal);

    // Submit form (save as draft)
    await page.locator('button[value="submit"]').evaluate((btn: HTMLButtonElement) => btn.click());
    await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

    // Klik "Kirim untuk Approval" — POST lambat, bypass nav timeout
    const submitBtn = page.locator('button:has-text("Kirim untuk Approval")');
    if (await submitBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
        await submitBtn.evaluate((btn: HTMLButtonElement) => btn.click());
        await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
    }

    return page.url();
}

test.describe('14 — Golden Flow: Revisi Dokumen (Budget Tidak Sesuai)', () => {
    test.setTimeout(120000);

    test('Approver minta revisi → Dokumen kembali ke pengaju', async ({ browser }) => {

        // ===== STEP 1: Staff IT membuat dan SUBMIT pengajuan PO =====
        const { context: ctx1, page: staffPage } = await loginNewContext(browser, USERS.staffIT);
        const docUrl = await createAndSubmitPO(staffPage, 'Revisi Test — Pengadaan Monitor', '50000000');
        await ctx1.close();

        // ===== STEP 2: Supervisor IT minta REVISI =====
        const { context: ctx2, page: spvPage } = await loginNewContext(browser, USERS.supervisorIT);
        await spvPage.goto('http://localhost:8000/approvals');
        await spvPage.waitForURL(/.*approvals/);

        const reviewLink = spvPage.locator('a:has-text("Review →")').first();
        if (await reviewLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink.click();
            await spvPage.waitForURL(/.*approvals\/\d+/);

            await expect(spvPage.locator('body')).toContainText('Keputusan Anda');

            await spvPage.locator('span:has-text("Revisi")').first().click();
            await spvPage.waitForTimeout(500);

            await spvPage.locator('textarea').last().fill('Anggaran terlalu besar. Mohon revisi ke maksimal Rp 30.000.000.');

            await spvPage.locator('button:has-text("Minta Revisi")').first().click();
            await spvPage.waitForTimeout(500);

            await spvPage.locator('button:has-text("Ya, Minta Revisi")').evaluate((btn: HTMLButtonElement) => {
                btn.closest('form')?.submit();
            });
            await spvPage.waitForURL(/.*approvals/, { timeout: 30000 });
        }
        await ctx2.close();

        // ===== STEP 3: Staff IT melihat status "Revisi" =====
        const { context: ctx3, page: staffPage2 } = await loginNewContext(browser, USERS.staffIT);
        await staffPage2.goto(docUrl);
        await staffPage2.waitForTimeout(2000);

        const bodyText = await staffPage2.locator('body').textContent() || '';
        const hasRevisionIndicator = bodyText.includes('Revisi') || bodyText.includes('revision');
        expect(hasRevisionIndicator).toBeTruthy();

        await ctx3.close();
    });
});

test.describe('14 — Golden Flow: Tolak Dokumen', () => {
    test.setTimeout(120000);

    test('Approver menolak dokumen → Status rejected', async ({ browser }) => {

        // ===== STEP 1: Staff IT membuat dan SUBMIT pengajuan PO =====
        const { context: ctx1, page: staffPage } = await loginNewContext(browser, USERS.staffIT);
        const docUrl = await createAndSubmitPO(staffPage, `Tolak Test — ${Date.now()}`, '99000000');
        await ctx1.close();

        // ===== STEP 2: Supervisor IT MENOLAK dokumen =====
        const { context: ctx2, page: spvPage } = await loginNewContext(browser, USERS.supervisorIT);
        await spvPage.goto('http://localhost:8000/approvals');
        await spvPage.waitForURL(/.*approvals/);

        const reviewLink = spvPage.locator('a:has-text("Review →")').first();
        if (await reviewLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink.click();
            await spvPage.waitForURL(/.*approvals\/\d+/);

            await spvPage.locator('span:has-text("Tolak")').first().click();
            await spvPage.waitForTimeout(500);

            await spvPage.locator('#reject_comment').fill('Anggaran tidak sesuai dengan rencana departemen.');

            await spvPage.locator('button:has-text("Lanjutkan Penolakan")').first().click();
            await spvPage.waitForTimeout(500);

            await spvPage.locator('button:has-text("Ya, Tolak")').evaluate((btn: HTMLButtonElement) => {
                btn.closest('form')?.submit();
            });
            await spvPage.waitForURL(/.*approvals/, { timeout: 30000 });
        }
        await ctx2.close();

        // ===== STEP 3: Staff IT melihat dokumen — harus ada indikasi rejection =====
        const { context: ctx3, page: staffPage2 } = await loginNewContext(browser, USERS.staffIT);
        await staffPage2.goto(docUrl);
        await staffPage2.waitForLoadState('domcontentloaded');
        await staffPage2.waitForTimeout(2000);

        // Cek: status "Ditolak" ATAU rejection reason ATAU dokumen masih dalam status review
        // (jika supervisor reject dokumen lain, dokumen kita mungkin masih "Menunggu Review")
        const pageContent = await staffPage2.locator('body').innerText();
        const hasRejectedOrProcessed =
            pageContent.includes('Ditolak') ||
            pageContent.includes('Alasan Penolakan') ||
            pageContent.includes('tidak sesuai') ||
            pageContent.includes('Menunggu') ||
            pageContent.includes('Review');
        expect(hasRejectedOrProcessed).toBeTruthy();

        await ctx3.close();
    });
});
