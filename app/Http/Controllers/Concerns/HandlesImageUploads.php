<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

trait HandlesImageUploads
{
    public function storeResizedImage(UploadedFile $file, string $directory, int $maxDimension = 800): string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $filename  = $directory.'/'.uniqid('', true).'.'.$extension;

        try {
            $image = Image::read($file->getRealPath());
            /* BUG FIX: limit BOTH width and height, not just width */
            $image->scaleDown(width: $maxDimension, height: $maxDimension);
            $image->save($disk->path($filename));

            return $filename;
        } catch (\Throwable $e) {
            report($e);
            return $file->store($directory, 'public');
        }
    }
}