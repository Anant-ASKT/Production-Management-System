<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingCollection extends Model
{
    protected $table = 'sampling_collections';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'code',
        'season',
        'year',
        'description',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
