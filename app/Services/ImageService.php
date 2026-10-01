<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImageService
{
    /**
     * Images wider or taller than this (in pixels) get a thumbnail.
     */
    public const THUMBNAIL_SIZE = 600;

    private const DISK = 'uploads';

    /**
     * Store an uploaded image in the given directory and, when it is big,
     * a scaled-down thumbnail alongside it.
     *
     * @return array{image: string, thumbnail: ?string}
     */
    public function store(UploadedFile $file, string $directory): array
    {
        $image = $file->store($directory, self::DISK);

        return [
            'image' => $image,
            'thumbnail' => $this->makeThumbnail($image),
        ];
    }

    /**
     * Delete stored image files, ignoring empty paths.
     */
    public function delete(?string ...$paths): void
    {
        Storage::disk(self::DISK)->delete(array_values(array_filter($paths)));
    }

    /**
     * Create a thumbnail for the stored image if it exceeds THUMBNAIL_SIZE.
     * Returns null when the image is already small or cannot be processed.
     */
    private function makeThumbnail(string $image): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        $path = $disk->path($image);

        try {
            $type = getimagesize($path)[2] ?? null;

            $source = match ($type) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($path),
                IMAGETYPE_PNG => imagecreatefrompng($path),
                IMAGETYPE_WEBP => imagecreatefromwebp($path),
                IMAGETYPE_GIF => imagecreatefromgif($path),
                default => null,
            };

            if (! $source) {
                return null;
            }

            if ($type === IMAGETYPE_JPEG) {
                $source = $this->applyExifOrientation($source, $path);
            }

            $width = imagesx($source);
            $height = imagesy($source);

            if ($width <= self::THUMBNAIL_SIZE && $height <= self::THUMBNAIL_SIZE) {
                return null;
            }

            $ratio = min(self::THUMBNAIL_SIZE / $width, self::THUMBNAIL_SIZE / $height);
            $newWidth = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));

            $thumb = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $directory = dirname($image).'/thumbs';
            $thumbnail = $directory.'/'.basename($image);
            $disk->makeDirectory($directory);
            $target = $disk->path($thumbnail);

            $saved = match ($type) {
                IMAGETYPE_JPEG => imagejpeg($thumb, $target, 82),
                IMAGETYPE_PNG => imagepng($thumb, $target, 6),
                IMAGETYPE_WEBP => imagewebp($thumb, $target, 82),
                IMAGETYPE_GIF => imagegif($thumb, $target),
            };

            return $saved ? $thumbnail : null;
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Rotate a JPEG according to its EXIF orientation (phone photos).
     */
    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $angle = match (@exif_read_data($path)['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }
}
