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

    protected $casts = [
        'is_integrated' => 'boolean',
    ];

    /**
     * Get the users belonging to this supplier.
     */
    public function users()
    {
        return $this->hasMany(SupplierUser::class, 'supplier_id', 'sno');
    }

    /**
     * Get the default owner user belonging to this supplier/retailer.
     */
    public function defaultOwner()
    {
        return $this->hasOne(SupplierUser::class, 'supplier_id', 'sno')->where('role', 'Owner');
    }

    /**
     * Determine if this entity is a supplier.
     */
    public function isSupplier(): bool
    {
        return ($this->type ?? 'supplier') === 'supplier';
    }

    /**
     * Determine if this entity is a retailer.
     */
    public function isRetailer(): bool
    {
        return ($this->type ?? 'supplier') === 'retailer';
    }
}
