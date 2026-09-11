<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingDesigner extends Model
{
    protected $table = 'sampling_designers';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'code',
        'email',
        'phone',
        'specialization',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
