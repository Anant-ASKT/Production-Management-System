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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Mockery\Matcher\AndAnyOtherArgs;

class DesignSpecificationController extends Controller
{
    /**
     * Show Design Specification Master page.
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
    'design-specifications.index',
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
        'clients'
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
            'sp.product_sku',

            'sp.created_at',
            'sp.updated_at'
        )

        ->where(
            'sp.status',
            'active'
        )
        ->where(function ($q) {
            $q->whereNull('sp.status_done')
            ->orWhere('sp.status_done', '');
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

    public function findBySupplierSku(Request $request)
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
    | CURRENT LOGIN CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER SKU
    |--------------------------------------------------------------------------
    */

    $supplierSku = trim(
        (string) $request->input('sku', '')
    );

    if ($supplierSku === '') {
        return response()->json([
            'success' => false,
            'message' => 'Supplier SKU is empty.'
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | FIND SPECIFICATION
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | supplier SKU is stored in:
    |
    |     sku_supplier
    |
    | NOT in the internally generated "sku".
    |
    */

    $specification = DB::table(
        'auto_designer_specification_master as dsm'
    )

        /*
        |--------------------------------------------------------------------------
        | MASTER TABLES
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as designer',
            'designer.id',
            '=',
            'dsm.designer_name'
        )

        ->leftJoin(
            'auto_itemtype_master as itemtype',
            'itemtype.id',
            '=',
            'dsm.item_type'
        )

        ->leftJoin(
            'auto_gender_master as gender',
            'gender.id',
            '=',
            'dsm.gender'
        )

        ->leftJoin(
            'auto_itemname_master as itemname',
            'itemname.id',
            '=',
            'dsm.item_name'
        )

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

        ->leftJoin(
            'auto_colour_master as colour',
            'colour.id',
            '=',
            'dsm.colour'
        )

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
            'AI_product_description as ai',
            'ai.product_id',
            '=',
            'dsm.id'
        )

        /*
        |--------------------------------------------------------------------------
        | FIND BY SUPPLIER SKU
        |--------------------------------------------------------------------------
        */

        ->where(
            'dsm.sku',
            $supplierSku
        )

        /*
        |--------------------------------------------------------------------------
        | CURRENT COMPANY / SUB COMPANY / PROJECT
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
        | CURRENT VERSION ONLY
        |--------------------------------------------------------------------------
        */

        ->where(function ($query) {
            $query
                ->whereNull('dsm.tedit')
                ->orWhere('dsm.tedit', '');
        })

        ->where(function ($query) {
            $query
                ->whereNull('dsm.status')
                ->orWhere('dsm.status', '')
                ->orWhere('dsm.status', 'done');
        })

        /*
        |--------------------------------------------------------------------------
        | SELECT COMPLETE SPECIFICATION
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

            'dsm.designer_name',
            'dsm.item_type',
            'dsm.gender',
            'dsm.item_name',
            'dsm.composition',
            'dsm.yarn',
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
            | DISPLAY NAMES
            |--------------------------------------------------------------------------
            */

            'designer.designername as designer_name_text',
            'itemtype.itemtype as item_type_text',
            'gender.name as gender_text',
            'itemname.itemname as item_name_text',
            'composition.composition_details as composition_text',
            'yarn.yarnname as yarn_text',
            'colour.colourname as colour_text',
            'size.size as size_text',

            'embellishment.embellishmentname as embellishment_text',
            'manufacturing.manufacturing_process as manufacturing_process_text',
            'craftsman.name as craftsman_text',
            'manufacture.name as manufacture_text',
            'client.name as client_text',

            /*
            |--------------------------------------------------------------------------
            | PRODUCT DATA
            |--------------------------------------------------------------------------
            */

            'dsm.barcode',
            'dsm.sku',
            'dsm.sku_supplier',

            'dsm.clientreference',

            'dsm.price',
            'dsm.sale_price',
            'dsm.min_price',

            'dsm.img_path',
            'dsm.subimg_path',

            'dsm.edatetime',
            'dsm.status',

            /*
            |--------------------------------------------------------------------------
            | AI DATA
            |--------------------------------------------------------------------------
            */

            'ai.AI_product_name',
            'ai.AI_product_description',
            'ai.AI_Metatitle',
            'ai.AI_Metakeywards',
            'ai.AI_Metadescription',
            'ai.AI_Producttag',
            'ai.AI_Imagealttext'
        ])

        ->orderByDesc('dsm.sno')
        ->first();


    /*
    |--------------------------------------------------------------------------
    | NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$specification) {

        return response()->json([
            'success' => true,
            'found' => false,
            'message' =>
                'No Design Specification found for this Supplier SKU.'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'found' => true,
        'data' => $specification
    ]);
}


    /**
     * AJAX:
     * Load all design specifications.
     *
     * This method is called ONLY when the user clicks
     * "Show All Specifications".
     */
    /**
 * AJAX:
 * Load design specifications with pagination and search.
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
        'auto_designer_specification_master as dsm'
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

            'dsm.price',
            'dsm.sale_price',
            'dsm.min_price',

            // Optional specification names
            'embellishment.embellishmentname as embellishment_text',

            'manufacturing.manufacturing_process
                as manufacturing_process_text',

            'craftsman.name as craftsman_text',

            'manufacture.name as manufacture_text',

            'client.name as client_text',

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
        'auto_designer_specification_master as dsm'
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
     * Save new Design Specification.
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
    | SUPPLIER SKU STOCK-ONLY CHECK
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | This check happens BEFORE normal validation.
    |
    | Reason:
    | The normal validation has:
    |
    | Rule::unique(
    |     'auto_designer_specification_master',
    |     'sku'
    | )
    |
    | So an existing SKU would otherwise fail validation.
    |
    |
    | LOGIC:
    |
    | 1. supplier_id exists
    | 2. sku exists
    | 3. SKU already exists in Design Specification Master
    |
    | Then:
    |
    | - DO NOT create another master record
    | - DO NOT generate barcode
    | - DO NOT upload images
    | - DO NOT generate another SKU
    | - ONLY insert stock into vendor_stock_web
    |
    |--------------------------------------------------------------------------
    */

    $incomingSupplierId = $request->input('supplier_id');

    // Normalize every supplier-id field used by the supplier-product UI.
    if (empty($incomingSupplierId)) {
        $incomingSupplierId = $request->input('login_supplier_id');
    }
    if (empty($incomingSupplierId)) {
        $incomingSupplierId = $request->input('loginsupplerid');
    }
    if (empty($incomingSupplierId)) {
        $incomingSupplierId = $request->input('supplier_id_selected');
    }

    $supplierProductId = $request->input('supplier_product_id');
    $supplierProduct = null;

    // If the Save request does not contain supplier_id, recover it from the
    // selected supplier_products row. This is important because the UI
    // already sends supplier_product_id when a supplier product is selected.
    if (!empty($supplierProductId)) {
        $supplierProduct = DB::table('supplier_products')
            ->where('sno', $supplierProductId)
            ->first([
                'supplier_id',
                'product_sku',
                'stock',
                'price',
                'sale_price',
                'min_price'
            ]);

        if (empty($incomingSupplierId) && $supplierProduct) {
            $incomingSupplierId = $supplierProduct->supplier_id ?? null;
        }
    }

    // The supplier SKU may be posted under different names depending on
    // which supplier-product control populated the form.
    $incomingSku = trim((string) $request->input('sku'));

    if ($incomingSku === '') {
        $incomingSku = trim((string) $request->input('supplier_sku'));
    }
    if ($incomingSku === '') {
        $incomingSku = trim((string) $request->input('supplierSku'));
    }
    if ($incomingSku === '') {
        $incomingSku = trim((string) $request->input('sku_supplier'));
    }
    if ($incomingSku === '' && $supplierProduct) {
        $incomingSku = trim((string) ($supplierProduct->product_sku ?? ''));
    }

    logger()->info('STORE SUPPLIER SKU CHECK', [
        'supplier_id' => $incomingSupplierId,
        'sku' => $incomingSku,
        'stock_qty' => $request->input('stock_qty'),
        'login_supplier_stock' => $request->input('login_supplier_stock'),
        'supplier_product_stock' => $supplierProduct->stock ?? null,
        'supplier_product_id' => $supplierProductId,
    ]);

    $hasActualSupplier = !empty($incomingSupplierId);


    /*
    |--------------------------------------------------------------------------
    | ONLY CHECK EXISTING SKU WHEN SUPPLIER IS SELECTED
    |--------------------------------------------------------------------------
    */

    if (
        $hasActualSupplier &&
        $incomingSku !== ''
    ) {

        /*
        |--------------------------------------------------------------------------
        | Find Existing Design Specification Master
        |--------------------------------------------------------------------------
        */

        // IMPORTANT: For the supplier-existing-SKU path, search the
        // Design Specification Master by SKU ONLY.  Do not add the
        // current company/sub-company/project filters here because the
        // selected supplier product is already the source of the SKU.
        $existingSpecification =
            DB::table(
                'auto_designer_specification_master'
            )
            ->where(function ($query) use ($incomingSku) {
                $query->whereRaw('TRIM(sku) = ?', [$incomingSku])
                      ->orWhereRaw('TRIM(sku_supplier) = ?', [$incomingSku]);
            })
            ->orderByDesc('sno')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | EXISTING SKU FOUND
        |--------------------------------------------------------------------------
        |
        | This is the important new path.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $existingSpecification
        ) {

            /*
            |--------------------------------------------------------------------------
            | Stock Qty
            |--------------------------------------------------------------------------
            |
            | First use the new Stock Qty field.
            |
            | Fallback to login_supplier_stock so existing frontend
            | code continues to work.
            |
            |--------------------------------------------------------------------------
            */

            $stockQty =
                $request->input(
                    'stock_qty'
                );


            if (
                $stockQty === null ||
                $stockQty === ''
            ) {

                $stockQty =
                    $request->input(
                        'login_supplier_stock'
                    );
            }

            if (
                ($stockQty === null || $stockQty === '') &&
                $supplierProduct
            ) {
                $stockQty = $supplierProduct->stock ?? null;
            }


            /*
            |--------------------------------------------------------------------------
            | Validate Stock Qty
            |--------------------------------------------------------------------------
            */

            if (
                $stockQty === null ||
                $stockQty === '' ||
                !is_numeric($stockQty)
            ) {

                return response()->json([

                    'success' => false,

                    'stock_only' => true,

                    'message' =>
                        'Please enter a valid Stock Qty.'

                ], 422);
            }


            $stockQty =
                (int) $stockQty;


            if (
                $stockQty <= 0
            ) {

                return response()->json([

                    'success' => false,

                    'stock_only' => true,

                    'message' =>
                        'Stock Qty must be greater than 0.'

                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | EXISTING MASTER ITEM ID
            |--------------------------------------------------------------------------
            */

            $masterItemId =
                (int) (
                    $existingSpecification->id
                    ?? $existingSpecification->sno
                    ?? 0
                );


            if (
                $masterItemId <= 0
            ) {

                return response()->json([

                    'success' => false,

                    'stock_only' => true,

                    'message' =>
                        'Existing Design Specification has no valid item ID.'

                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | EXISTING MASTER BARCODE
            |--------------------------------------------------------------------------
            */

            $masterBarcode =
                trim(
                    (string) (
                        $existingSpecification->barcode
                        ?? ''
                    )
                );


            if (
                $masterBarcode === ''
            ) {

                return response()->json([

                    'success' => false,

                    'stock_only' => true,

                    'message' =>
                        'Existing Design Specification has no barcode.'

                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | STOCK DATE
            |--------------------------------------------------------------------------
            */

            $createatDate =
                $request->input(
                    'login_createatdate'
                );


            if (
                empty($createatDate)
            ) {

                $createatDate =
                    now();
            }


            /*
            |--------------------------------------------------------------------------
            | Convert Stock Date
            |--------------------------------------------------------------------------
            */

            try {

                $stockDate =
                    \Carbon\Carbon::parse(
                        $createatDate
                    );

            } catch (
                \Throwable $e
            ) {

                $stockDate =
                    now();
            }


            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            /*
            | Supplier price if supplied from selected product.
            | Otherwise use existing DSM price.
            */

            $purchasePrice =
                $request->input(
                    'price'
                );


            if (
                ($purchasePrice === null || $purchasePrice === '') &&
                $supplierProduct
            ) {
                $purchasePrice = $supplierProduct->price ?? null;
            }

            if (
                $purchasePrice === null ||
                $purchasePrice === ''
            ) {

                $purchasePrice =
                    $existingSpecification->price
                    ?? 0;
            }


            if (
                !is_numeric($purchasePrice)
            ) {

                $purchasePrice =
                    0;
            }


            $purchasePrice =
                (float) $purchasePrice;


            /*
            |--------------------------------------------------------------------------
            | SALE PRICE
            |--------------------------------------------------------------------------
            */

            $salePrice =
                $request->input(
                    'saleprice'
                );


            if (
                ($salePrice === null || $salePrice === '') &&
                $supplierProduct
            ) {
                $salePrice = $supplierProduct->sale_price ?? null;
            }

            if (
                $salePrice === null ||
                $salePrice === ''
            ) {

                $salePrice =
                    $existingSpecification->sale_price
                    ?? 0;
            }


            if (
                !is_numeric($salePrice)
            ) {

                $salePrice =
                    0;
            }


            $salePrice =
                (float) $salePrice;


            /*
            |--------------------------------------------------------------------------
            | Generate vendor_stock_web IDs
            |--------------------------------------------------------------------------
            |
            | vendor_stock_web.id is UNIQUE but is NOT AUTO_INCREMENT.
            |
            |--------------------------------------------------------------------------
            */

            $nextVendorStockWebId =
                (
                    (int)
                    DB::table(
                        'vendor_stock_web'
                    )->max('id')
                ) + 1;


            /*
            |--------------------------------------------------------------------------
            | INSERT STOCK
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | stock_qty = 5
            |
            | Creates:
            |
            | row 1 -> quantity_received = 1
            | row 2 -> quantity_received = 1
            | row 3 -> quantity_received = 1
            | row 4 -> quantity_received = 1
            | row 5 -> quantity_received = 1
            |
            |--------------------------------------------------------------------------
            */

            try {

                $createdStockRows =
                    DB::transaction(
                        function () use (
                            $companyId,
                            $subCompanyId,
                            $projectId,
                            $incomingSupplierId,
                            $masterItemId,
                            $masterBarcode,
                            $incomingSku,
                            $stockQty,
                            $purchasePrice,
                            $salePrice,
                            $stockDate,
                            &$nextVendorStockWebId
                        ) {

                            $rows =
                                [];


                            for (
                                $stockIndex = 0;
                                $stockIndex < $stockQty;
                                $stockIndex++
                            ) {

                                $rows[] = [

                                    /*
                                    |--------------------------------------------------------------------------
                                    | UNIQUE ID
                                    |--------------------------------------------------------------------------
                                    */

                                    'id' =>
                                        $nextVendorStockWebId++,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | COMPANY
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
                                    | SUPPLIER
                                    |--------------------------------------------------------------------------
                                    */

                                    'vendor_id' =>
                                        (int) $incomingSupplierId,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | EXISTING DSM ITEM
                                    |--------------------------------------------------------------------------
                                    */

                                    'item_id' =>
                                        $masterItemId,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | SKU
                                    |--------------------------------------------------------------------------
                                    |
                                    | vendor_stock_web does not have a SKU column.
                                    |
                                    | Therefore SKU is stored in batch_no.
                                    |
                                    |--------------------------------------------------------------------------
                                    */

                                    'batch_no' =>
                                        $incomingSku,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | STOCK DATE
                                    |--------------------------------------------------------------------------
                                    */

                                    'stock_date' =>
                                        $stockDate->toDateString(),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ONE ROW = ONE STOCK ITEM
                                    |--------------------------------------------------------------------------
                                    */

                                    'quantity_received' =>
                                        1,

                                    'send_qty' =>
                                        0,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | PRICES
                                    |--------------------------------------------------------------------------
                                    */

                                    'sale_price' =>
                                        $salePrice,

                                    'purchase_price' =>
                                        $purchasePrice,

                                    'total_cost' =>
                                        $purchasePrice,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | OTHER STOCK FIELDS
                                    |--------------------------------------------------------------------------
                                    */

                                    'warehouse_id' =>
                                        null,

                                    'warehouse_location' =>
                                        null,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | KEEP SKU INFORMATION
                                    |--------------------------------------------------------------------------
                                    */

                                    'remarks' =>
                                        'Supplier SKU: ' .
                                        $incomingSku .
                                        ' | Existing DSM Item ID: ' .
                                        $masterItemId .
                                        ' | Existing DSM Barcode: ' .
                                        $masterBarcode,

                                    'boxid' =>
                                        null,

                                    'stock_transfer' =>
                                        null,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | DSM REFERENCE
                                    |--------------------------------------------------------------------------
                                    */

                                    'g_id' =>
                                        $masterItemId,

                                    'barcode' =>
                                        $masterBarcode,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | STOCK ENTRY DATE
                                    |--------------------------------------------------------------------------
                                    */

                                    'stockentrydate' =>
                                        $stockDate->toDateString(),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | RECEIVED FROM SUPPLIER
                                    |--------------------------------------------------------------------------
                                    */

                                    'receivedfromid' =>
                                        (int) $incomingSupplierId,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | REMAINING FIELDS
                                    |--------------------------------------------------------------------------
                                    */

                                    'measurementunit_id' =>
                                        null,

                                    'inputcost_measurementunit_id' =>
                                        null,

                                    'quantityreceived_id' =>
                                        null,

                                    'location_id' =>
                                        null,

                                    'stockremovaldate' =>
                                        null,

                                    'giventi_id' =>
                                        null,

                                    'quantity_given' =>
                                        null,

                                    'outputcost_measurementunit_id' =>
                                        null,

                                    'tedit' =>
                                        null,

                                    'flag_sendtointernal' =>
                                        null,

                                    'trolley_no' =>
                                        null,

                                    'trolley_remarks' =>
                                        null,

                                    'orderno' =>
                                        null,

                                    'orderid' =>
                                        null,

                                    'shipmentno' =>
                                        null,

                                    'shipmentreparks' =>
                                        null,

                                    'orderstatus' =>
                                        null,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | CATEGORY
                                    |--------------------------------------------------------------------------
                                    */

                                    'update_category' =>
                                        'SUPPLIER_SKU_STOCK',

                                    /*
                                    |--------------------------------------------------------------------------
                                    | DATE/TIME
                                    |--------------------------------------------------------------------------
                                    */

                                    'createat_date' =>
                                        $stockDate,

                                    'created_at' =>
                                        now(),

                                    'updated_at' =>
                                        now(),
                                ];
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | INSERT ALL STOCK ROWS
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !empty($rows)
                            ) {

                                DB::table(
                                    'vendor_stock_web'
                                )->insert(
                                    $rows
                                );
                            }


                            return count(
                                $rows
                            );
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | STOCK-ONLY SUCCESS
                |--------------------------------------------------------------------------
                */

                return response()->json([

                    'success' =>
                        true,

                    'stock_only' =>
                        true,

                    'message' =>
                        'Supplier stock saved successfully. Existing Design Specification was not duplicated.',

                    'sku' =>
                        $incomingSku,

                    'item_id' =>
                        $masterItemId,

                    'barcode' =>
                        $masterBarcode,

                    'quantity' =>
                        $stockQty,

                    'rows_created' =>
                        $createdStockRows

                ]);


            } catch (
                \Throwable $e
            ) {

                logger()->error(
                    'SUPPLIER EXISTING SKU STOCK SAVE ERROR',
                    [
                        'message' => $e->getMessage(),

                        'supplier_id' => $incomingSupplierId,

                        'sku' => $incomingSku,

                        'item_id' => $masterItemId,

                        'barcode' => $masterBarcode,

                        'stock_qty' => $stockQty
                    ]
                );


                return response()->json([

                    'success' =>
                        false,

                    'stock_only' =>
                        true,

                    'message' =>
                        'Unable to save supplier stock: ' .
                        $e->getMessage()

                ], 500);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NORMAL EXISTING PROCESS STARTS HERE
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | If supplier_id is empty:
    |     normal process
    |
    | If supplier_id exists but SKU does NOT exist in DSM:
    |     normal process
    |
    | Therefore your existing new-product functionality is preserved.
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (!empty($incomingSupplierId)) {
        $request->merge(['supplier_id' => $incomingSupplierId]);
    }
    if ($incomingSku !== '') {
        $request->merge(['sku' => $incomingSku]);
    }

    $validated = $request->validate([

        'item_name' =>
            'required',

        'item_type' =>
            'required',

        'designer_name' =>
            'required',

        'gender' =>
            'required',

        'composition' =>
            'required',

        'colour' =>
            'required',

        'sizes' =>
            'required',


        'item_name_code' =>
            'nullable|string|max:100',

        'item_type_code' =>
            'nullable|string|max:100',

        'designer_code' =>
            'nullable|string|max:100',

        'gender_code' =>
            'nullable|string|max:100',

        'composition_code' =>
            'nullable|string|max:100',

        'colour_code' =>
            'nullable|string|max:100',

        'size_code' =>
            'nullable|string|max:100',

        'embellishment_code' =>
            'nullable|string|max:100',

        'manufacturing_process_code' =>
            'nullable|string|max:100',

        'craftsman_code' =>
            'nullable|string|max:100',

        'manufacture_code' =>
            'nullable|string|max:100',

        'client_code' =>
            'nullable|string|max:100',


        'supplier_id' =>
            'nullable|integer',

        'supplier_user_id' =>
            'nullable|integer',

        'supplier_nickname' =>
            'nullable|string|max:10',

        'supplier_product_id' =>
            'nullable|integer',


        'embellishment' =>
            'nullable',

        'yarn' =>
            'nullable',

        'manufacturing_process' =>
            'nullable',

        'craftsman' =>
            'nullable',

        'manufecture' =>
            'nullable',

        'client' =>
            'nullable',


        'sku' => [

            'nullable',

            'string',

            'max:1000',

            Rule::unique(
                'auto_designer_specification_master',
                'sku'
            ),

        ],


        'clientreference' =>
            'nullable|string',

        'price' =>
            'nullable|string',

        'minprice' =>
            'nullable|string',

        'saleprice' =>
            'nullable|string',


        'AI_product_name' =>
            'nullable|string',

        'AI_product_description' =>
            'nullable|string',

        'AI_Metatitle' =>
            'nullable|string',

        'AI_Metakeywards' =>
            'nullable|string',

        'AI_Metadescription' =>
            'nullable|string',

        'AI_Producttag' =>
            'nullable|string',

        'AI_Imagealttext' =>
            'nullable|string',

    ]);


    /*
    |--------------------------------------------------------------------------
    | Verify selected master records belong to current context
    |--------------------------------------------------------------------------
    */

    $this->validateMasterRecord(
        'auto_designer_master',
        $validated['designer_name'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_itemtype_master',
        $validated['item_type'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_gender_master',
        $validated['gender'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_itemname_master',
        $validated['item_name'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_composition_master_stock',
        $validated['composition'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_colour_master',
        $validated['colour'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    $this->validateMasterRecord(
        'auto_size_master',
        $validated['sizes'],
        $companyId,
        $subCompanyId,
        $projectId
    );


    /*
    |--------------------------------------------------------------------------
    | Optional master validation
    |--------------------------------------------------------------------------
    */

    if (
        !empty($validated['embellishment'])
    ) {

        $this->validateMasterRecord(
            'auto_embellishment_master',
            $validated['embellishment'],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }


    if (
        !empty($validated['manufacturing_process'])
    ) {

        $this->validateMasterRecord(
            'auto_manufacturing_process_master',
            $validated['manufacturing_process'],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }


    if (
        !empty($validated['craftsman'])
    ) {

        $this->validateMasterRecord(
            'auto_craftsman_master',
            $validated['craftsman'],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }


    if (
        !empty($validated['manufecture'])
    ) {

        $this->validateMasterRecord(
            'auto_manufacture_master',
            $validated['manufecture'],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }


    if (
        !empty($validated['client'])
    ) {

        $this->validateMasterRecord(
            'auto_client_master',
            $validated['client'],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Craftsman Code
    |--------------------------------------------------------------------------
    */

    $craftsmanCode =
        $validated['craftsman_code'] ?? null;


    if (
        !empty($validated['craftsman'])
    ) {

        $craftsman =
            DB::table(
                'auto_craftsman_master'
            )
            ->where(
                'sno',
                $validated['craftsman']
            )
            ->where(
                'companyid',
                $companyId
            )
            ->where(
                'subcompanyid',
                $subCompanyId
            )
            ->where(
                'projectid',
                $projectId
            )
            ->first([
                'code'
            ]);


        if (
            $craftsman
        ) {

            $craftsmanCode =
                $craftsman->code;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Barcode
    |--------------------------------------------------------------------------
    */

    $barcode =
        $this->generateBarcode(
            $companyId,
            $subCompanyId,
            $projectId,
            $validated
        );


    $nextId =
        (
            (int)
            DB::table(
                'auto_designer_specification_master'
            )->max('id')
        ) + 1;


    /*
    |--------------------------------------------------------------------------
    | Supplier ID and Supplier Nickname for SKU
    |--------------------------------------------------------------------------
    */

    $supplierId =
        $validated['supplier_id'] ?? null;


    /*
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Check whether actual supplier was selected BEFORE
    | assigning project ID as fallback.
    |--------------------------------------------------------------------------
    */

    $hasSupplier =
        !empty($supplierId);


    $supplierNickname =
        '';


    if (
        empty($supplierId)
    ) {

        $supplierId =
            $projectId;


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
            !empty($projectMaster->projectname)
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

    } else {

        $supplierNickname =
            $validated['supplier_nickname'] ?? '';
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Automatic SKU
    |--------------------------------------------------------------------------
    */

    $itemTypeCode =
        $validated['item_name_code'] ?? '';


    $generatedSku =
        $itemTypeCode .
        '-' .
        $supplierNickname .
        '-' .
        $nextId;


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
    | Check Duplicate Barcode
    |--------------------------------------------------------------------------
    */

    $barcodeExistsInMaster =
        DB::table(
            'auto_designer_specification_master'
        )
        ->where(
            'barcode',
            $barcode
        )
        ->exists();


    $barcodeExistsInStock =
        DB::table(
            'vendor_stock'
        )
        ->where(
            'barcode',
            $barcode
        )
        ->exists();


    if (
        $barcodeExistsInMaster ||
        $barcodeExistsInStock
    ) {

        return response()->json([

            'success' =>
                false,

            'message' =>
                'This barcode already exists. Data was not saved.'

        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | Image path
    |--------------------------------------------------------------------------
    */

    $imgPath =
        null;


    $price =
        $validated['price'] ?? 0;


    $minprice =
        $validated['minprice'] ?? 0;


    $saleprice =
        $validated['saleprice'] ?? 0;


    /*
    |--------------------------------------------------------------------------
    | Save Database Record
    |--------------------------------------------------------------------------
    */

    $insertId =
        DB::table(
            'auto_designer_specification_master'
        )
        ->insertGetId([

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

            'companyid' =>
                $companyId,

            'subcompanyid' =>
                $subCompanyId,

            'projectid' =>
                $projectId,

            'supplier_person_id' =>
                $request->input(
                    'supplier_user_id'
                ) ?: null,

            'supplier_product_id' =>
                $request->input(
                    'supplier_product_id'
                ) ?: null,

            'supplier_id' =>
                $supplierId,

            'loginid' =>
                $user->username,

            'edatetime' =>
                now(),

            'id' =>
                $nextId,

            'barcode' =>
                $barcode,

            'qrcode' =>
                $barcode,

            'sku' =>
                $generatedSku,

            'price' =>
                $validated['price'] ?? null,

            'min_price' =>
                $validated['minprice'] ?? null,

            'sale_price' =>
                $validated['saleprice'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Supplier SKU
            |--------------------------------------------------------------------------
            */

            'sku_supplier' =>
                $supplierSku,

            'img_path' =>
                $imgPath,

            'status' =>
                '',

            'box_assign' =>
                '',

            'print_status' =>
                null,

            'description_id' =>
                null,

            'oc_product_id' =>
                null,

            'oc_main_img' =>
                null,

            'yarn' =>
                $validated['yarn'] ?? null,

        ]);


    /*
    |--------------------------------------------------------------------------
    | Image Upload
    |--------------------------------------------------------------------------
    |
    | For now store uploaded images under:
    |
    | public/ItemsDesigner_Masterwithbarcode/{barcode}/
    |
    |--------------------------------------------------------------------------
    */

    if (
        $request->hasFile(
            'design_images'
        )
    ) {

        /*
         * Main product directory
         */

        $imageDirectory =
            public_path(
                'ItemsDesigner_Masterwithbarcode/' .
                $barcode
            );


        /*
         * Create directory if it doesn't exist
         */

        if (
            !is_dir(
                $imageDirectory
            )
        ) {

            mkdir(
                $imageDirectory,
                0755,
                true
            );
        }


        /*
         * Store relative paths for database
         */

        $uploadedPaths =
            [];


        foreach (
            $request->file(
                'design_images'
            ) as $image
        ) {

            /*
             * Generate unique filename
             */

            $fileName =
                $generatedSku .
                '-' .
                \Illuminate\Support\Str::random(
                    10
                ) .
                '.' .
                strtolower(
                    $image->getClientOriginalExtension()
                );


            /*
             * Move image directly into:
             *
             * public/ItemsDesigner_Masterwithbarcode/{barcode}/
             */

            $image->move(
                $imageDirectory,
                $fileName
            );


            /*
             * Save web-accessible relative path
             */

            $uploadedPaths[] =
                'ItemsDesigner_Masterwithbarcode/' .
                $barcode .
                '/' .
                $fileName;
        }


        /*
         * Save paths in img_path
         */

        if (
            !empty($uploadedPaths)
        ) {

            $imgPath =
                json_encode(
                    $uploadedPaths,
                    JSON_UNESCAPED_SLASHES
                );


            DB::table(
                'auto_designer_specification_master'
            )
            ->where(
                'sno',
                $insertId
            )
            ->update([
                'img_path' =>
                    $imgPath
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SUB IMAGES
    |--------------------------------------------------------------------------
    |
    | Save into:
    |
    | public/
    |   ItemsDesigner_Masterwithbarcode/
    |       {barcode}/
    |           SubImgs/
    |--------------------------------------------------------------------------
    */

    $subImagePaths =
        [];


    /*
    |--------------------------------------------------------------------------
    | Check uploaded sub images
    |--------------------------------------------------------------------------
    */

    if (
        $request->hasFile(
            'sub_images'
        )
    ) {

        $subImages =
            $request->file(
                'sub_images'
            );


        /*
        |--------------------------------------------------------------------------
        | Make sure it is an array
        |--------------------------------------------------------------------------
        */

        if (
            !is_array(
                $subImages
            )
        ) {

            $subImages = [
                $subImages
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Sub Image Directory
        |--------------------------------------------------------------------------
        */

        $subImageDirectory =
            public_path(
                'ItemsDesigner_Masterwithbarcode/' .
                $barcode .
                '/SubImgs'
            );


        /*
        |--------------------------------------------------------------------------
        | Create Directory
        |--------------------------------------------------------------------------
        */

        if (
            !is_dir(
                $subImageDirectory
            )
        ) {

            mkdir(
                $subImageDirectory,
                0755,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Each Sub Image
        |--------------------------------------------------------------------------
        */

        foreach (
            $subImages as $subImage
        ) {

            /*
            |--------------------------------------------------------------------------
            | Check Valid Uploaded File
            |--------------------------------------------------------------------------
            */

            if (
                !$subImage ||
                !$subImage->isValid()
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Generate Unique File Name
            |--------------------------------------------------------------------------
            */

            $extension =
                strtolower(
                    $subImage
                        ->getClientOriginalExtension()
                );


            $fileName =
                $generatedSku .
                '-' .
                \Illuminate\Support\Str::random(
                    10
                ) .
                '.' .
                $extension;


            /*
            |--------------------------------------------------------------------------
            | Move File
            |--------------------------------------------------------------------------
            */

            $subImage->move(
                $subImageDirectory,
                $fileName
            );


            /*
            |--------------------------------------------------------------------------
            | Relative Database Path
            |--------------------------------------------------------------------------
            */

            $subImagePaths[] =
                'ItemsDesigner_Masterwithbarcode/' .
                $barcode .
                '/SubImgs/' .
                $fileName;
        }


        /*
        |--------------------------------------------------------------------------
        | Save JSON Path
        |--------------------------------------------------------------------------
        */

        if (
            !empty($subImagePaths)
        ) {

            DB::table(
                'auto_designer_specification_master'
            )
            ->where(
                'sno',
                $insertId
            )
            ->update([
                'subimg_path' =>
                    json_encode(
                        $subImagePaths,
                        JSON_UNESCAPED_SLASHES
                    )
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Save AI Product Description
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


    $hasAiDetails =
        false;


    foreach (
        $aiFields as $field
    ) {

        if (
            isset(
                $validated[$field]
            ) &&
            trim(
                (string)
                $validated[$field]
            ) !== ''
        ) {

            $hasAiDetails =
                true;

            break;
        }
    }


    if (
        $hasAiDetails
    ) {

        DB::table(
            'AI_product_description'
        )->insert([

            'product_id' =>
                $insertId,

            'AI_product_name' =>
                $validated['AI_product_name'] ?? null,

            'AI_product_description' =>
                $validated['AI_product_description'] ?? null,

            'AI_Metatitle' =>
                $validated['AI_Metatitle'] ?? null,

            'AI_Metakeywards' =>
                $validated['AI_Metakeywards'] ?? null,

            'AI_Metadescription' =>
                $validated['AI_Metadescription'] ?? null,

            'AI_Producttag' =>
                $validated['AI_Producttag'] ?? null,

            'AI_Imagealttext' =>
                $validated['AI_Imagealttext'] ?? null,

            'company_id' =>
                $companyId,

            'subcompany_id' =>
                $subCompanyId,

            'projectid' =>
                $projectId,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Supplier Product SKU
    |--------------------------------------------------------------------------
    */

    $supplierProductId =
        $request->input(
            'supplier_product_id'
        );


    if (
        $supplierProductId !== null &&
        $supplierProductId !== ''
    ) {

        DB::table(
            'supplier_products'
        )
        ->where(
            'sno',
            $supplierProductId
        )
        ->update([
            'status_done' =>
                $generatedSku,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Supplier Vendor Stock
    |--------------------------------------------------------------------------
    |
    | EXISTING NORMAL FUNCTIONALITY
    |
    | This section is intentionally unchanged.
    |
    |--------------------------------------------------------------------------
    */

    if (
        $hasSupplier
    ) {

        $supplierStock =
            $request->input(
                'login_supplier_stock'
            );


        /*
        |--------------------------------------------------------------------------
        | Stock Qty fallback
        |--------------------------------------------------------------------------
        */

        if (
            $supplierStock === null ||
            $supplierStock === '' ||
            !is_numeric($supplierStock) ||
            (int) $supplierStock <= 0
        ) {

            $supplierStock =
                1;

        } else {

            $supplierStock =
                (int) $supplierStock;
        }


        $createatDate =
            $request->input(
                'login_createatdate'
            );


        if (
            empty($createatDate)
        ) {

            $createatDate =
                now();
        }


        /*
        |--------------------------------------------------------------------------
        | Generate next vendor_stock.id
        |--------------------------------------------------------------------------
        */

        $nextVendorStockId =
            (
                (int)
                DB::table(
                    'vendor_stock'
                )->max('id')
            ) + 1;


        /*
        |--------------------------------------------------------------------------
        | Insert one row per physical stock item
        |--------------------------------------------------------------------------
        */

        $saleprice =
            $validated['saleprice'] ?? 0;


        for (
            $stockIndex = 0;
            $stockIndex < $supplierStock;
            $stockIndex++
        ) {

            DB::table(
                'vendor_stock'
            )->insert([

                'id' =>
                    $nextVendorStockId++,

                'companyid' =>
                    $companyId,

                'subcompanyid' =>
                    $subCompanyId,

                'projectid' =>
                    $projectId,

                'vendor_id' =>
                    $supplierId,

                'item_id' =>
                    $insertId,

                'quantity_received' =>
                    1,

                'send_qty' =>
                    0,

                'barcode' =>
                    $barcode,

                'g_id' =>
                    $insertId,

                'sale_price' =>
                    $saleprice,

                'createat_date' =>
                    $createatDate,

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' =>
            true,

        'message' =>
            'Design specification saved successfully.',

        'id' =>
            $insertId,

        'barcode' =>
            $barcode,

    ]);
}

    
        /**
         * Update existing Design Specification.
         *
         * IMPORTANT:
         * Barcode is NEVER regenerated during update.
         * Existing barcode remains unchanged.
         */
    /**
     * Update an existing Design Specification.
     *
     * IMPORTANT:
     * - Barcode is NEVER regenerated.
     * - The old row is kept as history.
     * - A NEW row is inserted with the SAME barcode.
     * - The old row is marked "history".
     * - The new row is marked "done".
     * - Images continue to use the SAME barcode folder.
     */
   



    
        /**
         * Update existing Design Specification.
         *
         * IMPORTANT:
         * Barcode is NEVER regenerated during update.
         * Existing barcode remains unchanged.
         */
    /**
     * Update an existing Design Specification.
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
            'auto_designer_specification_master'
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
                'message' => 'Current design specification not found.'
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
            'AI_product_name' => 'nullable|string',
            'AI_product_description' => 'nullable|string',
            'AI_Metatitle' => 'nullable|string',
            'AI_Metakeywards' => 'nullable|string',
            'AI_Metadescription' => 'nullable|string',
            'AI_Producttag' => 'nullable|string',
            'AI_Imagealttext' => 'nullable|string',
        ]);


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
                ->where('sno', $validated['craftsman'])
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
                'auto_designer_specification_master'
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
                'auto_designer_specification_master'
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
        | Generate New Internal SKU
        |--------------------------------------------------------------------------
        |
        | Generate this before image upload because the SKU is part of the
        | uploaded filename. The same generated SKU is used for the new row.
        |
        */

        $nextId =
            ((int) DB::table(
                'auto_designer_specification_master'
            )->max('id')) + 1;

        $itemNameCode =
            $validated['item_name_code']
            ?? '';

        $generatedSku =
            $itemNameCode .
            '-' .
            $supplierNickname .
            '-' .
            $nextId;


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
                    $generatedSku . '-' .
                    \Illuminate\Support\Str::random(10) .
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
                    $generatedSku . '-' .
                    \Illuminate\Support\Str::random(10) .
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
                : ($specification->price ?? null);


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

            'price' =>
                $price,

            'min_price' =>
                $minPrice,

            'sale_price' =>
                $salePrice,

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
                'auto_designer_specification_master'
            )
                ->where('sno', $specification->sno)
                ->update($commonData);


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
                            'auto_designer_specification_master'
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
                        'status_done' =>
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
                    'Design specification updated successfully.',

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
                'auto_designer_specification_history'
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
            'auto_designer_specification_master'
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

            'price' =>
                $price,

            'min_price' =>
                $minPrice,

            'sale_price' =>
                $salePrice,

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
                'auto_designer_specification_master'
            )
            ->insertGetId(
                $newRowData
            );


        /*
        |--------------------------------------------------------------------------
        | Link History To New Row
        |--------------------------------------------------------------------------
        */

        DB::table(
            'auto_designer_specification_history'
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
                'auto_designer_specification_master'
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
                    'status_done' =>
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
                'Design specification updated successfully. Old version saved in history.',

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
                ->where('sno', $sno)
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