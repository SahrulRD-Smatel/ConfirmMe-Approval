import json

file_path = 'resources/views/leave-requests/index.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = {
    'Pengajuan Cuti Saya': "{{ __('Pengajuan Cuti Saya') }}",
    'Riwayat dan status pengajuan cuti Anda': "{{ __('Riwayat dan status pengajuan cuti Anda') }}",
    '>Ajukan Cuti<': ">{{ __('Ajukan Cuti') }}<",
    'title="Sisa Cuti Tahunan"': 'title="{{ __(\'Sisa Cuti Tahunan\') }}"',
    'title="Hari Sakit (Digunakan)"': 'title="{{ __(\'Hari Sakit (Digunakan)\') }}"',
    'title="Menunggu Persetujuan"': 'title="{{ __(\'Menunggu Persetujuan\') }}"',
    '>Belum Ada Pengajuan<': ">{{ __('Belum Ada Pengajuan') }}<",
    'Anda belum pernah mengajukan cuti.': "{{ __('Anda belum pernah mengajukan cuti.') }}",
    '>Ajukan Cuti Pertama<': ">{{ __('Ajukan Cuti Pertama') }}<",
    '>Tanggal<': ">{{ __('Tanggal') }}<",
    '>Jenis<': ">{{ __('Jenis') }}<",
    '>Durasi<': ">{{ __('Durasi') }}<",
    '>Alasan<': ">{{ __('Alasan') }}<",
    '>Status<': ">{{ __('Status') }}<",
    '>Aksi<': ">{{ __('Aksi') }}<",
    '>Detail<': ">{{ __('Detail') }}<",
    ' hari<': " {{ __('hari') }}<"
}

for old, new in replacements.items():
    if old in content:
        content = content.replace(old, new)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Wrapped strings in index.blade.php")
