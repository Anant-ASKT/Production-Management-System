<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufactureMaster extends Model
{
    protected $table = 'auto_manufacture_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}