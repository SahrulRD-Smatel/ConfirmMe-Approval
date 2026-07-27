import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('08 — Executive Module (Presiden Direktur)', () => {

    test('Presiden Direktur: Akses Executive Dashboard', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        // Presiden Direktur di-redirect ke /executive (bukan /dashboard biasa)
        await page.goto('/executive');
        await page.waitForURL(/.*executive/);
    });

    test('Presiden Direktur: Akses Approval Queue Executive', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        await page.goto('/executive/approvals');
        await page.waitForURL(/.*executive\/approvals/);
    });

    test('Presiden Direktur: Akses Company Insights', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        await page.goto('/executive/insights');
        await page.waitForURL(/.*executive\/insights/);
    });

    test('Presiden Direktur: Akses Audit & History', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        await page.goto('/executive/audit');
        await page.waitForURL(/.*executive\/audit/);
    });

    test('Presiden Direktur: Akses Approval Cuti', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        await page.goto('/leave-approvals');
        await page.waitForURL(/.*leave-approvals/);
    });
});
