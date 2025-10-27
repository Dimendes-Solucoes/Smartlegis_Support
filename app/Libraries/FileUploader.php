<?php

namespace App\Libraries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Libraries\StorageCustom; 

class FileUploader
{
    public static function handleUpload(UploadedFile $file, string $storagePath): array
    {
        if (!$file instanceof UploadedFile) {
            return [];
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::random(10) . '.' . $extension;
        $filePath = rtrim($storagePath, '/') . '/' . $fileName;

        $path = StorageCustom::put($filePath, $file->get());
        
        return [
            'file_path' => "/" . $path, 
            'file_name' => $originalName
        ];
    }
}