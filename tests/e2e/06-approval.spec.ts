import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('06 — Approval Queue & Proses Approval', () => {

    test('Supervisor IT: Akses halaman Approval Queue', async ({ page }) => {
        await login(page, USERS.supervisorIT);
        await page.goto('/approvals');
        await page.waitForURL(/.*approvals/);
        // Judul halaman: "Dokumen Menunggu Approval"
        await expect(page.locator('body')).toContainText('Dokumen Menunggu Approval');
    });

    test('Supervisor IT: Buka detail dokumen via "Review →"', async ({ page }) => {
        await login(page, USERS.supervisorIT);
        await page.goto('/approvals');
        await page.waitForURL(/.*approvals/);

        // Cek apakah ada dokumen menunggu
        const reviewLink = page.locator('a:has-text("Review →")').first();
        const emptyState = page.locator('text=Semua Beres');

        if (await reviewLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await reviewLink.click();
            await page.waitForURL(/.*approvals\/\d+/);
            // Halaman show harus menampilkan "Keputusan Anda"
            await expect(page.locator('body')).toContainText('Keputusan Anda');
            // Harus ada 3 tombol aksi: Setujui, Revisi, Tolak
            await expect(page.locator('span:has-text("Setujui")')).toBeVisible();
            await expect(page.locator('span:has-text("Revisi")')).toBeVisible();
            await expect(page.locator('span:has-text("Tolak")')).toBeVisible();
        } else {
            // Jika tidak ada dokumen, cek empty state
            await expect(emptyState).toBeVisible();
        }
    });

    test('Supervisor IT: Riwayat Approval dapat diakses', async ({ page }) => {
        await login(page, USERS.supervisorIT);
        await page.goto('/approvals/history');
        await page.waitForURL(/.*approvals\/history/);
    });

    test('Manager IT: Halaman Approval Cuti dapat diakses', async ({ page }) => {
        await login(page, USERS.managerIT);
        await page.goto('/leave-approvals');
        await page.waitForURL(/.*leave-approvals/);
    });
});
