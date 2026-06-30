<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(
        private ?string $sessionId = null,
        private ?int $userId = null
    ) {}

    public function addItem(int $productId, int $quantity = 1): Cart
    {
        $existing = $this->getItemByProduct($productId);

        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + $quantity]);
            return $existing;
        }

        return Cart::create([
            'user_id' => $this->userId,
            'session_id' => $this->sessionId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function removeItem(int $productId): bool
    {
        $item = $this->getItemByProduct($productId);
        return $item?->delete() ?? false;
    }

    public function updateQuantity(int $productId, int $quantity): ?Cart
    {
        $item = $this->getItemByProduct($productId);

        if (!$item) {
            return null;
        }

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    public function getItems(): Collection
    {
        return Cart::with('product')
            ->where(function ($query) {
                if ($this->userId) {
                    $query->where('user_id', $this->userId);
                } elseif ($this->sessionId) {
                    $query->where('session_id', $this->sessionId);
                }
            })
            ->get();
    }

    public function getCount(): int
    {
        return Cart::where(function ($query) {
            if ($this->userId) {
                $query->where('user_id', $this->userId);
            } elseif ($this->sessionId) {
                $query->where('session_id', $this->sessionId);
            }
        })->sum('quantity');
    }

    public function getTotal(): int
    {
        return $this->getItems()->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    public function clear(): int
    {
        return Cart::where(function ($query) {
            if ($this->userId) {
                $query->where('user_id', $this->userId);
            } elseif ($this->sessionId) {
                $query->where('session_id', $this->sessionId);
            }
        })->delete();
    }

    public function mergeGuestCart(int $userId): int
    {
        if (!$this->sessionId) {
            return 0;
        }

        $guestItems = Cart::where('session_id', $this->sessionId)->get();
        $merged = 0;

        foreach ($guestItems as $item) {
            $existing = Cart::where('user_id', $userId)
                ->where('product_id', $item->product_id)
                ->first();

            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $item->quantity]);
            } else {
                $item->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
            $merged++;
        }

        return $merged;
    }

    public function isInCart(int $productId): bool
    {
        return $this->getItemByProduct($productId) !== null;
    }

    private function getItemByProduct(int $productId): ?Cart
    {
        return Cart::where(function ($query) use ($productId) {
            if ($this->userId) {
                $query->where('user_id', $this->userId);
            } elseif ($this->sessionId) {
                $query->where('session_id', $this->sessionId);
            }
            $query->where('product_id', $productId);
        })->first();
    }
}
