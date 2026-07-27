import { test, expect } from '@playwright/test';
import { login, USERS } from './helpers';

test.describe('16 — Notifikasi, Search, Announcements', () => {

    test('Staff IT: Akses halaman Notifikasi', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/notifications');
        await page.waitForURL(/.*notifications/);
        // Halaman harus menampilkan daftar notifikasi atau empty state
        await expect(page.locator('body')).toContainText(/Notifikasi|Pemberitahuan|notification/i);
    });

    test('Staff IT: Akses halaman Pencarian', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/search?q=test');
        await page.waitForURL(/.*search/);
        // Halaman pencarian harus ada
        await expect(page.locator('body')).toContainText(/Pencarian|Hasil|Search|Result/i);
    });

    test('Staff IT: Akses halaman Pengumuman', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/announcements');
        await page.waitForURL(/.*announcements/);
        // Halaman pengumuman harus ada
        await expect(page.locator('body')).toContainText(/Pengumuman|Announcement/i);
    });
});

test.describe('16 — My Loans (Pinjaman Saya)', () => {

    test('Staff IT: Akses halaman Pinjaman Saya', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/my-loans');
        await page.waitForURL(/.*my-loans/);
        // Halaman my-loans harus ada
        await expect(page.locator('body')).toContainText(/Pinjaman|Loan/i);
    });
});

test.describe('16 — Document Comment', () => {

    test('Staff IT: Halaman detail dokumen menampilkan section komentar', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/documents');
        await page.waitForURL(/.*documents/, { timeout: 15000 });

        // Klik link dokumen pertama di tabel (bukan link create/edit)
        const docLink = page.locator('table a[href*="/documents/"]').first();
        if (await docLink.isVisible({ timeout: 5000 }).catch(() => false)) {
            await docLink.click();
            await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });

            // Halaman detail harus menampilkan section komentar
            await expect(page.locator('body')).toContainText('Komentar');
            // Form komentar harus ada (textarea + tombol Kirim)
            await expect(page.locator('textarea[name="comment"]')).toBeVisible();
        } else {
            // Fallback: coba akses dokumen terakhir yg dibuat via link apapun
            const anyDocLink = page.locator('a').filter({ hasText: /DOC-/ }).first();
            if (await anyDocLink.isVisible({ timeout: 3000 }).catch(() => false)) {
                await anyDocLink.click();
                await page.waitForURL(/.*documents\/\d+/, { timeout: 30000 });
                await expect(page.locator('body')).toContainText('Komentar');
            }
        }
    });
});

test.describe('16 — Bulk Approval', () => {

    test('Supervisor IT: Akses halaman Bulk Approval', async ({ page }) => {
        await login(page, USERS.supervisorIT);
        await page.goto('/approvals/bulk');
        await page.waitForURL(/.*approvals\/bulk/);
        await expect(page.locator('body')).toContainText(/Bulk|Massal|batch/i);
    });
});

test.describe('16 — Attendance Admin', () => {

    test('Admin: Akses halaman Laporan Absensi', async ({ page }) => {
        await login(page, USERS.admin);
        await page.goto('/attendance-admin/report');
        await page.waitForURL(/.*attendance-admin\/report/);
        await expect(page.locator('body')).toContainText(/Absensi|Attendance|Laporan|Report/i);
    });
});

test.describe('16 — Leave Approval Actions', () => {

    test('Supervisor IT: Akses halaman Leave Approvals', async ({ page }) => {
        await login(page, USERS.supervisorIT);
        await page.goto('/leave-approvals');
        await page.waitForURL(/.*leave-approvals/);
        // Halaman harus menampilkan daftar cuti atau empty state
        await expect(page.locator('body')).toContainText(/Cuti|Leave|Approval/i);
    });
});
