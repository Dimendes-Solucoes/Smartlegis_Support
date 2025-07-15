<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageLibrary
{
    public function getDisk($disk = '')
    {
        if ($disk) {
            return $disk;
        }

        return env('FILESYSTEM_DISK') == 'local' ? 'public' : env('FILESYSTEM_DISK');
    }

    public function getTenancyPath()
    {
        $id_tenant = tenant()->id;
        return "/tenancy/tenant$id_tenant/app/public";
    }

    public function path($path, $disk)
    {
        $disk = $this->getDisk($disk);
        $base = "";

        if ($disk == 's3') {
            $base = $this->getTenancyPath();
        }

        return Storage::disk($disk)->path("$base/$path");
    }

    public function downloadFromUrl($url)
    {
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'tmp';
        $name = Str::random(40) . '.' . $extension;

        $file = file_get_contents($url);

        if ($file) {
            $success = Storage::disk('public')->put("temp/$name", $file);

            if ($success) {
                return storage_path("app/public/temp/$name");
            }
        }

        return null;
    }
}
