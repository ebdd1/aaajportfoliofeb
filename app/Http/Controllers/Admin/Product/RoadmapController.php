<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoadmapItemRequest;
use App\Http\Requests\UpdateRoadmapItemRequest;
use App\Models\Product\Product;
use App\Models\Product\ProductRoadmapItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function index(Product $product): Response
    {
        $roadmapItems = ProductRoadmapItem::where('product_id', $product->id)
            ->orderBy('display_order')
            ->get()
            ->groupBy('status');

        $todo = $roadmapItems->get('todo', collect());
        $inProgress = $roadmapItems->get('in_progress', collect());
        $done = $roadmapItems->get('done', collect());
        $cancelled = $roadmapItems->get('cancelled', collect());

        $stats = [
            'total' => $todo->count() + $inProgress->count() + $done->count() + $cancelled->count(),
            'todo' => $todo->count(),
            'in_progress' => $inProgress->count(),
            'done' => $done->count(),
            'cancelled' => $cancelled->count(),
        ];

        return inertia('Admin/Products/Roadmap', [
            'product' => $product,
            'roadmapItems' => [
                'todo' => $todo,
                'in_progress' => $inProgress,
                'done' => $done,
                'cancelled' => $cancelled,
            ],
            'stats' => $stats,
        ]);
    }

    public function store(StoreRoadmapItemRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $data['product_id'] = $product->id;

        $maxOrder = ProductRoadmapItem::where('product_id', $product->id)
            ->where('status', $data['status'])
            ->max('display_order') ?? 0;
        $data['display_order'] = $maxOrder + 1;

        ProductRoadmapItem::create($data);

        return redirect()
            ->back()
            ->with('success', 'Item roadmap berhasil dibuat.');
    }

    public function update(UpdateRoadmapItemRequest $request, Product $product, ProductRoadmapItem $roadmap): RedirectResponse
    {
        $roadmap->update($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Item roadmap berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductRoadmapItem $roadmap): RedirectResponse
    {
        $roadmap->delete();

        return redirect()
            ->back()
            ->with('success', 'Item roadmap berhasil dihapus.');
    }

    public function updateStatus(Request $request, Product $product, ProductRoadmapItem $roadmap): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:todo,in_progress,done,cancelled',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'done' && !$roadmap->completed_at) {
            $data['completed_at'] = now();
        } elseif ($request->status !== 'done' && $roadmap->completed_at) {
            $data['completed_at'] = null;
        }

        $maxOrder = ProductRoadmapItem::where('product_id', $product->id)
            ->where('status', $request->status)
            ->where('id', '!=', $roadmap->id)
            ->max('display_order') ?? 0;
        $data['display_order'] = $maxOrder + 1;

        $roadmap->update($data);

        return redirect()
            ->back()
            ->with('success', 'Status item roadmap berhasil diperbarui.');
    }
}
