<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$suppliers = DB::table('suppliers')->get();
echo 'Suppliers count: ' . $suppliers->count() . "\n";
foreach($suppliers as $s) {
    echo $s->sno . ': ' . $s->name . "\n";
}
