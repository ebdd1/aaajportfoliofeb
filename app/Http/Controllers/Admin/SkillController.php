<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Skills/Index', [
            'skills' => Skill::orderBy('display_order')->get(),
        ]);
    }

    public function store(StoreSkillRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tags'] = $request->input('tags');
        Skill::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.skills.index')->with('success', 'Skill berhasil ditambahkan.');
    }

    public function update(UpdateSkillRequest $request, Skill $skill): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tags'] = $request->input('tags');
        $skill->update($validated);

        return redirect()->route('admin.skills.index')->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();
        return redirect()->route('admin.skills.index')->with('success', 'Skill berhasil dihapus.');
    }

    public function toggle(Skill $skill): RedirectResponse
    {
        $skill->update(['is_active' => !$skill->is_active]);
        return back()->with('success', 'Status skill berhasil diubah.');
    }
}
