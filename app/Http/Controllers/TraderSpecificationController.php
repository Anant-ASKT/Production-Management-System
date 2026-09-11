<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\CompanyMaster;
use App\Models\CompanySubMaster;
use App\Models\ProjectMaster;
use Illuminate\Validation\Rule;
use Mockery\Matcher\AndAnyOtherArgs;

class TraderSpecificationController extends Controller
{
    /**
     * Show Trader Trader Design Specification Master page.
     *
     * IMPORTANT:
     * Do NOT load specifications here.
     * Specifications are loaded through AJAX only.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $companyId    = (int) $user->company_id;
        $subCompanyId = (int) $user->sub_company_id;
        $projectId    = (int) $user->project_id;

        
        /*
        |--------------------------------------------------------------------------
        | Current Company / Sub Company / Project Names
        |--------------------------------------------------------------------------
        | IDs are used internally.
        | Names are displayed to the user.
        */

        $company = CompanyMaster::where(
            'companyid',
            $companyId
        )->first();

        $subCompany = CompanySubMaster::where(
            'subcompanyid',
            $subCompanyId
        )
            ->where('companyid', $companyId)
            ->first();

        $project = ProjectMaster::where(
            'projectid',
            $projectId
        )
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->first();

        $companyName = $company?->companyname ?? 'N/A';

        $subCompanyName = $subCompany?->subcompanyname ?? 'N/A';

        $projectName = $project?->projectname ?? 'N/A';


        /*
        |--------------------------------------------------------------------------
        | Master Dropdown Data
        |--------------------------------------------------------------------------
        */

        $designers = DB::table('auto_designer_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('designername')
            ->get([
                'sno',
                'id',
                'designername',
                'code'
            ]);


        $itemTypes = DB::table('auto_itemtype_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('itemtype')
            ->get([
                'sno',
                'id',
                'itemtype',
                'code'
            ]);


        $genders = DB::table('auto_gender_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name',
                'code'
            ]);

            $yarns = DB::table('auto_yarn_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('yarnname')
            ->get([
                'sno',
                'id',
                'yarnname',
                'code'
            ]);


        $itemNames = DB::table('auto_itemname_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('itemname')
            ->get([
                'sno',
                'id',
                'itemname',
                'code'
            ]);


        $compositions = DB::table('auto_composition_master_stock')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('composition_details')
            ->get([
                'sno',
                'id',
                'composition_details',
                'code'
            ]);


        $colours = DB::table('auto_colour_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('colourname')
            ->get([
                'sno',
                'id',
                'colourname',
                'code'
            ]);


        $sizes = DB::table('auto_size_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('size')
            ->get([
                'sno',
                'id',
                'size',
                'code'
            ]);


        $embellishments = DB::table('auto_embellishment_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('embellishmentname')
            ->get([
                'sno',
                'id',
                'embellishmentname',
                'code'
            ]);


        $manufacturingProcesses = DB::table(
                'auto_manufacturing_process_master'
            )
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('manufacturing_process')
            ->get([
                'sno',
                'id',
                'manufacturing_process',
                'code'
            ]);


        $craftsmen = DB::table('auto_craftsman_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name',
                'code'
            ]);


        $manufactures = DB::table('auto_manufacture_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name',
                'code'
            ]);


        $clients = DB::table('auto_client_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name',
                'code'
            ]);

        $traders = DB::table('auto_trader_master')
        ->orderBy('name')
        ->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        |
        | NOTICE:
        |
        | There is NO $specifications here.
        |
        */

       return view(
    'trader-specifications.index',
    compact(
        'companyId',
        'subCompanyId',
        'projectId',

        'companyName',
        'subCompanyName',
        'projectName',

        'designers',
        'itemTypes',
        'genders',
        'yarns',
        'itemNames',
        'compositions',
        'colours',
        'sizes',
        'embellishments',
        'manufacturingProcesses',
        'craftsmen',
        'manufactures',
        'clients',
        'traders'
    )
);
    }


public function supplierProducts(Request $request)
{
    try {

        /*
        |--------------------------------------------------------------------------
        | GET CURRENT PROJECT CONTEXT
        |--------------------------------------------------------------------------
        |
        | Use the same session values used by your existing application.
        |
        */

        $companyId =
            session('companyid');

        $subCompanyId =
            session('subcompanyid');

        $projectId =
            session('projectid');


        /*
        |--------------------------------------------------------------------------
        | SUPPLIER PRODUCTS QUERY
        |--------------------------------------------------------------------------
        */

        $query = DB::table(
            'supplier_products as sp'
        )

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'suppliers as s',
            's.sno',
            '=',
            'sp.supplier_id'
        )
        ->leftJoin(
            'supplier_users as su',
            'su.sno',
            '=',
            'sp.supplier_user_id'
        )

        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        ->select(
            'sp.sno',

            'sp.companyid',
            'sp.subcompanyid',
            'sp.projectid',

            'sp.supplier_id',
            'sp.supplier_user_id',

            's.name as supplier_name',
            's.nickname as supplier_nickname',
            'su.name as supplier_user_name',
            'su.email as supplier_user_email',
            'su.phone as supplier_user_phone',
            'su.status as supplier_user_status',

            'sp.name',
            'sp.description',

            'sp.main_image',
            'sp.sub_images',

            'sp.design_names',
            'sp.compositions',
            'sp.mfg_processes',
            'sp.craftsmen',
            'sp.designers',
            'sp.variations',

            'sp.item_type',
            'sp.designer',
            'sp.gender',
            'sp.composition',
            'sp.colour',
            'sp.size',

            'sp.embellishment',
            'sp.manufacturing_process',
            'sp.craftsman',
            'sp.manufacture',
            'sp.collection',

            'sp.status',
            'sp.stock',
            'sp.price',
            'sp.sale_price',
            'sp.min_price',

            'sp.created_at',
            'sp.updated_at'
        )

        ->where(
            'sp.status',
            'active'
        )
        ->where(function ($q) {
            $q->whereNull('sp.product_sku')
            ->orWhere('sp.product_sku', '');
        });


        /*
        |--------------------------------------------------------------------------
        | COMPANY FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $companyId !== null &&
            $companyId !== ''
        ) {

            $query->where(
                'sp.companyid',
                $companyId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SUB COMPANY FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $subCompanyId !== null &&
            $subCompanyId !== ''
        ) {

            $query->where(
                'sp.subcompanyid',
                $subCompanyId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PROJECT FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $projectId !== null &&
            $projectId !== ''
        ) {

            $query->where(
                'sp.projectid',
                $projectId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */

        $products =
            $query
                ->orderBy(
                    'sp.sno',
                    'desc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'data' => $products
        ]);


    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}


    /**
     * AJAX:
     * Load all trader design specifications.
     *
     * This method is called ONLY when the user clicks
     * "Show All Specifications".
     */
    /**
 * AJAX:
 * Load trader design specifications with pagination and search.
 */
public function data(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $companyId    = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId    = (int) $user->project_id;

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $perPage = (int) $request->input('per_page', 10);

    // Prevent very large requests
    if (!in_array($perPage, [10, 20, 30, 50])) {
        $perPage = 10;
    }

    $search = trim($request->input('search', ''));

    /*
    |--------------------------------------------------------------------------
    | Specification Query
    |--------------------------------------------------------------------------
    */

    $query = DB::table(
        'auto_traders_designer_specification_master as dsm'
    )

        /*
        |--------------------------------------------------------------------------
        | Designer
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as designer',
            'designer.id',
            '=',
            'dsm.designer_name'
        )

        /*
        |--------------------------------------------------------------------------
        | Item Type
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemtype_master as itemtype',
            'itemtype.id',
            '=',
            'dsm.item_type'
        )

        /*
        |--------------------------------------------------------------------------
        | Gender
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_gender_master as gender',
            'gender.id',
            '=',
            'dsm.gender'
        )

        /*
        |--------------------------------------------------------------------------
        | Item Name
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemname_master as itemname',
            'itemname.id',
            '=',
            'dsm.item_name'
        )

        /*
        |--------------------------------------------------------------------------
        | Composition
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_composition_master_stock as composition',
            'composition.id',
            '=',
            'dsm.composition'
        )

        ->leftJoin(
            'auto_yarn_master as yarn',
            'yarn.id',
            '=',
            'dsm.yarn'
)


        /*
        |--------------------------------------------------------------------------
        | Colour
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_colour_master as colour',
            'colour.id',
            '=',
            'dsm.colour'
        )

        /*
        |--------------------------------------------------------------------------
        | Size
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_size_master as size',
            'size.id',
            '=',
            'dsm.sizes'
        )

        ->leftJoin(
                'auto_embellishment_master as embellishment',
                'embellishment.id',
                '=',
                'dsm.embellishment'
            )

            ->leftJoin(
                'auto_manufacturing_process_master as manufacturing',
                'manufacturing.id',
                '=',
                'dsm.manufacturing_process'
            )

            ->leftJoin(
                'auto_craftsman_master as craftsman',
                'craftsman.id',
                '=',
                'dsm.craftsman'
            )

            ->leftJoin(
                'auto_manufacture_master as manufacture',
                'manufacture.id',
                '=',
                'dsm.manufecture'
            )

            ->leftJoin(
                'auto_client_master as client',
                'client.id',
                '=',
                'dsm.client'
            )

            ->leftJoin(
            'auto_trader_master as trader',
            'trader.id',
            '=',
            'dsm.trader_id'
        )

        ->leftJoin(
            'auto_traders_designer_specification_buying as buying',
            'buying.trader_specification_id',
            '=',
            'dsm.id'
        )

        ->leftJoin(
                'AI_product_description as ai',
                'ai.product_id',
                '=',
                'dsm.id'
            )

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_company_master as company',
            'company.companyid',
            '=',
            'dsm.companyid'
        )

        /*
        |--------------------------------------------------------------------------
        | Sub Company
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_company_submaster as subcompany',
            function ($join) {
                $join->on(
                    'subcompany.companyid',
                    '=',
                    'dsm.companyid'
                )
                ->on(
                    'subcompany.subcompanyid',
                    '=',
                    'dsm.subcompanyid'
                );
            }
        )

        /*
        |--------------------------------------------------------------------------
        | Project
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_project_master as project',
            function ($join) {
                $join->on(
                    'project.companyid',
                    '=',
                    'dsm.companyid'
                )
                ->on(
                    'project.subcompanyid',
                    '=',
                    'dsm.subcompanyid'
                )
                ->on(
                    'project.projectid',
                    '=',
                    'dsm.projectid'
                );
            }
        )

        /*
        |--------------------------------------------------------------------------
        | Current Login Context
        |--------------------------------------------------------------------------
        */

        ->where(
            'dsm.companyid',
            $companyId
        )

        ->where(
            'dsm.subcompanyid',
            $subCompanyId
        )

        ->where(
            'dsm.projectid',
            $projectId
        );

        $query->where(function ($q) {
            $q->whereNull('dsm.tedit')
            ->orWhere('dsm.tedit', '');
        });

    // Edited products create a new version with the same barcode.
    // Only the current version is shown in the main list.
    $query->where(function ($q) {
        $q->whereNull('dsm.status')
          ->orWhere('dsm.status', '')
          ->orWhere('dsm.status', 'done');
    });

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {

        $query->where(function ($q) use ($search) {

            $q->where(
                'dsm.barcode',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'dsm.sku',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'designer.designername',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'itemtype.itemtype',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'gender.name',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'itemname.itemname',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'composition.composition_details',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'yarn.yarnname',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'colour.colourname',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'size.size',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'dsm.clientreference',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'dsm.trader_name',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'dsm.trader_sku',
                'like',
                '%' . $search . '%'
            )

            ->orWhere(
                'buying.purchase_bill_no',
                'like',
                '%' . $search . '%'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $specifications = $query

        ->orderByDesc('dsm.sno')

        ->select([

            // Internal values
            'dsm.sno',
            'dsm.designer_name',
            'dsm.item_type',
            'dsm.gender',
            'dsm.item_name',
            'dsm.composition',
            'dsm.yarn',
            'dsm.trader_id',
            'dsm.trader_name',
            'trader.name as trader_name_text',
            'trader.code as trader_code_text',
            'dsm.trader_sku',

            // Buying information is stored in the related buying table.
            'buying.purchase_bill_no',
            'buying.purchase_date',
            'buying.trader_quantity',
            'buying.trader_gst_percent',
            'buying.trader_discount',
            'buying.trader_payment_terms',
            'buying.trader_reference',
            'buying.trader_notes',

            // Trader pricing is stored in the main specification table.
            'dsm.trader_buying_price',
            'dsm.trader_selling_price',
            'dsm.trader_mrp',
            'dsm.trader_min_selling_price',
            'dsm.colour',
            'dsm.sizes',

            // Display values
            'designer.designername as designer_name_text',
            'itemtype.itemtype as item_type_text',
            'gender.name as gender_text',
            'itemname.itemname as item_name_text',
            'composition.composition_details as composition_text',
            'yarn.yarnname as yarn_text',
            'colour.colourname as colour_text',
            'size.size as size_text',

            // Master codes (always resolved through master.id)
            'designer.code as designer_code_text',
            'itemtype.code as item_type_code_text',
            'gender.code as gender_code_text',
            'itemname.code as item_name_code_text',
            'composition.code as composition_code_text',
            'yarn.code as yarn_code_text',
            'colour.code as colour_code_text',
            'size.code as size_code_text',

            // Context names
            'company.companyname as company_name',
            'subcompany.subcompanyname as subcompany_name',
            'project.projectname as project_name',

            // Specification data
            'dsm.barcode',
            'dsm.sku',
            'dsm.status',
            'dsm.img_path',
            // NEW
            'dsm.subimg_path',
            
            'dsm.edatetime',
            'dsm.clientreference',
            // Optional specification values
            'dsm.embellishment',
            'dsm.manufacturing_process',
            'dsm.craftsman',
            'dsm.craftsman_code',
            'dsm.manufecture',
            'dsm.client',

            // Optional specification names
            'embellishment.embellishmentname as embellishment_text',

            'manufacturing.manufacturing_process
                as manufacturing_process_text',

            'craftsman.name as craftsman_text',

            'manufacture.name as manufacture_text',

            'client.name as client_text',

            'embellishment.code as embellishment_code_text',
            'manufacturing.code as manufacturing_process_code_text',
            'craftsman.code as craftsman_code_text',
            'manufacture.code as manufacture_code_text',
            'client.code as client_code_text',

            // AI Product Details
            'ai.AI_product_name',
            'ai.AI_product_description',
            'ai.AI_Metatitle',
            'ai.AI_Metakeywards',
            'ai.AI_Metadescription',
            'ai.AI_Producttag',
            'ai.AI_Imagealttext',

        ])

        ->paginate($perPage);

    /*
    |--------------------------------------------------------------------------
    | Prepare Image URL
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Prepare Image URL
    |--------------------------------------------------------------------------
    */

    $specifications->getCollection()->transform(function ($item) {

        $item->image_url = null;

        if (empty($item->img_path)) {
            return $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Example database value
        |--------------------------------------------------------------------------
        |
        | ../../ItemsDesigner_Masterwithbarcode/147921111111/
        |
        */

        $imgPath = str_replace('\\', '/', trim($item->img_path));

        /*
        |--------------------------------------------------------------------------
        | Find ItemsDesigner_Masterwithbarcode
        |--------------------------------------------------------------------------
        */

        $marker = 'ItemsDesigner_Masterwithbarcode/';

        $position = strpos($imgPath, $marker);

        if ($position === false) {
            return $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Get barcode folder
        |--------------------------------------------------------------------------
        */

        $barcodeFolder = substr(
            $imgPath,
            $position + strlen($marker)
        );

        $barcodeFolder = trim($barcodeFolder, '/');

        if ($barcodeFolder === '') {
            return $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Physical folder
        |--------------------------------------------------------------------------
        */

        $folderPath = public_path(
            'ItemsDesigner_Masterwithbarcode/' . $barcodeFolder
        );

        /*
        |--------------------------------------------------------------------------
        | Check folder exists
        |--------------------------------------------------------------------------
        */

        if (!is_dir($folderPath)) {
            return $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Find image
        |--------------------------------------------------------------------------
        */

        $files = scandir($folderPath);

        foreach ($files as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            $extension = strtolower(
                pathinfo($file, PATHINFO_EXTENSION)
            );

            if (
                in_array(
                    $extension,
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp',
                        'gif'
                    ],
                    true
                )
            ) {

                /*
                |--------------------------------------------------------------------------
                | Browser URL
                |--------------------------------------------------------------------------
                */

                $item->image_url = asset(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $barcodeFolder .
                    '/' .
                    $file
                );

                break;
            }
        }

        return $item;
    });

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,

        'data' => $specifications->items(),

        'current_page' =>
            $specifications->currentPage(),

        'last_page' =>
            $specifications->lastPage(),

        'per_page' =>
            $specifications->perPage(),

        'total' =>
            $specifications->total(),

        'from' =>
            $specifications->firstItem(),

        'to' =>
            $specifications->lastItem(),

        'search' =>
            $search,
    ]);
}

    /**
 * AJAX:
 * Find Product Specification by Barcode
 *
 * Used by Ready to Sell Stock.
 */
public function findByBarcode(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    $user = Auth::user();

    if (!$user) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);

    }


    /*
    |--------------------------------------------------------------------------
    | CURRENT COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) $user->company_id;

    $subCompanyId =
        (int) $user->sub_company_id;

    $projectId =
        (int) $user->project_id;


    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */

    $barcode =
        trim(
            (string) $request->input('barcode')
        );


    if ($barcode === '') {

        return response()->json([
            'success' => false,
            'message' =>
                'Please enter or scan a barcode.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | FIND PRODUCT
    |--------------------------------------------------------------------------
    |
    | Edited products can create a new version with the same barcode.
    | Therefore we select the current/latest version.
    |
    */

    $product = DB::table(
        'auto_traders_designer_specification_master as dsm'
    )


    /*
    |--------------------------------------------------------------------------
    | DESIGNER
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_designer_master as designer',
        'designer.id',
        '=',
        'dsm.designer_name'
    )


    /*
    |--------------------------------------------------------------------------
    | ITEM TYPE
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_itemtype_master as itemtype',
        'itemtype.id',
        '=',
        'dsm.item_type'
    )


    /*
    |--------------------------------------------------------------------------
    | GENDER
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_gender_master as gender',
        'gender.id',
        '=',
        'dsm.gender'
    )


    /*
    |--------------------------------------------------------------------------
    | ITEM NAME
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_itemname_master as itemname',
        'itemname.id',
        '=',
        'dsm.item_name'
    )


    /*
    |--------------------------------------------------------------------------
    | COMPOSITION
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_composition_master_stock as composition',
        'composition.id',
        '=',
        'dsm.composition'
    )


    /*
    |--------------------------------------------------------------------------
    | COLOUR
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_colour_master as colour',
        'colour.id',
        '=',
        'dsm.colour'
    )


    /*
    |--------------------------------------------------------------------------
    | SIZE
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_size_master as size',
        'size.id',
        '=',
        'dsm.sizes'
    )


    /*
    |--------------------------------------------------------------------------
    | EMBELLISHMENT
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_embellishment_master as embellishment',
        'embellishment.id',
        '=',
        'dsm.embellishment'
    )


    /*
    |--------------------------------------------------------------------------
    | MANUFACTURING PROCESS
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_manufacturing_process_master as manufacturing',
        'manufacturing.id',
        '=',
        'dsm.manufacturing_process'
    )


    /*
    |--------------------------------------------------------------------------
    | CRAFTSMAN
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_craftsman_master as craftsman',
        'craftsman.id',
        '=',
        'dsm.craftsman'
    )


    /*
    |--------------------------------------------------------------------------
    | MANUFACTURE
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_manufacture_master as manufacture',
        'manufacture.id',
        '=',
        'dsm.manufecture'
    )


    /*
    |--------------------------------------------------------------------------
    | CLIENT
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'auto_client_master as client',
        'client.id',
        '=',
        'dsm.client'
    )


    /*
    |--------------------------------------------------------------------------
    | COMPANY / PROJECT
    |--------------------------------------------------------------------------
    */

    ->where(
        'dsm.companyid',
        $companyId
    )

    ->where(
        'dsm.subcompanyid',
        $subCompanyId
    )

    ->where(
        'dsm.projectid',
        $projectId
    )


    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */

    ->where(
        'dsm.barcode',
        $barcode
    )


    /*
    |--------------------------------------------------------------------------
    | CURRENT VERSION
    |--------------------------------------------------------------------------
    */

    ->where(
        function ($query) {

            $query
                ->whereNull(
                    'dsm.status'
                )
                ->orWhere(
                    'dsm.status',
                    ''
                )
                ->orWhere(
                    'dsm.status',
                    'done'
                );

        }
    )


    /*
    |--------------------------------------------------------------------------
    | SELECT
    |--------------------------------------------------------------------------
    */

    ->select([

        /*
        |--------------------------------------------------------------------------
        | IDs
        |--------------------------------------------------------------------------
        */

        'dsm.sno',

        'dsm.id',

        'dsm.companyid',

        'dsm.subcompanyid',

        'dsm.projectid',


        /*
        |--------------------------------------------------------------------------
        | BARCODE / SKU
        |--------------------------------------------------------------------------
        */

        'dsm.barcode',

        'dsm.sku',


        /*
        |--------------------------------------------------------------------------
        | SPECIFICATION IDs
        |--------------------------------------------------------------------------
        */

        'dsm.designer_name',

        'dsm.item_type',

        'dsm.gender',

        'dsm.item_name',

        'dsm.composition',

        'dsm.colour',

        'dsm.sizes',

        'dsm.embellishment',

        'dsm.manufacturing_process',

        'dsm.craftsman',

        'dsm.craftsman_code',

        'dsm.manufecture',

        'dsm.client',


        /*
        |--------------------------------------------------------------------------
        | SPECIFICATION NAMES
        |--------------------------------------------------------------------------
        */

        'designer.designername as designer_name_text',

        'itemtype.itemtype as item_type_text',

        'gender.name as gender_text',

        'itemname.itemname as item_name_text',

        'composition.composition_details as composition_text',

        'colour.colourname as colour_text',

        'size.size as size_text',

        'embellishment.embellishmentname as embellishment_text',

        'manufacturing.manufacturing_process as manufacturing_process_text',

        'craftsman.name as craftsman_text',

        'manufacture.name as manufacture_text',

        'client.name as client_text',


        /*
        |--------------------------------------------------------------------------
        | OTHER DATA
        |--------------------------------------------------------------------------
        */

        'dsm.craftsman_code',

        'dsm.img_path',

        'dsm.status',

        'dsm.box_assign',

        'dsm.print_status',

        'dsm.edatetime',

        'dsm.clientreference',

    ])


    /*
    |--------------------------------------------------------------------------
    | LATEST RECORD
    |--------------------------------------------------------------------------
    */

    ->orderByDesc(
        'dsm.sno'
    )

    ->first();


    /*
    |--------------------------------------------------------------------------
    | PRODUCT NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$product) {

        return response()->json([

            'success' => false,

            'message' =>
                'No product found for barcode: ' .
                $barcode

        ], 404);

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE URL
    |--------------------------------------------------------------------------
    */

    $product->image_url = null;


    /*
    |--------------------------------------------------------------------------
    | IMG_PATH
    |--------------------------------------------------------------------------
    */

    $imgPath =
        trim(
            (string) (
                $product->img_path ?? ''
            )
        );


    /*
    |--------------------------------------------------------------------------
    | FORMAT 1:
    |
    | ["ItemsDesigner_Masterwithbarcode/
    | 14794028211221022/
    | filename.jpg"]
    |--------------------------------------------------------------------------
    */

    if ($imgPath !== '') {

        /*
        |--------------------------------------------------------------------------
        | Try JSON decode
        |--------------------------------------------------------------------------
        */

        $decodedImgPath =
            json_decode(
                $imgPath,
                true
            );


        if (
            json_last_error() === JSON_ERROR_NONE &&
            is_array($decodedImgPath) &&
            !empty($decodedImgPath)
        ) {

            $imgPath =
                trim(
                    (string)
                    $decodedImgPath[0]
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Normalize Windows slashes
        |--------------------------------------------------------------------------
        */

        $imgPath =
            str_replace(
                '\\',
                '/',
                $imgPath
            );


        /*
        |--------------------------------------------------------------------------
        | Remove ../../
        |--------------------------------------------------------------------------
        */

        $imgPath =
            preg_replace(
                '#^(\.\./)+#',
                '',
                $imgPath
            );


        /*
        |--------------------------------------------------------------------------
        | Remove leading slash
        |--------------------------------------------------------------------------
        */

        $imgPath =
            ltrim(
                $imgPath,
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | Find ItemsDesigner folder
        |--------------------------------------------------------------------------
        */

        $marker =
            'ItemsDesigner_Masterwithbarcode/';


        $position =
            strpos(
                $imgPath,
                $marker
            );


        /*
        |--------------------------------------------------------------------------
        | We found the correct base folder
        |--------------------------------------------------------------------------
        */

        if (
            $position !== false
        ) {

            $relativePath =
                substr(
                    $imgPath,
                    $position
                );


            $relativePath =
                ltrim(
                    $relativePath,
                    '/'
                );


            /*
            |--------------------------------------------------------------------------
            | Check whether img_path points to a FILE
            |--------------------------------------------------------------------------
            */

            $physicalPath =
                public_path(
                    $relativePath
                );


            if (
                is_file(
                    $physicalPath
                )
            ) {

                /*
                |--------------------------------------------------------------------------
                | Exact image path
                |--------------------------------------------------------------------------
                */

                $product->image_url =
                    asset(
                        $relativePath
                    );

            }

            /*
            |--------------------------------------------------------------------------
            | img_path points to a FOLDER
            |--------------------------------------------------------------------------
            */

            else {

                $folderPath =
                    rtrim(
                        $physicalPath,
                        DIRECTORY_SEPARATOR
                    );


                /*
                |--------------------------------------------------------------------------
                | If directory exists
                |--------------------------------------------------------------------------
                */

                if (
                    is_dir(
                        $folderPath
                    )
                ) {

                    $files =
                        scandir(
                            $folderPath
                        );


                    foreach (
                        $files as $file
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Ignore . and ..
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $file === '.' ||
                            $file === '..'
                        ) {

                            continue;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Get extension
                        |--------------------------------------------------------------------------
                        */

                        $extension =
                            strtolower(
                                pathinfo(
                                    $file,
                                    PATHINFO_EXTENSION
                                )
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Supported image
                        |--------------------------------------------------------------------------
                        */

                        if (
                            in_array(
                                $extension,
                                [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'webp',
                                    'gif'
                                ],
                                true
                            )
                        ) {

                            $product->image_url =
                                asset(
                                    rtrim(
                                        $relativePath,
                                        '/'
                                    ) .
                                    '/' .
                                    $file
                                );


                            break;

                        }

                    }

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    |
    | If img_path did not produce an image, try:
    |
    | public/ItemsDesigner_Masterwithbarcode/{barcode}/
    |
    */

    if (
        !$product->image_url
    ) {

        $barcodeFolder =
            public_path(
                'ItemsDesigner_Masterwithbarcode/' .
                $barcode
            );


        /*
        |--------------------------------------------------------------------------
        | Folder exists
        |--------------------------------------------------------------------------
        */

        if (
            is_dir(
                $barcodeFolder
            )
        ) {

            $files =
                scandir(
                    $barcodeFolder
                );


            foreach (
                $files as $file
            ) {

                if (
                    $file === '.' ||
                    $file === '..'
                ) {

                    continue;

                }


                $extension =
                    strtolower(
                        pathinfo(
                            $file,
                            PATHINFO_EXTENSION
                        )
                    );


                if (
                    in_array(
                        $extension,
                        [
                            'jpg',
                            'jpeg',
                            'png',
                            'webp',
                            'gif'
                        ],
                        true
                    )
                ) {

                    $product->image_url =
                        asset(
                            'ItemsDesigner_Masterwithbarcode/' .
                            $barcode .
                            '/' .
                            $file
                        );


                    break;

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,

        'message' =>
            'Product found.',

        'data' =>
            $product

    ]);
}

    /*
|--------------------------------------------------------------------------
| Generate Automatic Product SKU
|--------------------------------------------------------------------------
|
| Format:
|
| companyid-productid-ITEM-TYP-GEN-SIZ-COL-COM
|
| Example:
|
| 1-125-SHI-CAS-MAL-LAR-BLU-COT
|
*/

private function generateProductSku(
    int $companyId,
    int $productId,
    int $itemNameId,
    int $itemTypeId,
    int $genderId,
    int $sizeId,
    int $colourId,
    int $compositionId
) {

    /*
    |--------------------------------------------------------------------------
    | Get Item Name
    |--------------------------------------------------------------------------
    */

    $itemName = DB::table('auto_itemname_master')
        ->where('id', $itemNameId)
        ->where('companyid', $companyId)
        ->first(['itemname']);


    /*
    |--------------------------------------------------------------------------
    | Get Item Type
    |--------------------------------------------------------------------------
    */

    $itemType = DB::table('auto_itemtype_master')
        ->where('id', $itemTypeId)
        ->where('companyid', $companyId)
        ->first(['itemtype']);


    /*
    |--------------------------------------------------------------------------
    | Get Gender
    |--------------------------------------------------------------------------
    */

    $gender = DB::table('auto_gender_master')
        ->where('id', $genderId)
        ->where('companyid', $companyId)
        ->first(['name']);


    /*
    |--------------------------------------------------------------------------
    | Get Size
    |--------------------------------------------------------------------------
    */

    $size = DB::table('auto_size_master')
        ->where('id', $sizeId)
        ->where('companyid', $companyId)
        ->first(['size']);


    /*
    |--------------------------------------------------------------------------
    | Get Colour
    |--------------------------------------------------------------------------
    */

    $colour = DB::table('auto_colour_master')
        ->where('id', $colourId)
        ->where('companyid', $companyId)
        ->first(['colourname']);


    /*
    |--------------------------------------------------------------------------
    | Get Composition
    |--------------------------------------------------------------------------
    */

    $composition = DB::table('auto_composition_master_stock')
        ->where('id', $compositionId)
        ->where('companyid', $companyId)
        ->first(['composition_details']);


    /*
    |--------------------------------------------------------------------------
    | Convert value to first 3 characters
    |--------------------------------------------------------------------------
    */

    $shortCode = function ($value) {

        $value = trim((string) $value);

        if ($value === '') {
            return 'XXX';
        }

        /*
        | Remove special characters.
        */
        $value = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            $value
        );

        /*
        | First 3 characters only.
        */
        return strtoupper(
            substr($value, 0, 3)
        );
    };


    /*
    |--------------------------------------------------------------------------
    | Create SKU
    |--------------------------------------------------------------------------
    */

    return
        $companyId .
        '-' .
        $productId .
        '-' .
        $shortCode(
            $itemName?->itemname
        ) .
        '-' .
        $shortCode(
            $itemType?->itemtype
        ) .
        '-' .
        $shortCode(
            $gender?->name
        ) .
        '-' .
        $shortCode(
            $size?->size
        ) .
        '-' .
        $shortCode(
            $colour?->colourname
        ) .
        '-' .
        $shortCode(
            $composition?->composition_details
        );
}


    /**
     * Save new Trader Design Specification.
     */
    public function store(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    /*
    |--------------------------------------------------------------------------
    | Current Login Context
    |--------------------------------------------------------------------------
    */

    $companyId    = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId    = (int) $user->project_id;

    /*
    |--------------------------------------------------------------------------
    | Validate Request
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        /*
        |--------------------------------------------------------------------------
        | Required Masters
        |--------------------------------------------------------------------------
        */

        'item_name' => 'required|integer',
        'item_type' => 'required|integer',
        'designer_name' => 'required|integer',
        'gender' => 'required|integer',
        'composition' => 'required|integer',
        'colour' => 'required|integer',
        'sizes' => 'required|integer',

        /*
        |--------------------------------------------------------------------------
        | Optional Masters
        |--------------------------------------------------------------------------
        */

        'yarn' => 'nullable|integer',
        'embellishment' => 'nullable|integer',
        'manufacturing_process' => 'nullable|integer',
        'craftsman' => 'nullable|integer',
        'manufecture' => 'nullable|integer',
        'client' => 'nullable|integer',

        /*
        |--------------------------------------------------------------------------
        | Master Codes
        |--------------------------------------------------------------------------
        */

        'item_name_code' => 'nullable|string|max:100',
        'item_type_code' => 'nullable|string|max:100',
        'designer_code' => 'nullable|string|max:100',
        'gender_code' => 'nullable|string|max:100',
        'composition_code' => 'nullable|string|max:100',
        'colour_code' => 'nullable|string|max:100',
        'size_code' => 'nullable|string|max:100',
        'yarn_code' => 'nullable|string|max:100',
        'embellishment_code' => 'nullable|string|max:100',
        'manufacturing_process_code' => 'nullable|string|max:100',
        'craftsman_code' => 'nullable|string|max:100',
        'manufacture_code' => 'nullable|string|max:100',
        'client_code' => 'nullable|string|max:100',

        /*
        |--------------------------------------------------------------------------
        | Client
        |--------------------------------------------------------------------------
        */

        'clientreference' => 'nullable|string|max:500',

        /*
        |--------------------------------------------------------------------------
        | SKU
        |--------------------------------------------------------------------------
        */

        'sku' => [
            'nullable',
            'string',
            'max:1000',
            Rule::unique(
                'auto_traders_designer_specification_master',
                'sku'
            ),
        ],

        /*
        |--------------------------------------------------------------------------
        | Trader
        |--------------------------------------------------------------------------
        */

        'trader_id' => 'required|integer',
        'trader_name' => 'nullable|string|max:255',
        'trader_sku' => 'nullable|string|max:255',

        /*
        |--------------------------------------------------------------------------
        | Trader Main Pricing
        |--------------------------------------------------------------------------
        */

        'trader_buying_price' => 'required|numeric|min:0',
        'trader_selling_price' => 'nullable|numeric|min:0',
        'trader_mrp' => 'nullable|numeric|min:0',
        'trader_min_selling_price' => 'nullable|numeric|min:0',

        /*
        |--------------------------------------------------------------------------
        | Buying Information
        |--------------------------------------------------------------------------
        */

        'purchase_bill_no' => 'nullable|string|max:255',
        'purchase_date' => 'nullable|date',
        'trader_quantity' => 'required|numeric|min:0.01',
        'trader_gst_percent' => 'nullable|numeric|min:0|max:100',
        'trader_discount' => 'nullable|numeric|min:0',
        'trader_payment_terms' => 'nullable|string|max:255',
        'trader_reference' => 'nullable|string|max:500',
        'trader_notes' => 'nullable|string|max:2000',

        /*
        |--------------------------------------------------------------------------
        | Supplier Information
        |--------------------------------------------------------------------------
        */

        'supplier_id' => 'nullable|integer',
        'supplier_user_id' => 'nullable|integer',
        'supplier_product_id' => 'nullable|integer',
        'supplier_nickname' => 'nullable|string|max:10',

        /*
        |--------------------------------------------------------------------------
        | Images
        |--------------------------------------------------------------------------
        */

        'design_images' => 'nullable|array',
        'design_images.*' =>
            'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

        'sub_images' => 'nullable|array',
        'sub_images.*' =>
            'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    $traderMaster = DB::table('auto_trader_master')
        ->where('id', $validated['trader_id'])
        ->first();

    if (!$traderMaster) {
        return response()->json([
            'success' => false,
            'message' => 'Selected Trader does not exist.'
        ], 422);
    }

    $validated['trader_name'] = $traderMaster->name;

    /*
    |--------------------------------------------------------------------------
    | Validate Required Masters
    |--------------------------------------------------------------------------
    */

    $requiredMasters = [

        'auto_designer_master'
            => 'designer_name',

        'auto_itemtype_master'
            => 'item_type',

        'auto_gender_master'
            => 'gender',

        'auto_itemname_master'
            => 'item_name',

        'auto_composition_master_stock'
            => 'composition',

        'auto_colour_master'
            => 'colour',

        'auto_size_master'
            => 'sizes',
    ];

    foreach ($requiredMasters as $table => $field) {

        $this->validateMasterRecord(
            $table,
            (int) $validated[$field],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Optional Masters
    |--------------------------------------------------------------------------
    */

    $optionalMasters = [

        'auto_yarn_master'
            => 'yarn',

        'auto_embellishment_master'
            => 'embellishment',

        'auto_manufacturing_process_master'
            => 'manufacturing_process',

        'auto_craftsman_master'
            => 'craftsman',

        'auto_manufacture_master'
            => 'manufecture',

        'auto_client_master'
            => 'client',
    ];

    foreach ($optionalMasters as $table => $field) {

        if (
            isset($validated[$field]) &&
            $validated[$field] !== null &&
            $validated[$field] !== ''
        ) {

            $this->validateMasterRecord(
                $table,
                (int) $validated[$field],
                $companyId,
                $subCompanyId,
                $projectId
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Craftsman Code
    |--------------------------------------------------------------------------
    */

    $craftsmanCode =
        $validated['craftsman_code'] ?? null;

    if (!empty($validated['craftsman'])) {

        $craftsman = DB::table('auto_craftsman_master')
            ->where('id', $validated['craftsman'])
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->first([
                'code'
            ]);

        if ($craftsman) {
            $craftsmanCode = $craftsman->code;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Transaction
    |--------------------------------------------------------------------------
    */

    try {

        $result = DB::transaction(function () use (
            $request,
            $validated,
            $user,
            $companyId,
            $subCompanyId,
            $projectId,
            $craftsmanCode
        ) {

            /*
            |--------------------------------------------------------------------------
            | Generate Product ID
            |--------------------------------------------------------------------------
            |
            | Product ID = MAX(id) + 1
            |
            */

            $maxId = DB::table(
                'auto_traders_designer_specification_master'
            )
                ->lockForUpdate()
                ->max('id');

            $nextId = ((int) $maxId) + 1;

            if ($nextId <= 0) {
                $nextId = 1;
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Barcode
            |--------------------------------------------------------------------------
            */

            $barcode = $this->generateBarcode(
                $companyId,
                $subCompanyId,
                $projectId,
                $validated
            );

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Barcode
            |--------------------------------------------------------------------------
            */

            $barcodeExistsInMaster = DB::table(
                'auto_traders_designer_specification_master'
            )
                ->where('barcode', $barcode)
                ->exists();

            if ($barcodeExistsInMaster) {

                throw new \Exception(
                    'This barcode already exists. Data was not saved.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            $supplierId =
                $validated['supplier_id'] ?? null;

            $supplierProductId =
                $validated['supplier_product_id'] ?? null;

            $supplierPersonId =
                $validated['supplier_user_id'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Supplier SKU
            |--------------------------------------------------------------------------
            */

            $supplierSku =
                !empty($validated['sku'])
                    ? trim($validated['sku'])
                    : null;

            /*
            |--------------------------------------------------------------------------
            | Generate System SKU
            |--------------------------------------------------------------------------
            */

            $itemNameCode =
                $validated['item_name_code'] ?? '';

            $supplierNickname =
                $validated['supplier_nickname'] ?? '';

            $generatedSku =
                $itemNameCode .
                '-' .
                $supplierNickname .
                '-' .
                $nextId;

            /*
            |--------------------------------------------------------------------------
            | Insert Main Trader Specification
            |--------------------------------------------------------------------------
            */

            $insertData = [

                /*
                |--------------------------------------------------------------------------
                | Product ID
                |--------------------------------------------------------------------------
                */

                'id' =>
                    $nextId,

                /*
                |--------------------------------------------------------------------------
                | Masters
                |--------------------------------------------------------------------------
                */

                'designer_name' =>
                    $validated['designer_name'],

                'item_type' =>
                    $validated['item_type'],

                'gender' =>
                    $validated['gender'],

                'item_name' =>
                    $validated['item_name'],

                'composition' =>
                    $validated['composition'],

                'colour' =>
                    $validated['colour'],

                'sizes' =>
                    $validated['sizes'],

                'yarn' =>
                    $validated['yarn'] ?? null,

                'embellishment' =>
                    $validated['embellishment'] ?? null,

                'manufacturing_process' =>
                    $validated['manufacturing_process'] ?? null,

                'craftsman' =>
                    $validated['craftsman'] ?? null,

                'craftsman_code' =>
                    $craftsmanCode,

                'manufecture' =>
                    $validated['manufecture'] ?? null,

                'client' =>
                    $validated['client'] ?? null,

                'clientreference' =>
                    $validated['clientreference'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Master Codes
                |--------------------------------------------------------------------------
                */

                'item_name_code' =>
                    $validated['item_name_code'] ?? null,

                'item_type_code' =>
                    $validated['item_type_code'] ?? null,

                'designer_code' =>
                    $validated['designer_code'] ?? null,

                'gender_code' =>
                    $validated['gender_code'] ?? null,

                'composition_code' =>
                    $validated['composition_code'] ?? null,

                'colour_code' =>
                    $validated['colour_code'] ?? null,

                'sizes_code' =>
                    $validated['size_code'] ?? null,

                'yarn_code' =>
                    $validated['yarn_code'] ?? null,

                'embellishment_code' =>
                    $validated['embellishment_code'] ?? null,

                'manufacturing_process_code' =>
                    $validated['manufacturing_process_code'] ?? null,

                'manufacture_code' =>
                    $validated['manufacture_code'] ?? null,

                'client_code' =>
                    $validated['client_code'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Company Context
                |--------------------------------------------------------------------------
                */

                'companyid' =>
                    $companyId,

                'subcompanyid' =>
                    $subCompanyId,

                'projectid' =>
                    $projectId,

                /*
                |--------------------------------------------------------------------------
                | Login
                |--------------------------------------------------------------------------
                */

                'loginid' =>
                    $user->username ?? null,

                'edatetime' =>
                    now(),

                /*
                |--------------------------------------------------------------------------
                | Barcode / QR
                |--------------------------------------------------------------------------
                */

                'barcode' =>
                    $barcode,

                'qrcode' =>
                    $barcode,

                /*
                |--------------------------------------------------------------------------
                | SKU
                |--------------------------------------------------------------------------
                */

                'sku' =>
                    $generatedSku,

                'sku_supplier' =>
                    $supplierSku,

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                'status' =>
                    '',

                'box_assign' =>
                    '',

                'print_status' =>
                    null,

                /*
                |--------------------------------------------------------------------------
                | Existing System Fields
                |--------------------------------------------------------------------------
                */

                'description_id' =>
                    null,

                'oc_product_id' =>
                    null,

                'oc_main_img' =>
                    null,

                'supplier_id' =>
                    $supplierId,

                'supplier_person_id' =>
                    $supplierPersonId,

                'supplier_product_id' =>
                    $supplierProductId,

                'is_sent_to_photo_section' =>
                    0,

                /*
                |--------------------------------------------------------------------------
                | Trader
                |--------------------------------------------------------------------------
                */

                'trader_name' =>
                    $validated['trader_name'],

                'trader_id' =>
                    $validated['trader_id'] ?? null,

                'trader_sku' =>
                    $validated['trader_sku'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Trader Pricing
                |--------------------------------------------------------------------------
                */

                'trader_buying_price' =>
                    $validated['trader_buying_price'],

                'trader_selling_price' =>
                    $validated['trader_selling_price'] ?? null,

                'trader_mrp' =>
                    $validated['trader_mrp'] ?? null,

                'trader_min_selling_price' =>
                    $validated['trader_min_selling_price'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Images
                |--------------------------------------------------------------------------
                */

                'img_path' =>
                    null,

                'subimg_path' =>
                    null,
            ];

            /*
            |--------------------------------------------------------------------------
            | Insert Main Record
            |--------------------------------------------------------------------------
            */

            DB::table(
                'auto_traders_designer_specification_master'
            )->insert($insertData);

            /*
            |--------------------------------------------------------------------------
            | Save Main Images
            |--------------------------------------------------------------------------
            */

            $mainImagePaths = [];

            if ($request->hasFile('design_images')) {

                $imageDirectory = public_path(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $barcode
                );

                if (!is_dir($imageDirectory)) {

                    mkdir(
                        $imageDirectory,
                        0755,
                        true
                    );
                }

                foreach (
                    $request->file('design_images')
                    as $image
                ) {

                    $fileName =
                        \Illuminate\Support\Str::random(40) .
                        '.' .
                        strtolower(
                            $image->getClientOriginalExtension()
                        );

                    $image->move(
                        $imageDirectory,
                        $fileName
                    );

                    $mainImagePaths[] =
                        'ItemsDesigner_Masterwithbarcode/' .
                        $barcode .
                        '/' .
                        $fileName;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save Sub Images
            |--------------------------------------------------------------------------
            */

            $subImagePaths = [];

            if ($request->hasFile('sub_images')) {

                $subImageDirectory = public_path(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $barcode .
                    '/SubImgs'
                );

                if (!is_dir($subImageDirectory)) {

                    mkdir(
                        $subImageDirectory,
                        0755,
                        true
                    );
                }

                foreach (
                    $request->file('sub_images')
                    as $image
                ) {

                    $fileName =
                        \Illuminate\Support\Str::random(40) .
                        '.' .
                        strtolower(
                            $image->getClientOriginalExtension()
                        );

                    $image->move(
                        $subImageDirectory,
                        $fileName
                    );

                    $subImagePaths[] =
                        'ItemsDesigner_Masterwithbarcode/' .
                        $barcode .
                        '/SubImgs/' .
                        $fileName;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update Image Paths
            |--------------------------------------------------------------------------
            */

            if (!empty($mainImagePaths)) {

                DB::table(
                    'auto_traders_designer_specification_master'
                )
                    ->where('id', $nextId)
                    ->update([
                        'img_path' =>
                            json_encode(
                                $mainImagePaths,
                                JSON_UNESCAPED_SLASHES
                            )
                    ]);
            }

            if (!empty($subImagePaths)) {

                DB::table(
                    'auto_traders_designer_specification_master'
                )
                    ->where('id', $nextId)
                    ->update([
                        'subimg_path' =>
                            json_encode(
                                $subImagePaths,
                                JSON_UNESCAPED_SLASHES
                            )
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Insert Buying Information
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | Related table uses PRODUCT id.
            |
            */

            DB::table(
                'auto_traders_designer_specification_buying'
            )->insert([

                'trader_specification_id' =>
                    $nextId,

                'purchase_bill_no' =>
                    $validated['purchase_bill_no'] ?? null,

                'purchase_date' =>
                    $validated['purchase_date'] ?? null,

                'trader_quantity' =>
                    $validated['trader_quantity'],

                'trader_gst_percent' =>
                    $validated['trader_gst_percent'] ?? null,

                'trader_discount' =>
                    $validated['trader_discount'] ?? null,

                'trader_payment_terms' =>
                    $validated['trader_payment_terms'] ?? null,

                'trader_reference' =>
                    $validated['trader_reference'] ?? null,

                'trader_notes' =>
                    $validated['trader_notes'] ?? null,

                'companyid' =>
                    $companyId,

                'subcompanyid' =>
                    $subCompanyId,

                'projectid' =>
                    $projectId,

                'loginid' =>
                    $user->username ?? null,

                'edatetime' =>
                    now(),

                'tedit' =>
                    null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Return Result
            |--------------------------------------------------------------------------
            */

            return [
                'id' =>
                    $nextId,

                'barcode' =>
                    $barcode,

                'sku' =>
                    $generatedSku,

                'main_images' =>
                    $mainImagePaths,

                'sub_images' =>
                    $subImagePaths,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'Trader Specification saved successfully.',

            'id' =>
                $result['id'],

            'barcode' =>
                $result['barcode'],

            'sku' =>
                $result['sku'],

            'main_images' =>
                $result['main_images'],

            'sub_images' =>
                $result['sub_images'],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        throw $e;

    } catch (\Throwable $e) {

        return response()->json([

            'success' => false,

            'message' =>
                'Unable to save Trader Specification.',

            'error' =>
                config('app.debug')
                    ? $e->getMessage()
                    : null,

        ], 500);
    }
}

    
        /**
         * Update existing Trader Design Specification.
         *
         * IMPORTANT:
         * Barcode is NEVER regenerated during update.
         * Existing barcode remains unchanged.
         */
    /**
     * Update an existing Trader Design Specification.
     *
     * IMPORTANT:
     * - Barcode is NEVER regenerated.
     * - The old row is kept as history.
     * - A NEW row is inserted with the SAME barcode.
     * - The old row is marked "history".
     * - The new row is marked "done".
     * - Images continue to use the SAME barcode folder.
     */
   

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $companyId    = (int) $user->company_id;
        $subCompanyId = (int) $user->sub_company_id;
        $projectId    = (int) $user->project_id;

        /*
        |--------------------------------------------------------------------------
        | Find Current Specification
        |--------------------------------------------------------------------------
        */

        $specification = DB::table(
            'auto_traders_designer_specification_master'
        )
            ->where(function ($query) use ($id) {
                $query->where('sno', $id)
                    ->orWhere('id', $id);
            })
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->first();

        if (!$specification) {
            return response()->json([
                'success' => false,
                'message' => 'Current trader design specification not found.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'item_name' => 'required|integer',
            'item_type' => 'required|integer',
            'designer_name' => 'required|integer',
            'gender' => 'required|integer',
            'composition' => 'required|integer',
            'colour' => 'required|integer',
            'sizes' => 'required|integer',

            'embellishment' => 'nullable|integer',
            'yarn' => 'nullable|integer',

            'manufacturing_process' => 'nullable|integer',
            'craftsman' => 'nullable|integer',
            'craftsman_code' => 'nullable|string|max:100',

            'manufecture' => 'nullable|integer',
            'client' => 'nullable|integer',

            'clientreference' => 'nullable|string|max:500',

            /*
            |--------------------------------------------------------------------------
            | Supplier SKU
            |--------------------------------------------------------------------------
            */
            'sku' => 'nullable|string|max:1000',

            /*
            |--------------------------------------------------------------------------
            | Master Codes
            |--------------------------------------------------------------------------
            */
            'item_name_code' => 'nullable|string|max:100',
            'item_type_code' => 'nullable|string|max:100',
            'designer_code' => 'nullable|string|max:100',
            'gender_code' => 'nullable|string|max:100',
            'composition_code' => 'nullable|string|max:100',
            'yarn_code' => 'nullable|string|max:100',
            'colour_code' => 'nullable|string|max:100',
            'size_code' => 'nullable|string|max:100',
            'embellishment_code' => 'nullable|string|max:100',
            'manufacturing_process_code' => 'nullable|string|max:100',
            'manufacture_code' => 'nullable|string|max:100',
            'client_code' => 'nullable|string|max:100',

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */
            'supplier_id' => 'nullable|integer',
            'supplier_user_id' => 'nullable|integer',
            'supplier_nickname' => 'nullable|string|max:10',
            'supplier_product_id' => 'nullable|integer',

            /*
            |--------------------------------------------------------------------------
            | Price
            |--------------------------------------------------------------------------
            */
            'price' => 'nullable|string',
            'minprice' => 'nullable|string',
            'saleprice' => 'nullable|string',

            /*
            |--------------------------------------------------------------------------
            | Main Images
            |--------------------------------------------------------------------------
            */
            'design_images' => 'nullable|array',
            'design_images.*' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            /*
            |--------------------------------------------------------------------------
            | Sub Images
            |--------------------------------------------------------------------------
            */
            'sub_images' => 'nullable|array',
            'sub_images.*' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            /*
            |--------------------------------------------------------------------------
            | AI
            |--------------------------------------------------------------------------
            */
    
        'trader_id' => 'required|integer',
        'trader_name' => 'nullable|string|max:255',
        'trader_sku' => 'nullable|string|max:255',
        'purchase_bill_no' => 'nullable|string|max:255',
        'purchase_date' => 'nullable|date',
        'trader_quantity' => 'required|numeric|min:0.01',
        'trader_buying_price' => 'required|numeric|min:0',
        'trader_selling_price' => 'nullable|numeric|min:0',
        'trader_mrp' => 'nullable|numeric|min:0',
        'trader_min_selling_price' => 'nullable|numeric|min:0',
        'trader_gst_percent' => 'nullable|numeric|min:0|max:100',
        'trader_discount' => 'nullable|numeric|min:0',
        'trader_payment_terms' => 'nullable|string|max:255',
        'trader_reference' => 'nullable|string|max:500',
        'trader_notes' => 'nullable|string|max:2000',

        'AI_product_name' => 'nullable|string',
            'AI_product_description' => 'nullable|string',
            'AI_Metatitle' => 'nullable|string',
            'AI_Metakeywards' => 'nullable|string',
            'AI_Metadescription' => 'nullable|string',
            'AI_Producttag' => 'nullable|string',
            'AI_Imagealttext' => 'nullable|string',
        ]);

        $traderMaster = DB::table('auto_trader_master')
            ->where('id', $validated['trader_id'])
            ->first();

        if (!$traderMaster) {
            return response()->json([
                'success' => false,
                'message' => 'Selected Trader does not exist.'
            ], 422);
        }

        $validated['trader_name'] = $traderMaster->name;


        /*
        |--------------------------------------------------------------------------
        | Validate Required Masters
        |--------------------------------------------------------------------------
        */

        $requiredMasters = [
            'auto_designer_master' =>
                'designer_name',

            'auto_itemtype_master' =>
                'item_type',

            'auto_gender_master' =>
                'gender',

            'auto_itemname_master' =>
                'item_name',

            'auto_composition_master_stock' =>
                'composition',

            'auto_colour_master' =>
                'colour',

            'auto_size_master' =>
                'sizes',
        ];


        foreach ($requiredMasters as $table => $field) {

            $this->validateMasterRecord(
                $table,
                (int) $validated[$field],
                $companyId,
                $subCompanyId,
                $projectId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Yarn
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['yarn'])) {

            $this->validateMasterRecord(
                'auto_yarn_master',
                (int) $validated['yarn'],
                $companyId,
                $subCompanyId,
                $projectId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Optional Masters
        |--------------------------------------------------------------------------
        */

        $optionalMasters = [

            'auto_embellishment_master' =>
                'embellishment',

            'auto_manufacturing_process_master' =>
                'manufacturing_process',

            'auto_craftsman_master' =>
                'craftsman',

            'auto_manufacture_master' =>
                'manufecture',

            'auto_client_master' =>
                'client',
        ];


        foreach ($optionalMasters as $table => $field) {

            if (!empty($validated[$field])) {

                $this->validateMasterRecord(
                    $table,
                    (int) $validated[$field],
                    $companyId,
                    $subCompanyId,
                    $projectId
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Craftsman Code
        |--------------------------------------------------------------------------
        */

        $craftsmanCode =
            $validated['craftsman_code']
            ?? $specification->craftsman_code
            ?? null;


        if (!empty($validated['craftsman'])) {

            $craftsman = DB::table(
                'auto_craftsman_master'
            )
                ->where('id', $validated['craftsman'])
                ->where('companyid', $companyId)
                ->where('subcompanyid', $subCompanyId)
                ->where('projectid', $projectId)
                ->first(['code']);

            if ($craftsman) {

                $craftsmanCode =
                    $craftsman->code;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Barcode From Current Edited Values
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Same logic as store().
        |
        */

        $newBarcode = $this->generateBarcode(
            $companyId,
            $subCompanyId,
            $projectId,
            $validated
        );


        /*
        |--------------------------------------------------------------------------
        | Compare Barcode
        |--------------------------------------------------------------------------
        */

        $oldBarcode =
            (string) $specification->barcode;

        $barcodeChanged =
            $oldBarcode !== (string) $newBarcode;


        /*
        |--------------------------------------------------------------------------
        | Supplier Fields
        |--------------------------------------------------------------------------
        |
        | If field is submitted, use new value.
        | If field is not submitted, keep old value.
        |
        */

        $supplierId =
            $request->has('supplier_id')
                ? ($validated['supplier_id'] ?? null)
                : ($specification->supplier_id ?? null);


        $supplierProductId =
            $request->has('supplier_product_id')
                ? ($validated['supplier_product_id'] ?? null)
                : ($specification->supplier_product_id ?? null);


        $supplierPersonId =
            $request->has('supplier_user_id')
                ? ($validated['supplier_user_id'] ?? null)
                : ($specification->supplier_person_id ?? null);


        $supplierNickname =
            $request->has('supplier_nickname')
                ? ($validated['supplier_nickname'] ?? '')
                : ($specification->supplier_nickname ?? '');


        /*
        |--------------------------------------------------------------------------
        | Supplier SKU
        |--------------------------------------------------------------------------
        */

        $supplierSku =
            !empty($validated['sku'])
                ? trim($validated['sku'])
                : null;


        /*
        |--------------------------------------------------------------------------
        | Check Supplier SKU
        |--------------------------------------------------------------------------
        */

        if (!empty($supplierSku)) {

            $skuExists = DB::table(
                'auto_traders_designer_specification_master'
            )
                ->where('sku_supplier', $supplierSku)
                ->where('sno', '!=', $specification->sno)
                ->exists();

            if ($skuExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'This Supplier SKU is already used by another product.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Barcode Duplicate Check
        |--------------------------------------------------------------------------
        |
        | Only check when barcode is changed.
        |
        */

        if ($barcodeChanged) {

            $barcodeExists = DB::table(
                'auto_traders_designer_specification_master'
            )
                ->where('barcode', $newBarcode)
                ->where('sno', '!=', $specification->sno)
                ->exists();

            if ($barcodeExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'This barcode already exists. Please check the selected specification values.'
                ], 422);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Existing Main Images
        |--------------------------------------------------------------------------
        */

        $existingPaths = [];

        if (!empty($specification->img_path)) {

            $decoded =
                json_decode(
                    $specification->img_path,
                    true
                );

            if (is_array($decoded)) {

                $existingPaths =
                    $decoded;

            } else {

                $existingPaths = [
                    $specification->img_path
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Main Images
        |--------------------------------------------------------------------------
        |
        | Same barcode:
        |     Store in old barcode folder.
        |
        | Changed barcode:
        |     Store new uploads in new barcode folder.
        |
        */

        $imageBarcode =
            $barcodeChanged
                ? $newBarcode
                : $oldBarcode;


        $allImagePaths =
            $existingPaths;


        if ($request->hasFile('design_images')) {

            $imageDirectory =
                public_path(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $imageBarcode
                );


            if (!is_dir($imageDirectory)) {

                mkdir(
                    $imageDirectory,
                    0755,
                    true
                );
            }


            foreach (
                $request->file('design_images')
                as $image
            ) {

                if (
                    !$image ||
                    !$image->isValid()
                ) {
                    continue;
                }


                $fileName =
                    \Illuminate\Support\Str::random(40) .
                    '.' .
                    strtolower(
                        $image->getClientOriginalExtension()
                    );


                $image->move(
                    $imageDirectory,
                    $fileName
                );


                $allImagePaths[] =
                    'ItemsDesigner_Masterwithbarcode/' .
                    $imageBarcode .
                    '/' .
                    $fileName;
            }
        }


        $allImagePaths =
            array_values(
                array_unique(
                    $allImagePaths
                )
            );


        $imgPath =
            !empty($allImagePaths)
                ? json_encode(
                    $allImagePaths,
                    JSON_UNESCAPED_SLASHES
                )
                : null;


        /*
        |--------------------------------------------------------------------------
        | Existing Sub Images
        |--------------------------------------------------------------------------
        */

        $existingSubImagePaths = [];


        if (!empty($specification->subimg_path)) {

            $decodedSubImages =
                json_decode(
                    $specification->subimg_path,
                    true
                );

            if (is_array($decodedSubImages)) {

                $existingSubImagePaths =
                    $decodedSubImages;

            } else {

                $existingSubImagePaths = [
                    $specification->subimg_path
                ];
            }
        }


        $allSubImagePaths =
            $existingSubImagePaths;


        /*
        |--------------------------------------------------------------------------
        | New Sub Images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('sub_images')) {

            $subImageDirectory =
                public_path(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $imageBarcode .
                    '/SubImgs'
                );


            if (!is_dir($subImageDirectory)) {

                mkdir(
                    $subImageDirectory,
                    0755,
                    true
                );
            }


            $subImages =
                $request->file('sub_images');


            if (!is_array($subImages)) {

                $subImages = [
                    $subImages
                ];
            }


            foreach ($subImages as $subImage) {

                if (
                    !$subImage ||
                    !$subImage->isValid()
                ) {
                    continue;
                }


                $extension =
                    strtolower(
                        $subImage
                            ->getClientOriginalExtension()
                    );


                $fileName =
                    \Illuminate\Support\Str::random(40) .
                    '.' .
                    $extension;


                $subImage->move(
                    $subImageDirectory,
                    $fileName
                );


                $allSubImagePaths[] =
                    'ItemsDesigner_Masterwithbarcode/' .
                    $imageBarcode .
                    '/SubImgs/' .
                    $fileName;
            }
        }


        $allSubImagePaths =
            array_values(
                array_unique(
                    $allSubImagePaths
                )
            );


        $subImgPath =
            !empty($allSubImagePaths)
                ? json_encode(
                    $allSubImagePaths,
                    JSON_UNESCAPED_SLASHES
                )
                : null;


        /*
        |--------------------------------------------------------------------------
        | Price Values
        |--------------------------------------------------------------------------
        */

        $price =
            $request->has('price')
                ? ($validated['price'] ?? null)
                : ($specification->buying_price ?? null);


        $minPrice =
            $request->has('minprice')
                ? ($validated['minprice'] ?? null)
                : ($specification->min_price ?? null);


        $salePrice =
            $request->has('saleprice')
                ? ($validated['saleprice'] ?? null)
                : ($specification->sale_price ?? null);


        /*
        |--------------------------------------------------------------------------
        | Yarn
        |--------------------------------------------------------------------------
        */

        $yarn =
            $request->has('yarn')
                ? ($validated['yarn'] ?? null)
                : ($specification->yarn ?? null);


        /*
        |--------------------------------------------------------------------------
        | Common Update Data
        |--------------------------------------------------------------------------
        */

        $commonData = [

            'designer_name' =>
                $validated['designer_name'],

            'item_type' =>
                $validated['item_type'],

            'gender' =>
                $validated['gender'],

            'item_name' =>
                $validated['item_name'],

            'composition' =>
                $validated['composition'],

            'colour' =>
                $validated['colour'],

            'sizes' =>
                $validated['sizes'],

            'embellishment' =>
                $validated['embellishment'] ?? 0,

            'yarn' =>
                $yarn,

            'trader_id' =>
                $validated['trader_id'],

            'trader_name' =>
                $validated['trader_name'],

            'trader_sku' =>
                $validated['trader_sku'] ?? null,

            // Trader pricing belongs to the main specification table.
            'trader_buying_price' =>
                $validated['trader_buying_price'],
            'trader_selling_price' =>
                $validated['trader_selling_price'] ?? null,
            'trader_mrp' =>
                $validated['trader_mrp'] ?? null,
            'trader_min_selling_price' =>
                $validated['trader_min_selling_price'] ?? null,

            'manufacturing_process' =>
                $validated['manufacturing_process'] ?? 0,

            'craftsman' =>
                $validated['craftsman'] ?? 0,

            'craftsman_code' =>
                $craftsmanCode,

            'manufecture' =>
                $validated['manufecture'] ?? 0,

            'client' =>
                $validated['client'] ?? 0,

            'clientreference' =>
                $validated['clientreference'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier_id' =>
                $supplierId,

            'supplier_person_id' =>
                $supplierPersonId,

            'supplier_product_id' =>
                $supplierProductId,

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'buying_price' =>

                $validated['trader_buying_price'],

            'min_price' =>

                $validated['trader_min_selling_price'] ?? null,

            'sale_price' =>

                $validated['trader_selling_price'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Supplier SKU
            |--------------------------------------------------------------------------
            */

            'sku_supplier' =>
                $supplierSku,

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            'img_path' =>
                $imgPath,

            'subimg_path' =>
                $subImgPath,

            /*
            |--------------------------------------------------------------------------
            | Edit User / Date
            |--------------------------------------------------------------------------
            */

            'loginid' =>
                $user->username,

            'edatetime' =>
                now(),
        ];


        /*
        |--------------------------------------------------------------------------
        | CASE 1:
        | SAME BARCODE
        |--------------------------------------------------------------------------
        */

        if (!$barcodeChanged) {

            /*
            |--------------------------------------------------------------------------
            | Keep Existing Internal SKU
            |--------------------------------------------------------------------------
            |
            | "sku" is the generated/internal SKU.
            | "sku_supplier" is the SKU entered by supplier.
            |
            */

            $commonData['sku'] =
                $specification->sku;


            /*
            |--------------------------------------------------------------------------
            | Keep Existing QR Code
            |--------------------------------------------------------------------------
            */

            $commonData['qrcode'] =
                $specification->qrcode
                ?? $oldBarcode;


            /*
            |--------------------------------------------------------------------------
            | Keep Existing Status / Other Data
            |--------------------------------------------------------------------------
            */

            $commonData['status'] =
                $specification->status;

            $commonData['box_assign'] =
                $specification->box_assign;

            $commonData['print_status'] =
                $specification->print_status;

            $commonData['description_id'] =
                $specification->description_id;

            $commonData['oc_product_id'] =
                $specification->oc_product_id;

            $commonData['oc_main_img'] =
                $specification->oc_main_img;


            /*
            |--------------------------------------------------------------------------
            | Update Existing Row
            |--------------------------------------------------------------------------
            */

            DB::table(
                'auto_traders_designer_specification_master'
            )
                ->where('sno', $specification->sno)
                ->update($commonData);

            DB::table('auto_traders_designer_specification_buying')
                ->updateOrInsert(
                    ['trader_specification_id' => $specification->id],
                    [
                        'purchase_bill_no' => $validated['purchase_bill_no'] ?? null,
                        'purchase_date' => $validated['purchase_date'] ?? null,
                        'trader_quantity' => $validated['trader_quantity'],
                        'trader_gst_percent' => $validated['trader_gst_percent'] ?? null,
                        'trader_discount' => $validated['trader_discount'] ?? null,
                        'trader_payment_terms' => $validated['trader_payment_terms'] ?? null,
                        'trader_reference' => $validated['trader_reference'] ?? null,
                        'trader_notes' => $validated['trader_notes'] ?? null,
                        'companyid' => $companyId,
                        'subcompanyid' => $subCompanyId,
                        'projectid' => $projectId,
                        'loginid' => $user->username ?? null,
                        'edatetime' => now(),
                        'tedit' => null,
                    ]
                );


            /*
            |--------------------------------------------------------------------------
            | AI DATA - SAME PRODUCT
            |--------------------------------------------------------------------------
            */

            $aiFields = [

                'AI_product_name',
                'AI_product_description',
                'AI_Metatitle',
                'AI_Metakeywards',
                'AI_Metadescription',
                'AI_Producttag',
                'AI_Imagealttext',

            ];


            $aiInputWasSent = false;


            foreach ($aiFields as $field) {

                if ($request->has($field)) {

                    $aiInputWasSent = true;

                    break;
                }
            }


            if ($aiInputWasSent) {

                $hasAiDetails = false;


                foreach ($aiFields as $field) {

                    if (
                        isset($validated[$field]) &&
                        trim(
                            (string)
                            $validated[$field]
                        ) !== ''
                    ) {

                        $hasAiDetails = true;

                        break;
                    }
                }


                $oldAi =
                    DB::table(
                        'AI_product_description'
                    )
                    ->where(
                        'product_id',
                        $specification->id
                    )
                    ->first();


                if ($hasAiDetails) {

                    $aiData = [

                        'AI_product_name' =>
                            $validated[
                                'AI_product_name'
                            ] ?? null,

                        'AI_product_description' =>
                            $validated[
                                'AI_product_description'
                            ] ?? null,

                        'AI_Metatitle' =>
                            $validated[
                                'AI_Metatitle'
                            ] ?? null,

                        'AI_Metakeywards' =>
                            $validated[
                                'AI_Metakeywards'
                            ] ?? null,

                        'AI_Metadescription' =>
                            $validated[
                                'AI_Metadescription'
                            ] ?? null,

                        'AI_Producttag' =>
                            $validated[
                                'AI_Producttag'
                            ] ?? null,

                        'AI_Imagealttext' =>
                            $validated[
                                'AI_Imagealttext'
                            ] ?? null,

                        'company_id' =>
                            $companyId,

                        'subcompany_id' =>
                            $subCompanyId,

                        'projectid' =>
                            $projectId,
                    ];


                    if ($oldAi) {

                        DB::table(
                            'AI_product_description'
                        )
                            ->where(
                                'product_id',
                                $specification->id
                            )
                            ->update($aiData);

                    } else {

                        $aiData['product_id'] =
                            $specification->id;


                        $descriptionId =
                            DB::table(
                                'AI_product_description'
                            )
                            ->insertGetId(
                                $aiData
                            );


                        DB::table(
                            'auto_traders_designer_specification_master'
                        )
                            ->where(
                                'sno',
                                $specification->sno
                            )
                            ->update([
                                'description_id' =>
                                    $descriptionId
                            ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Update Supplier Product SKU
            |--------------------------------------------------------------------------
            */

            if (
                $supplierProductId !== null &&
                $supplierProductId !== ''
            ) {

                DB::table('supplier_products')
                    ->where(
                        'sno',
                        $supplierProductId
                    )
                    ->update([
                        'product_sku' =>
                            $specification->sku
                    ]);
            }


            /*
            |--------------------------------------------------------------------------
            | SAME BARCODE RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' =>
                    true,

                'message' =>
                    'Trader design specification updated successfully.',

                'id' =>
                    $specification->sno,

                'old_id' =>
                    $specification->sno,

                'barcode' =>
                    $oldBarcode,

                'barcode_changed' =>
                    false,

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CASE 2:
        | BARCODE CHANGED
        |--------------------------------------------------------------------------
        |
        | Old row becomes history.
        | New row is created.
        |
        */


        /*
        |--------------------------------------------------------------------------
        | Save Complete Old Row To History
        |--------------------------------------------------------------------------
        */

        $historyId =
            DB::table(
                'trader_designer_specification_history'
            )
            ->insertGetId([

                'original_sno' =>
                    $specification->sno,

                'new_sno' =>
                    null,

                'barcode' =>
                    $oldBarcode,

                'old_data' =>
                    json_encode(
                        (array) $specification,
                        JSON_UNESCAPED_SLASHES |
                        JSON_UNESCAPED_UNICODE
                    ),

                'modified_by' =>
                    $user->username,

                'modified_at' =>
                    now(),

            ]);


        /*
        |--------------------------------------------------------------------------
        | Mark Old Row As Edited
        |--------------------------------------------------------------------------
        */

        DB::table(
            'auto_traders_designer_specification_master'
        )
            ->where(
                'sno',
                $specification->sno
            )
            ->update([

                'status' =>
                    '',

                'tedit' =>
                    now(),

            ]);


        /*
        |--------------------------------------------------------------------------
        | New Product ID
        |--------------------------------------------------------------------------
        */

        $nextId =
            ((int) DB::table(
                'auto_traders_designer_specification_master'
            )->max('id')) + 1;


        /*
        |--------------------------------------------------------------------------
        | Generate New Internal SKU
        |--------------------------------------------------------------------------
        */

        $itemNameCode =
            $validated['item_name_code']
            ?? '';


        /*
        |--------------------------------------------------------------------------
        | Supplier Nickname
        |--------------------------------------------------------------------------
        */

        if (empty($supplierId)) {

            $supplierId =
                $specification->supplier_id
                ?? $projectId;


            if (
                empty($supplierNickname)
            ) {

                $projectMaster =
                    DB::table(
                        'tbl_project_master'
                    )
                    ->where(
                        'projectid',
                        $projectId
                    )
                    ->first([
                        'projectname'
                    ]);


                if (
                    $projectMaster &&
                    !empty(
                        $projectMaster->projectname
                    )
                ) {

                    $projectName =
                        preg_replace(
                            '/[^A-Za-z0-9]/',
                            '',
                            $projectMaster->projectname
                        );


                    $supplierNickname =
                        strtoupper(
                            substr(
                                $projectName,
                                0,
                                3
                            )
                        );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Internal SKU
        |--------------------------------------------------------------------------
        */

        $generatedSku =
            $itemNameCode .
            '-' .
            $supplierNickname .
            '-' .
            $nextId;


        /*
        |--------------------------------------------------------------------------
        | New Row Data
        |--------------------------------------------------------------------------
        */

        $newRowData = [

            'designer_name' =>
                $validated['designer_name'],

            'item_type' =>
                $validated['item_type'],

            'gender' =>
                $validated['gender'],

            'item_name' =>
                $validated['item_name'],

            'composition' =>
                $validated['composition'],

            'colour' =>
                $validated['colour'],

            'sizes' =>
                $validated['sizes'],

            'embellishment' =>
                $validated['embellishment'] ?? 0,

            'yarn' =>
                $yarn,

            'trader_id' =>
                $validated['trader_id'],

            'trader_name' =>
                $validated['trader_name'],
            'trader_sku' =>
                $validated['trader_sku'] ?? null,
            'purchase_bill_no' =>
                $validated['purchase_bill_no'] ?? null,
            'purchase_date' =>
                $validated['purchase_date'] ?? null,
            'trader_quantity' =>
                $validated['trader_quantity'],
            'trader_buying_price' =>
                $validated['trader_buying_price'],
            'trader_selling_price' =>
                $validated['trader_selling_price'] ?? null,
            'trader_mrp' =>
                $validated['trader_mrp'] ?? null,
            'trader_min_selling_price' =>
                $validated['trader_min_selling_price'] ?? null,
            'trader_gst_percent' =>
                $validated['trader_gst_percent'] ?? null,
            'trader_discount' =>
                $validated['trader_discount'] ?? null,
            'trader_payment_terms' =>
                $validated['trader_payment_terms'] ?? null,
            'trader_reference' =>
                $validated['trader_reference'] ?? null,
            'trader_notes' =>
                $validated['trader_notes'] ?? null,

            'manufacturing_process' =>
                $validated['manufacturing_process'] ?? 0,

            'craftsman' =>
                $validated['craftsman'] ?? 0,

            'craftsman_code' =>
                $craftsmanCode,

            'manufecture' =>
                $validated['manufecture'] ?? 0,

            'client' =>
                $validated['client'] ?? 0,

            'clientreference' =>
                $validated['clientreference'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Company Context
            |--------------------------------------------------------------------------
            */

            'companyid' =>
                $companyId,

            'subcompanyid' =>
                $subCompanyId,

            'projectid' =>
                $projectId,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier_id' =>
                $supplierId,

            'supplier_person_id' =>
                $supplierPersonId,

            'supplier_product_id' =>
                $supplierProductId,

            /*
            |--------------------------------------------------------------------------
            | Login / Date
            |--------------------------------------------------------------------------
            */

            'loginid' =>
                $user->username,

            'edatetime' =>
                now(),

            /*
            |--------------------------------------------------------------------------
            | New ID
            |--------------------------------------------------------------------------
            */

            'id' =>
                $nextId,

            /*
            |--------------------------------------------------------------------------
            | NEW BARCODE
            |--------------------------------------------------------------------------
            */

            'barcode' =>
                $newBarcode,

            'qrcode' =>
                $newBarcode,

            /*
            |--------------------------------------------------------------------------
            | New Internal SKU
            |--------------------------------------------------------------------------
            */

            'sku' =>
                $generatedSku,

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'buying_price' =>

                $validated['trader_buying_price'],

            'min_price' =>

                $validated['trader_min_selling_price'] ?? null,

            'sale_price' =>

                $validated['trader_selling_price'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Supplier SKU
            |--------------------------------------------------------------------------
            */

            'sku_supplier' =>
                $supplierSku,

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            'img_path' =>
                $imgPath,

            'subimg_path' =>
                $subImgPath,

            /*
            |--------------------------------------------------------------------------
            | Other Existing Fields
            |--------------------------------------------------------------------------
            */

            'status' =>
                '',

            'box_assign' =>
                $specification->box_assign,

            'print_status' =>
                $specification->print_status,

            'description_id' =>
                null,

            'oc_product_id' =>
                $specification->oc_product_id,

            'oc_main_img' =>
                $specification->oc_main_img,

        ];


        /*
        |--------------------------------------------------------------------------
        | Insert New Version
        |--------------------------------------------------------------------------
        */

        $newSno =
            DB::table(
                'auto_traders_designer_specification_master'
            )
            ->insertGetId(
                $newRowData
            );

        DB::table('auto_traders_designer_specification_buying')
            ->insert([
                'trader_specification_id' => $nextId,
                'purchase_bill_no' => $validated['purchase_bill_no'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? null,
                'trader_quantity' => $validated['trader_quantity'],
                'trader_gst_percent' => $validated['trader_gst_percent'] ?? null,
                'trader_discount' => $validated['trader_discount'] ?? null,
                'trader_payment_terms' => $validated['trader_payment_terms'] ?? null,
                'trader_reference' => $validated['trader_reference'] ?? null,
                'trader_notes' => $validated['trader_notes'] ?? null,
                'companyid' => $companyId,
                'subcompanyid' => $subCompanyId,
                'projectid' => $projectId,
                'loginid' => $user->username ?? null,
                'edatetime' => now(),
                'tedit' => null,
            ]);


        /*
        |--------------------------------------------------------------------------
        | Link History To New Row
        |--------------------------------------------------------------------------
        */

        DB::table(
            'trader_designer_specification_history'
        )
            ->where(
                'sno',
                $historyId
            )
            ->update([
                'new_sno' =>
                    $newSno
            ]);


        /*
        |--------------------------------------------------------------------------
        | AI DATA - NEW VERSION
        |--------------------------------------------------------------------------
        */

        $aiFields = [

            'AI_product_name',
            'AI_product_description',
            'AI_Metatitle',
            'AI_Metakeywards',
            'AI_Metadescription',
            'AI_Producttag',
            'AI_Imagealttext',

        ];


        $aiInputWasSent = false;


        foreach ($aiFields as $field) {

            if ($request->has($field)) {

                $aiInputWasSent = true;

                break;
            }
        }


        $aiData = null;


        /*
        |--------------------------------------------------------------------------
        | AI Submitted
        |--------------------------------------------------------------------------
        */

        if ($aiInputWasSent) {

            $hasAiDetails = false;


            foreach ($aiFields as $field) {

                if (
                    isset($validated[$field]) &&
                    trim(
                        (string)
                        $validated[$field]
                    ) !== ''
                ) {

                    $hasAiDetails = true;

                    break;
                }
            }


            if ($hasAiDetails) {

                $aiData = [

                    'product_id' =>
                        $newSno,

                    'AI_product_name' =>
                        $validated[
                            'AI_product_name'
                        ] ?? null,

                    'AI_product_description' =>
                        $validated[
                            'AI_product_description'
                        ] ?? null,

                    'AI_Metatitle' =>
                        $validated[
                            'AI_Metatitle'
                        ] ?? null,

                    'AI_Metakeywards' =>
                        $validated[
                            'AI_Metakeywards'
                        ] ?? null,

                    'AI_Metadescription' =>
                        $validated[
                            'AI_Metadescription'
                        ] ?? null,

                    'AI_Producttag' =>
                        $validated[
                            'AI_Producttag'
                        ] ?? null,

                    'AI_Imagealttext' =>
                        $validated[
                            'AI_Imagealttext'
                        ] ?? null,

                    'company_id' =>
                        $companyId,

                    'subcompany_id' =>
                        $subCompanyId,

                    'projectid' =>
                        $projectId,

                ];
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | No AI Submitted - Copy Old AI
            |--------------------------------------------------------------------------
            */

            $oldAi =
                DB::table(
                    'AI_product_description'
                )
                ->where(
                    'product_id',
                    $specification->id
                )
                ->first();


            if ($oldAi) {

                $aiData = [

                    'product_id' =>
                        $newSno,

                    'AI_product_name' =>
                        $oldAi->AI_product_name,

                    'AI_product_description' =>
                        $oldAi->AI_product_description,

                    'AI_Metatitle' =>
                        $oldAi->AI_Metatitle,

                    'AI_Metakeywards' =>
                        $oldAi->AI_Metakeywards,

                    'AI_Metadescription' =>
                        $oldAi->AI_Metadescription,

                    'AI_Producttag' =>
                        $oldAi->AI_Producttag,

                    'AI_Imagealttext' =>
                        $oldAi->AI_Imagealttext,

                    'company_id' =>
                        $companyId,

                    'subcompany_id' =>
                        $subCompanyId,

                    'projectid' =>
                        $projectId,

                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Save AI
        |--------------------------------------------------------------------------
        */

        if ($aiData) {

            $descriptionId =
                DB::table(
                    'AI_product_description'
                )
                ->insertGetId(
                    $aiData
                );


            DB::table(
                'auto_traders_designer_specification_master'
            )
                ->where(
                    'sno',
                    $newSno
                )
                ->update([
                    'description_id' =>
                        $descriptionId
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update Supplier Product SKU
        |--------------------------------------------------------------------------
        */

        if (
            $supplierProductId !== null &&
            $supplierProductId !== ''
        ) {

            DB::table('supplier_products')
                ->where(
                    'sno',
                    $supplierProductId
                )
                ->update([
                    'product_sku' =>
                        $generatedSku
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | BARCODE CHANGED RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' =>
                true,

            'message' =>
                'Trader design specification updated successfully. Old version saved in history.',

            'id' =>
                $newSno,

            'old_id' =>
                $specification->sno,

            'barcode' =>
                $newBarcode,

            'old_barcode' =>
                $oldBarcode,

            'barcode_changed' =>
                true,

        ]);
    }

    /**
     * Generate barcode.
     */
    private function generateBarcode(
        int $companyId,
        int $subCompanyId,
        int $projectId,
        array $data
    ): string {

    return implode('', [
            
            $projectId,

            // Item Name
            $data['item_name'] ?? 0,

            // Item Type
            $data['item_type'] ?? 0,

            // Designer
            $data['designer_name'] ?? 0,

            // Colour
            $data['colour'] ?? 0,

            // Size
            $data['sizes'] ?? 0,

            // Client
            $data['client'] ?? 0,
        ]);

        // return implode('', [
        //     $companyId,
        //     $subCompanyId,
        //     $projectId,

        //     // Item Name
        //     $data['item_name'] ?? 0,

        //     // Item Type
        //     $data['item_type'] ?? 0,

        //     // Designer
        //     $data['designer_name'] ?? 0,

        //     // Colour
        //     $data['colour'] ?? 0,

        //     // Size
        //     $data['sizes'] ?? 0,

        //     // Client
        //     $data['client'] ?? 0,
        // ]);
    }


    /**
     * Validate master record against current company context.
     */
    private function validateMasterRecord(
        string $table,
        int $sno,
        int $companyId,
        int $subCompanyId,
        int $projectId
    ): void {

        $exists =
            DB::table($table)
                ->where('id', $sno)
                ->where('companyid', $companyId)
                ->where('subcompanyid', $subCompanyId)
                ->where('projectid', $projectId)
                ->exists();


        if (!$exists) {

            abort(
                response()->json([
                    'success' => false,
                    'message' =>
                        'Invalid master selection.'
                ], 422)
            );

        }
    }

    private function masterConfig(string $master): array
{
    $config = [

        'item_name' => [
            'title' => 'Item Name',
            'table' => 'auto_itemname_master',
            'name_column' => 'itemname',
            'select_id' => 'item_name',
            'has_code' => true,
        ],

        'item_type' => [
            'title' => 'Item Type',
            'table' => 'auto_itemtype_master',
            'name_column' => 'itemtype',
            'select_id' => 'item_type',
            'has_code' => true,
        ],

        'designer' => [
            'title' => 'Designer',
            'table' => 'auto_designer_master',
            'name_column' => 'designername',
            'select_id' => 'designer_name',
            'has_code' => true,
        ],

        'gender' => [
            'title' => 'Gender',
            'table' => 'auto_gender_master',
            'name_column' => 'name',
            'select_id' => 'gender_type',
            'has_code' => true,
        ],

        'composition' => [
            'title' => 'Composition',
            'table' => 'auto_composition_master_stock',
            'name_column' => 'composition_details',
            'select_id' => 'composition',
            'has_code' => true,
        ],

        'yarn' => [
            'title' => 'Yarn Name',
            'table' => 'auto_yarn_master',
            'name_column' => 'yarnname',
            'select_id' => 'yarn',
            'has_code' => true,
        ],

        'colour' => [
            'title' => 'Colour',
            'table' => 'auto_colour_master',
            'name_column' => 'colourname',
            'select_id' => 'colour',
            'has_code' => true,
        ],

        'size' => [
            'title' => 'Size',
            'table' => 'auto_size_master',
            'name_column' => 'size',
            'select_id' => 'sizes',
            'has_code' => true,
        ],

        'embellishment' => [
            'title' => 'Embellishment',
            'table' => 'auto_embellishment_master',
            'name_column' => 'embellishmentname',
            'select_id' => 'embellishment',
            'has_code' => true,
        ],

        'manufacturing_process' => [
            'title' => 'Manufacturing Process',
            'table' => 'auto_manufacturing_process_master',
            'name_column' => 'manufacturing_process',
            'select_id' => 'manufacturing_process',
            'has_code' => true,
        ],

        'craftsman' => [
            'title' => 'Craftsman',
            'table' => 'auto_craftsman_master',
            'name_column' => 'name',
            'select_id' => 'mcraftsman',
            'has_code' => true,
        ],

        'manufacture' => [
            'title' => 'Manufacture',
            'table' => 'auto_manufacture_master',
            'name_column' => 'name',
            'select_id' => 'cmbmanufacture',
            'has_code' => true,
        ],

        'client' => [
            'title' => 'Collection',
            'table' => 'auto_client_master',
            'name_column' => 'name',
            'select_id' => 'cmbclient',
            'has_code' => true,
        ],

    ];

    if (!isset($config[$master])) {
        abort(404, 'Invalid master type.');
    }

    return $config[$master];
}

private function generateMasterCode(
    string $name,
    string $table,
    int $companyId,
    int $subCompanyId,
    int $projectId,
    ?int $ignoreId = null
): string {
    $name = trim($name);

    /*
    |--------------------------------------------------------------------------
    | Create base 3-character code from master name
    |--------------------------------------------------------------------------
    */

    $cleanName = strtoupper(
        preg_replace(
            '/[^A-Z0-9 ]/',
            '',
            $name
        )
    );

    $words = preg_split(
        '/\s+/',
        trim($cleanName)
    );

    $code = '';

    /*
    | For multiple words:
    | Full Sleeve -> FSL
    | Ready Made  -> RMA
    */
    foreach ($words as $word) {

        if ($word === '') {
            continue;
        }

        $code .= substr($word, 0, 1);

        if (strlen($code) >= 3) {
            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fill remaining characters from name
    |--------------------------------------------------------------------------
    */

    $lettersOnly = preg_replace(
        '/[^A-Z0-9]/',
        '',
        $cleanName
    );

    for (
        $i = 0;
        $i < strlen($lettersOnly) &&
        strlen($code) < 3;
        $i++
    ) {
        $character = $lettersOnly[$i];

        if (strpos($code, $character) === false) {
            $code .= $character;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Always exactly 3 characters
    |--------------------------------------------------------------------------
    */

    $code = substr(
        str_pad(
            $code,
            3,
            'X'
        ),
        0,
        3
    );

    /*
    |--------------------------------------------------------------------------
    | Make code unique
    |--------------------------------------------------------------------------
    */

    $baseCode = $code;
    $counter = 1;

    while (true) {

        $query = DB::table($table)
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->where('code', $code);

        /*
        | During UPDATE, ignore the current record.
        */
        if ($ignoreId !== null) {
            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }

        if (!$query->exists()) {
            break;
        }

        /*
        | Keep exactly 3 characters.
        |
        | Example:
        | COT
        | CO1
        | CO2
        */
        $suffix = (string) $counter;

        $code =
            substr(
                $baseCode,
                0,
                3 - strlen($suffix)
            ) . $suffix;

        $counter++;
    }

    return strtoupper($code);
}

  public function masterList(string $master)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $config = $this->masterConfig($master);

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;

    $rows = DB::table($config['table'])
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->orderBy($config['name_column'])
        ->get([
            'id',
            $config['name_column'],
            'code'
        ]);

    return response()->json([
        'success' => true,
        'master' => $master,
        'title' => $config['title'],
        'select_id' => $config['select_id'],
        'has_code' => true,
        'data' => $rows
    ]);
}

public function masterStore(Request $request, string $master)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $config = $this->masterConfig($master);

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;

    $request->validate([
        'name' => 'required|string|max:500',
    ]);

    $name = trim($request->name);

    /*
    |--------------------------------------------------------------------------
    | Duplicate name check
    |--------------------------------------------------------------------------
    */

    $duplicate = DB::table($config['table'])
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->whereRaw(
            'LOWER(`' . $config['name_column'] . '`) = ?',
            [strtolower($name)]
        )
        ->exists();

    if ($duplicate) {
        return response()->json([
            'success' => false,
            'message' => $config['title'] . ' already exists.'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate 3-character code
    |--------------------------------------------------------------------------
    */

    $code = $this->generateMasterCode(
        $name,
        $config['table'],
        $companyId,
        $subCompanyId,
        $projectId
    );

    /*
    |--------------------------------------------------------------------------
    | Generate next ID
    |--------------------------------------------------------------------------
    |
    | Every master table uses:
    |
    | id = MAX(id) + 1
    |
    | This is required even if the table also has sno.
    |
    */

    $maxId = DB::table($config['table'])
        ->max('id');

    $nextId = ((int) $maxId) + 1;

    /*
    |--------------------------------------------------------------------------
    | Insert
    |--------------------------------------------------------------------------
    */

    $insertData = [
        'id' => $nextId,

        $config['name_column'] => $name,

        'companyid' => $companyId,

        'subcompanyid' => $subCompanyId,

        'projectid' => $projectId,

        'code' => $code,
    ];

    /*
    |--------------------------------------------------------------------------
    | Insert
    |--------------------------------------------------------------------------
    */

    $newSno = DB::table($config['table'])
        ->insertGetId($insertData);

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,

        'message' =>
            $config['title'] . ' added successfully.',

        'id' =>
            $nextId,

        'sno' =>
            $newSno,

        'name' =>
            $name,

        'code' =>
            $code,

        'select_id' =>
            $config['select_id']
    ]);
}

    public function masterUpdate(
    Request $request,
    string $master,
    int $id
) {
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $config = $this->masterConfig($master);

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;

    $request->validate([
        'name' => 'required|string|max:500',
    ]);

    $name = trim($request->name);

    /*
    |--------------------------------------------------------------------------
    | Find selected master using ID
    |--------------------------------------------------------------------------
    */

    $masterRow = DB::table($config['table'])
        ->where('id', $id)
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->first();

    if (!$masterRow) {
        return response()->json([
            'success' => false,
            'message' => 'Selected master record not found.'
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate name check
    |--------------------------------------------------------------------------
    */

    $duplicate = DB::table($config['table'])
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->where('id', '!=', $id)
        ->whereRaw(
            'LOWER(`' . $config['name_column'] . '`) = ?',
            [strtolower($name)]
        )
        ->exists();

    if ($duplicate) {
        return response()->json([
            'success' => false,
            'message' => $config['title'] . ' already exists.'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate new 3-character code
    |--------------------------------------------------------------------------
    */

    $code = $this->generateMasterCode(
        $name,
        $config['table'],
        $companyId,
        $subCompanyId,
        $projectId,
        $id
    );

    /*
    |--------------------------------------------------------------------------
    | Update using ID only
    |--------------------------------------------------------------------------
    */

    $updateData = [
        $config['name_column'] => $name,
        'code' => $code,
    ];

    DB::table($config['table'])
        ->where('id', $id)
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->update($updateData);

    return response()->json([
        'success' => true,
        'message' => $config['title'] . ' updated successfully.',
        'id' => $id,
        'name' => $name,
        'code' => $code,
        'select_id' => $config['select_id']
    ]);
}

    public function uploadedImages(Request $request)
{
    $user = Auth::user();

    if (!$user) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);

    }


    /*
    |--------------------------------------------------------------------------
    | Current company context
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) $user->company_id;

    $subCompanyId =
        (int) $user->sub_company_id;

    $projectId =
        (int) $user->project_id;


    /*
    |--------------------------------------------------------------------------
    | Get uploaded images
    |--------------------------------------------------------------------------
    |
    | Same logic as your Core PHP:
    |
    | imgused IS NULL / empty
    | tedit IS NULL / empty
    |
    |--------------------------------------------------------------------------
    */

    $rows = DB::table('GAR_initial_submission')

        ->where(function ($query) {

            $query
                ->whereNull('imgused')
                ->orWhere('imgused', '');

        })

        ->where(function ($query) {

            $query
                ->whereNull('tedit')
                ->orWhere('tedit', '');

        })

        /*
        |--------------------------------------------------------------------------
        | Current company / sub-company / project
        |--------------------------------------------------------------------------
        */

        // ->where('company_id', $companyId)
        // ->where('sub_companyid', $subCompanyId)
        // ->where('projectid', $projectId)

        ->orderByDesc('sno')

        ->get([
            'sno',
            'main_image',
            'download_filename',
            'user_id',
            'user_name',
            'company_id',
            'sub_companyid',
            'projectid',
            'garment_name',
            'garment_type'
        ]);


    /*
    |--------------------------------------------------------------------------
    | Build image URL
    |--------------------------------------------------------------------------
    */

    $data = $rows->map(function ($row) {

        $imageUrl =
        asset(
            'INITIALSUBMISSION/' .
            $row->company_id . '/' .
            $row->sub_companyid . '/' .
            $row->projectid . '/' .
            $row->user_id . '/main/' .
            $row->main_image
        );


        return [

            'sno' =>
                $row->sno,

            'main_image' =>
                $row->main_image,

            'download_filename' =>
                $row->download_filename,

            'user_id' =>
                $row->user_id,

            'user_name' =>
                $row->user_name,

            'company_id' =>
                $row->company_id,

            'sub_companyid' =>
                $row->sub_companyid,

            'projectid' =>
                $row->projectid,

            'garment_name' =>
                $row->garment_name,

            'garment_type' =>
                $row->garment_type,

            'image_url' =>
                $imageUrl

        ];

    });


    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

}