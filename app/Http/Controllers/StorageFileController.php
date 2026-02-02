<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageFileController extends Controller
{
    /**
     * Serve files from storage/app/public
     * Use this if symbolic links are not supported on your server
     */
    public function serve($path)
    {
        // Security: Prevent directory traversal
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Check if file exists in storage/app/public
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        // Get file path
        $filePath = Storage::disk('public')->path($path);
        
        // Determine MIME type
        $mimeType = Storage::disk('public')->mimeType($path);
        
        // Stream the file
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
