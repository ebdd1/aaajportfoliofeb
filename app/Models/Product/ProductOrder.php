<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

final class ProductOrder extends Model
{
    use HasFactory;

    protected $table = 'portfolio_product_orders';

    protected $fillable = [
        'product_id',
        'client_name',
        'client_email',
        'client_phone',
        'client_company',
        'status',
        'notes',
        'quoted_price',
        'agreed_price',
        'due_date',
        'invoice_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'quoted_price' => 'decimal:2',
        'agreed_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Finance\Invoice::class);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Baru',
            'in_discussion' => 'Dalam Diskusi',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }
}
