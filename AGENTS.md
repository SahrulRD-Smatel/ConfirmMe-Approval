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
