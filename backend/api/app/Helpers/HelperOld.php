<?php

use Illuminate\Support\Facades\Storage;

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
     * usage
     * uploadFile('path/to/file', $request->file('file'), 'custom_name.jpg');
     */
    function uploadFile($path, $file, $fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
        }

        try {
            Storage::putFileAs($path, $file, $fileName);
            $route = route("file-show", $path . '/' . $fileName);
            $relativePath = parse_url($route, PHP_URL_PATH);
            return $relativePath;
        } catch (\Exception $e) {
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

