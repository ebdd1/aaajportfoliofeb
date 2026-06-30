<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, $orderId): JsonResponse
    {
        $order = Order::with('items.product')->where('order_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Create a timeline based on dates
        $timeline = [
            [
                'status' => 'pending',
                'label' => 'Menunggu Pembayaran',
                'description' => 'Pesanan telah dibuat',
                'date' => $order->created_at,
                'completed' => true
            ]
        ];

        if ($order->paid_at) {
            $timeline[] = [
                'status' => 'paid',
                'label' => 'Pembayaran Berhasil',
                'description' => 'Pembayaran telah dikonfirmasi (' . ($order->payment_method ?? 'Pakasir') . ')',
                'date' => $order->paid_at,
                'completed' => true
            ];
            
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Selesai',
                'description' => 'Produk siap diunduh',
                'date' => $order->paid_at,
                'completed' => true
            ];
        } else if ($order->status === 'cancelled') {
            $timeline[] = [
                'status' => 'cancelled',
                'label' => 'Dibatalkan',
                'description' => 'Pesanan telah dibatalkan',
                'date' => $order->updated_at,
                'completed' => true
            ];
        } else if ($order->status === 'expired') {
            $timeline[] = [
                'status' => 'expired',
                'label' => 'Kedaluwarsa',
                'description' => 'Waktu pembayaran telah habis',
                'date' => $order->expired_at,
                'completed' => true
            ];
        } else {
             $timeline[] = [
                'status' => 'paid',
                'label' => 'Pembayaran',
                'description' => 'Menunggu konfirmasi pembayaran',
                'date' => null,
                'completed' => false
            ];
        }

        return response()->json([
            'order' => $order,
            'timeline' => $timeline
        ]);
    }
}
