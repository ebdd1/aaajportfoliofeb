<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SocialLinkController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SocialLinks/Index', [
            'socialLinks' => SocialLink::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:50'],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $maxOrder = SocialLink::max('display_order') ?? 0;
        $validated['display_order'] = $maxOrder + 1;

        SocialLink::create($validated);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link berhasil ditambahkan.');
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:50'],
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $socialLink->update($validated);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link berhasil diperbarui.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Social link berhasil dihapus.');
    }

    public function toggle(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update(['is_active' => !$socialLink->is_active]);

        return back()->with('success', 'Status social link berhasil diubah.');
    }
}