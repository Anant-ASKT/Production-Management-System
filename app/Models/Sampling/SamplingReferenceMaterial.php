<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingReferenceMaterial extends Model
{
    protected $table = 'sampling_reference_materials';

    protected $fillable = [
        'sample_id',
        'reference_type',
        'title',
        'description',
        'file_path',
        'url',
        'uploaded_by',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function uploader()
    {
        return $this->belongsTo(SamplingUser::class, 'uploaded_by');
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return asset($this->file_path);
        }
        return $this->url;
    }
}
