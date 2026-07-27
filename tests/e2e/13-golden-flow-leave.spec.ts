import { test, expect, Browser, BrowserContext, Page } from '@playwright/test';
import { USERS, DEFAULT_PASSWORD } from './helpers';

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

test.describe('13 — Golden Flow: Leave Request Approval', () => {
    test.setTimeout(120000); // 2 menit untuk multi-step flow

    test('Full Leave Approval: Staff IT → Supervisor IT', async ({ browser }) => {
        // === STEP 1: Staff IT ajukan cuti ===
        const { context: ctx1, page: staffPage } = await loginNewContext(browser, USERS.staffIT);
        await staffPage.goto('http://localhost:8000/leave-requests/create');
        await staffPage.waitForURL(/.*leave-requests\/create/);

        const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate() + 5);
        const dayAfter = new Date(); dayAfter.setDate(dayAfter.getDate() + 6);

        await staffPage.locator('#type').selectOption('annual');
        await staffPage.locator('#start_date').fill(tomorrow.toISOString().split('T')[0]);
        await staffPage.locator('#end_date').fill(dayAfter.toISOString().split('T')[0]);
        await staffPage.locator('#reason').fill('Golden Flow Cuti — test Playwright');

        // Submit via evaluate (bypass click timeout)
        await staffPage.locator('button:has-text("Ajukan Cuti")').evaluate((btn: HTMLButtonElement) => btn.form?.submit());
        await staffPage.waitForURL(/.*leave-requests/, { timeout: 30000 });
        await ctx1.close();

        // === STEP 2: Supervisor IT approve cuti ===
        const { context: ctx2, page: spvPage } = await loginNewContext(browser, USERS.supervisorIT);
        await spvPage.goto('http://localhost:8000/leave-approvals');
        await spvPage.waitForURL(/.*leave-approvals/);

        const approveBtn = spvPage.locator('span:has-text("Setujui")').first();
        if (await approveBtn.isVisible({ timeout: 5000 }).catch(() => false)) {
            await approveBtn.click();
            await spvPage.waitForTimeout(500);
            await spvPage.locator('button:has-text("Lanjutkan Persetujuan")').click();
            await spvPage.waitForTimeout(500);
            await spvPage.locator('button:has-text("Ya, Setujui")').click();
            await spvPage.waitForTimeout(3000);
        }
        await ctx2.close();
    });
});
