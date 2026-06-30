<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'wallet_id',
        'icon',
        'color',
        'is_achieved',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'is_achieved' => 'boolean',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ((float) $this->target_amount === 0) {
            return 0;
        }
        return min(100, ((float) $this->current_amount / (float) $this->target_amount) * 100);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->target_amount - (float) $this->current_amount);
    }

    public function scopeNotAchieved($query)
    {
        return $query->where('is_achieved', false);
    }
}
