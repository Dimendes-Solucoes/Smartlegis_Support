<?php

namespace App\Libraries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageCustom
{
    private static string $defaultDisk = 'public';

    public static function setDefaultDisk(string $disk): void
    {
        self::$defaultDisk = $disk;
    }

    public static function getDisk(?string $disk = null): string
    {
        if ($disk) {
            return $disk;
        }

        if (self::$defaultDisk !== 'public') {
            return self::$defaultDisk;
        }

        return env('FILESYSTEM_DISK') === 'local' ? 'public' : env('FILESYSTEM_DISK');
    }

    public static function getTenancyBasePath(): string
    {
        $tenantId = current_tenant_id() ? current_tenant_id() : 'default';
        return "tenancy/tenant{$tenantId}/app/public";
    }

    public static function upload(UploadedFile $file, string $saveTo, ?string $disk = null): object
    {
        $resolvedDisk = self::getDisk($disk);
        $basePath = self::getTenancyBasePath();

        $fullPath = "$basePath/$saveTo";

        $storedPath = Storage::disk($resolvedDisk)->putFile($fullPath, $file);

        $fileName = basename($storedPath);

        $publicLink = Storage::disk($resolvedDisk)->url($storedPath);

        return (object) [
            'link' => $publicLink,
            'file_name' => $fileName,
            'path' => $storedPath,
        ];
    }

    public static function getAbsolutePath(string $relativePath, ?string $disk = null): string
    {
        $resolvedDisk = self::getDisk($disk);
        return Storage::disk($resolvedDisk)->path($relativePath);
    }

    public static function get(string $relativePath, ?string $disk = null): ?string
    {
        $resolvedDisk = self::getDisk($disk);
        return Storage::disk($resolvedDisk)->get($relativePath);
    }

    public static function download(string $relativePath, ?string $disk = null): ?string
    {
        $resolvedDisk = self::getDisk($disk);

        if (!Storage::disk($resolvedDisk)->exists($relativePath)) {
            return null;
        }

        $content = Storage::disk($resolvedDisk)->get($relativePath);

        if ($content === null) {
            return null;
        }

        $tempDir = 'temp';
        if (!Storage::disk('public')->exists($tempDir)) {
            Storage::disk('public')->makeDirectory($tempDir);
        }

        $fileName = Str::random(40) . '.' . pathinfo($relativePath, PATHINFO_EXTENSION);
        $localPath = storage_path("app/public/{$tempDir}/{$fileName}");

        file_put_contents($localPath, $content);

        return $localPath;
    }

    public static function delete(string $relativePath, ?string $disk = null): bool
    {
        $resolvedDisk = self::getDisk($disk);
        return Storage::disk($resolvedDisk)->delete($relativePath);
    }

    public static function put(string $saveToPath, string $fileContent, ?string $disk = null): string
    {
        $resolvedDisk = self::getDisk($disk);
        $basePath = self::getTenancyBasePath();

        $fullPath = "$basePath/$saveToPath";

        $directory = dirname($fullPath);
        if (!Storage::disk($resolvedDisk)->exists($directory)) {
            Storage::disk($resolvedDisk)->makeDirectory($directory);
        }

        Storage::disk($resolvedDisk)->put($fullPath, $fileContent);

        return $fullPath;
    }

    public static function putFileAs(string $saveToDir, UploadedFile $file, string $fileName, ?string $disk = null): string
    {
        $resolvedDisk = self::getDisk($disk);
        $basePath = self::getTenancyBasePath();

        $fullDirPath = "$basePath/$saveToDir";

        if (!Storage::disk($resolvedDisk)->exists($fullDirPath)) {
            Storage::disk($resolvedDisk)->makeDirectory($fullDirPath);
        }

        $storedPath = Storage::disk($resolvedDisk)->putFileAs($fullDirPath, $file, $fileName);

        return $storedPath;
    }
}
