import json

ja_translations = {
    "-- Pilih Jenis Cuti --": "-- 休暇の種類を選択 --",
    "Audit & History": "監査と履歴",
    "Detail cuti": "休暇の詳細",
    "Info Karyawan": "従業員情報",
    "Jelaskan alasan pengajuan cuti Anda...": "休暇申請の理由を説明してください...",
    "No. Dokumen": "文書番号",
    "PPN": "消費税 (PPN)",
    "PPN 11%": "消費税 11%",
    "Pastikan informasi yang Anda masukkan sudah benar sebelum dikirim untuk diproses.": "送信して処理する前に、入力した情報が正しいことを確認してください。",
    "Riwayat pinjaman karyawan Anda": "従業員ローンの履歴",
    "Saldo cuti<br>tahun berjalan": "当年の<br>休暇残日数"
}

zh_translations = {
    "PPN": "增值税 (PPN)",
    "Periode Cuti": "休假期间",
    "WFH": "居家办公 (WFH)"
}

def update_lang(lang, updates):
    file_path = f'lang/{lang}.json'
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    for k, v in updates.items():
        if k in data:
            data[k] = v
            
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
        
update_lang('ja', ja_translations)
update_lang('zh', zh_translations)
print("Manual translations applied successfully!")
