<?php

namespace App\Libraries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploader
{
    public static function handleImageUpload(UploadedFile $imageFile): ?string
    {
        if (!$imageFile instanceof UploadedFile) {
            return null;
        }

        $image = Image::read($imageFile);

        if ($image->width() > 500 || $image->height() > 500) {
            $image->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $extension = $imageFile->getClientOriginalExtension();
        $fileName = Str::random(40) . '.' . $extension;
        $filePath = 'imagens_user/' . $fileName;

        $path = StorageCustom::put($filePath, (string) $image->encode());

        return "/" . $path;
    }
}
