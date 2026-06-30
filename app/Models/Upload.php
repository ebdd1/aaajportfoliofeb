<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_hash',
        'disk',
        'path',
        'upload_type',
        'ip_address',
        'status',
        'ocr_status',
        'virus_scan_status',
        'ocr_result',
        'processed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'ocr_result' => 'array',
        'processed_at' => 'datetime',
    ];

    protected $hidden = [
        'file_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('upload_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOcrPending($query)
    {
        return $query->where('ocr_status', 'pending');
    }

    public function scopeOcrCompleted($query)
    {
        return $query->where('ocr_status', 'completed');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getOcrTitleAttribute(): ?string
    {
        return $this->ocr_result['title'] ?? null;
    }

    public function getOcrIssuerAttribute(): ?string
    {
        return $this->ocr_result['issuer'] ?? null;
    }

    public function getOcrIssueDateAttribute(): ?string
    {
        return $this->ocr_result['issue_date'] ?? null;
    }

    public function getOcrConfidenceAttribute(): float
    {
        return $this->ocr_result['confidence'] ?? 0;
    }

    public function authorize($action, $model = null): bool
    {
        // Allow admins to do anything
        if (auth()->user()?->isAdmin()) {
            return true;
        }

        // Users can only manage their own uploads
        return $this->user_id === auth()->id();
    }
}
