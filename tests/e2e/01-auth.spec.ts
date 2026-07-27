import { test, expect } from '@playwright/test';
import { login, logout, USERS } from './helpers';

test.describe('01 — Authentication', () => {

    test('Login berhasil sebagai Staff IT', async ({ page }) => {
        await login(page, USERS.staffIT);
        // Harus masuk ke halaman /dashboard
        expect(page.url()).toContain('/dashboard');
    });

    test('Login gagal dengan password salah', async ({ page }) => {
        await page.goto('/login');
        await page.locator('#email').fill(USERS.staffIT);
        await page.locator('#password').fill('salahpassword');
        await page.locator('#submitBtn').click();
        // Harus tetap di halaman login + muncul error-message
        await expect(page.locator('.error-message')).toBeVisible({ timeout: 10000 });
    });

    test('Logout berhasil', async ({ page }) => {
        await login(page, USERS.staffIT);
        await logout(page);
        // Harus kembali ke halaman login
        expect(page.url()).toContain('/login');
    });
});
