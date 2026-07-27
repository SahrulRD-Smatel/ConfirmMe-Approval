import { Page } from '@playwright/test';

/**
 * USER CREDENTIALS — Sesuai data REAL dari user (email yg sudah diubah di database)
 * Sumber: Pesan user di awal percakapan, diverifikasi via browser login test.
 * 
 * Password universal: "password"
 */
export const USERS = {
    // Staff IT (Aditya Permana)
    staffIT:            'zethya.sahrul@gmail.com',
    // IT Team Lead / Supervisor IT (Dimas Saputra)
    supervisorIT:       'sahrulramadhani1022@gmail.com',
    // Manager IT (Fajar Nugroho)
    managerIT:          'manager.it@confirmme.com',
    // Direktur IT (Andi Wijaya)
    direkturIT:         'sahrul.r.dhani@gmail.com',
    // Manager HRD (Putri Handayani) — double role: manager + hrd
    managerHRD:         'manager.hrd@confirmme.com',
    // Direktur HR (Dewi Kartika)
    direkturHR:         'marwankosasih20@gmail.com',
    // Manager Finance (Hendra Gunawan) — double role: manager + finance
    managerFinance:     'manager.finance@confirmme.com',
    // Direktur Finance (Budi Santoso)
    direkturFinance:    'mambrakmaur20@gmail.com',
    // Presiden Direktur (Kromo Semitro)
    presidenDirektur:   'karsinem.k.semitro@gmail.com',
    // Admin (Rizky Pratama)
    admin:              'sukirno.k.semitro@gmail.com',
} as const;

export const DEFAULT_PASSWORD = 'password';

/**
 * Login helper — mengisi form login dan menunggu redirect ke dashboard.
 * Login button di UI: id="submitBtn", teks "Masuk".
 * Setelah login, redirect ke /dashboard (staff/mgr) atau /executive (presdir).
 */
export async function login(page: Page, email: string, password = DEFAULT_PASSWORD) {
    await page.goto('/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    // Tunggu animasi CSS login page selesai (element not stable pada cold start)
    await page.waitForTimeout(1500);
    await page.locator('#submitBtn').click();
    // Tunggu redirect — bisa ke /dashboard, /executive, /finance, /admin, dll
    await page.waitForURL(/.*(dashboard|executive|finance|admin)/, { timeout: 30000 });
}

/**
 * Logout helper — klik tombol "Keluar" di sidebar bawah.
 * Elemen: <span>Keluar</span> di dalam <button type="submit">
 */
export async function logout(page: Page) {
    await page.locator('button:has-text("Keluar"), span:has-text("Keluar")').first().click();
    await page.waitForURL(/.*login/, { timeout: 10000 });
}

/**
 * Helper: Isi tanggal hari ini dalam format YYYY-MM-DD
 */
export function todayDate(): string {
    return new Date().toISOString().split('T')[0];
}

/**
 * Helper: Bulan ini format YYYY-MM
 */
export function thisMonth(): string {
    return new Date().toISOString().slice(0, 7);
}
