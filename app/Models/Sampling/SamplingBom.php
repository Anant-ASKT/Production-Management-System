<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingBom extends Model
{
    protected $table = 'sampling_boms';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'material_id',
        'material_category',
        'description',
        'colour',
        'shade',
        'dye_lot',
        'unit_of_measure',
        'net_quantity',
        'wastage_percentage',
        'gross_quantity',
        'cost_rate',
        'material_cost',
        'notes',
    ];

    protected $casts = [
        'net_quantity' => 'decimal:4',
        'wastage_percentage' => 'decimal:2',
        'gross_quantity' => 'decimal:4',
        'cost_rate' => 'decimal:4',
        'material_cost' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::saving(function ($bom) {
            $net = (float) ($bom->net_quantity ?? 0);
            $wastage = (float) ($bom->wastage_percentage ?? 0);
            $rate = (float) ($bom->cost_rate ?? 0);

            $bom->gross_quantity = round($net * (1 + ($wastage / 100)), 4);
            $bom->material_cost = round($bom->gross_quantity * $rate, 4);
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
