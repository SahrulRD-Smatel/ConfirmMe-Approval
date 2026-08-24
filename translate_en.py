import json
import time
import sys
from deep_translator import GoogleTranslator

def is_same(key, val):
    return key.strip().lower() == val.strip().lower()

def main():
    try:
        with open('lang/en.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
            
        translator = GoogleTranslator(source='id', target='en')
        
        # Collect keys that need translation
        to_translate = []
        for key, val in data.items():
            if is_same(key, val):
                to_translate.append(key)
                
        print(f"Found {len(to_translate)} items to translate.")
        sys.stdout.flush()
        
        # Translate in small batches to avoid timeouts
        translated_data = data.copy()
        for i, key in enumerate(to_translate):
            try:
                # Basic protection for variables like :attribute
                result = translator.translate(key)
                translated_data[key] = result
                
                if (i + 1) % 20 == 0:
                    print(f"Translated {i + 1}/{len(to_translate)}", flush=True)
                    # Save progress
                    with open('lang/en.json', 'w', encoding='utf-8') as f:
                        json.dump(translated_data, f, ensure_ascii=False, indent=4)
                        
            except Exception as e:
                print(f"Error translating '{key}': {e}", flush=True)
                time.sleep(1)
                
        # Final save
        with open('lang/en.json', 'w', encoding='utf-8') as f:
            json.dump(translated_data, f, ensure_ascii=False, indent=4)
            
        print("Translation complete!", flush=True)
        
    except Exception as e:
        print(f"Failed: {e}", flush=True)

if __name__ == '__main__':
    main()
