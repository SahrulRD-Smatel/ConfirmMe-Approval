<?php
$content = file_get_contents('resources/views/loans/my-loans.blade.php');
preg_match_all("/__\(\s*['\"](.*?)['\"]\s*\)/is", $content, $matches);
print_r($matches[1]);
