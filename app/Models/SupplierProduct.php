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
        'supplier_id', 'supplier_user_id', 'name', 'description', 'status', 'stock', 'price', 'sale_price', 'min_price',
        'main_image', 'sub_images',
        'design_names', 'compositions', 'mfg_processes', 'craftsmen', 'designers', 'variations',
        'item_type', 'designer', 'gender', 'composition', 'colour', 'yarn', 'size', 'embellishment',
        'manufacturing_process', 'craftsman', 'manufacture', 'collection', 'item_name',
        'is_integrated', 'product_sku'
    ];

    protected $casts = [
        'is_integrated' => 'boolean',
    ];

    protected $appends = [
        'main_image_url',
        'item_type_name',
    ];

    public function getItemTypeNameAttribute()
    {
        $val = $this->item_type;
        if (empty($val) || $val === '-') return '-';

        if (is_numeric($val)) {
            static $itemTypeCache = null;
            if ($itemTypeCache === null) {
                $itemTypeCache = \Illuminate\Support\Facades\DB::table('auto_itemtype_master')
                    ->pluck('itemtype', 'id')
                    ->toArray();
            }
            return $itemTypeCache[(int) $val] ?? $val;
        }

        return $val;
    }

    public function getMainImageUrlAttribute()
    {
        $imgPath = $this->main_image;
        if (empty($imgPath)) return null;

        if (is_string($imgPath) && (str_starts_with($imgPath, '[') || str_starts_with($imgPath, '{'))) {
            $decoded = json_decode($imgPath, true);
            if (is_array($decoded) && !empty($decoded)) {
                $imgPath = is_array($decoded[0]) ? ($decoded[0]['url'] ?? reset($decoded[0])) : $decoded[0];
            }
        }

        if (empty($imgPath)) return null;

        if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
            return $imgPath;
        }

        $marker = 'ItemsDesigner_Masterwithbarcode/';
        $pos = strpos($imgPath, $marker);
        if ($pos !== false) {
            $rel = trim(substr($imgPath, $pos), '/');
            $fullPath = public_path($rel);
            if (is_file($fullPath)) {
                return asset($rel);
            }
            if (is_dir($fullPath)) {
                $files = @scandir($fullPath);
                if ($files) {
                    foreach ($files as $f) {
                        if ($f === '.' || $f === '..') continue;
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                            return asset($rel . '/' . $f);
                        }
                    }
                }
            }
            return asset($rel);
        }

        $cleanPath = ltrim($imgPath, '/');
        if (is_file(public_path($cleanPath))) {
            return asset($cleanPath);
        }
        if (is_file(storage_path('app/public/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        return asset($cleanPath);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'sno');
    }

    public function user()
    {
        return $this->belongsTo(SupplierUser::class, 'supplier_user_id', 'sno');
    }
}
