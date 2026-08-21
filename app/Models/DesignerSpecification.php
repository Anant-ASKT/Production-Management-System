<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignerSpecification extends Model
{
    protected $table = 'auto_designer_specification_master';

    protected $primaryKey = 'sno';

    public $timestamps = false;

    protected $fillable = [
        'designer_name',
        'item_type',
        'gender',
        'item_name',
        'composition',
        'colour',
        'sizes',
        'embellishment',
        'manufacturing_process',
        'craftsman',
        'craftsman_code',
        'manufecture',
        'client',
        'clientreference',

        'companyid',
        'subcompanyid',
        'projectid',

        'tedit',
        'loginid',
        'edatetime',

        'id',
        'barcode',
        'sku',
        'img_path',
        'status',
        'box_assign',
        'print_status',

        'description_id',
        'oc_product_id',
        'oc_main_img',
    ];

    protected $casts = [
        'sno' => 'integer',

        'designer_name' => 'integer',
        'item_type' => 'integer',
        'gender' => 'integer',
        'item_name' => 'integer',
        'composition' => 'integer',
        'colour' => 'integer',
        'sizes' => 'integer',
        'embellishment' => 'integer',
        'manufacturing_process' => 'integer',
        'craftsman' => 'integer',
        'manufecture' => 'integer',
        'client' => 'integer',

        'companyid' => 'integer',
        'subcompanyid' => 'integer',
        'projectid' => 'integer',

        'id' => 'integer',
        'description_id' => 'integer',
        'oc_product_id' => 'integer',

        'edatetime' => 'datetime',
    ];
}