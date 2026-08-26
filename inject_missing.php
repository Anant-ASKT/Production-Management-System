<?php
$original = file_get_contents('resources/views/design-specifications/index.blade.php');

// Extract getSpecificationImage
preg_match('/function getSpecificationImage\(.*?\)(.*?)\/\* =====================================================\s*LARGE IMAGE/s', $original, $imgMatch);
$getSpecificationImageFn = $imgMatch[0] ?? '';

// Extract openLargeImage
preg_match('/function openLargeImage\(.*?\)(.*?)\/\* =====================================================\s*ESCAPE HTML/s', $original, $largeImgMatch);
$openLargeImageFn = $largeImgMatch[0] ?? '';

if ($getSpecificationImageFn && $openLargeImageFn) {
    $pending = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');
    $pending = str_replace('</script>', $getSpecificationImageFn . "\n" . $openLargeImageFn . "\n</script>", $pending);
    file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $pending);
    echo "Successfully injected getSpecificationImage and openLargeImage.";
} else {
    echo "Failed to extract functions.";
}
