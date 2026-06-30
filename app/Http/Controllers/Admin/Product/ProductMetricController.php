<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductMetricController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'metric_key' => 'required|string|max:50',
            'metric_label' => 'required|string|max:100',
            'value' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
            'recorded_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        ProductMetric::create([
            'product_id' => $product->id,
            'metric_key' => $request->metric_key,
            'metric_label' => $request->metric_label,
            'value' => $request->value,
            'unit' => $request->unit,
            'recorded_at' => Carbon::parse($request->recorded_at),
            'notes' => $request->notes,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Metric berhasil ditambahkan.');
    }

    public function destroy(Product $product, ProductMetric $metric): RedirectResponse
    {
        $metric->delete();

        return redirect()
            ->back()
            ->with('success', 'Metric berhasil dihapus.');
    }
}
