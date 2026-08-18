<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// আপলোড করা ছবি সার্ভ করে (persistent volume থেকে, public/ এর বাইরে)
class MediaController extends Controller
{
    public function show(string $name, UploadService $uploads): BinaryFileResponse
    {
        $path = $uploads->path($name);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type'  => $uploads->contentType($name),
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
