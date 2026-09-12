<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingAssignmentHistory extends Model
{
    protected $table = 'sampling_assignment_histories';

    protected $fillable = [
        'sample_id',
        'assigned_to_user_id',
        'assigned_by_user_id',
        'assigned_date',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(SamplingUser::class, 'assigned_to_user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(SamplingUser::class, 'assigned_by_user_id');
    }
}
