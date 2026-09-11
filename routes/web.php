<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ModuleAccessController;
use App\Http\Controllers\DesignSpecificationController;
use App\Http\Controllers\Inventory\ReadyToSellStockController;
use App\Http\Controllers\Inventory\FabricYarnBuyingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\AllGarmentsController;
use App\Http\Controllers\AdminPublishProductsController;
use App\Http\Controllers\ProductSpecificationMasterController;

use App\Http\Controllers\TraderSpecificationController;
use App\Http\Controllers\TraderProductSpecificationMasterController;




Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit');

    Route::get('/login/sub-companies', [LoginController::class, 'getSubCompanies'])
        ->name('login.subcompanies');

    Route::get('/login/projects', [LoginController::class, 'getProjects'])
        ->name('login.projects');
});


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard', function () {

        return view('admin.dashboard');

    })
    ->middleware(['auth', 'prevent.back'])
    ->name('admin.dashboard');

    Route::get('/user/dashboard', function () {

        return view('user.dashboard');

    })
    ->middleware(['auth', 'prevent.back'])
    ->name('user.dashboard');

});

/*
|--------------------------------------------------------------------------
| Module Access
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
    '/admin/module-access',
    [ModuleAccessController::class, 'index']
    )
    ->middleware(['auth', 'prevent.back'])
    ->name('admin.module-access');

    Route::post(
        '/admin/module-access',
        [ModuleAccessController::class, 'save']
    )
    ->middleware(['auth', 'prevent.back'])
    ->name('admin.module-access.save');

});

use App\Http\Controllers\AdminAiPhotoEnhancerController;

Route::middleware('auth')->group(function () {
    Route::resource('admin/ai-photo-enhancers', AdminAiPhotoEnhancerController::class)->names('admin.ai-photo-enhancers')->except(['show', 'destroy']);
});

use App\Http\Controllers\AiEnhancer\Auth\LoginController as AiEnhancerLoginController;
use App\Http\Controllers\AiEnhancer\DashboardController as AiEnhancerDashboardController;
use App\Http\Controllers\AiEnhancer\AssignedProductController;

Route::prefix('ai-enhancer')->name('ai-enhancer.')->group(function () {
    Route::middleware('guest:ai_enhancer')->group(function () {
        Route::get('login', [AiEnhancerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AiEnhancerLoginController::class, 'login'])->name('login.submit');
    });
    
    Route::middleware('auth:ai_enhancer')->group(function () {
        Route::get('dashboard', [AiEnhancerDashboardController::class, 'index'])->name('dashboard');
        Route::get('assigned-products', [AssignedProductController::class, 'index'])->name('assigned-products.index');
        Route::get('assigned-products/{id}', [AssignedProductController::class, 'show'])->name('assigned-products.show');
        Route::post('submissions/upload', [AssignedProductController::class, 'uploadEnhancedImage'])->name('submissions.upload');
        Route::get('upload-history', [App\Http\Controllers\AiEnhancer\UploadHistoryController::class, 'index'])->name('upload-history.index');
        Route::get('upload-history/{id}', [App\Http\Controllers\AiEnhancer\UploadHistoryController::class, 'show'])->name('upload-history.show');
        Route::post('logout', [AiEnhancerLoginController::class, 'logout'])->name('logout');
    });
});

Route::middleware('auth')->group(function () {

   Route::get(
    '/admin/design-specifications',
    [DesignSpecificationController::class, 'index']
)->name('design-specifications.index');

Route::get(
    '/admin/design-specifications/data',
    [DesignSpecificationController::class, 'data']
)->name('design-specifications.data');

Route::post(
    '/admin/design-specifications',
    [DesignSpecificationController::class, 'store']
)->name('design-specifications.store');

Route::put(
    '/admin/design-specifications/{id}',
    [DesignSpecificationController::class, 'update']
)->name('design-specifications.update');

Route::patch(
    '/admin/design-specifications/{id}',
    [DesignSpecificationController::class, 'update']
)->name('design-specifications.update.patch');

Route::get(
    '/admin/design-specifications/master/{master}',
    [DesignSpecificationController::class, 'masterList']
)->name('design-specifications.master.list');

Route::post(
    '/admin/design-specifications/master/{master}',
    [DesignSpecificationController::class, 'masterStore']
)->name('design-specifications.master.store');

Route::put(
    '/admin/design-specifications/master/{master}/{id}',
    [DesignSpecificationController::class, 'masterUpdate']
)->name('design-specifications.master.update');

Route::get(
    '/admin/design-specifications/uploaded-images',
    [DesignSpecificationController::class, 'uploadedImages']
)->name('design-specifications.uploaded-images');

Route::get(
    '/inventory/ready-to-sell-stock',
    [ReadyToSellStockController::class, 'index']
)->name('inventory.ready-to-sell-stock');

    Route::get(
        '/admin/design-specifications/find-by-barcode',
        [DesignSpecificationController::class, 'findByBarcode']
    )->name('design-specifications.find-by-barcode');

    // ==========================================
    // AI Photo Enhancing Admin Module
    // ==========================================
    Route::get(
        '/admin/ai-photo-enhancing/pending',
        [App\Http\Controllers\AdminAiPhotoEnhancingController::class, 'pendingProducts']
    )->name('admin.ai-photo-enhancing.pending');

    Route::get(
        '/admin/ai-photo-enhancing/pending/data',
        [App\Http\Controllers\AdminAiPhotoEnhancingController::class, 'pendingData']
    )->name('admin.ai-photo-enhancing.pending.data');

    Route::post(
        '/admin/ai-photo-enhancing/assign',
        [App\Http\Controllers\AdminAiPhotoEnhancingController::class, 'assignToEnhancers']
    )->name('admin.ai-photo-enhancing.assign');

    Route::get(
        '/admin/ai-photo-enhancing/receiving',
        [App\Http\Controllers\AdminAiPhotoReceivingController::class, 'index']
    )->name('admin.ai-photo-enhancing.receiving');

    Route::get(
        '/admin/ai-photo-enhancing/receiving/{id}',
        [App\Http\Controllers\AdminAiPhotoReceivingController::class, 'show']
    )->name('admin.ai-photo-enhancing.receiving.show');

    Route::post(
        '/admin/ai-photo-enhancing/receiving/{id}/approve',
        [App\Http\Controllers\AdminAiPhotoReceivingController::class, 'approve']
    )->name('admin.ai-photo-enhancing.receiving.approve');

    Route::post(
        '/admin/ai-photo-enhancing/receiving/{id}/approve-need-version',
        [App\Http\Controllers\AdminAiPhotoReceivingController::class, 'approveNeedVersion']
    )->name('admin.ai-photo-enhancing.receiving.approve-need-version');

    Route::post(
        '/admin/ai-photo-enhancing/receiving/{id}/reject',
        [App\Http\Controllers\AdminAiPhotoReceivingController::class, 'reject']
    )->name('admin.ai-photo-enhancing.receiving.reject');



Route::get(
    '/inventory/ready-to-sell-stock/warehouses',
    [ReadyToSellStockController::class, 'getWarehouses']
)->name('inventory.ready-to-sell-stock.warehouses');


Route::get(
    '/inventory/ready-to-sell-stock/locations',
    [ReadyToSellStockController::class, 'getLocations']
)->name('inventory.ready-to-sell-stock.locations');


Route::get(
    '/inventory/ready-to-sell-stock/boxes',
    [ReadyToSellStockController::class, 'getBoxes']
)->name('inventory.ready-to-sell-stock.boxes');

Route::post(
    '/inventory/ready-to-sell-stock/warehouse',
    [ReadyToSellStockController::class, 'storeWarehouse']
)->name(
    'inventory.ready-to-sell-stock.warehouse.store'
);

Route::get(
    '/inventory/ready-to-sell-stock/states',
    [ReadyToSellStockController::class, 'getStates']
)->name(
    'inventory.ready-to-sell-stock.states'
);

Route::post(
    '/inventory/ready-to-sell-stock/location',
    [ReadyToSellStockController::class, 'storeLocation']
)->name(
    'inventory.ready-to-sell-stock.location.store'
);

Route::get(
    '/inventory/ready-to-sell-stock/box-titles',
    [ReadyToSellStockController::class, 'getBoxTitles']
)->name(
    'inventory.ready-to-sell-stock.box-titles'
);


Route::post(
    '/inventory/ready-to-sell-stock/box',
    [ReadyToSellStockController::class, 'storeBox']
)->name(
    'inventory.ready-to-sell-stock.box.store'
);

Route::post(
    '/inventory/ready-to-sell-stock/save',
    [
        ReadyToSellStockController::class,
        'saveReadyToSellStock'
    ]
)->name(
    'inventory.ready-to-sell-stock.save'
);

Route::get(
    '/inventory/view-stock',
    [ReadyToSellStockController::class, 'viewStock']
)->name(
    'inventory.ready-to-sell-stock.view-stock'
);

Route::get(
    '/inventory/view-stock/product/{barcode}',
    [
        ReadyToSellStockController::class,
        'getProductStockDetails'
    ]
)->name(
    'inventory.ready-to-sell-stock.product-details'
);

Route::get(
    '/inventory/pattern-test-fit-stock',
    [
        ReadyToSellStockController::class,
        'patternTestFitStock'
    ]
)->name(
    'inventory.pattern-test-fit-stock'
);

Route::post(
    '/inventory/pattern-test-fit-stock/save',
    [
        ReadyToSellStockController::class,
        'savePatternTestFitStock'
    ]
)->name(
    'inventory.pattern-test-fit-stock.save'
);

Route::get(
    '/inventory/pattern-test-fit-stock/assignments',
    [
        ReadyToSellStockController::class,
        'getPatternTestFitAssignments'
    ]
)->name(
    'inventory.pattern-test-fit-stock.assignments'
);

Route::get(
    '/inventory/pattern-test-fit-stock/view',
    [
        ReadyToSellStockController::class,
        'viewPatternTestFitStock'
    ]
)->name(
    'inventory.pattern-test-fit-stock.view'
);

Route::get(
    '/inventory/pattern-test-fit-stock/data',
    [
        ReadyToSellStockController::class,
        'getPatternTestFitStock'
    ]
)->name(
    'inventory.pattern-test-fit-stock.data'
);

/*
|--------------------------------------------------------------------------
| Fabric-Yarn Buying Application
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Fabric-Yarn Buying Application
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Main page
    Route::get(
        '/inventory/fabric-yarn-buying',
        [FabricYarnBuyingController::class, 'index']
    )->name(
        'inventory.fabric-yarn-buying'
    );


    // Markets
    Route::get(
        '/inventory/fabric-yarn-buying/markets',
        [FabricYarnBuyingController::class, 'getMarkets']
    )->name(
        'inventory.fabric-yarn-buying.markets'
    );


    // Shops according to selected market
    Route::get(
        '/inventory/fabric-yarn-buying/shops',
        [FabricYarnBuyingController::class, 'getShops']
    )->name(
        'inventory.fabric-yarn-buying.shops'
    );


    // Selected shop details
    Route::get(
        '/inventory/fabric-yarn-buying/shop-details',
        [FabricYarnBuyingController::class, 'getShopDetails']
    )->name(
        'inventory.fabric-yarn-buying.shop-details'
    );


    // Generate PKU number
    Route::get(
        '/inventory/fabric-yarn-buying/generate-pku',
        [FabricYarnBuyingController::class, 'generatePku']
    )->name(
        'inventory.fabric-yarn-buying.generate-pku'
    );


    // Save complete Fabric/Yarn purchase
    Route::post(
        '/inventory/fabric-yarn-buying/save',
        [FabricYarnBuyingController::class, 'save']
    )->name(
        'inventory.fabric-yarn-buying.save'
    );

});

/*
|--------------------------------------------------------------------------
| END PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

});

Route::get(
    '/admin/product-specification-masters',
    [ProductSpecificationMasterController::class, 'index']
)->name('product-specification-masters.index');

Route::get(
    '/admin/product-specification-masters/data',
    [ProductSpecificationMasterController::class, 'data']
)->name('product-specification-masters.data');

Route::get(
    '/admin/product-specification-masters/details',
    [ProductSpecificationMasterController::class, 'details']
)->name(
    'product-specification-masters.details'
);

Route::post(
    '/admin/product-specification-masters/{id}',
    [ProductSpecificationMasterController::class, 'update']
)->name('product-specification-masters.update');

Route::get(
    '/admin/make-all-masters',
    function () {
        return view('make-all-masters.index');
    }
)->name('make-all-masters.index');



Route::prefix('admin/trader-specifications')->group(function () {

    Route::get(
        '/',
        [TraderSpecificationController::class, 'index']
    )->name('trader-specifications.index');

    Route::get(
        '/data',
        [TraderSpecificationController::class, 'data']
    )->name('trader-specifications.data');

    Route::post(
        '/',
        [TraderSpecificationController::class, 'store']
    )->name('trader-specifications.store');

    Route::put(
        '/{id}',
        [TraderSpecificationController::class, 'update']
    )->name('trader-specifications.update');

    Route::get(
        '/find-by-barcode',
        [TraderSpecificationController::class, 'findByBarcode']
    )->name('trader-specifications.find-by-barcode');

    Route::get(
        '/uploaded-images',
        [TraderSpecificationController::class, 'uploadedImages']
    )->name('trader-specifications.uploaded-images');
});


/*
|--------------------------------------------------------------------------
| Trader Product Specification Master
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/trader-product-specification-masters',
    [TraderProductSpecificationMasterController::class, 'index']
)->name('trader-product-specification-masters.index');

/*
|--------------------------------------------------------------------------
| PUBLIC BOX QR VIEW
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This route MUST be outside auth middleware.
|
*/

Route::get(
    '/inventory/box-view',
    [
        ReadyToSellStockController::class,
        'boxView'
    ]
)->name(
    'inventory.ready-to-sell-stock.box-view'
);

// Route::get('/inventory/box-view-debug', function (Request $request) {

//     $companyId = (int) $request->query('company_id');
//     $subCompanyId = (int) $request->query('sub_company_id');
//     $projectId = (int) $request->query('project_id');
//     $warehouseId = (int) $request->query('warehouse_id');
//     $locationId = (int) $request->query('location_id');
//     $boxId = (int) $request->query('box_id');
//     $boxQr = trim((string) $request->query('box_qr'));

//     /*
//     |--------------------------------------------------------------------------
//     | Check box by ID only
//     |--------------------------------------------------------------------------
//     */

//     $boxById = DB::table('tbl_boxes')
//         ->where('sno', $boxId)
//         ->first();


//     /*
//     |--------------------------------------------------------------------------
//     | Check exact box
//     |--------------------------------------------------------------------------
//     */

//     $exactBox = DB::table('tbl_boxes')
//         ->where('sno', $boxId)
//         ->where('companyid', $companyId)
//         ->where('subcompanyid', $subCompanyId)
//         ->where('projectid', $projectId)
//         ->where('warehouseid', $warehouseId)
//         ->where('location', $locationId)
//         ->where('boxno', $boxQr)
//         ->first();


//     return response()->json([

//         'success' => true,

//         'received' => [
//             'company_id' => $companyId,
//             'sub_company_id' => $subCompanyId,
//             'project_id' => $projectId,
//             'warehouse_id' => $warehouseId,
//             'location_id' => $locationId,
//             'box_id' => $boxId,
//             'box_qr' => $boxQr,
//         ],

//         'box_found_by_id' => $boxById ? true : false,

//         'box_by_id' => $boxById,

//         'exact_box_found' => $exactBox ? true : false,

//         'exact_box' => $exactBox,

//     ]);

// });


use App\Http\Controllers\AdminSupplierController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminSamplingCompanyController;

Route::middleware('auth')->group(function () {
    Route::resource('admin/suppliers', AdminSupplierController::class)->names('admin.suppliers')->except(['show', 'destroy']);
    Route::post('admin/suppliers/{supplier}/users', [AdminSupplierController::class, 'addUser'])->name('admin.suppliers.users.store');
    Route::put('admin/suppliers/{supplier}/users/{user}', [AdminSupplierController::class, 'updateUser'])->name('admin.suppliers.users.update');
    Route::delete('admin/suppliers/{supplier}/users/{user}', [AdminSupplierController::class, 'deleteUser'])->name('admin.suppliers.users.destroy');
    Route::resource('admin/categories', AdminCategoryController::class)->names('admin.categories');

    // Sampling Companies Admin Routes
    Route::resource('admin/sampling-companies', AdminSamplingCompanyController::class)->names('admin.sampling-companies')->except(['show', 'destroy']);
    Route::post('admin/sampling-companies/{company}/users', [AdminSamplingCompanyController::class, 'addUser'])->name('admin.sampling-companies.users.store');
    Route::put('admin/sampling-companies/{company}/users/{user}', [AdminSamplingCompanyController::class, 'updateUser'])->name('admin.sampling-companies.users.update');
    Route::delete('admin/sampling-companies/{company}/users/{user}', [AdminSamplingCompanyController::class, 'deleteUser'])->name('admin.sampling-companies.users.destroy');
});



use App\Http\Controllers\Supplier\Auth\LoginController as SupplierLoginController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;

Route::prefix('supplier')->name('supplier.')->group(function () {
    Route::middleware('guest:supplier')->group(function () {
        Route::get('login', [SupplierLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [SupplierLoginController::class, 'login'])->name('login.submit');
    });
    
    Route::middleware('auth:supplier')->group(function () {
        Route::get('dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [SupplierLoginController::class, 'logout'])->name('logout');
        
        Route::delete('products/{product}/image', [\App\Http\Controllers\Supplier\ProductController::class, 'deleteImage'])->name('products.delete-image');
        Route::resource('products', \App\Http\Controllers\Supplier\ProductController::class)->except(['show']);

        // Supplier Website Orders Routes
        Route::get('orders', [\App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/data', [\App\Http\Controllers\Supplier\OrderController::class, 'data'])->name('orders.data');
        Route::get('orders/{id}', [\App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{id}/update-status', [\App\Http\Controllers\Supplier\OrderController::class, 'updateStatusAndShipping'])->name('orders.update-status');
    });
});

use App\Http\Controllers\Sampling\Auth\LoginController as SamplingLoginController;
use App\Http\Controllers\Sampling\DashboardController as SamplingDashboardController;
use App\Http\Controllers\Sampling\SamplingProjectController;
use App\Http\Controllers\Sampling\SamplingSampleController;
use App\Http\Controllers\Sampling\SamplingMasterController;
use App\Http\Controllers\Sampling\SamplingStorageController;
use App\Http\Controllers\Sampling\SamplingUserController;

Route::prefix('sampling')->name('sampling.')->group(function () {
    Route::get('login', [SamplingLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [SamplingLoginController::class, 'login'])->name('login.submit');

    Route::middleware('auth:sampling')->group(function () {
        Route::get('dashboard', [SamplingDashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [SamplingLoginController::class, 'logout'])->name('logout');

        // Team & User Management
        Route::resource('users', SamplingUserController::class)->except(['show']);

        // Projects & Batches
        Route::resource('projects', SamplingProjectController::class)->except(['destroy']);
        Route::post('projects/{project}/batches', [SamplingProjectController::class, 'storeBatch'])->name('projects.batches.store');

        // Samples 360 Workspace & Tabs
        Route::resource('samples', SamplingSampleController::class)->except(['destroy']);
        Route::put('samples/{id}/overview', [SamplingSampleController::class, 'updateOverview'])->name('samples.update-overview');
        Route::post('samples/{id}/references', [SamplingSampleController::class, 'storeReference'])->name('samples.references');
        Route::post('samples/{id}/attempts', [SamplingSampleController::class, 'storeAttempt'])->name('samples.attempts');
        Route::post('samples/{id}/approval', [SamplingSampleController::class, 'submitApproval'])->name('samples.approval');
        Route::post('samples/{id}/bom', [SamplingSampleController::class, 'storeBom'])->name('samples.bom');
        Route::delete('samples/{sampleId}/bom/{bomId}', [SamplingSampleController::class, 'deleteBom'])->name('samples.bom.delete');
        Route::post('samples/{id}/operations', [SamplingSampleController::class, 'storeOperation'])->name('samples.operations');
        Route::delete('samples/{sampleId}/operations/{opId}', [SamplingSampleController::class, 'deleteOperation'])->name('samples.operations.delete');
        Route::post('samples/{id}/specs', [SamplingSampleController::class, 'storeSpec'])->name('samples.specs');
        Route::delete('samples/{sampleId}/specs/{specId}', [SamplingSampleController::class, 'deleteSpec'])->name('samples.specs.delete');
        Route::post('samples/{id}/measurements', [SamplingSampleController::class, 'storeMeasurement'])->name('samples.measurements');
        Route::delete('samples/{sampleId}/measurements/{measurementId}', [SamplingSampleController::class, 'deleteMeasurement'])->name('samples.measurements.delete');
        Route::post('samples/{id}/patterns', [SamplingSampleController::class, 'storePattern'])->name('samples.patterns');
        Route::delete('samples/{sampleId}/patterns/{patternId}', [SamplingSampleController::class, 'deletePattern'])->name('samples.patterns.delete');
        Route::post('samples/{id}/photos', [SamplingSampleController::class, 'storeFinalImage'])->name('samples.photos');
        Route::delete('samples/{sampleId}/photos/{photoId}', [SamplingSampleController::class, 'deleteFinalImage'])->name('samples.photos.delete');
        Route::post('samples/{id}/notes', [SamplingSampleController::class, 'storeProductionNote'])->name('samples.notes');
        Route::delete('samples/{sampleId}/notes/{noteId}', [SamplingSampleController::class, 'deleteProductionNote'])->name('samples.notes.delete');
        Route::post('samples/{id}/costing', [SamplingSampleController::class, 'updateCosting'])->name('samples.costing');
        Route::post('samples/{id}/storage', [SamplingSampleController::class, 'updateStorage'])->name('samples.storage');
        Route::post('samples/{id}/freeze', [SamplingSampleController::class, 'freeze'])->name('samples.freeze');
        Route::post('samples/{id}/create-revision', [SamplingSampleController::class, 'createRevision'])->name('samples.create-revision');
        Route::post('samples/{id}/learning-note', [SamplingSampleController::class, 'storeLearningNote'])->name('samples.learning-note');

        // Masters Hub CRUD - Separate Pages
        Route::get('masters', [SamplingMasterController::class, 'index'])->name('masters.index');
        
        // Departments (Divisions)
        Route::get('masters/departments', [SamplingMasterController::class, 'departmentsIndex'])->name('masters.departments.index');
        Route::post('masters/departments', [SamplingMasterController::class, 'storeDepartment'])->name('masters.departments');
        Route::put('masters/departments/{id}', [SamplingMasterController::class, 'updateDepartment'])->name('masters.departments.update');
        Route::delete('masters/departments/{id}', [SamplingMasterController::class, 'destroyDepartment'])->name('masters.departments.destroy');

        // Divisions (backward-compatible aliases)
        Route::get('masters/divisions', [SamplingMasterController::class, 'divisionsIndex'])->name('masters.divisions.index');
        Route::post('masters/divisions', [SamplingMasterController::class, 'storeDivision'])->name('masters.divisions');
        Route::put('masters/divisions/{id}', [SamplingMasterController::class, 'updateDivision'])->name('masters.divisions.update');
        Route::delete('masters/divisions/{id}', [SamplingMasterController::class, 'destroyDivision'])->name('masters.divisions.destroy');

        // Skill Levels
        Route::get('masters/skill-levels', [SamplingMasterController::class, 'skillLevelsIndex'])->name('masters.skill-levels.index');
        Route::post('masters/skill-levels', [SamplingMasterController::class, 'storeSkillLevel'])->name('masters.skill-levels');
        Route::put('masters/skill-levels/{id}', [SamplingMasterController::class, 'updateSkillLevel'])->name('masters.skill-levels.update');
        Route::delete('masters/skill-levels/{id}', [SamplingMasterController::class, 'destroySkillLevel'])->name('masters.skill-levels.destroy');

        // Measurement Points
        Route::get('masters/measurement-points', [SamplingMasterController::class, 'measurementPointsIndex'])->name('masters.measurement-points.index');
        Route::post('masters/measurement-points', [SamplingMasterController::class, 'storeMeasurementPoint'])->name('masters.measurement-points');
        Route::put('masters/measurement-points/{id}', [SamplingMasterController::class, 'updateMeasurementPoint'])->name('masters.measurement-points.update');
        Route::delete('masters/measurement-points/{id}', [SamplingMasterController::class, 'destroyMeasurementPoint'])->name('masters.measurement-points.destroy');

        // Storage Locations
        Route::get('masters/locations', [SamplingMasterController::class, 'storageLocationsIndex'])->name('masters.locations.index');
        Route::post('masters/locations', [SamplingMasterController::class, 'storeStorageLocation'])->name('masters.locations');
        Route::put('masters/locations/{id}', [SamplingMasterController::class, 'updateStorageLocation'])->name('masters.locations.update');
        Route::delete('masters/locations/{id}', [SamplingMasterController::class, 'destroyStorageLocation'])->name('masters.locations.destroy');

        // Operations
        Route::get('masters/operations', [SamplingMasterController::class, 'operationsIndex'])->name('masters.operations.index');
        Route::post('masters/operations', [SamplingMasterController::class, 'storeOperation'])->name('masters.operations');
        Route::put('masters/operations/{id}', [SamplingMasterController::class, 'updateOperation'])->name('masters.operations.update');
        Route::delete('masters/operations/{id}', [SamplingMasterController::class, 'destroyOperation'])->name('masters.operations.destroy');

        // Materials
        Route::get('masters/materials', [SamplingMasterController::class, 'materialsIndex'])->name('masters.materials.index');
        Route::post('masters/materials', [SamplingMasterController::class, 'storeMaterial'])->name('masters.materials');
        Route::put('masters/materials/{id}', [SamplingMasterController::class, 'updateMaterial'])->name('masters.materials.update');
        Route::delete('masters/materials/{id}', [SamplingMasterController::class, 'destroyMaterial'])->name('masters.materials.destroy');

        // UOMs
        Route::get('masters/uoms', [SamplingMasterController::class, 'uomsIndex'])->name('masters.uoms.index');
        Route::post('masters/uoms', [SamplingMasterController::class, 'storeUom'])->name('masters.uoms');
        Route::put('masters/uoms/{id}', [SamplingMasterController::class, 'updateUom'])->name('masters.uoms.update');
        Route::delete('masters/uoms/{id}', [SamplingMasterController::class, 'destroyUom'])->name('masters.uoms.destroy');

        // Designers
        Route::get('masters/designers', [SamplingMasterController::class, 'designersIndex'])->name('masters.designers.index');
        Route::post('masters/designers', [SamplingMasterController::class, 'storeDesigner'])->name('masters.designers');
        Route::put('masters/designers/{id}', [SamplingMasterController::class, 'updateDesigner'])->name('masters.designers.update');
        Route::delete('masters/designers/{id}', [SamplingMasterController::class, 'destroyDesigner'])->name('masters.designers.destroy');

        // Collections
        Route::get('masters/collections', [SamplingMasterController::class, 'collectionsIndex'])->name('masters.collections.index');
        Route::post('masters/collections', [SamplingMasterController::class, 'storeCollection'])->name('masters.collections');
        Route::put('masters/collections/{id}', [SamplingMasterController::class, 'updateCollection'])->name('masters.collections.update');
        Route::delete('masters/collections/{id}', [SamplingMasterController::class, 'destroyCollection'])->name('masters.collections.destroy');

        // Technical Spec Attributes
        Route::get('masters/specs', [SamplingMasterController::class, 'specsIndex'])->name('masters.specs.index');
        Route::post('masters/specs', [SamplingMasterController::class, 'storeSpecAttribute'])->name('masters.specs');
        Route::put('masters/specs/{id}', [SamplingMasterController::class, 'updateSpecAttribute'])->name('masters.specs.update');
        Route::delete('masters/specs/{id}', [SamplingMasterController::class, 'destroySpecAttribute'])->name('masters.specs.destroy');

        // Sketch & Swatch Types Master
        Route::get('masters/reference-types', [SamplingMasterController::class, 'referenceTypesIndex'])->name('masters.reference-types.index');
        Route::post('masters/reference-types', [SamplingMasterController::class, 'storeReferenceType'])->name('masters.reference-types');
        Route::put('masters/reference-types/{id}', [SamplingMasterController::class, 'updateReferenceType'])->name('masters.reference-types.update');
        Route::delete('masters/reference-types/{id}', [SamplingMasterController::class, 'destroyReferenceType'])->name('masters.reference-types.destroy');

        Route::get('storage', [SamplingStorageController::class, 'index'])->name('storage.index');
    });
});

Route::get(
    '/admin/design-specifications/supplier-products',
    [DesignSpecificationController::class, 'supplierProducts']
)->name(
    'design-specifications.supplier-products'
);

Route::get(
        '/admin/all-garments',
        [AllGarmentsController::class, 'index']
    )->name('all-garments.index');


    Route::get(
        '/admin/all-garments/data',
        [AllGarmentsController::class, 'data']
    )->name('all-garments.data');


    Route::get(
        '/admin/all-garments/sub-companies',
        [AllGarmentsController::class, 'subCompanies']
    )->name('all-garments.sub-companies');


    Route::get(
        '/admin/all-garments/projects',
        [AllGarmentsController::class, 'projects']
    )->name('all-garments.projects');

    Route::get(
    '/admin/make-ai-description',
    [AllGarmentsController::class, 'makeAiDescription']
)->name('ai-description.index');

Route::get(
    '/admin/make-ai-description/data',
    [AllGarmentsController::class, 'aiDescriptionData']
)->name('ai-description.data');

Route::get(
    '/admin/make-ai-description/approved-images',
    [AllGarmentsController::class, 'aiApprovedImages']
)->name('ai-description.approved-images');

Route::post(
    '/all-garments/ai-description/save',
    [AllGarmentsController::class, 'saveAiDescription']
)->name(
    'all-garments.ai-description.save'
);

/*
|--------------------------------------------------------------------------
| PUBLISH PRODUCTS ROUTES
|--------------------------------------------------------------------------
*/
Route::get(
    '/admin/publish-products',
    [AdminPublishProductsController::class, 'index']
)->name('admin.publish-products.index');

Route::get(
    '/admin/publish-products/data',
    [AdminPublishProductsController::class, 'data']
)->name('admin.publish-products.data');

Route::get(
    '/admin/publish-products/{id}/detail',
    [AdminPublishProductsController::class, 'detail']
)->name('admin.publish-products.detail');

Route::get(
    '/admin/publish-products/categories-by-supplier/{supplierId}',
    [AdminPublishProductsController::class, 'getCategoriesBySupplier']
)->name('admin.publish-products.categories-by-supplier');

Route::get(
    '/admin/publish-products/{id}',
    [AdminPublishProductsController::class, 'show']
)->name('admin.publish-products.show');

Route::post(
    '/admin/publish-products/{id}/publish',
    [AdminPublishProductsController::class, 'publish']
)->name('admin.publish-products.publish');

/*
|--------------------------------------------------------------------------
| PUBLISHED PRODUCTS (STORE HISTORY) ROUTES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AdminPublishedProductsController;

Route::get(
    '/admin/published-products',
    [AdminPublishedProductsController::class, 'index']
)->name('admin.published-products.index');

Route::get(
    '/admin/published-products/data',
    [AdminPublishedProductsController::class, 'data']
)->name('admin.published-products.data');

/*
|--------------------------------------------------------------------------
| UPDATE PRODUCT (STOCK & PRICE SYNC WITH WOOCOMMERCE & ERP) ROUTES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AdminUpdateProductController;

Route::middleware('auth')->group(function () {
    Route::get(
        '/admin/update-products',
        [AdminUpdateProductController::class, 'index']
    )->name('admin.update-products.index');

    Route::get(
        '/admin/update-products/data',
        [AdminUpdateProductController::class, 'data']
    )->name('admin.update-products.data');

    Route::post(
        '/admin/update-products/{id}',
        [AdminUpdateProductController::class, 'update']
    )->name('admin.update-products.update');

    Route::get(
        '/admin/update-products/logs',
        [AdminUpdateProductController::class, 'logs']
    )->name('admin.update-products.logs');

    Route::get(
        '/admin/update-products/{id}/logs',
        [AdminUpdateProductController::class, 'productLogs']
    )->name('admin.update-products.product-logs');
});

/*
|--------------------------------------------------------------------------
| WEBSITE ORDERS (WOOCOMMERCE WEBHOOK DATA) ROUTES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AdminWebsiteOrderController;

Route::middleware('auth')->group(function () {
    Route::get(
        '/admin/website-orders',
        [AdminWebsiteOrderController::class, 'index']
    )->name('admin.website-orders.index');

    Route::get(
        '/admin/website-orders/data',
        [AdminWebsiteOrderController::class, 'data']
    )->name('admin.website-orders.data');

    Route::get(
        '/admin/website-orders/{id}',
        [AdminWebsiteOrderController::class, 'show']
    )->name('admin.website-orders.show');

    Route::post(
        '/admin/website-orders/{id}/send-email',
        [AdminWebsiteOrderController::class, 'sendSupplierEmail']
    )->name('admin.website-orders.send-email');

    Route::post(
        '/admin/website-orders/{id}/update-status',
        [AdminWebsiteOrderController::class, 'updateStatusAndShipping']
    )->name('admin.website-orders.update-status');
});

/*
|--------------------------------------------------------------------------
| WOOCOMMERCE ORDER WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/order_webhook_payloads', [App\Http\Controllers\OrderWebhookController::class, 'handle'])->name('order.webhook.payloads');
Route::post('/webhook/orders', [App\Http\Controllers\OrderWebhookController::class, 'handle']);


