<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingApproval extends Model
{
    protected $table = 'sampling_approvals';

    protected $fillable = [
        'sample_id',
        'submitted_by',
        'submitted_date',
        'reviewed_by',
        'review_date',
        'review_comments',
        'approved_by',
        'approval_date',
        'status',
    ];

    protected $casts = [
        'submitted_date' => 'datetime',
        'review_date' => 'datetime',
        'approval_date' => 'datetime',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function submitter()
    {
        return $this->belongsTo(SamplingUser::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(SamplingUser::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(SamplingUser::class, 'approved_by');
    }
}
