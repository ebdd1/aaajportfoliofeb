<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'order_id',
        'signature_hash',
        'event',
        'payload',
        'ip_address',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public static function isProcessed(string $signatureHash): bool
    {
        return self::where('signature_hash', $signatureHash)->exists();
    }

    public static function logWebhook(string $orderId, string $signatureHash, string $event, array $payload, ?string $ipAddress = null): self
    {
        return self::create([
            'order_id' => $orderId,
            'signature_hash' => $signatureHash,
            'event' => $event,
            'payload' => $payload,
            'ip_address' => $ipAddress,
            'processed_at' => now(),
        ]);
    }
}
