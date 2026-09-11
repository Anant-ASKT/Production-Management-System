<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingProject extends Model
{
    protected $table = 'sampling_projects';

    protected $fillable = [
        'sampling_company_id',
        'project_code',
        'project_name',
        'designer_id',
        'collection_id',
        'start_date',
        'target_completion_date',
        'final_completion_date',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'final_completion_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }

    public function batches()
    {
        return $this->hasMany(SamplingBatch::class, 'sampling_project_id');
    }

    public function samples()
    {
        return $this->hasMany(SamplingSample::class, 'sampling_project_id');
    }
}
