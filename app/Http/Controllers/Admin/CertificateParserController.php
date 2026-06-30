<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CertificateParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CertificateParserController extends Controller
{
    public function __construct(
        private CertificateParserService $parser
    ) {}

    public function parse(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $path = $file->store('temp');

        try {
            $result = $this->parser->parse(storage_path('app/' . $path));

            // Log parsing attempt for debugging
            Log::info('Certificate PDF parsing attempt', [
                'filename' => $originalName,
                'result' => $result,
            ]);

            Storage::delete($path);

            // Return result (even if all null - that's OK, user can fill manually)
            return response()->json($result);
        } catch (\Exception $e) {
            Storage::delete($path);

            Log::error('Certificate PDF parsing failed', [
                'filename' => $originalName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'title' => null,
                'issuer' => null,
                'issue_date' => null,
                'credential_url' => null,
                'error' => 'Tidak dapat mengekstrak data dari PDF. ' .
                           'Pastikan file adalah PDF text-based (bukan hasil scan). ' .
                           'Isi data secara manual.',
            ], 422);
        }
    }
}
