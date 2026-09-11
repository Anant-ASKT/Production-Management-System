<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingRevision extends Model
{
    protected $table = 'sampling_revisions';

    protected $fillable = [
        'sample_id',
        'revision_number',
        'revision_code',
        'frozen_date',
        'frozen_by',
        'change_summary',
        'is_active',
        'checklist_snapshot_json',
        'direct_production_cost',
    ];

    protected $casts = [
        'frozen_date' => 'datetime',
        'is_active' => 'boolean',
        'checklist_snapshot_json' => 'array',
        'direct_production_cost' => 'decimal:4',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function freezer()
    {
        return $this->belongsTo(SamplingUser::class, 'frozen_by');
    }

    public function boms()
    {
        return $this->hasMany(SamplingBom::class, 'sampling_revision_id');
    }

    public function operations()
    {
        return $this->hasMany(SamplingOperation::class, 'sampling_revision_id')->orderBy('sequence_number', 'asc');
    }

    public function specifications()
    {
        return $this->hasMany(SamplingSpecification::class, 'sampling_revision_id');
    }

    public function measurements()
    {
        return $this->hasMany(SamplingMeasurement::class, 'sampling_revision_id');
    }

    public function patterns()
    {
        return $this->hasMany(SamplingPattern::class, 'sampling_revision_id');
    }

    public function finalImages()
    {
        return $this->hasMany(SamplingFinalImage::class, 'sampling_revision_id');
    }

    public function productionNotes()
    {
        return $this->hasMany(SamplingProductionNote::class, 'sampling_revision_id')->orderBy('sequence', 'asc');
    }

    public function costing()
    {
        return $this->hasOne(SamplingCosting::class, 'sampling_revision_id');
    }

    public function physicalStorage()
    {
        return $this->hasOne(SamplingPhysicalStorage::class, 'sampling_revision_id');
    }

    public function learningNotes()
    {
        return $this->hasMany(SamplingProductionLearningNote::class, 'sampling_revision_id')->orderBy('created_at', 'desc');
    }
}
