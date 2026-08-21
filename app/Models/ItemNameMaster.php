<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemNameMaster extends Model
{
    protected $table = 'auto_itemname_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}