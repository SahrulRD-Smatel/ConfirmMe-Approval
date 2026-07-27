import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('11 — Role-Based Access Control (RBAC)', () => {
    test('Staff IT TIDAK bisa akses /admin/users', async ({ page }) => {
        await login(page, USERS.staffIT);
        const resp = await page.goto('/admin/users');
        // Harus di-redirect atau 403
        expect(resp?.status()).toBeGreaterThanOrEqual(400);
    });
    test('Staff IT TIDAK bisa akses /finance', async ({ page }) => {
        await login(page, USERS.staffIT);
        const resp = await page.goto('/finance');
        expect(resp?.status()).toBeGreaterThanOrEqual(400);
    });
    test('Staff IT TIDAK bisa akses /executive', async ({ page }) => {
        await login(page, USERS.staffIT);
        const resp = await page.goto('/executive');
        expect(resp?.status()).toBeGreaterThanOrEqual(400);
    });
    test('Staff IT TIDAK bisa akses /approvals', async ({ page }) => {
        await login(page, USERS.staffIT);
        const resp = await page.goto('/approvals');
        expect(resp?.status()).toBeGreaterThanOrEqual(400);
    });
    test('Admin TIDAK bisa akses /executive', async ({ page }) => {
        await login(page, USERS.admin);
        const resp = await page.goto('/executive');
        expect(resp?.status()).toBeGreaterThanOrEqual(400);
    });
});
