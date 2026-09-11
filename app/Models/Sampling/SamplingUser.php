<?php

namespace App\Models\Sampling;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SamplingUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'sampling_users';

    protected $fillable = [
        'sampling_company_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'division_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function company()
    {
        return $this->belongsTo(SamplingCompany::class, 'sampling_company_id');
    }

    public function division()
    {
        return $this->belongsTo(SamplingDivision::class, 'division_id');
    }

    public function assignedSamples()
    {
        return $this->hasMany(SamplingSample::class, 'assigned_person_id');
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['company_admin', 'sampling_manager']);
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['company_admin', 'sampling_manager', 'approver']);
    }
}
