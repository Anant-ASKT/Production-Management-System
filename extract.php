<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
preg_match('/function renderSpecifications\(.*?\).*?\}\s*\/\*.*?function handlePagination/s', $content, $matches);
file_put_contents('scratch_render.txt', $matches[0] ?? "Not found");
