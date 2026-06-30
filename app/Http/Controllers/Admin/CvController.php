<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCvRequest;
use App\Models\Cv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CvController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Cv/Index', [
            'cvs' => Cv::orderByDesc('is_active')->orderByDesc('created_at')->get(),
            'activeCv' => Cv::getActive(),
        ]);
    }

    public function store(StoreCvRequest $request): RedirectResponse
    {
        $file = $request->file('cv');
        Cv::create([
            'file_path' => $file->store('cvs', 'public'),
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'is_active' => Cv::count() === 0,
        ]);

        return redirect()->route('admin.cv.index')->with('success', 'CV berhasil diupload.');
    }

    public function activate(Cv $cv): RedirectResponse
    {
        Cv::where('is_active', true)->update(['is_active' => false]);
        $cv->update(['is_active' => true]);

        return redirect()->route('admin.cv.index')->with('success', 'CV berhasil diaktifkan.');
    }

    public function destroy(Cv $cv): RedirectResponse
    {
        Storage::disk('public')->delete($cv->file_path);
        $cv->delete();

        return redirect()->route('admin.cv.index')->with('success', 'CV berhasil dihapus.');
    }
}
