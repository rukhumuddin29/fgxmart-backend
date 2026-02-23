<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // Use GD driver by default. Can be changed to Imagick if available.
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload an image, convert to AVIF, and generate optional thumbnail.
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param int|null $width
     * @param int|null $height
     * @param bool $genThumbnail
     * @return array [path, thumbnail_path]
     */
    public function upload(UploadedFile $file, string $directory, $width = 1200, $height = null, $genThumbnail = true): array
    {
        try {
            // Attempt to process and convert to AVIF
            $filename = Str::random(40) . '.avif';
            $path = $directory . '/' . $filename;

            // 1. Process Main Image
            $image = $this->manager->read($file);

            // Resize if needed (keep aspect ratio)
            if ($width || $height) {
                $image->scale($width, $height);
            }

            // Encode to AVIF
            $encoded = $image->toAvif(80);

            // Save to Disk
            Storage::disk('public')->put($path, (string)$encoded);

            $result = ['path' => $path, 'thumbnail_path' => null];

            // 2. Process Thumbnail
            if ($genThumbnail) {
                $thumbFilename = 'thumb_' . $filename;
                $thumbPath = $directory . '/thumbnails/' . $thumbFilename;

                $thumb = $this->manager->read($file);
                $thumb->cover(400, 400); // Create a 400x400 centered crop

                $thumbEncoded = $thumb->toAvif(70);

                Storage::disk('public')->put($thumbPath, (string)$thumbEncoded);
                $result['thumbnail_path'] = $thumbPath;
            }

            return $result;

        }
        catch (\Intervention\Image\Exceptions\DecoderException $e) {
            // Fallback: If decoding fails (e.g. unsupported format or GD issue), save original file directly
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(40) . '.' . $extension;
            $path = $file->storeAs($directory, $filename, 'public');

            $result = ['path' => $path, 'thumbnail_path' => null];

            if ($genThumbnail) {
                // Copy original as thumbnail fallback
                $thumbFilename = 'thumb_' . $filename;
                $thumbPath = $directory . '/thumbnails/' . $thumbFilename;
                Storage::disk('public')->copy($path, $thumbPath);
                $result['thumbnail_path'] = $thumbPath;
            }

            return $result;
        }
    }

    /**
     * Delete an image and its thumbnail if it exists.
     * 
     * @param string|null $path
     * @return void
     */
    public function delete(?string $path): void
    {
        if (!$path)
            return;

        $disk = Storage::disk('public');

        // Delete main
        if ($disk->exists($path)) {
            $disk->delete($path);
        }

        // Try to delete thumbnail
        $directory = dirname($path);
        $filename = basename($path);
        $thumbPath = $directory . '/thumbnails/thumb_' . $filename;

        if ($disk->exists($thumbPath)) {
            $disk->delete($thumbPath);
        }
    }

    /**
     * Get the explicit thumbnail path from a main image path.
     * 
     * @param string|null $path
     * @return string|null
     */
    public static function getThumbnailPath(?string $path): ?string
    {
        if (!$path)
            return null;
        $directory = dirname($path);
        $filename = basename($path);
        return $directory . '/thumbnails/thumb_' . $filename;
    }
}
