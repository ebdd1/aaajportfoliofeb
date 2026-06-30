<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function showPurchases()
    {
        $user = auth()->user();

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->where('status', Order::STATUS_PAID)
            ->orderBy('paid_at', 'desc')
            ->get();

        return inertia('User/Purchases', [
            'orders' => $orders,
        ]);
    }

    public function download(Order $order, int $productId): RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_PAID) {
            abort(403, 'Order not paid');
        }

        $item = $order->items()->where('product_id', $productId)->first();

        if (!$item || !$item->product->file_path) {
            abort(404, 'File not found');
        }

        $product = $item->product;

        if (!Storage::exists($product->file_path)) {
            abort(404, 'File not found on storage');
        }

        $product->incrementDownloads();

        $filename = $product->name . '-' . $product->version . '.zip';

        try {
            $temporaryUrl = Storage::temporaryUrl(
                $product->file_path,
                now()->addMinutes(15),
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                ]
            );

            return redirect($temporaryUrl);
        } catch (\RuntimeException $e) {
            return Storage::download($product->file_path, $filename);
        }
    }
}
