<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    use HasFactory;

    protected $primaryKey = 'sno';

    protected $fillable = [
        'countryid', 'companyid', 'subcompanyid', 'projectid', 'subprojectid',
        'supplier_id', 'name', 'description', 'status', 'stock', 'price', 'sale_price',
        'main_image', 'sub_images',
        'design_names', 'compositions', 'mfg_processes', 'craftsmen', 'designers', 'variations',
        'item_type', 'designer', 'gender', 'composition', 'colour', 'size', 'embellishment',
        'manufacturing_process', 'craftsman', 'manufacture', 'collection'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sno');
    }
}
