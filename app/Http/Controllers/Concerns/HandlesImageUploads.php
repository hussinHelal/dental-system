<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

trait HandlesImageUploads
{
    /**
     * Resizes an uploaded photo down to a sane max dimension before
     * storing it. Without this, a full-resolution phone camera photo
     * (still several MB even under our 2MB upload cap) gets downloaded
     * in full by every browser just to render a 32px list thumbnail -
     * that adds up fast once a clinic has hundreds of patient/staff/
     * item photos on file. scaleDown() only shrinks - it never enlarges
     * an already-small image.
     *
     * Best-effort: if Intervention/GD can't process the file for any
     * reason, falls back to storing the original untouched rather than
     * losing the upload.
     */
    protected function storeResizedImage(UploadedFile $file, string $directory, int $maxDimension = 1000): string
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory($directory);

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $filename = $directory.'/'.uniqid('', true).'.'.$extension;

        try {
            $image = Image::read($file->getRealPath());
            $image->scaleDown(width: $maxDimension, height: $maxDimension);
            $image->save($disk->path($filename));

            return $filename;
        } catch (\Throwable $e) {
            report($e);

            return $file->store($directory, 'public');
        }
    }
}
