---
name: confirmme-architecture
description: >-
  Panduan arsitektur sistem ConfirmMe-Approval. Membahas konsep Hybrid Approver,
  Template Workflow Dinamis, serta larangan keras penggunaan hardcoding pada
  route middleware, controller, dan algoritma persetujuan. Juga mencakup panduan
  sinkronisasi translasi bahasa. Gunakan skill ini saat menambahkan fitur baru 
  atau memperbaiki bug di ConfirmMe.
---

# Panduan Arsitektur: ConfirmMe-Approval

Proyek **ConfirmMe-Approval** telah bertransformasi dari sistem persetujuan (approval) yang kaku dan statis menjadi **Sistem Workflow Dinamis**. Panduan ini wajib dipahami oleh developer/agen mana pun yang akan melakukan modifikasi pada *codebase* ini agar tidak merusak arsitektur dinamis yang sudah dibangun.

## 1. Konsep Utama: Dynamic Templates
Semua dokumen pengajuan sekarang berpusat pada **Template** yang diatur secara dinamis oleh Admin melalui *database*.
- **Kategori Otomatis:** Saat *user* membuat pengajuan, "Kategori Dokumen" (misal: *IT Internal*, *Gudang*) tidak lagi diisi manual melalui *dropdown statis*. Sistem otomatis menarik `default_category` berdasarkan template yang dipilih.
- **Workflow Bebas:** Rute persetujuan (siapa yang harus menyetujui, urutannya, dan posisinya) murni didikte oleh field `workflow` bertipe array JSON di dalam tabel `templates`.

## 2. Algoritma "Hybrid Approver"
File sentral yang mengatur jalannya persetujuan adalah `app/Services/ApprovalWorkflowService.php`. Sistem tidak lagi mencari *role* secara absolut (*hardcode*). 

Algoritma pencarian *Approver* untuk sebuah peran (*role*) yang ditugaskan di Template bekerja secara berjenjang:
1. **User Spesifik:** Jika template menugaskan `user:12`, sistem akan langsung menunjuk *user* dengan ID 12.
2. **Meta-Role:** Jika template menugaskan `direktur`, sistem akan mencari siapa Direktur yang memimpin Departemen tempat si pengaju (*requester*) berada (menggunakan `Department::directorRoleFor()`).
3. **Pencarian Dalam Departemen:** Sistem memprioritaskan pencarian *role* (misal `supervisor`) **di dalam departemen yang sama** dengan pengaju. Hal ini memastikan "Manager IT" tidak menyetujui dokumen milik staff "Gudang".
4. **Pencarian Global (Fallback):** Jika tidak ditemukan di departemen yang sama, sistem akan mencari siapa pun di perusahaan yang memegang *role* tersebut secara global (berguna untuk divisi *cross-department* seperti `hrd` atau `purchasing`).

## 3. Aturan Ketat: DILARANG KERAS HARDCODING (Zero Hardcode Policy)

Kesalahan paling fatal di masa lalu adalah mengunci fitur menggunakan nama *role* (*jabatan*) tertentu. Karena sistem ini dinamis, Admin bisa kapan saja membuat jabatan aneh seperti `admin_ops_gudang` atau `hrga_coordinator`. Jika fitur digembok secara *hardcode*, jabatan baru tidak akan bisa menggunakan fitur tersebut.

Patut diingat:
- **DILARANG** menulis `middleware('role:supervisor|manager|...')` di dalam `routes/web.php` untuk rute-rute universal seperti `/approvals`, `/2fa`, atau `/reports`. Biarkan semua *user* (termasuk jabatan baru) bisa mengakses rute tersebut. Validasi apakah mereka berhak melihat/menyetujui dokumen cukup ditangani di dalam *Controller* menggunakan `policy` atau query pencarian `current_approver_id`.
- **DILARANG** menggunakan `if ($user->hasRole('finance'))` untuk memberikan izin spesifik mengubah "Nominal Disetujui" (Budget). Di sistem dinamis, *approver* manapun dalam rantai persetujuan (misal: Direktur) berhak mengubah budget jika mereka memang bagian dari rantai persetujuan untuk dokumen yang memiliki *budget* (`po`, `invoice`, `loan`).
- **Dashboard Widget:** Penentuan apakah seorang *user* berhak melihat widget "Pending Approvals" di halaman Dashboard bukan berdasarkan pengecekan *role* spesifik, melainkan dengan logika sederhana: *"Semua user yang bukan sekadar `staff` biasa (atau `admin`) adalah calon Approver"*.

## 4. Keamanan dan Perlindungan Data (Fallback)
Setiap fitur laporan (`ReportController`) wajib memiliki **fallback pengaman (`else`)** untuk jabatan yang belum dikenali. Jika seorang *user* masuk dan sistem tidak mengenali posisi khusus mereka (misal mereka bukan manager atau finance), maka secara *default* (fallback), sistem hanya boleh memunculkan **dokumen milik user itu sendiri** (`user_id = auth()->id()`). Ini untuk mencegah kebocoran seluruh data rahasia perusahaan ke jabatan baru yang belum diatur hierarkinya.

## 5. Sync Translations Workflow

Follow these steps to synchronize and translate new hardcoded texts in the Laravel application.

### Prerequisites
Ensure the application texts in blade/php files are wrapped in the Laravel translation helper `__()` before running this workflow.

### Steps

1. **Extract New Keys**
   Run the PHP synchronization script to scan all files and extract new translation keys into both `lang/en.json` and `lang/id.json`.
   ```bash
   php sync_lang.php
   ```
   *Note: This script only extracts the keys and copies them as the default values. It does NOT translate them.*

2. **Translate to English (EN)**
   Run the AI Translator script for English. This script will intelligently identify untranslated keys in `lang/en.json` and translate them from Indonesian to English using the `deep-translator` library.
   ```bash
   python translate_en.py
   ```
   *Wait for the script to finish processing in the background. It may take a minute if there are many new keys.*

3. **Fix Indonesian Keys (ID)**
   Run the AI Translator script for Indonesian. This script is crucial because if the original hardcoded text in the codebase was in English (e.g., "Dashboard" or "Control Panel"), `sync_lang.php` would have copied that English text into `id.json` as the default value. This script detects and translates those English leftovers back into standard Indonesian.
   ```bash
   python translate_id.py
   ```

### Verification
- Check the output logs of both Python scripts to ensure they completed without critical errors.
- Do NOT modify special protected terms like "Dashboard", "Finance", "Role", "Approval", and "HR" unless explicitly requested by the user, as the `translate_id.py` script automatically respects these exceptions.