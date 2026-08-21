<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PMUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'pm_users';

    protected $fillable = [
        'company_id',
        'sub_company_id',
        'project_id',
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'company_id' => 'integer',
            'sub_company_id' => 'integer',
            'project_id' => 'integer',
            'status' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | User Module Access
    |--------------------------------------------------------------------------
    */

    public function moduleAccess()
    {
        return $this->hasMany(
            PMUserModuleAccess::class,
            'user_id'
        )
        ->where('company_id', $this->company_id)
        ->where('sub_company_id', $this->sub_company_id)
        ->where('project_id', $this->project_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Check Admin
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | Check Module Access
    |--------------------------------------------------------------------------
    */

    public function hasModuleAccess(
        int $moduleId,
        string $permission = 'can_view'
    ): bool {

        // Admin gets complete access
        if ($this->isAdmin()) {
            return true;
        }

        return $this->moduleAccess()
            ->where('module_id', $moduleId)
            ->where($permission, true)
            ->exists();
    }
}