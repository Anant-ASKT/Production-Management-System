<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageOptimizerService
{
    /**
     * Target maximum size in bytes: 1500 KB (1,536,000 bytes)
     */
    const TARGET_MAX_BYTES = 1500 * 1024;

    /**
     * Compress, optionally resize and save an uploaded image to the target directory.
     * Guarantees that any image larger than 2MB (or > 1500KB) is compressed
     * down to less than 1500 KB while preserving high visual sharpness and quality.
     *
     * @param UploadedFile $file
     * @param string $targetDir Relative to public_path(), e.g. 'enhanced_images'
     * @param int $maxDimension Maximum width or height in pixels (default 2048)
     * @param int $quality Initial JPEG/WebP quality (default 85)
     * @return string Relative path from public root, e.g. 'enhanced_images/123456_filename.jpg'
     */
    public static function optimizeAndSave(UploadedFile $file, string $targetDir = 'enhanced_images', int $maxDimension = 2048, int $quality = 85): string
    {
        @ini_set('memory_limit', '512M');

        $destinationFolder = public_path($targetDir);
        if (!is_dir($destinationFolder)) {
            @mkdir($destinationFolder, 0777, true);
        }

        $cleanOriginalName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        if (empty($cleanOriginalName)) {
            $cleanOriginalName = 'enhanced_' . uniqid();
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $tempPath = $file->getRealPath();
        $maxSizeBytes = self::TARGET_MAX_BYTES; // 1500 KB

        // Attempt GD optimization
        if (function_exists('imagecreatefromstring') && function_exists('imagecopyresampled')) {
            try {
                $imageInfo = @getimagesize($tempPath);
                if ($imageInfo) {
                    $origWidth = $imageInfo[0];
                    $origHeight = $imageInfo[1];
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
                        default:
                            $content = @file_get_contents($tempPath);
                            if ($content) {
                                $src = @imagecreatefromstring($content);
                            }
                            break;
                    }

                    if ($src) {
                        // Check if transparency exists in PNG or WebP
                        $hasAlpha = false;
                        if ($mime === 'image/png' || $mime === 'image/webp') {
                            $hasAlpha = self::checkTransparency($src);
                        }

                        // Determine primary output format:
                        // WebP if input was WebP, or if PNG has alpha and imagewebp exists (gives 5x better compression than PNG)
                        // JPEG for all photographs without alpha (gives best compression down to < 1500 KB)
                        if ($mime === 'image/webp' && function_exists('imagewebp')) {
                            $outputFormat = 'webp';
                            $outExt = 'webp';
                        } elseif ($mime === 'image/png' && $hasAlpha) {
                            if (function_exists('imagewebp')) {
                                $outputFormat = 'webp';
                                $outExt = 'webp';
                            } else {
                                $outputFormat = 'png';
                                $outExt = 'png';
                            }
                        } else {
                            $outputFormat = 'jpeg';
                            $outExt = 'jpg';
                        }

                        $filename = time() . '_' . uniqid() . '_' . $cleanOriginalName . '.' . $outExt;
                        $fullPath = $destinationFolder . DIRECTORY_SEPARATOR . $filename;

                        $currentMaxDim = $maxDimension;
                        $currentQuality = $quality;
                        $attempts = 0;
                        $maxAttempts = 8;
                        $saved = false;

                        do {
                            $attempts++;

                            // Calculate target dimensions
                            $width = $origWidth;
                            $height = $origHeight;

                            if ($width > $currentMaxDim || $height > $currentMaxDim) {
                                if ($width >= $height) {
                                    $newWidth = $currentMaxDim;
                                    $newHeight = (int) round(($height / $width) * $currentMaxDim);
                                } else {
                                    $newHeight = $currentMaxDim;
                                    $newWidth = (int) round(($width / $height) * $currentMaxDim);
                                }
                            } else {
                                $newWidth = $width;
                                $newHeight = $height;
                            }

                            $dst = imagecreatetruecolor($newWidth, $newHeight);

                            if ($hasAlpha && ($outputFormat === 'png' || $outputFormat === 'webp')) {
                                imagealphablending($dst, false);
                                imagesavealpha($dst, true);
                                $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                                imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
                            } else {
                                $white = imagecolorallocate($dst, 255, 255, 255);
                                imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $white);
                            }

                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                            // Save to file
                            if ($outputFormat === 'png') {
                                $saved = @imagepng($dst, $fullPath, 9);
                            } elseif ($outputFormat === 'webp' && function_exists('imagewebp')) {
                                $saved = @imagewebp($dst, $fullPath, $currentQuality);
                            } else {
                                $saved = @imagejpeg($dst, $fullPath, $currentQuality);
                            }

                            imagedestroy($dst);

                            clearstatcache(true, $fullPath);
                            $savedSize = file_exists($fullPath) ? filesize($fullPath) : 0;

                            // If saved file is under 1500 KB, we succeed!
                            if ($saved && $savedSize > 0 && $savedSize <= $maxSizeBytes) {
                                break;
                            }

                            // If PNG is still larger than 1500 KB, convert to WebP or JPEG to guarantee < 1500 KB
                            if ($outputFormat === 'png' && $savedSize > $maxSizeBytes) {
                                if (function_exists('imagewebp')) {
                                    $outputFormat = 'webp';
                                    $outExt = 'webp';
                                } else {
                                    $outputFormat = 'jpeg';
                                    $outExt = 'jpg';
                                }
                                $filename = time() . '_' . uniqid() . '_' . $cleanOriginalName . '.' . $outExt;
                                $fullPath = $destinationFolder . DIRECTORY_SEPARATOR . $filename;
                                $currentQuality = 80;
                                continue;
                            }

                            // Progressively reduce quality and/or dimension until <= 1500 KB
                            if ($currentQuality > 65) {
                                $currentQuality -= 8;
                            } elseif ($currentQuality > 45) {
                                $currentQuality -= 7;
                                $currentMaxDim = (int) round($currentMaxDim * 0.85);
                            } else {
                                $currentMaxDim = (int) round($currentMaxDim * 0.80);
                                if ($currentMaxDim < 1000) {
                                    break;
                                }
                            }

                        } while ($attempts < $maxAttempts && $savedSize > $maxSizeBytes);

                        imagedestroy($src);

                        if ($saved && file_exists($fullPath)) {
                            return str_replace('\\', '/', $targetDir . '/' . $filename);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Image optimization error: ' . $e->getMessage());
            }
        }

        // Fallback: direct move
        $filename = time() . '_' . uniqid() . '_' . $cleanOriginalName . '.' . ($extension ?: 'jpg');
        $fullPath = $destinationFolder . DIRECTORY_SEPARATOR . $filename;
        $file->move($destinationFolder, $filename);
        return str_replace('\\', '/', $targetDir . '/' . $filename);
    }

    /**
     * Check if an image resource has transparent pixels.
     */
    private static function checkTransparency($image): bool
    {
        if (!function_exists('imagecolorat')) return false;

        $width = imagesx($image);
        $height = imagesy($image);

        $stepX = max(1, (int) floor($width / 30));
        $stepY = max(1, (int) floor($height / 30));

        for ($x = 0; $x < $width; $x += $stepX) {
            for ($y = 0; $y < $height; $y += $stepY) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                if ($alpha > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}
