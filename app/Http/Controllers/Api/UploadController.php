<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use App\Jobs\ProcessOcrJob;
use App\Models\Upload;
use App\Services\Upload\DocumentParserService;
use App\Services\Upload\FileStorageService;
use App\Services\Upload\FileValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function __construct(
        private readonly FileValidationService $validator,
        private readonly FileStorageService $storage,
        private readonly DocumentParserService $parser
    ) {}

    /**
     * Upload file.
     *
     * @OA\Post(
     *     path="/api/upload",
     *     summary="Upload a file",
     *     tags={"Upload"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="file", description="File to upload"),
     *                 @OA\Property(property="type", type="string", enum={"certificate", "avatar", "document"})
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Upload successful"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(UploadFileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $uploadType = $request->input('type', 'certificate');

        // Validate file
        $validationResult = $this->validator->validate($file);

        if (!$validationResult->isValid()) {
            Log::warning('File validation failed', [
                'filename' => $file->getClientOriginalName(),
                'error' => $validationResult->toArray(),
            ]);

            return response()->json($validationResult->toArray(), 422);
        }

        // Generate secure filename
        $filename = $this->validator->generateSecureFilename($file);
        $originalFilename = $this->validator->sanitizeFilename($file->getClientOriginalName());
        $fileHash = $this->validator->getFileHash($file);

        try {
            // Store file
            $storedFile = $this->storage->store($file, $filename);

            // Create upload record
            $upload = Upload::create([
                'filename' => $storedFile->filename,
                'original_filename' => $originalFilename,
                'mime_type' => $storedFile->mimeType,
                'file_size' => $storedFile->size,
                'file_hash' => $fileHash,
                'disk' => $storedFile->disk,
                'path' => $storedFile->path,
                'upload_type' => $uploadType,
                'user_id' => $request->user()?->id,
                'ip_address' => $request->ip(),
                'status' => 'processing',
            ]);

            // Dispatch OCR job if needed
            if ($uploadType === 'certificate' && config('upload.ocr.enabled', true)) {
                ProcessOcrJob::dispatch($upload);
            }

            Log::info('File uploaded successfully', [
                'upload_id' => $upload->id,
                'filename' => $originalFilename,
                'size' => $storedFile->size,
            ]);

            return response()->json([
                'success' => true,
                'data' => array_merge($storedFile->toArray(), [
                    'id' => $upload->id,
                    'ocr_status' => $uploadType === 'certificate' ? 'pending' : null,
                ]),
            ]);

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'filename' => $originalFilename,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to store file. Please try again.',
                ],
            ], 500);
        }
    }

    /**
     * Get upload status and OCR results.
     */
    public function show(Upload $upload): JsonResponse
    {
        $this->authorize('view', $upload);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $upload->id,
                'filename' => $upload->filename,
                'original_filename' => $upload->original_filename,
                'mime_type' => $upload->mime_type,
                'size' => $upload->file_size,
                'size_formatted' => $upload->file_size_formatted,
                'status' => $upload->status,
                'ocr_status' => $upload->ocr_status,
                'ocr_result' => $upload->ocr_result,
                'created_at' => $upload->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Delete an upload.
     */
    public function destroy(Upload $upload): JsonResponse
    {
        $this->authorize('delete', $upload);

        try {
            // Delete physical file
            $this->storage->delete($upload->path);

            // Delete record
            $upload->delete();

            Log::info('File deleted', ['upload_id' => $upload->id]);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'upload_id' => $upload->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to delete file.',
                ],
            ], 500);
        }
    }

    /**
     * Get signed URL for file access.
     */
    public function signedUrl(Upload $upload): JsonResponse
    {
        $this->authorize('view', $upload);

        $signedUrl = $this->storage->getSignedUrl($upload->path);
        $expiresAt = now()->addMinutes(config('upload.signed_url.expiry_minutes', 60));

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $signedUrl,
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ]);
    }
}
