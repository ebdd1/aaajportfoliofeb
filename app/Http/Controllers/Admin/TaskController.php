<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');
            
        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => [
                'todo' => $tasks->get('todo', []),
                'in_progress' => $tasks->get('in_progress', []),
                'done' => $tasks->get('done', []),
            ]
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['order'] = $request->input('order', 0);

        Task::create($validated);

        return back()->with('success', 'Task berhasil ditambahkan.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:todo,in_progress,done'],
            'priority' => ['sometimes', 'in:low,medium,high,critical'],
            'due_date' => ['nullable', 'date'],
            'order' => ['sometimes', 'integer'],
        ]);

        $task->update($validated);

        return back()->with('success', 'Task berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $task->update(['status' => $validated['status']]);

        return back()->with('success', 'Status task berhasil diubah.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'Task berhasil dihapus.');
    }
}