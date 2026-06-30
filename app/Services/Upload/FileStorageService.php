<?php

declare(strict_types=1);

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

final class FileStorageService
{
    private string $disk;
    private string $basePath;

    public function __construct()
    {
        $this->disk = config('upload.storage.disk', 'local');
        $this->basePath = config('upload.storage.path', 'certificates');
    }

    /**
     * Store uploaded file and return stored file info.
     */
    public function store(UploadedFile $file, string $filename): StoredFile
    {
        $datePath = date('Y/m');
        $fullPath = "{$this->basePath}/{$datePath}/{$filename}";

        // Store file
        $path = $file->storeAs(
            dirname($fullPath),
            $filename,
            $this->disk
        );

        if (!$path) {
            throw new \RuntimeException('Failed to store file.');
        }

        // Get file size
        $size = Storage::disk($this->disk)->size($path);

        return new StoredFile(
            id: (string) \Illuminate\Support\Str::uuid(),
            filename: $filename,
            originalFilename: $file->getClientOriginalName(),
            mimeType: $file->getMimeType(),
            size: $size,
            disk: $this->disk,
            path: $path,
            url: $this->getUrl($path),
            signedUrl: $this->getSignedUrl($path),
            signedUrlExpiresAt: now()->addMinutes(config('upload.signed_url.expiry_minutes', 60))
        );
    }

    /**
     * Get public URL for file.
     */
    public function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Get signed URL for private files.
     */
    public function getSignedUrl(string $path): string
    {
        if (!config('upload.signed_url.enabled', true)) {
            return $this->getUrl($path);
        }

        $expiryMinutes = config('upload.signed_url.expiry_minutes', 60);

        return Storage::disk($this->disk)->temporaryUrl(
            $path,
            now()->addMinutes($expiryMinutes)
        );
    }

    /**
     * Check if file exists.
     */
    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Delete file.
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Get file contents.
     */
    public function get(string $path): ?string
    {
        return Storage::disk($this->disk)->get($path);
    }

    /**
     * Copy file to temporary location for processing.
     */
    public function copyToTemp(string $path): string
    {
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempFile = $tempDir . '/' . basename($path) . '_' . time();

        Storage::disk($this->disk)->copy($path, $tempFile);

        return storage_path('app/' . $tempFile);
    }

    /**
     * Get storage disk name.
     */
    public function getDisk(): string
    {
        return $this->disk;
    }
}

/**
 * DTO for stored file information.
 */
final class StoredFile
{
    public function __construct(
        public readonly string $id,
        public readonly string $filename,
        public readonly string $originalFilename,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $disk,
        public readonly string $path,
        public readonly string $url,
        public readonly ?string $signedUrl,
        public readonly ?\DateTimeInterface $signedUrlExpiresAt
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_filename' => $this->originalFilename,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'size_formatted' => $this->formatBytes($this->size),
            'disk' => $this->disk,
            'path' => $this->path,
            'url' => $this->url,
            'signed_url' => $this->signedUrl,
            'signed_url_expires_at' => $this->signedUrlExpiresAt?->format('c'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
