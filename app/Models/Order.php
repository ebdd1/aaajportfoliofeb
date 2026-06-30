<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'order_id',
        'total_amount',
        'price_locked_amount',
        'price_locked_at',
        'status',
        'payment_method',
        'payment_token',
        'payment_number',
        'expired_at',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'price_locked_amount' => 'integer',
        'price_locked_at' => 'datetime',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_id)) {
                $order->order_id = self::generateSecureOrderId();
            }
        });
    }

    /**
     * Generate secure order ID with UUID pattern: /ord/{year}/{v}/{uuid}
     * Example: /ord/2026/v1/a1b2c3d4-e5f6-7890-abcd-ef1234567890
     */
    public static function generateSecureOrderId(): string
    {
        $year = date('Y');
        $version = 'v1';
        $uuid = Str::uuid()->toString();

        return "ord/{$year}/{$version}/{$uuid}";
    }

    /**
     * Extract UUID from order_id string
     */
    public function getUuidFromOrderId(): ?string
    {
        if (preg_match('/ord\/\d{4}\/v\d+\/([a-f0-9-]+)$/', $this->order_id, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get display-friendly order ID (last 8 chars of UUID)
     */
    public function getShortOrderId(): string
    {
        $uuid = $this->getUuidFromOrderId();
        return $uuid ? substr($uuid, 0, 8) : $this->order_id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            self::STATUS_PENDING => [
                self::STATUS_PAID,
                self::STATUS_EXPIRED,
                self::STATUS_CANCELLED,
            ],
            self::STATUS_PAID => [],
            self::STATUS_CANCELLED => [],
            self::STATUS_EXPIRED => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    public function markAsPaid(string $paymentMethod): void
    {
        abort_unless($this->canTransitionTo(self::STATUS_PAID), 400, 'Invalid status transition');

        $this->update([
            'status' => self::STATUS_PAID,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        abort_unless($this->canTransitionTo(self::STATUS_EXPIRED), 400, 'Invalid status transition');

        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    public function markAsCancelled(): void
    {
        abort_unless($this->canTransitionTo(self::STATUS_CANCELLED), 400, 'Invalid status transition');

        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function lockPrice(): void
    {
        $this->update([
            'price_locked_amount' => $this->total_amount,
            'price_locked_at' => now(),
        ]);
    }

    public function formatTotal(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            default => $this->status,
        };
    }
}
