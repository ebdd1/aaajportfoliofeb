<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Upload;
use App\Services\Upload\DocumentParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOcrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 120;

    public function __construct(
        private readonly Upload $upload
    ) {}

    public function handle(DocumentParserService $parser): void
    {
        Log::info('Starting OCR processing', ['upload_id' => $this->upload->id]);

        // Update status
        $this->upload->update(['ocr_status' => 'processing']);

        try {
            // Get file path
            $filePath = Storage::disk($this->upload->disk)->path($this->upload->path);

            if (!file_exists($filePath)) {
                throw new \RuntimeException('File not found: ' . $filePath);
            }

            // Parse document
            $result = $parser->parse($filePath, $this->upload->mime_type);

            // Update upload with OCR results
            $this->upload->update([
                'ocr_status' => $result->error ? 'failed' : 'completed',
                'ocr_result' => $result->toArray(),
                'status' => 'completed',
            ]);

            Log::info('OCR processing completed', [
                'upload_id' => $this->upload->id,
                'confidence' => $result->confidence,
                'title' => $result->title,
            ]);

        } catch (\Exception $e) {
            Log::error('OCR processing failed', [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
            ]);

            $this->upload->update([
                'ocr_status' => 'failed',
                'ocr_result' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('OCR job permanently failed', [
            'upload_id' => $this->upload->id,
            'error' => $e->getMessage(),
        ]);

        $this->upload->update([
            'ocr_status' => 'failed',
            'ocr_result' => ['error' => 'OCR processing failed: ' . $e->getMessage()],
        ]);
    }

    public function tags(): array
    {
        return ['ocr', 'upload:' . $this->upload->id];
    }
}
