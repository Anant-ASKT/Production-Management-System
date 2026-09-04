<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;

    protected $table = 'order_histories';

    protected $fillable = [
        'order_webhook_payload_id',
        'user_type',
        'user_id',
        'user_name',
        'action',
        'from_status',
        'to_status',
        'courier_name',
        'tracking_id',
        'comment',
    ];

    /**
     * Get the order associated with this history record.
     */
    public function order()
    {
        return $this->belongsTo(OrderWebhookPayload::class, 'order_webhook_payload_id');
    }
}
