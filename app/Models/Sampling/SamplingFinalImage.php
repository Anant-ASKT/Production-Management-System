<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingFinalImage extends Model
{
    protected $table = 'sampling_final_images';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'image_type',
        'file_path',
        'caption',
        'uploaded_by',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }

    public function uploader()
    {
        return $this->belongsTo(SamplingUser::class, 'uploaded_by');
    }

    public function getImageUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
