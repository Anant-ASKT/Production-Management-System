<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageOptimizerService
{
    /**
     * Compress, optionally resize and save an uploaded image to the target directory.
     *
     * @param UploadedFile $file
     * @param string $targetDir Relative to public_path(), e.g. 'enhanced_images'
     * @param int $maxDimension Maximum width or height in pixels (default 2048)
     * @param int $quality JPEG/WebP quality (default 82)
     * @return string Relative path from public root, e.g. 'enhanced_images/123456_filename.jpg'
     */
    public static function optimizeAndSave(UploadedFile $file, string $targetDir = 'enhanced_images', int $maxDimension = 2048, int $quality = 82): string
    {
        @ini_set('memory_limit', '512M');

        $destinationFolder = public_path($targetDir);
        if (!is_dir($destinationFolder)) {
            @mkdir($destinationFolder, 0777, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $cleanOriginalName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        if (empty($cleanOriginalName)) {
            $cleanOriginalName = 'enhanced_' . uniqid();
        }

        $filename = time() . '_' . uniqid() . '_' . $cleanOriginalName . '.' . ($extension ?: 'jpg');
        $fullPath = $destinationFolder . DIRECTORY_SEPARATOR . $filename;
        $tempPath = $file->getRealPath();

        // Attempt GD optimization
        if (function_exists('imagecreatefromstring') && function_exists('imagecopyresampled')) {
            try {
                $imageInfo = @getimagesize($tempPath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    $mime = $imageInfo['mime'] ?? '';

                    $src = null;
                    switch ($mime) {
                        case 'image/jpeg':
                            $src = @imagecreatefromjpeg($tempPath);
                            break;
                        case 'image/png':
                            $src = @imagecreatefrompng($tempPath);
                            break;
                        case 'image/webp':
                            $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tempPath) : null;
                            break;
                    }

                    if ($src) {
                        // Calculate target dimensions
                        $newWidth = $width;
                        $newHeight = $height;

                        if ($width > $maxDimension || $height > $maxDimension) {
                            if ($width >= $height) {
                                $newWidth = $maxDimension;
                                $newHeight = (int) round(($height / $width) * $maxDimension);
                            } else {
                                $newHeight = $maxDimension;
                                $newWidth = (int) round(($width / $height) * $maxDimension);
                            }
                        }

                        $dst = imagecreatetruecolor($newWidth, $newHeight);

                        // Preserve transparency for PNG and WebP
                        if ($mime === 'image/png' || $mime === 'image/webp') {
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
                        }

                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        $saved = false;
                        if ($mime === 'image/png') {
                            // PNG compression level 0-9 (7 provides great compression)
                            $saved = @imagepng($dst, $fullPath, 7);
                        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
                            $saved = @imagewebp($dst, $fullPath, $quality);
                        } else {
                            $saved = @imagejpeg($dst, $fullPath, $quality);
                        }

                        imagedestroy($dst);
                        imagedestroy($src);

                        if ($saved && file_exists($fullPath)) {
                            return str_replace('\\', '/', $targetDir . '/' . $filename);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // In case GD error happens, fallback to direct move below
            }
        }

        // Fallback: direct move
        $file->move($destinationFolder, $filename);
        return str_replace('\\', '/', $targetDir . '/' . $filename);
    }
}
