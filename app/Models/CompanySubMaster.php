<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySubMaster extends Model
{
    protected $table = 'tbl_company_submaster';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(
            CompanyMaster::class,
            'companyid',
            'companyid'
        );
    }

    public function projects()
    {
        return $this->hasMany(
            ProjectMaster::class,
            'subcompanyid',
            'subcompanyid'
        )->whereColumn(
            'tbl_project_master.companyid',
            'tbl_company_submaster.companyid'
        );
    }
}