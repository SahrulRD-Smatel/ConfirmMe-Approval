import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('09 — Admin Module', () => {
    // Admin login bisa lambat karena redirect ke /finance (403) dulu
    // Beri extra timeout untuk setiap tes
    test.setTimeout(90000);

    test('Admin: Akses Manajemen User', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/users');
        await page.waitForURL(/.*admin\/users/, { timeout: 30000 });
    });
    test('Admin: Akses Role & Permission', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/roles');
        await page.waitForURL(/.*admin\/roles/, { timeout: 30000 });
    });
    test('Admin: Akses Konfigurasi Workflow', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/workflow');
        await page.waitForURL(/.*admin\/workflow/, { timeout: 30000 });
    });
    test('Admin: Akses Departemen', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/departments');
        await page.waitForURL(/.*admin\/departments/, { timeout: 30000 });
    });
    test('Admin: Akses Template Dokumen', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/templates');
        await page.waitForURL(/.*admin\/templates/, { timeout: 30000 });
        // Verifikasi halaman benar-benar termuat
        await expect(page.locator('body')).toContainText('Template Dokumen');
    });
    test('Admin: Akses Audit Log', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/admin/audit-logs');
        await page.waitForURL(/.*admin\/audit-logs/, { timeout: 30000 });
    });
});
