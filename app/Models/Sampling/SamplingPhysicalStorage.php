<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingPhysicalStorage extends Model
{
    protected $table = 'sampling_physical_storages';

    protected $fillable = [
        'sample_id',
        'sampling_revision_id',
        'storage_location_id',
        'date_stored',
        'stored_by',
        'sample_condition',
        'quantity',
        'barcode_qr',
        'notes',
    ];

    protected $casts = [
        'date_stored' => 'date',
        'quantity' => 'integer',
    ];

    public function sample()
    {
        return $this->belongsTo(SamplingSample::class, 'sample_id');
    }

    public function revision()
    {
        return $this->belongsTo(SamplingRevision::class, 'sampling_revision_id');
    }

    public function location()
    {
        return $this->belongsTo(SamplingStorageLocation::class, 'storage_location_id');
    }

    public function storedByUser()
    {
        return $this->belongsTo(SamplingUser::class, 'stored_by');
    }
}
