<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'icon',
        'color',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeIncome($query)
    {
        return $query->whereIn('type', ['income', 'both']);
    }

    public function scopeExpense($query)
    {
        return $query->whereIn('type', ['expense', 'both']);
    }
}
