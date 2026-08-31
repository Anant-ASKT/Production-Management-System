<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllGarmentsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ALL GARMENTS PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        if (!$user) {

            return redirect()->route('login');

        }


        /*
        |--------------------------------------------------------------------------
        | PROJECTS
        |--------------------------------------------------------------------------
        |
        | Project is treated as Supplier on this page.
        |
        | We need:
        |
        | projectid
        | projectname
        | companyid
        | subcompanyid
        |
        */

        $projects = DB::table(
            'tbl_project_master'
        )
        ->orderBy(
            'projectname',
            'asc'
        )
        ->get([
            'projectid',
            'projectname',
            'companyid',
            'subcompanyid'
        ]);


        return view(
            'all-garments.index',
            compact(
                'projects'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD GARMENTS
    |--------------------------------------------------------------------------
    */

    public function data(Request $request)
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
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input(
            'per_page',
            20
        );


        if (!in_array(
            $perPage,
            [10, 20, 30, 50],
            true
        )) {

            $perPage = 20;

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $search = trim(
            $request->input(
                'search',
                ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | SELECTED PROJECT
        |--------------------------------------------------------------------------
        */

        $projectId = $request->filled('project_id')
            ? (int) $request->input('project_id')
            : null;


        $companyId = $request->filled('company_id')
            ? (int) $request->input('company_id')
            : null;


        $subCompanyId = $request->filled('subcompany_id')
            ? (int) $request->input('subcompany_id')
            : null;


        /*
        |--------------------------------------------------------------------------
        | MAIN QUERY
        |--------------------------------------------------------------------------
        */

        $query = DB::table(
            'auto_designer_specification_master as dsm'
        );


        /*
        |--------------------------------------------------------------------------
        | DESIGNER
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_designer_master as designer',
            'designer.sno',
            '=',
            'dsm.designer_name'
        );


        /*
        |--------------------------------------------------------------------------
        | ITEM TYPE
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_itemtype_master as itemtype',
            'itemtype.id',
            '=',
            'dsm.item_type'
        );


        /*
        |--------------------------------------------------------------------------
        | GENDER
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_gender_master as gender',
            'gender.id',
            '=',
            'dsm.gender'
        );


        /*
        |--------------------------------------------------------------------------
        | ITEM NAME
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_itemname_master as itemname',
            'itemname.id',
            '=',
            'dsm.item_name'
        );


        /*
        |--------------------------------------------------------------------------
        | COMPOSITION
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_composition_master_stock as composition',
            'composition.id',
            '=',
            'dsm.composition'
        );


        /*
        |--------------------------------------------------------------------------
        | COLOUR
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_colour_master as colour',
            'colour.id',
            '=',
            'dsm.colour'
        );


        /*
        |--------------------------------------------------------------------------
        | SIZE
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_size_master as size',
            'size.id',
            '=',
            'dsm.sizes'
        );


        /*
        |--------------------------------------------------------------------------
        | EMBELLISHMENT
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_embellishment_master as embellishment',
            'embellishment.sno',
            '=',
            'dsm.embellishment'
        );


        /*
        |--------------------------------------------------------------------------
        | MANUFACTURING PROCESS
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_manufacturing_process_master as manufacturing',
            'manufacturing.sno',
            '=',
            'dsm.manufacturing_process'
        );


        /*
        |--------------------------------------------------------------------------
        | CRAFTSMAN
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_craftsman_master as craftsman',
            'craftsman.sno',
            '=',
            'dsm.craftsman'
        );


        /*
        |--------------------------------------------------------------------------
        | MANUFACTURER
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_manufacture_master as manufacture',
            'manufacture.sno',
            '=',
            'dsm.manufecture'
        );


        /*
        |--------------------------------------------------------------------------
        | CLIENT
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'auto_client_master as client',
            'client.sno',
            '=',
            'dsm.client'
        );


        /*
        |--------------------------------------------------------------------------
        | AI PRODUCT DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'AI_product_description as ai',
            'ai.product_id',
            '=',
            'dsm.id'
        );


        /*
        |--------------------------------------------------------------------------
        | COMPANY
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
            'tbl_company_master as company',
            'company.companyid',
            '=',
            'dsm.companyid'
        );


        /*
        |--------------------------------------------------------------------------
        | SUB COMPANY
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
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
        );


        /*
        |--------------------------------------------------------------------------
        | PROJECT
        |--------------------------------------------------------------------------
        */

        $query->leftJoin(
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
        );


        /*
        |--------------------------------------------------------------------------
        | PROJECT FILTER
        |--------------------------------------------------------------------------
        |
        | When nothing is selected:
        |     ALL projects
        |
        | When project is selected:
        |     Company + Sub Company + Project
        |
        */

        if (
            $projectId !== null &&
            $companyId !== null &&
            $subCompanyId !== null
        ) {

            $query->where(
                'dsm.companyid',
                $companyId
            );

            $query->where(
                'dsm.subcompanyid',
                $subCompanyId
            );

            $query->where(
                'dsm.projectid',
                $projectId
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $query->where(
                function ($q) use ($search) {

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
                        'itemname.itemname',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'designer.designername',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'project.projectname',
                        'like',
                        '%' . $search . '%'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $query->select([

            /*
            |--------------------------------------------------------------------------
            | INTERNAL IDs
            |--------------------------------------------------------------------------
            |
            | These are returned for internal use only.
            | They will NOT be displayed on the card.
            |
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
            | ORIGINAL VALUES
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
            | HUMAN READABLE VALUES
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
            | COMPANY / SUB COMPANY / PROJECT NAMES
            |--------------------------------------------------------------------------
            */

            'company.companyname as company_name',

            'subcompany.subcompanyname as subcompany_name',

            'project.projectname as project_name',


            /*
            |--------------------------------------------------------------------------
            | OTHER PRODUCT DATA
            |--------------------------------------------------------------------------
            */

            'dsm.craftsman_code',

            'dsm.img_path',

            'dsm.subimg_path',

            'dsm.status',

            'dsm.box_assign',

            'dsm.print_status',

            'dsm.edatetime',

            'dsm.clientreference',

            'dsm.description_id',

            'dsm.oc_product_id',

            'dsm.oc_main_img',


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

            'ai.AI_Imagealttext',

        ]);


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'dsm.sno',
            'desc'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $garments =
            $query->paginate(
                $perPage
            );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'data' =>
                $garments->items(),

            'current_page' =>
                $garments->currentPage(),

            'last_page' =>
                $garments->lastPage(),

            'per_page' =>
                $garments->perPage(),

            'total' =>
                $garments->total(),

            'from' =>
                $garments->firstItem(),

            'to' =>
                $garments->lastItem()

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROJECTS
    |--------------------------------------------------------------------------
    |
    | AJAX endpoint if required.
    |
    */

    public function projects(
        Request $request
    ) {

        $projects =
            DB::table(
                'tbl_project_master'
            )
            ->orderBy(
                'projectname',
                'asc'
            )
            ->get([
                'projectid',
                'projectname',
                'companyid',
                'subcompanyid'
            ]);


        return response()->json([

            'success' => true,

            'data' =>
                $projects

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SUB COMPANIES
    |--------------------------------------------------------------------------
    |
    | Not required for All Garments.
    |
    */

    public function subCompanies(
        Request $request
    ) {

        return response()->json([

            'success' => true,

            'data' => []

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | MAKE AI DESCRIPTION PAGE
    |--------------------------------------------------------------------------
    */

    public function makeAiDescription()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('ai-description.index');
    }

    /*
|--------------------------------------------------------------------------
| MAKE AI DESCRIPTION DATA
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| MAKE AI DESCRIPTION DATA
|--------------------------------------------------------------------------
*/

public function aiDescriptionData(Request $request)
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
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $perPage = (int) $request->input(
        'per_page',
        20
    );


    if (!in_array(
        $perPage,
        [10, 20, 30, 50],
        true
    )) {

        $perPage = 20;

    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    $search = trim(
        $request->input(
            'search',
            ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | MAIN QUERY
    |--------------------------------------------------------------------------
    */

    $query = DB::table(
        'auto_designer_specification_master as dsm'
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
    | APPROVED AI MAIN IMAGE
    |--------------------------------------------------------------------------
    |
    | dsm.sno = aei.specification_id
    |
    */

    ->leftJoin(
        'approved_enhanced_images as aei',
        function ($join) {

            $join->on(
                'aei.specification_id',
                '=',
                'dsm.sno'
            )

            ->where(
                'aei.status',
                '=',
                'approved'
            )

            ->where(
                'aei.image_type',
                '=',
                'main'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {

        $query->where(
            function ($q) use ($search) {

                $q->where(
                    'dsm.sku',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'dsm.barcode',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'itemname.itemname',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'itemtype.itemtype',
                    'like',
                    '%' . $search . '%'
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT
    |--------------------------------------------------------------------------
    */

   $query->select([

    /*
    |--------------------------------------------------------------------------
    | SPECIFICATION
    |--------------------------------------------------------------------------
    */

    'dsm.sno',

    'dsm.id',


    /*
    |--------------------------------------------------------------------------
    | MAIN IMAGE
    |--------------------------------------------------------------------------
    */

    'dsm.img_path',


    /*
    |--------------------------------------------------------------------------
    | APPROVED AI IMAGE
    |--------------------------------------------------------------------------
    */

    'aei.sno as approved_image_id',

    'aei.enhanced_image_path as ai_approved_image',


    /*
    |--------------------------------------------------------------------------
    | PRODUCT NAME
    |--------------------------------------------------------------------------
    */

    'dsm.item_name',

    'itemname.itemname as product_name',


    /*
    |--------------------------------------------------------------------------
    | PRODUCT TYPE
    |--------------------------------------------------------------------------
    */

    'dsm.item_type',

    'itemtype.itemtype as product_type',


    /*
    |--------------------------------------------------------------------------
    | GENDER
    |--------------------------------------------------------------------------
    */

    'gender.name as gender_name',


    /*
    |--------------------------------------------------------------------------
    | COMPOSITION
    |--------------------------------------------------------------------------
    */

    'composition.composition_details as composition_name',


    /*
    |--------------------------------------------------------------------------
    | COLOUR
    |--------------------------------------------------------------------------
    */

    'colour.colourname as colour_name',


    /*
    |--------------------------------------------------------------------------
    | SIZE
    |--------------------------------------------------------------------------
    */

    'size.size as size_name',


    /*
    |--------------------------------------------------------------------------
    | SKU
    |--------------------------------------------------------------------------
    */

    'dsm.sku',

    'dsm.sku_supplier',


    /*
    |--------------------------------------------------------------------------
    | BARCODE
    |--------------------------------------------------------------------------
    */

    'dsm.barcode'

    ]);

    /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */

    $query->orderBy(
        'dsm.sno',
        'desc'
    );


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $products =
        $query->paginate(
            $perPage
        );


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,

        'data' =>
            $products->items(),

        'current_page' =>
            $products->currentPage(),

        'last_page' =>
            $products->lastPage(),

        'per_page' =>
            $products->perPage(),

        'total' =>
            $products->total(),

        'from' =>
            $products->firstItem(),

        'to' =>
            $products->lastItem()

    ]);
}
}