<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignerMaster extends Model
{
    protected $table = 'auto_designer_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}