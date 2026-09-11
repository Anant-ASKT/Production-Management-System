<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingReferenceType extends Model
{
    protected $table = 'sampling_reference_types';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'code',
        'description',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
