<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingMeasurementPoint extends Model
{
    protected $table = 'sampling_measurement_points';

    protected $fillable = [
        'sampling_company_id',
        'category_id',
        'point_name',
        'code',
        'default_unit',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function measurements()
    {
        return $this->hasMany(SamplingMeasurement::class, 'measurement_point_id');
    }
}
