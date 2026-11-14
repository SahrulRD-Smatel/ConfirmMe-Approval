<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan kesalahan default yang digunakan oleh
    | kelas validator. Beberapa aturan memiliki beberapa versi seperti
    | aturan ukuran. Silakan sesuaikan setiap pesan ini sesuai kebutuhan.
    |
    */

    'accepted' => 'Isian :attribute harus diterima.',
    'accepted_if' => 'Isian :attribute harus diterima bila :other bernilai :value.',
    'active_url' => 'Isian :attribute bukan URL yang valid.',
    'after' => 'Isian :attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => 'Isian :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Isian :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Isian :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Isian :attribute hanya boleh berisi huruf dan angka.',
    'any_of' => 'Isian :attribute tidak valid.',
    'array' => 'Isian :attribute harus berupa sebuah array.',
    'ascii' => 'Isian :attribute hanya boleh berisi karakter alfanumerik dan simbol single-byte.',
    'before' => 'Isian :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => 'Isian :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Isian :attribute harus memiliki antara :min dan :max item.',
        'file' => 'Berkas :attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => 'Isian :attribute harus bernilai antara :min dan :max.',
        'string' => 'Isian :attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean' => 'Isian :attribute harus bernilai benar atau salah.',
    'can' => 'Isian :attribute berisi nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => 'Isian :attribute tidak memiliki nilai yang diwajibkan.',
    'current_password' => 'Kata sandi saat ini salah.',
    'date' => 'Isian :attribute bukan tanggal yang valid.',
    'date_equals' => 'Isian :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => 'Isian :attribute tidak cocok dengan format :format.',
    'decimal' => 'Isian :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'Isian :attribute harus ditolak.',
    'declined_if' => 'Isian :attribute harus ditolak bila :other bernilai :value.',
    'different' => 'Isian :attribute dan :other harus berbeda.',
    'digits' => 'Isian :attribute harus terdiri dari :digits digit.',
    'digits_between' => 'Isian :attribute harus berukuran antara :min dan :max digit.',
    'dimensions' => 'Isian :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'Isian :attribute memiliki nilai yang duplikat.',
    'doesnt_end_with' => 'Isian :attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with' => 'Isian :attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email' => 'Isian :attribute harus berupa alamat email yang valid.',
    'ends_with' => 'Isian :attribute harus diakhiri dengan salah satu dari: :values.',
    'enum' => 'Isian :attribute yang dipilih tidak valid.',
    'exists' => 'Isian :attribute yang dipilih tidak valid.',
    'extensions' => 'Isian :attribute harus memiliki ekstensi: :values.',
    'file' => 'Isian :attribute harus berupa berkas.',
    'filled' => 'Isian :attribute wajib memiliki nilai.',
    'gt' => [
        'array' => 'Isian :attribute harus memiliki lebih dari :value item.',
        'file' => 'Berkas :attribute harus berukuran lebih dari :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai lebih besar dari :value.',
        'string' => 'Isian :attribute harus berisi lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Isian :attribute harus memiliki :value item atau lebih.',
        'file' => 'Berkas :attribute harus berukuran minimal :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai minimal :value.',
        'string' => 'Isian :attribute harus berisi minimal :value karakter.',
    ],
    'hex_color' => 'Isian :attribute harus berupa kode warna heksadesimal yang valid.',
    'image' => 'Isian :attribute harus berupa gambar.',
    'in' => 'Isian :attribute yang dipilih tidak valid.',
    'in_array' => 'Isian :attribute harus ada di dalam :other.',
    'integer' => 'Isian :attribute harus berupa bilangan bulat.',
    'ip' => 'Isian :attribute harus berupa alamat IP yang valid.',
    'ipv4' => 'Isian :attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => 'Isian :attribute harus berupa alamat IPv6 yang valid.',
    'json' => 'Isian :attribute harus berupa string JSON yang valid.',
    'list' => 'Isian :attribute harus berupa daftar (list).',
    'lowercase' => 'Isian :attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => 'Isian :attribute harus memiliki kurang dari :value item.',
        'file' => 'Berkas :attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai kurang dari :value.',
        'string' => 'Isian :attribute harus berisi kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Isian :attribute tidak boleh memiliki lebih dari :value item.',
        'file' => 'Berkas :attribute tidak boleh berukuran lebih dari :value kilobita.',
        'numeric' => 'Isian :attribute tidak boleh bernilai lebih besar dari :value.',
        'string' => 'Isian :attribute tidak boleh berisi lebih dari :value karakter.',
    ],
    'mac_address' => 'Isian :attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Isian :attribute tidak boleh memiliki lebih dari :max item.',
        'file' => 'Berkas :attribute tidak boleh berukuran lebih dari :max kilobita.',
        'numeric' => 'Isian :attribute tidak boleh bernilai lebih besar dari :max.',
        'string' => 'Isian :attribute tidak boleh berisi lebih dari :max karakter.',
    ],
    'max_digits' => 'Isian :attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => 'Isian :attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => 'Isian :attribute harus berupa berkas bertipe: :values.',
    'min' => [
        'array' => 'Isian :attribute harus memiliki minimal :min item.',
        'file' => 'Berkas :attribute harus berukuran minimal :min kilobita.',
        'numeric' => 'Isian :attribute harus bernilai minimal :min.',
        'string' => 'Isian :attribute harus berisi minimal :min karakter.',
    ],
    'min_digits' => 'Isian :attribute harus memiliki minimal :min digit.',
    'missing' => 'Isian :attribute tidak boleh ada.',
    'missing_if' => 'Isian :attribute tidak boleh ada bila :other bernilai :value.',
    'missing_unless' => 'Isian :attribute tidak boleh ada kecuali :other bernilai :value.',
    'missing_with' => 'Isian :attribute tidak boleh ada bila terdapat :values.',
    'missing_with_all' => 'Isian :attribute tidak boleh ada bila terdapat :values.',
    'multiple_of' => 'Isian :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Isian :attribute yang dipilih tidak valid.',
    'not_regex' => 'Format isian :attribute tidak valid.',
    'numeric' => 'Isian :attribute harus berupa angka.',
    'password' => [
        'letters' => 'Isian :attribute harus mengandung setidaknya satu huruf.',
        'mixed' => 'Isian :attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Isian :attribute harus mengandung setidaknya satu angka.',
        'symbols' => 'Isian :attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => 'Isian :attribute yang diberikan telah bocor dalam kebocoran data. Silakan pilih :attribute yang lain.',
    ],
    'present' => 'Isian :attribute wajib ada.',
    'present_if' => 'Isian :attribute wajib ada bila :other bernilai :value.',
    'present_unless' => 'Isian :attribute wajib ada kecuali :other bernilai :value.',
    'present_with' => 'Isian :attribute wajib ada bila terdapat :values.',
    'present_with_all' => 'Isian :attribute wajib ada bila terdapat :values.',
    'prohibited' => 'Isian :attribute dilarang.',
    'prohibited_if' => 'Isian :attribute dilarang bila :other bernilai :value.',
    'prohibited_if_accepted' => 'Isian :attribute dilarang bila :other diterima.',
    'prohibited_if_declined' => 'Isian :attribute dilarang bila :other ditolak.',
    'prohibited_unless' => 'Isian :attribute dilarang kecuali :other ada di dalam :values.',
    'prohibits' => 'Isian :attribute melarang :other untuk hadir.',
    'regex' => 'Format isian :attribute tidak valid.',
    'required' => 'Isian :attribute wajib diisi.',
    'required_array_keys' => 'Isian :attribute harus berisi entri untuk: :values.',
    'required_if' => 'Isian :attribute wajib diisi bila :other bernilai :value.',
    'required_if_accepted' => 'Isian :attribute wajib diisi bila :other diterima.',
    'required_if_declined' => 'Isian :attribute wajib diisi bila :other ditolak.',
    'required_unless' => 'Isian :attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with' => 'Isian :attribute wajib diisi bila terdapat :values.',
    'required_with_all' => 'Isian :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Isian :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Isian :attribute wajib diisi bila tidak ada satupun dari :values yang hadir.',
    'same' => 'Isian :attribute dan :other harus sama.',
    'size' => [
        'array' => 'Isian :attribute harus mengandung :size item.',
        'file' => 'Berkas :attribute harus berukuran :size kilobita.',
        'numeric' => 'Isian :attribute harus bernilai :size.',
        'string' => 'Isian :attribute harus berisi :size karakter.',
    ],
    'starts_with' => 'Isian :attribute harus diawali dengan salah satu dari: :values.',
    'string' => 'Isian :attribute harus berupa teks/string.',
    'timezone' => 'Isian :attribute harus berupa zona waktu yang valid.',
    'unique' => 'Isian :attribute sudah digunakan.',
    'uploaded' => 'Berkas :attribute gagal diunggah.',
    'uppercase' => 'Isian :attribute harus berupa huruf besar.',
    'url' => 'Isian :attribute harus berupa URL yang valid.',
    'ulid' => 'Isian :attribute harus berupa ULID yang valid.',
    'uuid' => 'Isian :attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Pesan Validasi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi kustom untuk atribut
    | menggunakan konvensi "attribute.rule" untuk menamai baris.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Kustom
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar penampung atribut kami
    | dengan sesuatu yang lebih ramah pembaca seperti "Alamat Email" daripada
    | "email". Ini hanya membantu kami membuat pesan kami lebih ekspresif.
    |
    */

    'attributes' => [
        'title' => 'Judul Dokumen',
        'description' => 'Keterangan',
        'document_template_id' => 'Template Dokumen',
        'document_category' => 'Kategori Dokumen',
        'department' => 'Departemen',
        'estimated_budget' => 'Estimasi Anggaran',
        'approved_budget' => 'Anggaran Disetujui',
        'effective_date' => 'Tanggal Berlaku',
        'expiry_date' => 'Tanggal Kadaluarsa',
        'name' => 'Nama',
        'email' => 'Alamat Email',
        'password' => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
        'current_password' => 'Kata Sandi Saat Ini',
        'phone' => 'Nomor Telepon',
        'notes' => 'Catatan',
        'reason' => 'Alasan',
        'status' => 'Status',
        'file' => 'Berkas Lampiran',
        'attachment' => 'Lampiran',
        'metadata' => 'Data Formulir',
        'documents' => 'Daftar Dokumen',
    ],

];

