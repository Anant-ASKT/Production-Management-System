<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingSpecification extends Model
{
    protected $table = 'sampling_specifications';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'attribute_name',
        'attribute_value',
        'unit',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }
}
