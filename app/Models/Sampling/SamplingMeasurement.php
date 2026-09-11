<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingMeasurement extends Model
{
    protected $table = 'sampling_measurements';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'measurement_point_id',
        'point_name',
        'spec_value',
        'unit',
        'tolerance_plus',
        'tolerance_minus',
        'notes',
    ];

    protected $casts = [
        'spec_value' => 'decimal:2',
        'tolerance_plus' => 'decimal:2',
        'tolerance_minus' => 'decimal:2',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }

    public function pointMaster()
    {
        return $this->belongsTo(SamplingMeasurementPoint::class, 'measurement_point_id');
    }
}
