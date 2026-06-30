<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'tags',
        'repo_url',
        'demo_url',
        'repo_status',
        'image_path',
        'is_featured',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected $appends = ['image_url', 'repo_status_label'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('display_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    public function getRepoStatusLabelAttribute(): string
    {
        return match ($this->repo_status) {
            'available' => 'Lihat Repo',
            'coming_soon' => 'Repo Segera Tersedia',
            'private' => 'Repo Private',
            default => 'Repo Segera Tersedia',
        };
    }
}
