<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeMaster extends Model
{
    protected $table = 'auto_size_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}