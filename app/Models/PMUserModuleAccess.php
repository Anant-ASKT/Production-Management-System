<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PMUserModuleAccess extends Model
{
    protected $table = 'pm_user_module_access';

    protected $fillable = [
        'company_id',
        'sub_company_id',
        'project_id',
        'user_id',
        'module_id',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'sub_company_id' => 'integer',
        'project_id' => 'integer',
        'user_id' => 'integer',
        'module_id' => 'integer',
        'can_view' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(
            PMUser::class,
            'user_id'
        );
    }

    public function module()
    {
        return $this->belongsTo(
            PMModule::class,
            'module_id'
        );
    }
}