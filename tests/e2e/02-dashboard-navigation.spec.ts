import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('02 — Dashboard & Navigasi Sidebar', () => {

    test('Staff IT: Dashboard menampilkan konten', async ({ page }) => {
        await login(page, USERS.staffIT);
        // Sidebar menu items yang seharusnya ada untuk Staff
        await expect(page.locator('nav')).toContainText('Dashboard');
        await expect(page.locator('nav')).toContainText('Buat Pengajuan');
        await expect(page.locator('nav')).toContainText('Daftar Pengajuan');
        await expect(page.locator('nav')).toContainText('Pinjaman Saya');
        await expect(page.locator('nav')).toContainText('Pengajuan Cuti');
        await expect(page.locator('nav')).toContainText('Check-in / Out');
        await expect(page.locator('nav')).toContainText('Riwayat Absensi');
    });

    test('Staff IT: Navigasi ke Buat Pengajuan', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/documents/create');
        await page.waitForURL(/.*documents\/create/);
        // Halaman harus menampilkan "Jenis Approval"
        await expect(page.locator('body')).toContainText('Jenis Approval');
    });

    test('Staff IT: Navigasi ke Daftar Pengajuan', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/documents');
        await page.waitForURL(/.*documents/);
    });

    test('Staff IT: Navigasi ke Pinjaman Saya', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/my-loans');
        await page.waitForURL(/.*my-loans/);
    });

    test('Staff IT: Navigasi ke Pengajuan Cuti', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/leave-requests');
        await page.waitForURL(/.*leave-requests/);
    });

    test('Manager IT: Sidebar menampilkan menu Approval', async ({ page }) => {
        await login(page, USERS.managerIT);
        await expect(page.locator('nav')).toContainText('Approval Queue');
        await expect(page.locator('nav')).toContainText('Riwayat Approval');
        await expect(page.locator('nav')).toContainText('Dokumen Tim');
        await expect(page.locator('nav')).toContainText('Laporan');
    });

    test('Manager Finance: Sidebar menampilkan menu Keuangan', async ({ page }) => {
        await login(page, USERS.managerFinance);
        await expect(page.locator('nav')).toContainText('Dashboard Keuangan');
        await expect(page.locator('nav')).toContainText('Manajemen Anggaran');
        await expect(page.locator('nav')).toContainText('Purchase Orders');
        await expect(page.locator('nav')).toContainText('Invoices');
        await expect(page.locator('nav')).toContainText('Pinjaman Karyawan');
    });

    test('Admin: Sidebar menampilkan menu Administrasi', async ({ page }) => {
        await login(page, USERS.admin);
        await expect(page.locator('nav')).toContainText('Manajemen User');
        await expect(page.locator('nav')).toContainText('Role & Permission');
        await expect(page.locator('nav')).toContainText('Konfigurasi Workflow');
        await expect(page.locator('nav')).toContainText('Departemen');
        await expect(page.locator('nav')).toContainText('Template Dokumen');
        await expect(page.locator('nav')).toContainText('Audit Log');
    });

    test('Presiden Direktur: Sidebar menampilkan menu Executive', async ({ page }) => {
        await login(page, USERS.presidenDirektur);
        await expect(page.locator('nav')).toContainText('Dashboard');
        await expect(page.locator('nav')).toContainText('Approval Queue');
        await expect(page.locator('nav')).toContainText('Company Insights');
        await expect(page.locator('nav')).toContainText('Audit & History');
    });
});
