<?php

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