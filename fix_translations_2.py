import json

ja_translations = {
    "Ajukan Cuti Pertama": "最初の休暇を申請する",
    "Anda belum pernah mengajukan cuti.": "まだ休暇を申請していません。",
    "Belum Ada Pengajuan": "申請はまだありません",
    "Hari Sakit (Digunakan)": "病気休暇 (使用済み)",
    "Menunggu Persetujuan": "承認待ち",
    "Pengajuan Cuti Saya": "私の休暇申請",
    "Riwayat dan status pengajuan cuti Anda": "休暇申請の履歴とステータス",
    "Sisa Cuti Tahunan": "年次休暇の残日数"
}

zh_translations = {
    "Ajukan Cuti Pertama": "申请第一次休假",
    "Anda belum pernah mengajukan cuti.": "您还没有申请过休假。",
    "Belum Ada Pengajuan": "尚无申请",
    "Hari Sakit (Digunakan)": "病假 (已使用)",
    "Menunggu Persetujuan": "等待批准",
    "Pengajuan Cuti Saya": "我的休假申请",
    "Riwayat dan status pengajuan cuti Anda": "您的休假申请历史和状态",
    "Sisa Cuti Tahunan": "剩余年假"
}

en_translations = {
    "Ajukan Cuti Pertama": "Apply for First Leave",
    "Anda belum pernah mengajukan cuti.": "You have never requested leave.",
    "Belum Ada Pengajuan": "No Request Yet",
    "Hari Sakit (Digunakan)": "Sick Leave (Used)",
    "Menunggu Persetujuan": "Waiting for Approval",
    "Pengajuan Cuti Saya": "My Leave Requests",
    "Riwayat dan status pengajuan cuti Anda": "History and status of your leave requests",
    "Sisa Cuti Tahunan": "Remaining Annual Leave"
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
update_lang('en', en_translations)
print("Manual translations applied successfully!")
