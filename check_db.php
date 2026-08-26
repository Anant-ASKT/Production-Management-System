<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('auto_designer_specification_master')->count();
$withSupplier2 = DB::table('auto_designer_specification_master')->where('supplier_id', 2)->count();
$withSupplierNull = DB::table('auto_designer_specification_master')->whereNull('supplier_id')->count();

echo "Total specs: $count\n";
echo "With supplier 2: $withSupplier2\n";
echo "With supplier null: $withSupplierNull\n";

if ($withSupplierNull > 0) {
    DB::table('auto_designer_specification_master')->whereNull('supplier_id')->update(['supplier_id' => 2]);
    echo "Updated $withSupplierNull records to have supplier_id = 2.\n";
}
