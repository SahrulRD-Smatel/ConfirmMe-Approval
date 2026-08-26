<?php

$files = ['lang/en.json', 'lang/zh.json', 'lang/ja.json'];
$errorString = 'Error 500';

$totalCleaned = 0;
foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $data = json_decode(file_get_contents($file), true);
    if (!$data) continue;
    
    $cleaned = 0;
    foreach ($data as $key => $val) {
        if (is_string($val) && strpos($val, $errorString) !== false) {
            $data[$key] = $key;
            $cleaned++;
            $totalCleaned++;
        }
    }
    
    if ($cleaned > 0) {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Cleaned $cleaned entries in $file\n";
    }
}
echo "Done! Total cleaned: $totalCleaned\n";
