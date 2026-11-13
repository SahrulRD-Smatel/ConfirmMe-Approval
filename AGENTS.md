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

### Arsitektur Dual-Preset PDF Engine:
Sistem memiliki 2 preset layout visual terpadu di dalam `pdf.document`:
1. **Formulir Kerja Resmi Perusahaan (Portrait / A4 Tegak):**
   - **Standar untuk 90% dokumen kantor:** Peminjaman Kendaraan (`VEHICLE_LOAN`), Izin Gudang (`SURAT_IJIN_GUDANG`), Surat Tugas, Form Cuti, Permintaan Akun IT, dll.
   - **Komponen Wajib:**
     - Header fixed kotak resmi **Formulir Kerja** (`@include('pdf.partials.company-header')`).
     - Margin atas `@page` minimal `140px` (kop surat fixed `105px`) agar saat dokumen tumpah ke halaman 2, konten memiliki ruang bernapas aman di bawah kop surat (tidak menabrak tepi atas).
     - Sub-header banner kategori & judul dokumen resmi.
     - Isian formulir (`info-table`) bersih tanpa kotak tebal/merah.
     - Tabel dinamis (seperti *Data Penumpang dalam Mobil*) mengalir langsung di bawah isian form tanpa pemotongan halaman paksa (`page-break-before: always`).
     - Lampiran foto/berkas (seperti *Foto SIM A*) disematkan berbingkai rapi dan proporsional.
     - Disclaimer / syarat & ketentuan.
     - Tanda tangan digital ber-QR Code dengan verifikasi resmi.
2. **Daftar Pengadaan / Pembelian Barang (Landscape / A4 Mendatar):**
   - **Khusus untuk dokumen belanja tabular:** Permintaan Pembelian Barang (ATK, P3K, Pantry, Aset).
   - **Komponen Wajib:**
     - Kertas mendatar (Landscape) dengan margin `@page { size: A4 landscape; margin: 8mm 12mm 8mm 12mm; }` agar konten proporsional dan tidak menyisakan ruang kosong berlebih di pinggir kertas.
     - Tabel belanja 7–8 kolom dengan sub-kategori emas `#F7CE3E`, kalkulasi Grand Total otomatis berlatar kuning emas, dan kop pembelian Info Data.
     - 4 kotak tanda tangan resmi (Pemohon, Disetujui, Bagian Keuangan, Mengetahui) dengan QR code barcode ukuran seragam 70x70px yang mudah di-scan dan tinggi kotak presisi rata sejajar.

### Pemilihan Layout di Form Builder:
- Admin dapat menentukan tipe layout di menu *Admin -> Buat/Edit Template* melalui dropdown `fields[pdf_layout]`:
  - `auto` (Otomatis: Landscape jika tabel belanja $\ge 4$ kolom, Portrait jika formulir biasa).
  - `formulir_kerja` (Formulir Kerja Resmi - Portrait).
  - `tabel_pengadaan` (Daftar Pengadaan / Pembelian - Landscape).
- Di controller ([DocumentController.php](file:///f:/Dhani/JOB/ConfirmMe-Approval/app/Http/Controllers/DocumentController.php)), rute print selalu mengarah ke `pdf.document` (kecuali invoice & purchase-order yang merupakan dokumen finansial khusus).
