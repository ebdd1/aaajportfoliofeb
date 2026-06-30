<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStatsRequest;
use App\Models\Stat;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StatsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Stats/Edit', [
            'stats' => Stat::getSingleton(),
        ]);
    }

    public function update(UpdateStatsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $stats = Stat::getSingleton();
        $stats->update($validated);

        return redirect()->route('admin.stats.edit')->with('success', 'Statistik berhasil diperbarui.');
    }
}
