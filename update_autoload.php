<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
$content = preg_replace('/\/\*.*We DO NOT call loadSpecifications\(\).*?\*\//s', 'loadSpecifications();', $content);
file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo "Updated auto load.";
