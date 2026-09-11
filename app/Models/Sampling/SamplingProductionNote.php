<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingProductionNote extends Model
{
    protected $table = 'sampling_production_notes';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'sequence',
        'subject',
        'division_id',
        'written_note',
        'voice_audio_path',
        'audio_duration_seconds',
        'created_by',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'audio_duration_seconds' => 'integer',
    ];

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

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(SamplingProductionNoteAttachment::class, 'production_note_id');
    }

    public function getAudioUrlAttribute(): ?string
    {
        return $this->voice_audio_path ? asset($this->voice_audio_path) : null;
    }
}
