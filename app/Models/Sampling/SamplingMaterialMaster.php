<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingMaterialMaster extends Model
{
    protected $table = 'sampling_material_masters';

    protected $fillable = [
        'sampling_company_id',
        'material_category',
        'material_name',
        'item_code',
        'unit_of_measure',
        'standard_cost',
        'status',
    ];

    protected $casts = [
        'standard_cost' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
