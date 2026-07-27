import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('07 — Finance Module', () => {

    test('Manager Finance: Akses Dashboard Keuangan', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await page.goto('/finance');
        await page.waitForURL(/.*finance/);
    });

    test('Manager Finance: Akses Manajemen Anggaran', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await page.goto('/finance/budgets');
        await page.waitForURL(/.*finance\/budgets/);
    });

    test('Manager Finance: Akses Purchase Orders', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await page.goto('/finance/po');
        await page.waitForURL(/.*finance\/po/);
    });

    test('Manager Finance: Akses Invoices', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await page.goto('/finance/invoices');
        await page.waitForURL(/.*finance\/invoices/);
    });

    test('Manager Finance: Akses Pinjaman Karyawan', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await page.goto('/finance/loans');
        await page.waitForURL(/.*finance\/loans/);
    });
});
