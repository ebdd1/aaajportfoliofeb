<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCertificateRequest;
use App\Http\Requests\UpdateCertificateRequest;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CertificateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => Certificate::orderBy('display_order')->get(),
        ]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('certificates', 'public');
        }

        Certificate::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }

        if ($request->hasFile('image')) {
            if ($certificate->image_path) {
                Storage::disk('public')->delete($certificate->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('certificates', 'public');
        }

        $certificate->update($validated);

        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }
        if ($certificate->image_path) {
            Storage::disk('public')->delete($certificate->image_path);
        }
        $certificate->delete();
        return redirect()->route('admin.certificates.index')->with('success', 'Sertifikat berhasil dihapus.');
    }

    public function toggle(Certificate $certificate): RedirectResponse
    {
        $certificate->update(['is_active' => !$certificate->is_active]);
        return back()->with('success', 'Status sertifikat berhasil diubah.');
    }
}
