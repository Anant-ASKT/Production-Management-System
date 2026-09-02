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
    public function pendingProducts()
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
                'designername'
            ]);


        $itemTypes = DB::table('auto_itemtype_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('itemtype')
            ->get([
                'sno',
                'id',
                'itemtype'
            ]);


        $genders = DB::table('auto_gender_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name'
            ]);


        $itemNames = DB::table('auto_itemname_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('itemname')
            ->get([
                'sno',
                'id',
                'itemname'
            ]);


        $compositions = DB::table('auto_composition_master_stock')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('composition_details')
            ->get([
                'sno',
                'id',
                'composition_details'
            ]);


        $colours = DB::table('auto_colour_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('colourname')
            ->get([
                'sno',
                'id',
                'colourname'
            ]);


        $sizes = DB::table('auto_size_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('size')
            ->get([
                'sno',
                'id',
                'size'
            ]);


        $embellishments = DB::table('auto_embellishment_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('embellishmentname')
            ->get([
                'sno',
                'id',
                'embellishmentname'
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
                'manufacturing_process'
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
                'name'
            ]);


        $clients = DB::table('auto_client_master')
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->orderBy('name')
            ->get([
                'sno',
                'id',
                'name'
            ]);

        $suppliers = DB::table('suppliers')
            ->orderBy('name')
            ->get([
                'sno',
                'name'
            ]);

        $aiPhotoEnhancers = \App\Models\AiPhotoEnhancer::orderBy('first_name')->get();

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
    'admin.ai_photo_enhancing.pending',
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
        'itemNames',
        'compositions',
        'colours',
        'sizes',
        'embellishments',
        'manufacturingProcesses',
        'craftsmen',
        'manufactures',
        'clients',
        'suppliers',
        'aiPhotoEnhancers'
    )
);
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

    public function pendingData(Request $request)
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
    $supplierId = $request->input('supplier_id', '');

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
        | Supplier
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'suppliers as supplier',
            'supplier.sno',
            '=',
            'dsm.supplier_id'
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
                    'subcompany.sno',
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
                    'project.sno',
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
        )
        ->where('dsm.is_sent_to_photo_section', 0);

    // Edited products create a new version with the same barcode.
    // Only the current version is shown in the main list.
    $query->where(function ($q) {
        $q->whereNull('dsm.status')
          ->orWhere('dsm.status', '')
          ->orWhere('dsm.status', 'done');
    });

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    if ($supplierId !== '' && $supplierId !== 'all') {
        $query->where('dsm.supplier_id', $supplierId);
    }

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
                'supplier.name',
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
            'dsm.colour',
            'dsm.sizes',

            // Display values
            'designer.designername as designer_name_text',
            'itemtype.itemtype as item_type_text',
            'gender.name as gender_text',
            'itemname.itemname as item_name_text',
            'composition.composition_details as composition_text',
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
            'dsm.edatetime',
            'dsm.clientreference',
            'supplier.name as supplier_name',
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

    public function assignToEnhancers(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'specification_id' => 'required|integer|exists:auto_designer_specification_master,sno',
            'enhancer_ids'     => 'required|array',
            'enhancer_ids.*'   => 'integer|exists:ai_photo_enhancers,sno',
        ]);

        $specificationId = $request->input('specification_id');
        $enhancerIds     = $request->input('enhancer_ids');

        DB::beginTransaction();

        try {
            // Delete existing assignments if re-assigning (optional, but good practice if allowing updates later)
            // DB::table('ai_photo_enhancer_assignments')->where('specification_id', $specificationId)->delete();

            foreach ($enhancerIds as $enhancerId) {
                DB::table('ai_photo_enhancer_assignments')->insert([
                    'specification_id'     => $specificationId,
                    'ai_photo_enhancer_id' => $enhancerId,
                    'status'               => 'assigned',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }

            DB::table('auto_designer_specification_master')
                ->where('sno', $specificationId)
                ->update(['is_sent_to_photo_section' => 1]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product successfully sent to photo section.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning product: ' . $e->getMessage()
            ], 500);
        }
    }

}