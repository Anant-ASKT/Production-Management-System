<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublishedProductUpdateLog extends Model
{
    protected $table = 'published_product_update_logs';

    protected $primaryKey = 'sno';

    protected $guarded = [];

    public function publishedProduct()
    {
        return $this->belongsTo(PublishedProduct::class, 'published_product_id', 'sno');
    }

    public function user()
    {
        return $this->belongsTo(PMUser::class, 'updated_by', 'id');
    }

    public function targetSupplier()
    {
        return $this->belongsTo(Supplier::class, 'target_supplier_id', 'sno');
    }
}
