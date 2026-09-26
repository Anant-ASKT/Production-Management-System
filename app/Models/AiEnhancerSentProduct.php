<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AiEnhancerSentProduct extends Model
{
    protected $table = 'ai_enhancer_sent_products';

    protected $fillable = [
        'garment_id',
        'barcode',
        'user_id',
        'company_id',
        'sub_company_id',
        'project_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
