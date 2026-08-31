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

Route::middleware('auth')->group(function () {
    Route::resource('admin/suppliers', AdminSupplierController::class)->names('admin.suppliers')->except(['show', 'destroy']);
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
        Route::resource('products', \App\Http\Controllers\Supplier\ProductController::class)->except(['show', 'destroy']);
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

