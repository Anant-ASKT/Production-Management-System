<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingProcessMaster extends Model
{
    protected $table = 'auto_manufacturing_process_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}