<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMaster extends Model
{
    protected $table = 'tbl_company_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $guarded = [];

    public function subCompanies()
    {
        return $this->hasMany(
            CompanySubMaster::class,
            'companyid',
            'companyid'
        );
    }

    public function projects()
    {
        return $this->hasMany(
            ProjectMaster::class,
            'companyid',
            'companyid'
        );
    }
}