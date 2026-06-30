<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExperienceRequest;
use App\Http\Requests\UpdateExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Experiences/Index', [
            'experiences' => Experience::orderBy('display_order')->get(),
        ]);
    }

    public function store(StoreExperienceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Experience::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.experiences.index')->with('success', 'Pengalaman berhasil ditambahkan.');
    }

    public function update(UpdateExperienceRequest $request, Experience $experience): RedirectResponse
    {
        $validated = $request->validated();
        $experience->update($validated);

        return redirect()->route('admin.experiences.index')->with('success', 'Pengalaman berhasil diperbarui.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();
        return redirect()->route('admin.experiences.index')->with('success', 'Pengalaman berhasil dihapus.');
    }

    public function toggle(Experience $experience): RedirectResponse
    {
        $experience->update(['is_active' => !$experience->is_active]);
        return back()->with('success', 'Status pengalaman berhasil diubah.');
    }
}
