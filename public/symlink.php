<?php
/**
 * ============================================================
 * Storage Symlink Creator untuk cPanel Hosting
 * ============================================================
 * 
 * Jalankan SEKALI di browser: https://confirmme.sahrulrdhani.my.id/symlink.php
 * Lalu HAPUS file ini setelah berhasil!
 * 
 * NOTE: Jika symlink gagal (banyak cPanel memblokir symlink()),
 * tidak perlu khawatir — Laravel 12 sudah dikonfigurasi untuk
 * serve file dari storage/app/public tanpa symlink.
 * (lihat: config/filesystems.php → 'public' disk → 'serve' => true)
 * ============================================================
 */

$target = $_SERVER['DOCUMENT_ROOT'] . '/../storage/app/public';
$link = $_SERVER['DOCUMENT_ROOT'] . '/storage';

echo "<h2>ConfirmMe - Storage Symlink Setup</h2>";
echo "<hr>";
echo "<p><b>Target:</b> $target</p>";
echo "<p><b>Link:</b> $link</p>";
echo "<hr>";

// Check if target exists
if (!is_dir($target)) {
    echo "<p>❌ Target directory tidak ditemukan: $target</p>";
    echo "<p>Pastikan folder storage/app/public ada di server.</p>";
    exit;
}

// Check if storage already exists as a real directory
if (is_dir($link) && !is_link($link)) {
    echo "<p>⚠️ /storage sudah ada sebagai FOLDER biasa (bukan symlink).</p>";
    echo "<p>Hapus folder tersebut dulu melalui File Manager cPanel, lalu jalankan ulang script ini.</p>";
    exit;
}

// Remove old symlink if exists
if (is_link($link)) {
    unlink($link);
    echo "<p>🗑️ Symlink lama dihapus.</p>";
}

// Try to create symlink
if (@symlink($target, $link)) {
    echo "<p>✅ Symlink berhasil dibuat!</p>";
    echo "<p><b>PENTING:</b> Hapus file symlink.php ini sekarang!</p>";
} else {
    echo "<p>⚠️ symlink() gagal — kemungkinan diblokir oleh cPanel security.</p>";
    echo "<p>Tapi <b>tidak masalah</b> — Laravel 12 sudah dikonfigurasi untuk serve file tanpa symlink.</p>";
    echo "<p>File avatar, lampiran, dll akan tetap bisa diakses via route <code>/storage/{path}</code>.</p>";
    echo "<hr>";
    echo "<h3>Alternatif Manual (opsional):</h3>";
    echo "<ol>";
    echo "<li>Buka <b>cPanel → File Manager</b></li>";
    echo "<li>Masuk ke <code>public_html/</code></li>";
    echo "<li>Buat folder baru bernama <code>storage</code></li>";
    echo "<li>Di dalamnya, buat symlink ke <code>../storage/app/public</code> via SSH:<br><code>ln -s ../storage/app/public storage</code></li>";
    echo "</ol>";
}
