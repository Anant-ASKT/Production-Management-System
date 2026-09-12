<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingPattern extends Model
{
    protected $table = 'sampling_patterns';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'pattern_code',
        'pattern_name',
        'pattern_type',
        'version',
        'size_label',
        'file_path',
        'instructions',
        'approved_by',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }

    public function approver()
    {
        return $this->belongsTo(SamplingUser::class, 'approved_by');
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
}
