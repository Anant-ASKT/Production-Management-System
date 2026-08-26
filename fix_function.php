<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
$content = str_replace('openViewModal', 'openSpecificationView', $content);
file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo 'Fixed function call.';
