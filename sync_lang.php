<?php

$viewsPath = __DIR__ . '/resources/views';
$appPath = __DIR__ . '/app';
$enJsonPath = __DIR__ . '/lang/en.json';
$idJsonPath = __DIR__ . '/lang/id.json';
$zhJsonPath = __DIR__ . '/lang/zh.json';
$jaJsonPath = __DIR__ . '/lang/ja.json';

if (!is_dir($viewsPath)) {
    die("Views directory not found.\n");
}

$foundKeys = [];

// 1. Scan blade templates in resources/views
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsPath));
$bladeFiles = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file[0]);
    preg_match_all("/__\(\s*['\"](.+?)['\"]\s*\)/is", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $key) {
            $key = stripslashes($key);
            $foundKeys[$key] = $key;
        }
    }
}

// 2. Scan PHP files in app/ (Mail, Services, Controllers, etc.)
if (is_dir($appPath)) {
    $appIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appPath));
    $phpFiles = new RegexIterator($appIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

    foreach ($phpFiles as $file) {
        $content = file_get_contents($file[0]);
        preg_match_all("/__\(\s*['\"](.+?)['\"]\s*\)/is", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $key = stripslashes($key);
                $foundKeys[$key] = $key;
            }
        }
    }
}

function updateDictionary($path, $foundKeys) {
    $existing = [];
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $existing = json_decode($content, true) ?: [];
    }
    
    $added = 0;
    foreach ($foundKeys as $key => $val) {
        if (!isset($existing[$key])) {
            $existing[$key] = $val;
            $added++;
        }
    }
    
    // Sort keys alphabetically for neatness
    ksort($existing);
    
    file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $added;
}

$addedEn = updateDictionary($enJsonPath, $foundKeys);
$addedId = updateDictionary($idJsonPath, $foundKeys);
$addedZh = updateDictionary($zhJsonPath, $foundKeys);
$addedJa = updateDictionary($jaJsonPath, $foundKeys);

echo "Sync complete!\n";
echo "Added $addedEn new keys to en.json\n";
echo "Added $addedId new keys to id.json\n";
echo "Added $addedZh new keys to zh.json\n";
echo "Added $addedJa new keys to ja.json\n";
