import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('10 — Profil & Arsip & Laporan', () => {
    test('Staff IT: Akses halaman Profil', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/profile');
        await page.waitForURL(/.*profile/);
    });
    test('Staff IT: Akses Arsip / Riwayat Dokumen', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/archive');
        await page.waitForURL(/.*archive/);
    });
    test('Manager IT: Akses halaman Laporan', async ({ page }) => {
        await login(page, USERS.managerIT);
        await page.goto('/reports');
        await page.waitForURL(/.*reports/);
    });
    test('Manager IT: Akses Dokumen Tim', async ({ page }) => {
        await login(page, USERS.managerIT);
        await page.goto('/team/documents');
        await page.waitForURL(/.*team\/documents/);
    });
});
