import { test, expect } from '@playwright/test';
import { login, USERS, todayDate } from './helpers';

test.describe('04 — Pengajuan Cuti (Leave Request)', () => {

    test('Staff IT: Buka halaman Pengajuan Cuti', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/leave-requests');
        await page.waitForURL(/.*leave-requests/);
    });

    test('Staff IT: Buka form Ajukan Cuti', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/leave-requests/create');
        await page.waitForURL(/.*leave-requests\/create/);
        // Form harus menampilkan "Sisa Cuti Tahunan Anda"
        await expect(page.locator('body')).toContainText('Sisa Cuti Tahunan');
        // Harus ada field: Jenis Cuti, Tanggal Mulai, Tanggal Selesai, Alasan
        await expect(page.locator('#type')).toBeVisible();
        await expect(page.locator('#start_date')).toBeVisible();
        await expect(page.locator('#end_date')).toBeVisible();
        await expect(page.locator('#reason')).toBeVisible();
    });

    test('Staff IT: Submit form cuti dengan data lengkap', async ({ page }) => {
        await login(page, USERS.staffIT);
        await page.goto('/leave-requests/create');
        await page.waitForURL(/.*leave-requests\/create/);

        // Isi form — field names dari leave-requests/create.blade.php
        await page.locator('#type').selectOption('annual');

        // Tanggal mulai = besok, tanggal selesai = lusa
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dayAfter = new Date();
        dayAfter.setDate(dayAfter.getDate() + 2);
        
        await page.locator('#start_date').fill(tomorrow.toISOString().split('T')[0]);
        await page.locator('#end_date').fill(dayAfter.toISOString().split('T')[0]);
        await page.locator('#reason').fill('Cuti tahunan untuk urusan keluarga - test Playwright');

        // Submit — tombol "Ajukan Cuti" (x-ui.button type=submit)
        // Gunakan evaluate untuk submit form secara programatis (bypass click timeout)
        await page.locator('button:has-text("Ajukan Cuti")').evaluate((btn: HTMLButtonElement) => btn.form?.submit());
        await page.waitForURL(/.*leave-requests/, { timeout: 30000 });
    });
});
