---
name: confirmme-architecture
description: >-
  Panduan arsitektur sistem ConfirmMe-Approval. Membahas konsep Hybrid Approver,
  Template Workflow Dinamis, standardisasi universal PDF engine (70x70px QR, sig-card),
  keamanan impor Excel/CSV, serta larangan keras penggunaan hardcoding pada
  route middleware, controller, template matching, dan algoritma persetujuan.
  Gunakan skill ini saat menambahkan fitur baru atau memperbaiki bug di ConfirmMe.
---

# Panduan Arsitektur: ConfirmMe-Approval

Proyek **ConfirmMe-Approval** adalah sistem alur kerja persetujuan dokumen perusahaan (*enterprise-grade*) berbasis **Dynamic Workflow Engine** dan **Dynamic Form Builder**. Panduan ini wajib dipatuhi oleh seluruh AI Agent dan pengembang agar sistem tetap konsisten, tidak mengalami regresi, dan tidak mengandalkan hardcoding (*zero hardcoding policy*).

---

## 1. Konsep Utama: Dynamic Templates & Universal Form Builder

Semua dokumen pengajuan berpusat pada **Template** yang dikonfigurasi secara dinamis oleh Admin melalui menu *Admin -> Templates* (`/admin/templates`):
- **Dynamic Fields:** Setiap template dapat memiliki kombinasi tak terbatas dari isian form (`text`, `textarea`, `number`, `date`, `time`, `select`, `file`, dan tabel dinamis `table`).
- **Kategori Otomatis:** Sistem membaca `default_category` atau `fields[document_category]` dari template. Dilarang memaksakan nama kategori berdasarkan kata kunci judul dokumen.
- **Workflow Dinamis:** Rute persetujuan (siapa yang harus menyetujui, urutan langkah, dan posisinya) murni didikte oleh field `workflow` bertipe array JSON di database.

---

## 2. Algoritma "Hybrid Approver"

File sentral yang mengatur jalannya persetujuan adalah `app/Services/ApprovalWorkflowService.php`. Sistem tidak mencari *role* secara kaku (*hardcode*), melainkan melalui hierarki berlapis:
1. **User Spesifik:** Jika template menugaskan `user:12`, sistem langsung menunjuk *user* ID 12.
2. **Meta-Role:** Jika template menugaskan `direktur`, sistem secara dinamis memetakan ke Direktur divisi pemohon (`Department::directorRoleFor()`).
3. **Pencarian Dalam Departemen:** Memprioritaskan pencarian *role* (misal: `supervisor`, `manager`) **di dalam departemen yang sama** dengan pemohon.
4. **Pencarian Global (Fallback):** Jika tidak ditemukan di departemen yang sama, sistem mencari *role* lintas departemen secara global (seperti `hrd`, `purchasing`, `ga`).

---

## 3. Kebijakan Tanpa Hardcoding (Zero Hardcoding Policy)

### A. Rute & Controller:
- **DILARANG** menulis `middleware('role:supervisor|manager|...')` di dalam `routes/web.php` untuk rute universal (`/approvals`, `/2fa`, `/reports`). Gunakan permission check via `$user->can('permission-name')` atau directive `@can('permission-name')`.
- **DILARANG** menggunakan `$user->hasPermissionTo(...)` langsung di kode umum karena dapat melempar *fatal exception 500* jika data permission belum ter-seed.

### B. PDF Engine & Template Matching:
- **DILARANG KERAS MEMBAJAK TEMPLATE BARU DENGAN PENCARI KATA KUNCI JUDUL (`str_contains`):**
  - Dilarang menulis `str_contains(strtolower($document->title), 'surat tugas')` atau `str_contains(..., 'uang muka')` untuk membelokkan dokumen ke layout preset khusus.
  - Jika Admin membuat template baru bernama *"Surat Tugas Pengawasan"* atau *"Pengajuan Uang Muka Vendor"*, dokumen tersebut **WAJIB diproses oleh Universal Dynamic Form Engine** agar seluruh kolom dinamis dan tabel buatannya tercetak di PDF.
- **DILARANG MENGGUNAKAN NAMA DUMMY ORANG SECARA HARDCODE:**
  - Jangan pernah menuliskan nama orang dummy (seperti `'Faisal Abduh'` atau `'Fajar Trimuri Jantono'`) sebagai fallback. Jika approver belum menandatangani, gunakan nama dari database atau tampilkan garis titik-titik formal `...........................`.

---

## 4. Standar Universal PDF Engine & Geometri Halaman

Semua dokumen persetujuan wajib dirender melalui pintu terpusat: `resources/views/pdf/document.blade.php` (kecuali dokumen finansial khusus `purchase-order.blade.php` dan `invoice.blade.php`).

### A. Geometri Halaman & Spacing Universal (Portrait):
Seluruh dokumen Portrait wajib menggunakan parameter layout yang identik:
- **Margin `@page`:**
  ```css
  @page {
      margin: 140px 45px 35px 45px !important;
  }
  ```
- **Kop Surat Fixed Header:**
  ```css
  header {
      position: fixed;
      top: -115px;
      left: 0;
      right: 0;
      height: 105px;
  }
  ```
- **Ritme Spacing Judul Dokumen:**
  ```css
  .doc-title-banner {
      text-align: center;
      margin-top: 4px;
      margin-bottom: 14px;
      font-size: 13px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #1a1a1a;
  }
  ```
  Ini memberikan jarak bernapas yang bersih (~25px) di bawah garis merah kop surat, mencegah teks dokumen menabrak tepi atas atau kop surat.

### B. Standardisasi Barcode QR Code (Wajib 70px x 70px):
- **Ukuran Barcode Tanda Tangan:** Wajib tepat **70px x 70px** di seluruh template dokumen (`pdf.document`, `pdf.purchase-order`, `pdf.invoice`).
- **Ukuran Barcode Footer Verifikasi:** Wajib seragam **70px x 70px**.
- **Parameter Pembuatan QR SVG:**
  ```php
  QrCode::format('svg')->size(300)->margin(1)->generate($url)
  ```
  *Penting:* `margin(1)` memberikan *quiet zone* (ruang putih pembatas) sesuai standar ISO/IEC 18004 agar kamera ponsel dan scanner fisik dapat membaca barcode secara instan. Dilarang menggunakan `margin(0)`.

### C. Standardisasi Kotak Tanda Tangan (`sig-card`):
Struktur tanda tangan resmi mengadopsi kartu berbingkai terstruktur (`sig-card`):
- **Border Kotak:** `border: 1px solid #000`.
- **Header (`sig-card-header`):** Jabatan / Peran (*Role*), huruf tebal, border bawah 1px.
- **Body (`sig-card-body`):** Barcode QR 70x70px di tengah (atau placeholder status jika pending).
- **Footer (`sig-card-footer`):** Nama penandatangan dan NIP, border atas 1px.
- **Grid Adaptif:** Maksimal 3-4 kartu per baris. Jika terdapat 6 penandatangan, otomatis dibagi seimbang menjadi 2 baris x 3 kolom.

---

## 5. Keamanan Impor & Ekspor Data Excel/CSV

Saat memproses impor data tabular (misal: FastExcel / OpenSpout pada `AttendanceController`):
- **Penanganan Objek DateTime:** File `.xlsx` sering mengembalikan sel tanggal sebagai objek `\DateTimeInterface`. Dilarang memanggil `trim($value)` langsung tanpa pengecekan tipe, karena akan melempar fatal error: `TypeError: trim(): Argument #1 must be of type string, DateTime given`.
- **Pola Penanganan Wajib:**
  ```php
  $cleanKey = strtolower(trim((string)$key));
  if ($value instanceof \DateTimeInterface) {
      $cleanVal = $value->format('Y-m-d H:i:s');
  } else {
      $cleanVal = trim((string)($value ?? ''));
  }
  ```
- Normalisasi nama kolom wajib bersifat case-insensitive dan trimmed untuk mendukung variasi penamaan kolom dari pengguna.

---

## 6. Standar UI/UX Profesional: Flat Default Material

Aplikasi ConfirmMe adalah sistem internal perusahaan (*enterprise-grade*):
1. **DILARANG KERAS MENGGUNAKAN EMOJI / EMOTICON:**
   Jangan gunakan unicode emoji (🛡️, ⚡, 🔍, 🚀, ✨, 📌, dll) pada teks UI, judul card, tombol, atau badge. Gunakan selalu icon SVG bawaan (`<x-icon name="..." />` Heroicons) atau tipografi bersih.
2. **DILARANG MENGGUNAKAN GRADIENT & WARNA ACAK:**
   Gunakan warna bawaan default aplikasi:
   - Primary/Aksen: Maroon (`primary-600`, `primary-700`, `bg-primary-50`).
   - Netral: `gray-50`, `gray-100`, `gray-200`, `gray-700`, `gray-800`.
   - Border: `border-gray-200` (light) dan `border-gray-700` (dark).
3. **GUNAKAN KOMPONEN DEFAULT TEMPLATE:**
   Pertahankan komponen UI standar: `<x-ui.card>`, `<x-ui.button>`, `<x-ui.badge>`, `<x-icon>`.

---

## 7. Sinkronisasi Bahasa (Multi-Language)

- Setiap teks antarmuka Blade wajib dibungkus dengan helper translasi: `__('Teks Anda')`.
- Jalankan sinkronisasi bahasa secara rutin saat menambahkan teks antarmuka baru.
