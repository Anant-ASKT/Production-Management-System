<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');

$startContext = strpos($content, '<div class="context-card');
if ($startContext !== false) {
    // Find the end of this context card which has 3 inner divs.
    // It's easier to use a regex to strip the whole block.
    $content = preg_replace('/<div class="context-card mb-4".*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', '', $content, 1);
}

// Since the user might be referring to the controller fetching them unnecessarily, I will also remove them from the controller if they exist there and are unused.
// But the view is the main thing.
file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo "Context card removed.";
