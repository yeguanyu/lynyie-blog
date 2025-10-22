<?php
// 格式化文件大小
function format_file_size($size) {
    $units = array('B', 'KB', 'MB', 'GB');
    $unit = 0;
    while ($size >= 1024 && $unit < count($units) - 1) {
        $size /= 1024;
        $unit++;
    }
    return round($size, 2) . ' ' . $units[$unit];
}
?>