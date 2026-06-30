<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageUploadController extends Controller
{
    public function __construct(
        private readonly ImageManager $imageManager = new ImageManager(new Driver())
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'], // 5MB max
            'type' => ['sometimes', 'in:content,featured'],
        ]);

        $file = $request->file('image');
        $type = $request->input('type', 'content');

        $filename = $this->generateFilename($file->getClientOriginalExtension());
        $path = $type === 'featured'
            ? 'blog/featured'
            : 'blog/content';

        $fullPath = "{$path}/{$filename}";

        try {
            // Process and optimize image
            $image = $this->imageManager->read($file->getPathname());

            // Resize if too large (max 1920px width for content, 1200px for featured)
            $maxWidth = $type === 'featured' ? 1200 : 1920;
            if ($image->width() > $maxWidth) {
                $image->scale(width: $maxWidth);
            }

            // Save to storage
            Storage::disk('public')->put(
                $fullPath,
                $image->toJpg(quality: 85)
            );

            // Generate thumbnail for featured images
            if ($type === 'featured') {
                $thumbnailPath = "{$path}/thumb_{$filename}";
                $thumbnail = $this->imageManager->read($file->getPathname());
                if ($thumbnail->width() > 600) {
                    $thumbnail->scale(width: 600);
                }
                Storage::disk('public')->put(
                    $thumbnailPath,
                    $thumbnail->toJpg(quality: 80)
                );
            }

            return response()->json([
                'success' => true,
                'url' => "/storage/{$fullPath}",
                'filename' => $filename,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);

        $path = str_replace('/storage/', '', $request->input('path'));

        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan',
            ], 404);
        }

        try {
            // Delete original
            Storage::disk('public')->delete($path);

            // Delete thumbnail if exists
            $dirname = pathinfo($path, PATHINFO_DIRNAME);
            $basename = pathinfo($path, PATHINFO_BASENAME);
            $thumbnailPath = "{$dirname}/thumb_{$basename}";
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateFilename(string $extension): string
    {
        $extension = strtolower($extension);
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $extension = 'jpg';
        }

        return Str::uuid()->toString() . '.' . $extension;
    }
}
