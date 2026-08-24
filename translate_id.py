import json
import time
import sys
from deep_translator import GoogleTranslator

def is_same(key, val):
    return key.strip().lower() == val.strip().lower()

def main():
    try:
        with open('lang/id.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
            
        translator = GoogleTranslator(source='auto', target='id')
        
        # We only want to translate if the key == value, which is true for all of them right now.
        # But to be safe, let's translate all keys that might be English.
        # Translating 'auto' -> 'id' will naturally fix English words.
        to_translate = []
        for key, val in data.items():
            if is_same(key, val):
                to_translate.append(key)
                
        print(f"Found {len(to_translate)} items to translate to Indonesian.")
        sys.stdout.flush()
        
        translated_data = data.copy()
        
        # Just to skip translating everything if we don't need to, we can use a heuristic:
        # If it's already Indonesian, translating auto -> id will return the same or similar.
        
        for i, key in enumerate(to_translate):
            retries = 3
            while retries > 0:
                try:
                    result = translator.translate(key)
                    # If it translated to something different, update it
                    # If it translated to the exact same thing, it was probably already Indonesian
                    if result and result.strip().lower() != key.strip().lower():
                        translated_data[key] = result
                    break
                except Exception as e:
                    retries -= 1
                    if retries == 0:
                        print(f"Error translating '{key}': {e}", flush=True)
                    time.sleep(2)
                
            if (i + 1) % 50 == 0:
                print(f"Processed {i + 1}/{len(to_translate)}", flush=True)
                with open('lang/id.json', 'w', encoding='utf-8') as f:
                    json.dump(translated_data, f, ensure_ascii=False, indent=4)
                    
        with open('lang/id.json', 'w', encoding='utf-8') as f:
            json.dump(translated_data, f, ensure_ascii=False, indent=4)
            
        print("ID Translation complete!", flush=True)
        
    except Exception as e:
        print(f"Failed: {e}", flush=True)

if __name__ == '__main__':
    main()
