<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingBatch extends Model
{
    protected $table = 'sampling_batches';

    protected $fillable = [
        'sampling_project_id',
        'batch_number',
        'batch_name',
        'start_date',
        'target_date',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(SamplingProject::class, 'sampling_project_id');
    }

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }

    public function samples()
    {
        return $this->hasMany(SamplingSample::class, 'sampling_batch_id');
    }
}
