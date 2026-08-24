import json
import time
import sys
from deep_translator import GoogleTranslator

def is_same(key, val):
    return key.strip().lower() == val.strip().lower()

def main():
    try:
        with open('lang/zh.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
            
        translator = GoogleTranslator(source='auto', target='zh-CN')
        
        to_translate = []
        for key, val in data.items():
            if is_same(key, val):
                to_translate.append(key)
                
        print(f"Found {len(to_translate)} items to translate to Chinese.")
        sys.stdout.flush()
        
        translated_data = data.copy()
        
        for i, key in enumerate(to_translate):
            retries = 3
            while retries > 0:
                try:
                    result = translator.translate(key)
                    if result:
                        translated_data[key] = result
                    break
                except Exception as e:
                    retries -= 1
                    if retries == 0:
                        print(f"Error translating '{key}': {e}", flush=True)
                    time.sleep(2)
                
            if (i + 1) % 50 == 0:
                print(f"Processed {i + 1}/{len(to_translate)}", flush=True)
                with open('lang/zh.json', 'w', encoding='utf-8') as f:
                    json.dump(translated_data, f, ensure_ascii=False, indent=4)
                    
        with open('lang/zh.json', 'w', encoding='utf-8') as f:
            json.dump(translated_data, f, ensure_ascii=False, indent=4)
            
        print("Chinese Translation complete!", flush=True)
        
    except Exception as e:
        print(f"Failed: {e}", flush=True)

if __name__ == '__main__':
    main()
