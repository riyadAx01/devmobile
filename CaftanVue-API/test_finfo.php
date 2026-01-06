<?php
$filePath = 'c:/Users/UltraPc/Desktop/MOBILE/CaftanVue-API/uploads/caftans/caftan_wedding_gold_1766001024860.png';
if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

echo "Testing fileinfo on $filePath...\n";
$start = microtime(true);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filePath);
finfo_close($finfo);
$end = microtime(true);

echo "MIME type: $mime\n";
echo "Time taken: " . ($end - $start) . "s\n";
?>
