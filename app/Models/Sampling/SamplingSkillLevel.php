<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingSkillLevel extends Model
{
    protected $table = 'sampling_skill_levels';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'code',
        'default_rate_per_hour',
        'description',
        'status',
    ];

    protected $casts = [
        'default_rate_per_hour' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }
}
