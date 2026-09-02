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
        'payload',
        'headers',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];
}
