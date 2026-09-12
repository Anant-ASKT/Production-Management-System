<?php

namespace App\Models\Sampling;

use Illuminate\Database\Eloquent\Model;
use App\Models\PMUser;

class SamplingCompany extends Model
{
    protected $table = 'sampling_companies';

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(PMUser::class, 'created_by', 'sno');
    }

    public function users()
    {
        return $this->hasMany(SamplingUser::class, 'sampling_company_id');
    }

    public function projects()
    {
        return $this->hasMany(SamplingProject::class, 'sampling_company_id');
    }

    public function samples()
    {
        return $this->hasMany(SamplingSample::class, 'sampling_company_id');
    }

    public function divisions()
    {
        return $this->hasMany(SamplingDivision::class, 'sampling_company_id');
    }

    public function storageLocations()
    {
        return $this->hasMany(SamplingStorageLocation::class, 'sampling_company_id');
    }
}
