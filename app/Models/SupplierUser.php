<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupplierUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'supplier_users';
    protected $primaryKey = 'sno';

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'countryid',
        'companyid',
        'subcompanyid',
        'projectid',
        'subprojectid',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the parent supplier company for this user.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sno');
    }
}
