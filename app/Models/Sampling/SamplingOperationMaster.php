<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingOperationMaster extends Model
{
    protected $table = 'sampling_operation_masters';

    protected $fillable = [
        'sampling_company_id',
        'operation_name',
        'division_id',
        'skill_level',
        'default_time_minutes',
        'default_rate_per_hour',
        'status',
    ];

    protected $casts = [
        'default_time_minutes' => 'decimal:2',
        'default_rate_per_hour' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function division()
    {
        return $this->belongsTo(SamplingDivision::class, 'division_id');
    }
}
