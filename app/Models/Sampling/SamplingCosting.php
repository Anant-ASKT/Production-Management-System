<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingCosting extends Model
{
    protected $table = 'sampling_costings';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'material_cost_total',
        'labour_cost_total',
        'dyeing_cost',
        'washing_processing_cost',
        'trims_accessories_cost',
        'outside_services_cost',
        'other_direct_cost',
        'total_estimated_direct_cost',
        'costing_method',
    ];

    protected $casts = [
        'material_cost_total' => 'decimal:4',
        'labour_cost_total' => 'decimal:4',
        'dyeing_cost' => 'decimal:4',
        'washing_processing_cost' => 'decimal:4',
        'trims_accessories_cost' => 'decimal:4',
        'outside_services_cost' => 'decimal:4',
        'other_direct_cost' => 'decimal:4',
        'total_estimated_direct_cost' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::saving(function ($costing) {
            $costing->total_estimated_direct_cost = round(
                (float)$costing->material_cost_total +
                (float)$costing->labour_cost_total +
                (float)$costing->dyeing_cost +
                (float)$costing->washing_processing_cost +
                (float)$costing->trims_accessories_cost +
                (float)$costing->outside_services_cost +
                (float)$costing->other_direct_cost,
                4
            );
        });
    }

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }
}
