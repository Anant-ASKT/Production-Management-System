<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Supplier extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'sno';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the users belonging to this supplier.
     */
    public function users()
    {
        return $this->hasMany(SupplierUser::class, 'supplier_id', 'sno');
    }
}
