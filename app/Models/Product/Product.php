<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

final class Product extends Model
{
    use HasFactory;

    protected $table = 'portfolio_products';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'short_description',
        'description',
        'price',
        'currency',
        'thumbnail_path',
        'demo_url',
        'repo_url',
        'landing_url',
        'tags',
        'is_public',
        'display_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tags' => 'array',
        'is_public' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(ProductOrder::class, 'product_id');
    }

    public function roadmapItems(): HasMany
    {
        return $this->hasMany(ProductRoadmapItem::class, 'product_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(ProductMetric::class, 'product_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'idea' => 'Ide',
            'building' => 'Dibangun',
            'active' => 'Aktif',
            'paused' => 'Dijeda',
            'discontinued' => 'Dihentikan',
            default => $this->status,
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === null) {
            return 'Gratis / Hubungi';
        }
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }
}
