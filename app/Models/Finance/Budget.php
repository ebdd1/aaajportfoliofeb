<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

final class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'month',
        'amount',
        'spent',
    ];

    protected $casts = [
        'month' => 'date',
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->spent);
    }

    public function getPercentageUsedAttribute(): float
    {
        if ((float) $this->amount === 0) {
            return 0;
        }
        return min(100, ((float) $this->spent / (float) $this->amount) * 100);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('month', Carbon::now()->month)
            ->whereYear('month', Carbon::now()->year);
    }
}
