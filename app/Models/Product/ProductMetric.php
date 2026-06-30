<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProductMetric extends Model
{
    use HasFactory;

    protected $table = 'portfolio_product_metrics';

    protected $fillable = [
        'product_id',
        'metric_key',
        'metric_label',
        'value',
        'unit',
        'recorded_at',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'recorded_at' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getFormattedValueAttribute(): string
    {
        $value = number_format((float) $this->value, 0, ',', '.');
        return $this->unit ? "{$value} {$this->unit}" : $value;
    }
}
