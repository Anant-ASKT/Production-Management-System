<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');

$search1 = "/* =====================================================\r\n                        LARGE IMAGE\r\nfunction openLargeImage(";
$replace1 = "/* =====================================================\r\n                        LARGE IMAGE\r\n                        ====================================================== */\r\nfunction openLargeImage(";

if (strpos($content, $search1) === false) {
    // Try LF instead of CRLF
    $search1 = "/* =====================================================\n                        LARGE IMAGE\nfunction openLargeImage(";
    $replace1 = "/* =====================================================\n                        LARGE IMAGE\n                        ====================================================== */\nfunction openLargeImage(";
}

$content = str_replace($search1, $replace1, $content);

$search2 = "/* =====================================================\r\n                        ESCAPE HTML\r\n</script>";
$replace2 = "/* =====================================================\r\n                        ESCAPE HTML\r\n                        ====================================================== */\r\n</script>";

if (strpos($content, $search2) === false) {
    $search2 = "/* =====================================================\n                        ESCAPE HTML\n</script>";
    $replace2 = "/* =====================================================\n                        ESCAPE HTML\n                        ====================================================== */\n</script>";
}

$content = str_replace($search2, $replace2, $content);

file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo "Fixed comments.";
