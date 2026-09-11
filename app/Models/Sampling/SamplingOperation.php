<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingOperation extends Model
{
    protected $table = 'sampling_operations';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'sequence_number',
        'operation_name',
        'division_id',
        'skill_level',
        'estimated_time_minutes',
        'labour_rate_per_hour',
        'estimated_labour_cost',
        'notes',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'estimated_time_minutes' => 'decimal:2',
        'labour_rate_per_hour' => 'decimal:2',
        'estimated_labour_cost' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($op) {
            $mins = (float) ($op->estimated_time_minutes ?? 0);
            $rate = (float) ($op->labour_rate_per_hour ?? 0);
            if ($mins > 0 && $rate > 0) {
                $op->estimated_labour_cost = round(($mins / 60) * $rate, 2);
            }
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

    public function division()
    {
        return $this->belongsTo(SamplingDivision::class, 'division_id');
    }
}
