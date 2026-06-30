<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

// Load YAML config if exists, otherwise use PHP config
$yamlConfigPath = base_path('config.yaml');
$yamlConfig = [];

if (file_exists($yamlConfigPath)) {
    $yamlConfig = Yaml::parseFile($yamlConfigPath);
}

$uploadConfig = $yamlConfig['upload'] ?? [];

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | Konfigurasi whitelist extension yang diizinkan.
    | Tidak boleh hardcode - harus dari config.
    |
    */
    'allowed_types' => $uploadConfig['allowed_types'] ?? [
        'pdf',
        'jpeg',
        'jpg',
        'png',
        'webp',
        'heic',
        'docx',
        'zip',
    ],

    /*
    |--------------------------------------------------------------------------
    | MIME Type Mapping
    |--------------------------------------------------------------------------
    |
    | Mapping dari extension ke MIME type yang valid.
    |
    */
    'mime_types' => $uploadConfig['mime_types'] ?? [
        'pdf' => 'application/pdf',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'heic' => 'image/heic',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'zip' => 'application/zip',
    ],

    /*
    |--------------------------------------------------------------------------
    | Magic Number Signatures
    |--------------------------------------------------------------------------
    |
    | Hex signatures untuk validasi file (authoritative).
    | Magic number adalah cara paling aman untuk validasi file type.
    |
    */
    'magic_numbers' => $uploadConfig['magic_numbers'] ?? [
        'pdf' => ['25 50 44 46'],           // %PDF
        'jpeg' => ['FF D8 FF DB', 'FF D8 FF E0', 'FF D8 FF E1', 'FF D8 FF E2', 'FF D8 FF E3', 'FF D8 FF E8'],
        'jpg' => ['FF D8 FF DB', 'FF D8 FF E0', 'FF D8 FF E1', 'FF D8 FF E2', 'FF D8 FF E3', 'FF D8 FF E8'],
        'png' => ['89 50 4E 47 0D 0A 1A 0A'],
        'webp' => ['52 49 46 46'],
        'heic' => ['66 74 79 70 68 65 69 63', '66 74 79 70 6D 69 66 31'], // ftypheic, ftypmif1
        'docx' => ['50 4B 03 04'],  // Same as ZIP, differentiate by extension
        'zip' => ['50 4B 03 04'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum File Sizes (bytes)
    |--------------------------------------------------------------------------
    |
    | Batas ukuran per tipe file.
    |
    */
    'max_sizes' => $uploadConfig['max_sizes'] ?? [
        'pdf' => 10 * 1024 * 1024,        // 10MB
        'jpeg' => 5 * 1024 * 1024,        // 5MB
        'jpg' => 5 * 1024 * 1024,          // 5MB
        'png' => 5 * 1024 * 1024,           // 5MB
        'webp' => 5 * 1024 * 1024,         // 5MB
        'heic' => 5 * 1024 * 1024,         // 5MB
        'docx' => 10 * 1024 * 1024,       // 10MB
        'zip' => 50 * 1024 * 1024,        // 50MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Maximum Upload Size
    |--------------------------------------------------------------------------
    |
    | Batas maksimal untuk semua file upload.
    |
    */
    'global_max_size' => $uploadConfig['global_max_size'] ?? 50 * 1024 * 1024, // 50MB

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi storage driver.
    |
    */
    'storage' => [
        'disk' => env('UPLOAD_DISK', 'local'),  // local, s3, r2, minio, azure, gcs

        'path' => $uploadConfig['storage']['path'] ?? 'certificates',

        'local' => [
            'root' => storage_path('app/private/certificates'),
        ],

        's3' => [
            'bucket' => env('AWS_BUCKET_CERTIFICATES'),
            'region' => env('AWS_DEFAULT_REGION'),
            'visibility' => 'private',
        ],

        'cloudflare_r2' => [
            'bucket' => env('CLOUDFLARE_R2_CERTIFICATES_BUCKET'),
            'account_id' => env('CLOUDFLARE_R2_ACCOUNT_ID'),
            'visibility' => 'private',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Signed URL Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk temporary signed URLs.
    |
    */
    'signed_url' => [
        'enabled' => $uploadConfig['signed_url']['enabled'] ?? true,
        'expiry_minutes' => $uploadConfig['signed_url']['expiry_minutes'] ?? 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | OCR Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk OCR processing.
    |
    */
    'ocr' => [
        'enabled' => $uploadConfig['ocr']['enabled'] ?? true,
        'queue' => $uploadConfig['ocr']['queue'] ?? 'ocr-process',
        'timeout' => $uploadConfig['ocr']['timeout'] ?? 120,
        'languages' => $uploadConfig['ocr']['languages'] ?? ['eng', 'ind'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Virus Scan Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk virus scanning.
    |
    */
    'virus_scan' => [
        'enabled' => $uploadConfig['virus_scan_enabled'] ?? false,
        'queue' => 'virus-scan',
        'timeout' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Batas rate limiting untuk upload endpoint.
    |
    */
    'rate_limit' => [
        'per_minute' => $uploadConfig['rate_limit']['per_minute'] ?? 10,
        'per_hour' => $uploadConfig['rate_limit']['per_hour'] ?? 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan keamanan upload.
    |
    */
    'security' => [
        'sanitize_filename' => true,
        'generate_uuid_filename' => true,
        'strip_exif' => true,
        'block_polyglot' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk preview generation.
    |
    */
    'preview' => [
        'enabled' => true,
        'max_dimension' => 800,
        'quality' => 85,
        'thumbnail_size' => 200,
    ],
];
