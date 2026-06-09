<?php

namespace App\Services;

use App\Models\LandlordVerificationDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SecureDocumentService
{
    public function store(UploadedFile $file, string $directory, string $type): array
    {
        $disk = config('security.document_disk', 'private_documents');
        $path = $file->store($directory, ['disk' => $disk, 'visibility' => 'private']);

        return [
            'document_type' => $type,
            'disk' => $disk,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'sha256_checksum' => hash_file('sha256', $file->getRealPath()),
            'status' => 'pending',
        ];
    }

    public function temporaryViewUrl(LandlordVerificationDocument $document, array $signedRouteParameters = []): string
    {
        $ttl = now()->addSeconds((int) config('security.presigned_url_ttl_seconds', 300));
        $disk = $document->disk ?: config('security.document_disk', 'private_documents');

        if (method_exists(Storage::disk($disk), 'temporaryUrl')) {
            try {
                return Storage::disk($disk)->temporaryUrl(
                    $document->file_path,
                    $ttl,
                    ['ResponseContentDisposition' => 'inline; filename="' . ($document->original_filename ?: basename($document->file_path)) . '"']
                );
            } catch (\Throwable) {
                // Local disks use the signed streaming route below during development.
            }
        }

        return URL::temporarySignedRoute('admin.verification-documents.stream', $ttl, array_merge([
            'document' => $document->id,
        ], $signedRouteParameters));
    }
}
