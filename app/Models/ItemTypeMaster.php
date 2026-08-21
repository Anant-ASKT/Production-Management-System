<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTypeMaster extends Model
{
    protected $table = 'auto_itemtype_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}