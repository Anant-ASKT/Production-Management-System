<?php
$originalContent = file_get_contents('resources/views/design-specifications/index.blade.php');

// Extract openSpecificationView
$startPattern = 'function openSpecificationView(';
$startIndex = strpos($originalContent, $startPattern);

if ($startIndex !== false) {
    // find the end of the function.
    // The function ends right before "/* =================================================" which precedes "VIEW SPECIFICATION" 
    $endPattern = '/* =================================================
                        VIEW';
    $endIndex = strpos($originalContent, $endPattern, $startIndex);
    
    $functionCode = substr($originalContent, $startIndex, $endIndex - $startIndex);
    
    // Now insert it into pending.blade.php just before the closing </script>
    $pendingContent = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
    
    $pendingContent = str_replace('</script>', $functionCode . "\n</script>", $pendingContent);
    
    file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $pendingContent);
    echo "Successfully extracted and injected openSpecificationView.";
} else {
    echo "Could not find openSpecificationView.";
}
