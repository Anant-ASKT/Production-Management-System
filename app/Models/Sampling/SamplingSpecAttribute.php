<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingSpecAttribute extends Model
{
    protected $table = 'sampling_spec_attributes';

    protected $fillable = [
        'sampling_company_id',
        'category_id',
        'attribute_name',
        'field_type',
        'options_json',
        'unit',
        'is_required',
    ];

    protected $casts = [
        'options_json' => 'array',
        'is_required' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
