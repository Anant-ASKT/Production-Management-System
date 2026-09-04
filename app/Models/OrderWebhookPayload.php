<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderWebhookPayload extends Model
{
    use HasFactory;

    protected $table = 'order_webhook_payloads';

    protected $fillable = [
        'order_id',
        'order_key',
        'status',
        'courier_name',
        'tracking_id',
        'tracking_url',
        'shipped_at',
        'shipping_notes',
        'payload',
        'headers',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'shipped_at' => 'datetime',
    ];

    /**
     * Get the audit history logs for this order.
     */
    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_webhook_payload_id')->orderBy('created_at', 'desc');
    }
}
