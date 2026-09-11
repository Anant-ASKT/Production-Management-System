<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;

class SamplingStorageLocation extends Model
{
    protected $table = 'sampling_storage_locations';

    protected $fillable = [
        'sampling_company_id',
        'studio_name',
        'room',
        'rack',
        'shelf',
        'box',
        'full_location_code',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function physicalStorages()
    {
        return $this->hasMany(SamplingPhysicalStorage::class, 'storage_location_id');
    }

    public function getFormattedLocationAttribute(): string
    {
        $parts = array_filter([
            $this->studio_name,
            $this->room,
            $this->rack ? "Rack {$this->rack}" : null,
            $this->shelf ? "Shelf {$this->shelf}" : null,
            $this->box ? "Box {$this->box}" : null,
        ]);

        return implode(' → ', $parts);
    }
}
