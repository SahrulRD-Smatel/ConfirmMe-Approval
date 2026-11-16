# ConfirmMe-Approval: Panduan & Aturan AI Agent

Dokumen ini berisi aturan wajib yang harus dipatuhi oleh setiap AI Agent saat membaca, menganalisis, maupun memodifikasi kode di repositori **ConfirmMe-Approval**.

---

## 1. Standar UI/UX: Flat Default Material (Zero AI-Design Policy)

Aplikasi ConfirmMe adalah sistem internal perusahaan (*enterprise-grade*) untuk alur persetujuan dokumen. Tampilan harus selalu **profesional, flat, minimalis, dan mudah digunakan (UX user-friendly)**:

### Larangan Keras:
- **DILARANG MENGGUNAKAN EMOJI / EMOTICON:**
  - Jangan gunakan unicode emoji (misalnya 🛡️, ⚡, 🔍, 🚀, ✨, 📌, dll) pada teks UI, judul card, tombol, maupun badge status.
  - Gunakan selalu icon SVG bawaan proyek: `<x-icon name="..." />` (Heroicons) atau tipografi bersih.
- **DILARANG MENGGUNAKAN WARNA GRADIENT & WARNA ACAK:**
  - Dilarang menambahkan `bg-gradient-...` atau kombinasi warna-warni acak (seperti ungu, pink, cyan, lime, amber) yang memberikan kesan "desain AI generatif".
  - Gunakan warna bawaan default aplikasi:
    - **Primary / Aksen:** Maroon / Merah Anggur (`primary-600`, `primary-700`, `bg-primary-50`).
    - **Netral Flat:** `gray-50`, `gray-100`, `gray-200`, `gray-700`, `gray-800`, `gray-900`.
    - **Border:** `border-gray-200` (light) dan `border-gray-700` (dark).
    - **Card/Surface:** `bg-white dark:bg-gray-800` flat material.
- **GUNAKAN KOMPONEN DEFAULT TEMPLATE:**
  - Pertahankan komponen UI standar yang sudah ada: `<x-ui.card>`, `<x-ui.button>`, `<x-ui.badge>`, `<x-icon>`.
  - Jangan mengubah struktur atau styling dasar tombol dan kartu tanpa instruksi eksplisit dari user.

---

## 2. Kebijakan Tanpa Hardcoding (Zero Hardcoding Policy)

Sistem persetujuan dokumen bersifat dinamis berbasis *Template* dan tabel database:

- **Middleware Routes:** Dilarang mengunci rute dengan `middleware('role:supervisor|manager|...')` pada fitur universal (`/approvals`, `/reports`, `/2fa`).
- **Pengecekan Permission:** Gunakan `$user->can('permission-name')` atau directive Blade `@can('permission-name')` untuk hak akses. Jangan gunakan `$user->hasPermissionTo(...)` langsung di kode umum karena dapat melempar *fatal exception 500* jika data permission belum di-seed di database.
- **Hybrid Approver:** Alur persetujuan diproses secara dinamis oleh `ApprovalWorkflowService` berdasarkan hierarki departemen, meta-role (`direktur`), dan fallback global.
- **Dilarang Membajak Template Baru (`str_contains` Judul):**
  - Dilarang menuliskan logika pencocokan substring judul seperti `str_contains(strtolower($document->title), 'surat tugas')` atau `str_contains(..., 'uang muka')` yang membajak template baru buatan Admin ke layout kaku lama.
  - Seluruh template baru wajib diproses oleh **Universal Dynamic Form Engine** agar seluruh kolom dinamis (*dynamic fields*) dan tabel yang dikonfigurasi Admin tercetak lengkap di PDF.
- **Dilarang Hardcoding Nama Orang Dummy:**
  - Jangan pernah menuliskan nama orang secara statis (seperti `'Faisal Abduh'` atau `'Fajar Trimuri Jantono'`). Semua nama penandatangan wajib ditarik dari database relasi approver atau menggunakan placeholder titik-titik formal `...........................`.
- **Kategori Dokumen Dinamis:**
  - Dilarang memaksakan nama kategori (misal memaksakan "PENGGUNAAN KENDARAAN OPERASIONAL" jika judul ada kata "mobil"). Kategori harus murni dinamis membaca `fields[document_category]` atau `template->name`.

---

## 3. Sinkronisasi Bahasa (Multi-Language)

- Setiap teks antarmuka Blade wajib dibungkus dengan helper translasi: `__('Teks Anda')`.
- Jalankan `php sync_lang.php` dan skrip AI translator yang tersedia jika menambahkan teks baru ke dalam sistem.

---

## 4. Standar PDF Engine Dinamis (Zero Hardcoded Blade Views)

Sistem ConfirmMe menggunakan mesin PDF dinamis terpusat pada file `resources/views/pdf/document.blade.php`:

### Larangan Keras:
- **DILARANG MEMBUAT FILE BLADE BARU PER TEMPLATE (misal: `pdf/pembelian_barang.blade.php`):**
  - Membuat file Blade baru setiap kali ada template baru adalah pelanggaran terhadap prinsip *Dynamic Form Builder*. Jika Admin membuat template baru di web, dokumen PDF-nya pasti tidak cocok (*miss*).
  - Seluruh dokumen persetujuan wajib dirender melalui satu pintu mesin universal: `resources/views/pdf/document.blade.php`.

### Geometri & Spacing Universal Halaman PDF:
Seluruh dokumen Portrait (`document.blade.php`, `purchase-order.blade.php`, `invoice.blade.php`) wajib memiliki geometri yang seragam:
- **Margin `@page`:** `@page { margin: 140px 45px 35px 45px !important; }`
- **Fixed Kop Surat:** `header { position: fixed; top: -115px; left: 0; right: 0; height: 105px; }`
- **Spacing Judul Dokumen:** `.doc-title-banner { text-align: center; margin-top: 4px; margin-bottom: 14px; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #1a1a1a; }`

### Standardisasi Barcode QR Code & Kartu Tanda Tangan (`sig-card`):
- **Ukuran Barcode TTD:** Wajib seragam tepat **70px x 70px** di SEMUA dokumen (`pdf.document`, `pdf.purchase-order`, `pdf.invoice`).
- **Ukuran Barcode Verifikasi:** Wajib seragam tepat **70px x 70px**.
- **Parameter Generator QR SVG:** `QrCode::format('svg')->size(300)->margin(1)->generate($url)`. `margin(1)` memberikan *quiet zone* standar ISO/IEC 18004 agar kamera scanner langsung membaca dengan cepat. Dilarang menggunakan `margin(0)`.
- **Struktur Kartu Tanda Tangan:** Mengadopsi struktur `sig-card` (border `1px solid #000`, header jabatan, body QR 70x70px, footer nama & NIP) dengan grid adaptif seimbang.

### Pemilihan Layout di Form Builder:
- Admin dapat menentukan tipe layout di menu *Admin -> Buat/Edit Template* melalui dropdown `fields[pdf_layout]`:
  - `auto` (Otomatis: Landscape jika tabel belanja $\ge 4$ kolom, Portrait jika formulir biasa).
  - `formulir_kerja` (Formulir Kerja Resmi - Portrait).
  - `tabel_pengadaan` (Daftar Pengadaan / Pembelian - Landscape).
- Di controller ([DocumentController.php](file:///f:/Dhani/JOB/ConfirmMe-Approval/app/Http/Controllers/DocumentController.php)), rute print selalu mengarah ke `pdf.document` (kecuali invoice & purchase-order yang merupakan dokumen finansial khusus).

---

## 5. Keamanan Impor Data Tabular (Excel / CSV)

- Saat membaca data baris Excel via library seperti FastExcel:
  - Sel bertipe tanggal pada file `.xlsx` dikembalikan sebagai objek `\DateTimeInterface`.
  - Dilarang memanggil `trim($value)` langsung tanpa pengecekan tipe.
  - Wajib dikonversi terlebih dahulu:
    ```php
    $cleanKey = strtolower(trim((string)$key));
    if ($value instanceof \DateTimeInterface) {
        $cleanVal = $value->format('Y-m-d H:i:s');
    } else {
        $cleanVal = trim((string)($value ?? ''));
    }
    ```
