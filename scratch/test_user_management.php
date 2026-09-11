<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sampling\SamplingCompany;
use App\Models\Sampling\SamplingUser;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminSamplingCompanyController;
use App\Http\Controllers\Sampling\SamplingUserController;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ADMIN COMPANY CREATION & SAMPLING USER MANAGEMENT ===\n";

// 1. Test Admin creating company & company_admin
$adminController = app(AdminSamplingCompanyController::class);
$companyCode = 'TEST-CO-' . time();
$request = Request::create('/admin/sampling-companies', 'POST', [
    'name' => 'Royal Heritage Silks',
    'code' => $companyCode,
    'contact_person' => 'Vikram Seth',
    'email' => 'admin@royalheritagesilks.com',
    'phone' => '+91 98888 11111',
    'address' => 'Industrial Area, Srinagar',
    'user_name' => 'Vikram Seth',
    'user_email' => 'vikram_' . time() . '@royalheritagesilks.com',
    'password' => 'secret123',
    'role' => 'company_admin',
]);

$response = $adminController->store($request);
$company = SamplingCompany::where('code', $companyCode)->first();
if (!$company) {
    throw new Exception("Failed to create company");
}
echo "1. Company Created: [{$company->code}] {$company->name}\n";

$companyAdmin = SamplingUser::where('sampling_company_id', $company->id)->first();
if (!$companyAdmin || $companyAdmin->role !== 'company_admin') {
    throw new Exception("Company Admin not created with role company_admin");
}
echo "2. Company Admin Created: {$companyAdmin->name} ({$companyAdmin->email}) - Role: {$companyAdmin->role}\n";

// 2. Test Logging in as Company Admin in sampling guard
Auth::guard('sampling')->login($companyAdmin);
echo "3. Logged in as Company Admin: " . Auth::guard('sampling')->user()->name . "\n";

// 3. Test SamplingUserController: Add new staff/artisan user inside sampling portal
$samplingUserController = app(SamplingUserController::class);
$artisanEmail = 'artisan_' . time() . '@royalheritagesilks.com';
$addUserReq = Request::create('/sampling/users', 'POST', [
    'name' => 'Mohammad Rafiq (Master Karigar)',
    'email' => $artisanEmail,
    'password' => 'artisan123',
    'role' => 'sampling_staff',
    'phone' => '+91 99999 22222',
    'status' => 'active',
]);

$resAdd = $samplingUserController->store($addUserReq);
$artisan = SamplingUser::where('email', $artisanEmail)->first();
if (!$artisan || $artisan->sampling_company_id != $company->id) {
    throw new Exception("Failed to add artisan user for company");
}
echo "4. New Team Member Created inside Sampling Portal: {$artisan->name} ({$artisan->role})\n";

// 4. Test Updating the artisan
$updateReq = Request::create("/sampling/users/{$artisan->id}", 'PUT', [
    'name' => 'Mohammad Rafiq (Head Weaver)',
    'email' => $artisanEmail,
    'role' => 'pattern_maker',
    'phone' => '+91 99999 33333',
    'status' => 'active',
]);
$samplingUserController->update($updateReq, $artisan->id);
$artisan->refresh();
if ($artisan->name !== 'Mohammad Rafiq (Head Weaver)' || $artisan->role !== 'pattern_maker') {
    throw new Exception("Failed to update user");
}
echo "5. User Updated: {$artisan->name} - Role: {$artisan->role}\n";

// 5. Test self-delete protection
$selfDeleteRes = $samplingUserController->destroy($companyAdmin->id);
if (!SamplingUser::find($companyAdmin->id)) {
    throw new Exception("Self-delete should have been prevented!");
}
echo "6. Self-delete protection verified: Company admin cannot delete themselves.\n";

// 6. Test rendering the team view
$view = $samplingUserController->index(new Request());
$rendered = $view->render();
echo "7. Team view rendered successfully! Output length: " . strlen($rendered) . " bytes\n";

echo "=== ALL TESTS PASSED SUCCESSFULLY! ===\n";
