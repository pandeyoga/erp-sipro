<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    use ApiResponse;

    public function showFile($path)
    {
        $path = str_replace('..', '', $path);

        if (!Storage::exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $mimeType = Storage::mimeType($path);
        $fileContent = Storage::get($path);

        return response($fileContent, 200)
                ->header('Content-Type', $mimeType);
    }

    public function downloadFile($path)
    {
        $path = str_replace('..', '', $path);

        if (!Storage::exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return Storage::download($path);
    }

    // temp url
    public function handleTempUrl($path)
    {
        // TODO : implement temp url manually
        return $this->successResponse(null, 'api on construction');
    }
}
