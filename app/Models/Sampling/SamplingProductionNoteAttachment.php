<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingProductionNoteAttachment extends Model
{
    protected $table = 'sampling_production_note_attachments';

    protected $fillable = [
        'production_note_id',
        'file_type',
        'file_path',
        'file_name',
    ];

    public function productionNote()
    {
        return $this->belongsTo(SamplingProductionNote::class, 'production_note_id');
    }

    public function getFileUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
