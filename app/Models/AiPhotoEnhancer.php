<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AiPhotoEnhancer extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'sno';

    protected $fillable = [
        'countryid',
        'companyid',
        'subcompanyid',
        'projectid',
        'subprojectid',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
}
