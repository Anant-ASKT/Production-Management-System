<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingDivision extends Model
{
    protected $table = 'sampling_divisions';

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

    public function users()
    {
        return $this->hasMany(SamplingUser::class, 'division_id');
    }

    public function samplesAsOverall()
    {
        return $this->hasMany(SamplingSample::class, 'overall_division_id');
    }

    public function samplesAsSupporting()
    {
        return $this->belongsToMany(SamplingSample::class, 'sampling_sample_supporting_divisions', 'division_id', 'sample_id')
                    ->withPivot('role_description')
                    ->withTimestamps();
    }
}
