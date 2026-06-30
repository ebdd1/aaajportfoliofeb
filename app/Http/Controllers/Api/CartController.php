<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->initService($request);
        $items = $this->cartService->getItems();

        return response()->json([
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $item->product->price,
                    'thumbnail' => $item->product->thumbnail,
                ],
            ]),
            'count' => $this->cartService->getCount(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1|max:10',
        ]);

        $this->initService($request);

        // Check if product already in cart
        if ($this->cartService->isInCart($validated['product_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di keranjang',
            ], 422);
        }

        $item = $this->cartService->addItem(
            $validated['product_id'],
            $validated['quantity'] ?? 1
        );

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'item' => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ],
            'count' => $this->cartService->getCount(),
        ]);
    }

    public function update(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $this->initService($request);
        $item = $this->cartService->updateQuantity($productId, $validated['quantity']);

        if ($validated['quantity'] == 0 || $item === null) {
            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari keranjang',
                'count' => $this->cartService->getCount(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jumlah diperbarui',
            'item' => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ],
            'count' => $this->cartService->getCount(),
        ]);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        $this->initService($request);
        $this->cartService->removeItem($productId);

        return response()->json([
            'success' => true,
            'message' => 'Produk dihapus dari keranjang',
            'count' => $this->cartService->getCount(),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $this->initService($request);
        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang dikosongkan',
            'count' => 0,
        ]);
    }

    private function initService(Request $request): void
    {
        $sessionId = $request->session()->getId();
        $userId = $request->user()?->id;

        $this->cartService = new CartService($sessionId, $userId);
    }
}