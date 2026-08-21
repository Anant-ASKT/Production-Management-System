<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMaster extends Model
{
    protected $table = 'auto_client_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];
}