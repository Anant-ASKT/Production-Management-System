<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenderMaster extends Model
{
    protected $table = 'auto_gender_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}