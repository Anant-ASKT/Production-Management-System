<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingUom extends Model
{
    protected $table = 'sampling_uoms';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'symbol',
        'type',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
