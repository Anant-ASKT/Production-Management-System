<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
$content = str_replace(
    "if (typeof openSpecificationView === 'function') {", 
    "console.log('View button clicked', typeof openSpecificationView); if (typeof openSpecificationView === 'function') {", 
    $content
);
$content = str_replace(
    "openSpecificationView(specification);", 
    "openSpecificationView(specification);\n                            } else {\n                                console.error('openSpecificationView is not defined as a function!', openSpecificationView);", 
    $content
);
file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo 'Added debug logs';
