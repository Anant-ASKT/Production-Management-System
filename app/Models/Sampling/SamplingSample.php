<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingSample extends Model
{
    protected $table = 'sampling_samples';

    protected $fillable = [
        'sampling_company_id',
        'sampling_project_id',
        'sampling_batch_id',
        'sample_code',
        'style_name',
        'category_id',
        'subcategory_id',
        'description',
        'overall_division_id',
        'assigned_person_id',
        'date_assigned',
        'start_date',
        'target_date',
        'approval_status',
        'approval_date',
        'approved_by',
        'is_frozen',
        'current_revision_id',
        'priority',
        'general_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_assigned' => 'date',
        'start_date' => 'date',
        'target_date' => 'date',
        'approval_date' => 'date',
        'is_frozen' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function project()
    {
        return $this->belongsTo(SamplingProject::class, 'sampling_project_id');
    }

    public function batch()
    {
        return $this->belongsTo(SamplingBatch::class, 'sampling_batch_id');
    }

    public function overallDivision()
    {
        return $this->belongsTo(SamplingDivision::class, 'overall_division_id');
    }

    public function supportingDivisions()
    {
        return $this->belongsToMany(SamplingDivision::class, 'sampling_sample_supporting_divisions', 'sample_id', 'division_id')
                    ->withPivot('role_description')
                    ->withTimestamps();
    }

    public function assignedPerson()
    {
        return $this->belongsTo(SamplingUser::class, 'assigned_person_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(SamplingUser::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(SamplingUser::class, 'created_by');
    }

    public function revisions()
    {
        return $this->hasMany(SamplingRevision::class, 'sample_id')->orderBy('revision_number', 'desc');
    }

    public function currentRevision()
    {
        return $this->belongsTo(SamplingRevision::class, 'current_revision_id');
    }

    public function referenceMaterials()
    {
        return $this->hasMany(SamplingReferenceMaterial::class, 'sample_id');
    }

    public function developmentAttempts()
    {
        return $this->hasMany(SamplingDevelopmentAttempt::class, 'sample_id')->orderBy('attempt_number', 'asc');
    }

    public function approvals()
    {
        return $this->hasMany(SamplingApproval::class, 'sample_id')->orderBy('created_at', 'desc');
    }

    public function assignmentHistories()
    {
        return $this->hasMany(SamplingAssignmentHistory::class, 'sample_id')->orderBy('assigned_date', 'desc');
    }

    // Working or active technical documents (where sampling_revision_id is null for drafts, or matching current revision)
    public function boms()
    {
        return $this->hasMany(SamplingBom::class, 'sample_id');
    }

    public function activeBoms()
    {
        return $this->hasMany(SamplingBom::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function operations()
    {
        return $this->hasMany(SamplingOperation::class, 'sample_id')->orderBy('sequence_number', 'asc');
    }

    public function activeOperations()
    {
        return $this->hasMany(SamplingOperation::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            })->orderBy('sequence_number', 'asc');
    }

    public function specifications()
    {
        return $this->hasMany(SamplingSpecification::class, 'sample_id');
    }

    public function activeSpecifications()
    {
        return $this->hasMany(SamplingSpecification::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function measurements()
    {
        return $this->hasMany(SamplingMeasurement::class, 'sample_id');
    }

    public function activeMeasurements()
    {
        return $this->hasMany(SamplingMeasurement::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function patterns()
    {
        return $this->hasMany(SamplingPattern::class, 'sample_id');
    }

    public function activePatterns()
    {
        return $this->hasMany(SamplingPattern::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function finalImages()
    {
        return $this->hasMany(SamplingFinalImage::class, 'sample_id');
    }

    public function activeFinalImages()
    {
        return $this->hasMany(SamplingFinalImage::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function productionNotes()
    {
        return $this->hasMany(SamplingProductionNote::class, 'sample_id')->orderBy('sequence', 'asc');
    }

    public function activeProductionNotes()
    {
        return $this->hasMany(SamplingProductionNote::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            })->orderBy('sequence', 'asc');
    }

    public function costings()
    {
        return $this->hasMany(SamplingCosting::class, 'sample_id');
    }

    public function activeCosting()
    {
        return $this->hasOne(SamplingCosting::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }

    public function physicalStorages()
    {
        return $this->hasMany(SamplingPhysicalStorage::class, 'sample_id');
    }

    public function activePhysicalStorage()
    {
        return $this->hasOne(SamplingPhysicalStorage::class, 'sample_id')
            ->where(function ($query) {
                if ($this->current_revision_id) {
                    $query->where('sampling_revision_id', $this->current_revision_id);
                } else {
                    $query->whereNull('sampling_revision_id');
                }
            });
    }
}
