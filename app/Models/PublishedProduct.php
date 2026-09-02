<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishedProduct extends Model
{
    protected $table = 'published_products';

    protected $primaryKey = 'sno';

    protected $guarded = [];

    public function targetSupplier()
    {
        return $this->belongsTo(Supplier::class, 'target_supplier_id', 'sno');
    }

    public function originSupplier()
    {
        return $this->belongsTo(Supplier::class, 'origin_supplier_id', 'sno');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'sno');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by', 'id');
    }
}
