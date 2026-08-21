<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMaster extends Model
{
    protected $table = 'tbl_project_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'projectid' => 'integer',
    ];
}