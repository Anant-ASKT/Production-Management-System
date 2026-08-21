<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMModule extends Model
{
    protected $table = 'pm_modules';

    protected $fillable = [
        'company_id',
        'sub_company_id',
        'project_id',
        'module_name',
        'module_slug',
        'icon',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'sub_company_id' => 'integer',
        'project_id' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function access()
    {
        return $this->hasMany(
            PMUserModuleAccess::class,
            'module_id'
        );
    }
}