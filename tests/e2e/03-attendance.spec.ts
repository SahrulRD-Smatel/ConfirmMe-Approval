import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('03 — Absensi (Attendance)', () => {

    test('Staff IT: Halaman Absensi menampilkan status hari ini', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/attendance');
        await page.waitForURL(/.*attendance/);
        // Halaman harus menampilkan judul "Absensi"
        await expect(page.locator('h1')).toContainText('Absensi');
        // Harus ada card Check In dan Check Out
        await expect(page.locator('body')).toContainText('Check In');
        await expect(page.locator('body')).toContainText('Check Out');
        // Harus ada ringkasan bulan ini
        await expect(page.locator('body')).toContainText('Ringkasan Bulan Ini');
    });

    test('Staff IT: Halaman Riwayat Absensi dapat diakses', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/attendance/history');
        await page.waitForURL(/.*attendance\/history/);
    });

    test('Staff IT: Tombol Check In/Out terlihat (disabled sampai selfie)', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/attendance');
        await page.waitForURL(/.*attendance/);
        // Tombol "Check In Sekarang" atau "Check Out Sekarang" atau "Absensi Hari Ini Selesai!"
        const checkBtn = page.locator('button:has-text("Check In Sekarang"), button:has-text("Check Out Sekarang")');
        const doneText = page.locator('text=Absensi Hari Ini Selesai');
        // Salah satu harus terlihat
        const hasButton = await checkBtn.count() > 0;
        const hasDone = await doneText.count() > 0;
        expect(hasButton || hasDone).toBeTruthy();
    });
});
