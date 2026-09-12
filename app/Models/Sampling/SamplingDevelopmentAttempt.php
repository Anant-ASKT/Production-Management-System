<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingDevelopmentAttempt extends Model
{
    protected $table = 'sampling_development_attempts';

    protected $fillable = [
        'sample_id',
        'attempt_number',
        'attempt_date',
        'notes',
        'result_status',
        'photos_json',
        'created_by',
    ];

    protected $casts = [
        'attempt_date' => 'date',
        'photos_json' => 'array',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }
}
