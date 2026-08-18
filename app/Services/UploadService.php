<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

// ছবি সার্ভারের local disk-এ সেভ করে (external Blob/S3 লাগে না)।
// Coolify-তে একটা persistent volume এই ডিরেক্টরিতে mount করতে হবে,
// নাহলে redeploy-এ আপলোড করা ছবি মুছে যাবে।
class UploadService
{
    private const ALLOWED = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'svg'];

    public function dir(): string
    {
        return rtrim(config('store.upload_dir'), '/\\');
    }

    /** ফাইল সেভ করে same-origin URL ফেরত দেয় */
    public function save(UploadedFile $file, string $prefix = 'img'): string
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (! in_array($ext, self::ALLOWED, true)) {
            throw new RuntimeException('এই ধরনের ফাইল আপলোড করা যাবে না');
        }

        $dir = $this->dir();

        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new RuntimeException('আপলোড ডিরেক্টরি তৈরি করা যায়নি');
        }

        $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName() ?: 'image');
        $name = sprintf('%s-%d-%s-%s', $prefix, time(), bin2hex(random_bytes(3)), $safe);

        $file->move($dir, $name);

        return route('media', ['name' => $name]);
    }

    public function path(string $name): string
    {
        return $this->dir().DIRECTORY_SEPARATOR.basename($name); // basename → path traversal ঠেকায়
    }

    public function contentType(string $name): string
    {
        return match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            'avif'        => 'image/avif',
            'svg'         => 'image/svg+xml',
            default       => 'application/octet-stream',
        };
    }
}
