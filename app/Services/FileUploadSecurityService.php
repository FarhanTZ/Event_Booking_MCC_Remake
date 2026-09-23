<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileUploadSecurityService
{
    /**
     * Allowed image MIME types and signatures.
     */
    protected const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
    ];

    /**
     * Securely store an image file with sanitization and safe random naming.
     *
     * @throws ValidationException
     */
    public static function storeImage(UploadedFile $file, string $directory = 'logos'): string
    {
        $mimeType = $file->getMimeType();

        // 1. Validasi MIME Type Asli (Bukan cuma ekstensi)
        if (! in_array($mimeType, self::ALLOWED_IMAGE_MIMES, true)) {
            SecurityAuditLogger::logBlockedFileUpload($file->getClientOriginalName(), $mimeType);
            throw ValidationException::withMessages([
                'logo' => ['Format file tidak diizinkan. Hanya file JPG, JPEG, atau PNG asli yang diperbolehkan.'],
            ]);
        }

        // 2. Cegah double-extension attack (contoh: shell.php.png)
        $extension = match ($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            default => 'png'
        };

        // 3. Generate nama file unik dan aman dengan random hash UUID
        $safeFileName = Str::random(40) . '.' . $extension;

        // 4. Simpan ke storage publik
        return $file->storeAs($directory, $safeFileName, 'public');
    }
}
