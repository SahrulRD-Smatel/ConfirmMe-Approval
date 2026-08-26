import json

with open('test_output.txt', 'w', encoding='utf-8') as out:
    for lang in ['id', 'en', 'zh', 'ja']:
        with open(f'lang/{lang}.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
            matches = {k: v for k, v in data.items() if 'Riwayat pinjaman' in k}
            out.write(f"--- {lang}.json ---\n")
            for k, v in matches.items():
                out.write(f"{k}: {v}\n")
