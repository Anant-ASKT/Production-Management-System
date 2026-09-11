<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingProductionLearningNote extends Model
{
    protected $table = 'sampling_production_learning_notes';

    protected $fillable = [
        'sampling_revision_id',
        'production_order_id',
        'written_note',
        'voice_audio_path',
        'file_path',
        'status',
        'created_by',
        'reviewed_by',
        'review_date',
        'review_comments',
    ];

    protected $casts = [
        'review_date' => 'datetime',
    ];

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(SamplingUser::class, 'reviewed_by');
    }

    public function getAudioUrlAttribute(): ?string
    {
        return $this->voice_audio_path ? asset($this->voice_audio_path) : null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }
}
