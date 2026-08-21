<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompositionMaster extends Model
{
    protected $table = 'auto_composition_master_stock';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}