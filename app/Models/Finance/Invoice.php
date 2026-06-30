<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

final class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    // SECURITY: Only allow these fields to be mass-assigned
    protected $fillable = [
        'invoice_number',
        'client_name',
        'client_email',
        'client_company',
        'status',
        'issue_date',
        'due_date',
        'discount',
        'tax_percentage',
        'notes',
        'paid_at',
        'wallet_id',
        'product_order_id',
    ];

    // SECURITY: These fields are calculated server-side ONLY - never from user input
    // Kept as casts for reading from DB, but NOT in $fillable
    protected $guarded = [
        'subtotal',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('display_order');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function productOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product\ProductOrder::class, 'product_order_id');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['sent', 'overdue']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['sent'])
                    ->where('due_date', '<', Carbon::today());
            });
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('paid_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ]);
    }

    public function getFormattedNumberAttribute(): string
    {
        return $this->invoice_number;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < Carbon::today();
    }

    public static function generateNumber(): string
    {
        $year = Carbon::now()->year;
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "INV-{$year}-{$newNumber}";
    }
}
