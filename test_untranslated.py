import json

for lang in ['id', 'en', 'zh', 'ja']:
    with open(f'lang/{lang}.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
        untranslated = [k for k, v in data.items() if k == v]
        print(f"{lang}.json has {len(untranslated)} untranslated keys.")
        if lang == 'ja':
            for k in untranslated:
                print(f"  - {k}")
