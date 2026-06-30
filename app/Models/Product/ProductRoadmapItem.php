<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProductRoadmapItem extends Model
{
    use HasFactory;

    protected $table = 'portfolio_product_roadmap_items';

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'estimated_hours',
        'actual_hours',
        'due_date',
        'completed_at',
        'display_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis',
            default => $this->priority,
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'feature' => 'Fitur',
            'bug' => 'Bug',
            'improvement' => 'Perbaikan',
            'research' => 'Riset',
            default => $this->category,
        };
    }

    public function getIsDoneAttribute(): bool
    {
        return $this->status === 'done';
    }
}
