<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColourMaster extends Model
{
    protected $table = 'auto_colour_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}