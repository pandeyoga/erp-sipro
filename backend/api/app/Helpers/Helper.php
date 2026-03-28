<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

if (!function_exists('pluckAllPermissionItems')) {
    function pluckAllPermissionItems(): array
    {
        $data = config('permissions');
        $codes = [];

        array_walk_recursive($data, function ($value, $key) use (&$codes) {
            if ($key === 'code') {
                $codes[] = $value;
            }
        });

        return $codes;
    }
}

if (!function_exists('getPermissionTitle')) {
    function getPermissionTitle(string $code): ?string
    {
        $data = config('permissions');
        
        foreach ($data as $module) {
            if (isset($module['features'])) {
                foreach ($module['features'] as $feature) {
                    foreach ($feature as $permission) {
                        if (isset($permission['code']) && $permission['code'] === $code) {
                            return $permission['label'];
                        }
                    }
                }
            }
        }
    
        return null; // Kalau tidak ditemukan
    }
}


if (!function_exists('uploadFile')) {
    /**
     * Upload file dengan optional compress jika image
     *
     * @param string $path
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $fileName
     * @return string|false
     */
    function uploadFile($path, $file, $fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
        }

        try {
            // cek apakah file adalah image
            $manager = new ImageManager(new Driver());

            if (str_starts_with($file->getMimeType(), 'image/')) {
                // baca image dari file upload
                $image = $manager->read($file->getRealPath());

                // kompres dengan mengubah kualitas (jika format mendukung)
                $extension = strtolower($file->getClientOriginalExtension());
                $quality = 15;
                
                // simpan hasil kompresi ke storage
                $encoded = match ($extension) {
                    'jpg', 'jpeg' => $image->toJpeg($quality),
                    'png' => $image->toPng(),
                    'webp' => $image->toWebp($quality),
                    default => $image->toJpeg($quality),
                };

                Storage::put($path . '/' . $fileName, (string) $encoded);
            } else {
                // kalau bukan image, upload biasa
                Storage::putFileAs($path, $file, $fileName);
            }

            $route = route("file-show", $path . '/' . $fileName);
            return parse_url($route, PHP_URL_PATH);
        } catch (\Exception $e) {
            \Log::error("Upload file gagal: " . $e->getMessage());
            return false;
        }
    }
}



if (!function_exists('deleteFile')) {
    /**
     * usage
     * deleteFile('path/to/file.ext');
     */
    function deleteFile($path)
    {
        try {
            Storage::delete($path);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}

