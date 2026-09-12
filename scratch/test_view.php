<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sample = App\Models\Sampling\SamplingSample::first();
$user = App\Models\Sampling\SamplingUser::where('sampling_company_id', $sample->sampling_company_id)->first();
auth()->guard('sampling')->login($user);
$controller = app(App\Http\Controllers\Sampling\SamplingSampleController::class);
$view = $controller->show($sample->id);
$rendered = $view->render();
echo "SUCCESS! Rendered length: " . strlen($rendered) . " bytes\n";
