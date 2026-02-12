<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait DS_UploadHelper
 * 
 * Centralized utility for handling secure file and image uploads 
 * across the DropSaaS ecosystem.
 */
trait DS_UploadHelper
{
    /**
     * Upload a file to a specified directory.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFile
     * @return string Path to the uploaded file.
     */
    public function uploadFile(UploadedFile $file, string $directory, ?string $oldFile = null): string
    {
        if (!empty($oldFile) && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(20) . '.' . $extension;
        
        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Upload an image with optional resizing.
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param int|null $width
     * @param int|null $height
     * @param string|null $oldFile
     * @return string Path to the uploaded file.
     */
    public function uploadImage(UploadedFile $file, string $directory, ?int $width = null, ?int $height = null, ?string $oldFile = null): string
    {
        $this->deleteFile($oldFile);

        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(20) . '.' . $extension;
        $path = $directory . '/' . $fileName;

        // Fallback: If no resize needed, or if drivers are missing, just store the file
        if ((!$width && !$height) || (!extension_loaded('gd') && !extension_loaded('imagick'))) {
            return $file->storeAs($directory, $fileName, 'public');
        }

        try {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);

            if ($width && $height) {
                $image->cover($width, $height);
            } elseif ($width || $height) {
                $image->scale($width, $height);
            }

            Storage::disk('public')->put($path, (string) $image->encodeByExtension($extension));

            return $path;
        } catch (\Throwable $e) {
            // Fallback on error (e.g. invalid image format or driver memory issue)
            report($e); // Log the error for admin review
            return $file->storeAs($directory, $fileName, 'public');
        }
    }

    /**
     * Delete a file from storage safely.
     *
     * @param string|null $path
     * @return void
     */
    public function deleteFile(?string $path): void
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
