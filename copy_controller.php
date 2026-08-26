<?php
$src = file_get_contents('app/Http/Controllers/DesignSpecificationController.php');
preg_match('/public function index\(\).*?public function data\(Request \$request\)/s', $src, $matches1);
preg_match('/public function data\(Request \$request\).*?public function findByBarcode/s', $src, $matches2);

$index = substr($matches1[0], 0, -strlen('public function data(Request $request)'));
$data = substr($matches2[0], 0, -strlen('public function findByBarcode'));

$index = str_replace("'design-specifications.index'", "'admin.ai_photo_enhancing.pending'", $index);
$index = str_replace("public function index()", "public function pendingProducts()", $index);
$data = str_replace("public function data(Request \$request)", "public function pendingData(Request \$request)", $data);

$controller = <<<PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CompanyMaster;
use App\Models\CompanySubMaster;
use App\Models\ProjectMaster;

class AdminAiPhotoEnhancingController extends Controller
{
    $index
    $data
}
PHP;
file_put_contents('app/Http/Controllers/AdminAiPhotoEnhancingController.php', $controller);
echo "Done";
