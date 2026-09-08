<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSpecificationMasterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW ALL PRODUCT SPECIFICATION MASTER
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
        | FILTER MASTER DATA
        |--------------------------------------------------------------------------
        */

        $itemTypes = DB::table('auto_itemtype_master')
            ->orderBy('itemtype', 'asc')
            ->get([
                'id',
                'itemtype',
                'code'
            ]);

        $itemNames = DB::table('auto_itemname_master')
            ->orderBy('itemname', 'asc')
            ->get([
                'id',
                'itemname',
                'code'
            ]);

        $compositions = DB::table('auto_composition_master_stock')
            ->orderBy('composition_details', 'asc')
            ->get([
                'id',
                'composition_details',
                'code'
            ]);

        $genders = DB::table('auto_gender_master')
            ->orderBy('name', 'asc')
            ->get([
                'id',
                'name',
                'code'
            ]);

        $designers = DB::table('auto_designer_master')
            ->orderBy('designername', 'asc')
            ->get(['id', 'designername', 'code']);

        $yarns = DB::table('auto_yarn_master')
            ->orderBy('yarnname', 'asc')
            ->get(['id', 'yarnname', 'code']);

        $colours = DB::table('auto_colour_master')
            ->orderBy('colourname', 'asc')
            ->get(['id', 'colourname', 'code']);

        $sizes = DB::table('auto_size_master')
            ->orderBy('size', 'asc')
            ->get(['id', 'size', 'code']);

        $embellishments = DB::table('auto_embellishment_master')
            ->orderBy('embellishmentname', 'asc')
            ->get(['id', 'embellishmentname', 'code']);

        $manufacturingProcesses = DB::table('auto_manufacturing_process_master')
            ->orderBy('manufacturing_process', 'asc')
            ->get(['id', 'manufacturing_process', 'code']);

        $craftsmen = DB::table('auto_craftsman_master')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'code']);

        $manufactures = DB::table('auto_manufacture_master')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'code']);

        $clients = DB::table('auto_client_master')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'code']);

        return view(
            'product-specification-masters.index',
            compact(
                'itemTypes',
                'itemNames',
                'compositions',
                'genders',
                'designers',
                'yarns',
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


    /*
    |--------------------------------------------------------------------------
    | LOAD ALL PRODUCT SPECIFICATION MASTER DATA
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
        | CURRENT USER COMPANY / SUB COMPANY / PROJECT
        |--------------------------------------------------------------------------
        */

        $companyId = (int) $user->company_id;
        $subCompanyId = (int) $user->sub_company_id;
        $projectId = (int) $user->project_id;

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        $itemType = $request->input('item_type');
        $itemName = $request->input('item_name');
        $composition = $request->input('composition');
        $gender = $request->input('gender');

        /*
        |--------------------------------------------------------------------------
        | STOCK
        |--------------------------------------------------------------------------
        |
        | Same conditions as Core PHP:
        |
        | company
        | subcompany
        | project
        | tedit empty/null
        | available qty > 0
        | boxid required
        | orderstatus empty/null
        |
        */

        $stockRows = DB::table(
            'vendor_stock as vs'
        )
        ->where(
            'vs.companyid',
            $companyId
        )
        ->where(
            'vs.subcompanyid',
            $subCompanyId
        )
        ->where(
            'vs.projectid',
            $projectId
        )
        ->where(function ($q) {
            $q->whereNull('vs.tedit')
              ->orWhere('vs.tedit', '');
        })
        ->where(
            'vs.avilable_qty',
            '>',
            0
        )
        ->whereNotNull(
            'vs.boxid'
        )
        ->where(
            'vs.boxid',
            '<>',
            ''
        )
        ->where(function ($q) {
            $q->whereNull('vs.orderstatus')
              ->orWhere('vs.orderstatus', '');
        })
        ->whereNotNull(
            'vs.barcode'
        )
        ->where(
            'vs.barcode',
            '<>',
            ''
        )
        ->get([
            'vs.quantity_received',
            'vs.barcode'
        ]);

        /*
        |--------------------------------------------------------------------------
        | NO STOCK
        |--------------------------------------------------------------------------
        */

        if ($stockRows->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => max(
                        1,
                        min(
                            50,
                            (int) $request->input(
                                'per_page',
                                20
                            )
                        )
                    ),
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD BARCODE RELATIONS ONCE
        |--------------------------------------------------------------------------
        |
        | Previously resolveProductBarcode() was called inside the stock loop.
        | That caused one database query for every stock barcode.
        |
        | Load the complete relation map once and resolve old -> current
        | barcodes in memory instead.
        |
        */

        $barcodeHistory = DB::table(
            'product_specification_barcode_history'
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
        ->orderByDesc('id')
        ->get([
            'old_barcode',
            'new_barcode'
        ]);

        $barcodeMap = [];

        foreach ($barcodeHistory as $history) {
            $old = trim((string) $history->old_barcode);
            $new = trim((string) $history->new_barcode);

            if (
                $old === '' ||
                $new === '' ||
                isset($barcodeMap[$old])
            ) {
                continue;
            }

            $barcodeMap[$old] = $new;
        }

        /*
        |--------------------------------------------------------------------------
        | RESOLVE ALL STOCK BARCODES IN MEMORY
        |--------------------------------------------------------------------------
        */

        $resolvedBarcodes = [];

        foreach ($stockRows as $stock) {
            $current = trim((string) $stock->barcode);

            if ($current === '') {
                continue;
            }

            $seen = [];

            for ($i = 0; $i < 20; $i++) {
                if (isset($seen[$current])) {
                    break;
                }

                $seen[$current] = true;

                $next = $barcodeMap[$current] ?? null;

                if (
                    !$next ||
                    (string) $next === $current
                ) {
                    break;
                }

                $current = (string) $next;
            }

            $resolvedBarcodes[] = $current;
        }

        $resolvedBarcodes = array_values(
            array_unique($resolvedBarcodes)
        );

        /*
        |--------------------------------------------------------------------------
        | GET SPECIFICATIONS IN BULK
        |--------------------------------------------------------------------------
        |
        | Previously this query ran once for every stock row.
        | Now all required specifications are fetched in bulk.
        |
        */

        $specifications = collect();

        foreach (array_chunk($resolvedBarcodes, 1000) as $barcodeChunk) {
            $specificationQuery = DB::table(
                'auto_designer_specification_master as dsm'
            )
            ->leftJoin(
                'auto_itemtype_master as itemtype',
                'itemtype.id',
                '=',
                'dsm.item_type'
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
                'auto_gender_master as gender',
                'gender.id',
                '=',
                'dsm.gender'
            )
            ->whereIn(
                'dsm.barcode',
                $barcodeChunk
            )
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
            ->where(function ($q) {
                $q->whereNull('dsm.status')
                  ->orWhere('dsm.status', '');
            })
            ->where(function ($q) {
                $q->whereNull('dsm.tedit')
                  ->orWhere('dsm.tedit', '');
            });

            /*
            |--------------------------------------------------------------------------
            | APPLY FILTERS IN SQL
            |--------------------------------------------------------------------------
            */

            if (
                $itemType !== null &&
                $itemType !== ''
            ) {
                $specificationQuery->where(
                    'dsm.item_type',
                    $itemType
                );
            }

            if (
                $itemName !== null &&
                $itemName !== ''
            ) {
                $specificationQuery->where(
                    'dsm.item_name',
                    $itemName
                );
            }

            if (
                $composition !== null &&
                $composition !== ''
            ) {
                $specificationQuery->where(
                    'dsm.composition',
                    $composition
                );
            }

            if (
                $gender !== null &&
                $gender !== ''
            ) {
                $specificationQuery->where(
                    'dsm.gender',
                    $gender
                );
            }

            $specifications = $specifications->merge(
                $specificationQuery->get([
                    'dsm.barcode',
                    'dsm.item_type',
                    'dsm.item_name',
                    'dsm.composition',
                    'dsm.gender',
                    'dsm.img_path',
                    'itemtype.itemtype as item_type_text',
                    'itemname.itemname as item_name_text',
                    'composition.composition_details as composition_text',
                    'gender.name as gender_text'
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | INDEX SPECIFICATIONS BY BARCODE
        |--------------------------------------------------------------------------
        */

        $specificationMap = [];

        foreach ($specifications as $specification) {
            $specificationMap[
                (string) $specification->barcode
            ] = $specification;
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = [];

        foreach ($stockRows as $stock) {
            $currentBarcode = trim(
                (string) $stock->barcode
            );

            if ($currentBarcode === '') {
                continue;
            }

            $seen = [];

            for ($i = 0; $i < 20; $i++) {
                if (isset($seen[$currentBarcode])) {
                    break;
                }

                $seen[$currentBarcode] = true;

                $next = $barcodeMap[$currentBarcode] ?? null;

                if (
                    !$next ||
                    (string) $next === $currentBarcode
                ) {
                    break;
                }

                $currentBarcode = (string) $next;
            }

            $specification =
                $specificationMap[$currentBarcode]
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | BARCODE DOES NOT HAVE SPECIFICATION
            |--------------------------------------------------------------------------
            */

            if (!$specification) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | GROUP PRODUCT
            |--------------------------------------------------------------------------
            */

            $groupKey =
                $specification->item_type . '|' .
                $specification->item_name . '|' .
                $specification->composition . '|' .
                $specification->gender;

            if (!isset($products[$groupKey])) {
                $products[$groupKey] = [
                    'item_type' =>
                        $specification->item_type,

                    'item_type_text' =>
                        $specification->item_type_text,

                    'item_name' =>
                        $specification->item_name,

                    'item_name_text' =>
                        $specification->item_name_text,

                    'composition' =>
                        $specification->composition,

                    'composition_text' =>
                        $specification->composition_text,

                    'gender' =>
                        $specification->gender,

                    'gender_text' =>
                        $specification->gender_text,

                    /*
                    |--------------------------------------------------------------------------
                    | FIRST PRODUCT IMAGE
                    |--------------------------------------------------------------------------
                    */

                    'first_image' =>
                        $this->getFirstImage(
                            $specification->img_path ?? null
                        ),

                    'stock_qty' => 0
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | ADD STOCK QUANTITY
            |--------------------------------------------------------------------------
            */

            $products[$groupKey]['stock_qty'] +=
                (float) ($stock->quantity_received ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | RESET ARRAY INDEXES
        |--------------------------------------------------------------------------
        */

        $products = array_values($products);

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        usort(
            $products,
            function ($a, $b) {

                $stockQtyA =
                    (float) ($a['stock_qty'] ?? 0);

                $stockQtyB =
                    (float) ($b['stock_qty'] ?? 0);

                return $stockQtyB <=> $stockQtyA;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $page = max(
            1,
            (int) $request->input(
                'page',
                1
            )
        );

        $perPage = max(
            1,
            min(
                50,
                (int) $request->input(
                    'per_page',
                    20
                )
            )
        );

        $total = count($products);

        $lastPage = max(
            1,
            (int) ceil(
                $total / $perPage
            )
        );

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $offset =
            ($page - 1) *
            $perPage;

        $paginatedProducts =
            array_slice(
                $products,
                $offset,
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
                $paginatedProducts,

            'pagination' => [
                'current_page' =>
                    $page,

                'per_page' =>
                    $perPage,

                'total' =>
                    $total,

                'last_page' =>
                    $lastPage,

                'from' =>
                    $total > 0
                        ? $offset + 1
                        : 0,

                'to' =>
                    min(
                        $offset + $perPage,
                        $total
                    )
            ]
        ]);
    }

    /*
|--------------------------------------------------------------------------
| SHOW PRODUCT DETAILS + ALL STOCK DETAILS
|--------------------------------------------------------------------------
*/

public function details(Request $request)
{
    $user = Auth::user();

    if (!$user) {

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);

    }


    $companyId =
        (int) $user->company_id;

    $subCompanyId =
        (int) $user->sub_company_id;

    $projectId =
        (int) $user->project_id;


    /*
    |--------------------------------------------------------------------------
    | SELECTED PRODUCT
    |--------------------------------------------------------------------------
    */

    $itemType =
        $request->input('item_type');

    $itemName =
        $request->input('item_name');

    $composition =
        $request->input('composition');

    $gender =
        $request->input('gender');


    if (
        $itemType === null ||
        $itemType === '' ||
        $itemName === null ||
        $itemName === '' ||
        $composition === null ||
        $composition === '' ||
        $gender === null ||
        $gender === ''
    ) {

        return response()->json([
            'success' => false,
            'message' =>
                'Product information is incomplete.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL STOCK OF THIS PRODUCT
    |--------------------------------------------------------------------------
    */

    $stock = DB::table(
        'vendor_stock as vs'
    )


    /*
    |--------------------------------------------------------------------------
    | SPECIFICATION
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'product_specification_barcode_history as barcode_map',
        function ($join) {
            $join->on(
                'barcode_map.old_barcode',
                '=',
                'vs.barcode'
            )
            ->on(
                'barcode_map.companyid',
                '=',
                'vs.companyid'
            )
            ->on(
                'barcode_map.subcompanyid',
                '=',
                'vs.subcompanyid'
            )
            ->on(
                'barcode_map.projectid',
                '=',
                'vs.projectid'
            );
        }
    )

    ->leftJoin(
        'auto_designer_specification_master as dsm',
        function ($join) {

            $join->on(
                'dsm.barcode',
                '=',
                DB::raw(
                    "COALESCE(barcode_map.new_barcode, vs.barcode)"
                )
            )

            ->on(
                'dsm.companyid',
                '=',
                'vs.companyid'
            )

            ->on(
                'dsm.subcompanyid',
                '=',
                'vs.subcompanyid'
            )

            ->on(
                'dsm.projectid',
                '=',
                'vs.projectid'
            );

        }
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

    ->leftJoin(
        'auto_yarn_master as yarn',
        'yarn.id',
        '=',
        'dsm.yarn'
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
    | WAREHOUSE
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'strs_warehouse as warehouse',
        'warehouse.id',
        '=',
        'vs.warehouse_id'
    )


    /*
    |--------------------------------------------------------------------------
    | LOCATION
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'strs_locationmaster as location',
        'location.id',
        '=',
        'vs.location_id'
    )


    /*
    |--------------------------------------------------------------------------
    | BOX
    |--------------------------------------------------------------------------
    */

    ->leftJoin(
        'tbl_boxes as box',
        function ($join) {

            $join->on(
                'box.id',
                '=',
                'vs.boxid'
            )

            ->on(
                'box.warehouseid',
                '=',
                'vs.warehouse_id'
            )

            ->on(
                'box.location',
                '=',
                'vs.location_id'
            );

        }
    )


    /*
    |--------------------------------------------------------------------------
    | COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    ->where(
        'vs.companyid',
        $companyId
    )

    ->where(
        'vs.subcompanyid',
        $subCompanyId
    )

    ->where(
        'vs.projectid',
        $projectId
    )


    /*
    |--------------------------------------------------------------------------
    | STOCK CONDITIONS
    |--------------------------------------------------------------------------
    */

    ->where(function ($q) {

        $q->whereNull('vs.tedit')
          ->orWhere(
              'vs.tedit',
              ''
          );

    })

    ->where(
        'vs.avilable_qty',
        '>',
        0
    )

    ->whereNotNull(
        'vs.boxid'
    )

    ->where(
        'vs.boxid',
        '<>',
        ''
    )

    ->where(function ($q) {

        $q->whereNull(
            'vs.orderstatus'
        )

        ->orWhere(
            'vs.orderstatus',
            ''
        );

    })


    /*
    |--------------------------------------------------------------------------
    | CURRENT SPECIFICATION
    |--------------------------------------------------------------------------
    */

    ->where(function ($q) {

        $q->whereNull(
            'dsm.tedit'
        )

        ->orWhere(
            'dsm.tedit',
            ''
        );

    })


    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */

    ->where(
        'dsm.item_type',
        $itemType
    )

    ->where(
        'dsm.item_name',
        $itemName
    )

    ->where(
        'dsm.composition',
        $composition
    )

    ->where(
        'dsm.gender',
        $gender
    )


    /*
    |--------------------------------------------------------------------------
    | SELECT
    |--------------------------------------------------------------------------
    */

    ->select([

        /*
        | Product
        */

        'dsm.img_path',

        'dsm.subimg_path',

        'dsm.id as specification_id',

        'dsm.sno as specification_sno',

        'dsm.designer_name',
        'dsm.item_type',
        'dsm.item_name',
        'dsm.composition',
        'dsm.gender',
        'dsm.yarn',
        'dsm.colour',
        'dsm.sizes',
        'dsm.embellishment',
        'dsm.manufacturing_process',
        'dsm.craftsman',
        'dsm.craftsman_code',
        'dsm.manufecture',
        'dsm.client',

        'dsm.barcode',

        'dsm.sku',

        'dsm.sku_supplier',

        'dsm.price',
        'dsm.sale_price',
        'dsm.min_price',

        'designer.designername as designer_name',

        'itemtype.itemtype as item_type_text',

        'itemname.itemname as item_name_text',

        'composition.composition_details as composition_text',

        'gender.name as gender_text',

        'colour.colourname as colour_text',

        'size.size as size_text',

        'embellishment.embellishmentname as embellishment_text',

        'manufacturing.manufacturing_process as manufacturing_process_text',

        'yarn.yarnname as yarn_text',

        'craftsman.name as craftsman_text',

        'manufacture.name as manufacture_text',

        'client.name as client_text',

        'dsm.clientreference',
/*
        | Stock
        */

        'vs.id as stock_id',


        'vs.avilable_qty',


        'vs.stock_date',


        'vs.barcode as stock_barcode',


        /*
        | Box
        */

        'box.boxno',


        /*
        | Warehouse
        */


        'warehouse.warehousename',


        /*
        | Location
        */


        'location.locationname',


    ])


    ->orderBy(
        'warehouse.warehousename'
    )

    ->orderBy(
        'location.locationname'
    )

    ->orderBy(
        'box.boxno'
    )

    ->orderBy(
        'vs.id'
    )

    ->get();


    /*
    |--------------------------------------------------------------------------
    | NO STOCK
    |--------------------------------------------------------------------------
    */

    if ($stock->isEmpty()) {

        return response()->json([
            'success' => true,
            'product' => null,
            'stock' => []
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT INFORMATION
    |--------------------------------------------------------------------------
    */

    $first =
        $stock->first();


    $product = [

        'first_image' =>
            $this->getFirstImage(
                $first->img_path
            ),

        'designer_name' =>
            $first->designer_name,

        'item_type' =>
            $first->item_type_text,

        'gender' =>
            $first->gender_text,

        'item_name' =>
            $first->item_name_text,

        'composition' =>
            $first->composition_text,

        'colour' =>
            $first->colour_text,

        'sizes' =>
            $first->size_text,

        'embellishment' =>
            $first->embellishment_text,

        'manufacturing_process' =>
            $first->manufacturing_process_text,

        'clientreference' =>
            $first->clientreference,

        'stock_qty' =>
            $stock->sum(
                function ($row) {
                    return (float)
                        ($row->avilable_qty ?? 0);
                }
            )

    ];


    /*
    |--------------------------------------------------------------------------
    | STOCK DETAILS
    |--------------------------------------------------------------------------
    */

    $stockDetails =
        $stock->map(
            function ($row) {

                return [

                    'stock_id' =>
                        $row->stock_id,

                    'specification_id' =>
                        $row->specification_id,

                    'specification_sno' =>
                        $row->specification_sno,

                    'barcode' =>
                        $row->stock_barcode,

                    'sku' =>
                        $row->sku,

                    'sku_supplier' =>
                        $row->sku_supplier,

                    'designer_id' =>
                        $row->designer_name,

                    'item_type_id' =>
                        $row->item_type,

                    'item_name_id' =>
                        $row->item_name,

                    'composition_id' =>
                        $row->composition,

                    'gender_id' =>
                        $row->gender,

                    'yarn_id' =>
                        $row->yarn,

                    'colour_id' =>
                        $row->colour,

                    'sizes_id' =>
                        $row->sizes,

                    'embellishment_id' =>
                        $row->embellishment,

                    'manufacturing_process_id' =>
                        $row->manufacturing_process,

                    'craftsman_id' =>
                        $row->craftsman,

                    'manufacture_id' =>
                        $row->manufecture,

                    'client_id' =>
                        $row->client,

                    'craftsman_code' =>
                        $row->craftsman_code,

                    'designer_name' =>
                        $row->designer_name,

                    'item_type' =>
                        $row->item_type_text,

                    'item_name' =>
                        $row->item_name_text,

                    'composition' =>
                        $row->composition_text,

                    'gender' =>
                        $row->gender_text,

                    'colour' =>
                        $row->colour_text,

                    'sizes' =>
                        $row->size_text,

                    'embellishment' =>
                        $row->embellishment_text,

                    'manufacturing_process' =>
                        $row->manufacturing_process_text,

                    'yarn' =>
                        $row->yarn_text,

                    'craftsman' =>
                        $row->craftsman_text,

                    'manufacture' =>
                        $row->manufacture_text,

                    'client' =>
                        $row->client_text,

                    'price' =>
                        $row->price,

                    'sale_price' =>
                        $row->sale_price,

                    'min_price' =>
                        $row->min_price,
'clientreference' =>
                        $row->clientreference,

                    'stock_image' =>
                        $this->getFirstImage($row->img_path),

                    'sub_images' =>
                        $this->getImages($row->subimg_path),

                    'available_qty' =>
                        $row->avilable_qty,

                    'stock_date' =>
                        $row->stock_date,

                    'boxno' =>
                        $row->boxno,

                    'warehousename' =>
                        $row->warehousename,

                    'locationname' =>
                        $row->locationname

                ];

            }
        )
        ->values();


    return response()->json([

        'success' => true,

        'product' =>
            $product,

        'stock' =>
            $stockDetails

    ]);
}


/*
|--------------------------------------------------------------------------
| UPDATE PRODUCT SPECIFICATION / GENERATE NEW BARCODE
|--------------------------------------------------------------------------
|
| Same versioning approach used by DesignSpecificationController:
| - Required masters are validated.
| - Barcode is regenerated from edited master IDs.
| - If barcode is unchanged, current row is updated.
| - If barcode changes, old row is marked as edited and a new row
|   is inserted.
| - Old/new barcode relation is saved in
|   product_specification_barcode_history.
|
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

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;

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
            'message' => 'Product specification not found.'
        ], 404);
    }

    $validated = $request->validate([
        'item_name' => 'required|integer',
        'item_type' => 'required|integer',
        'designer_name' => 'required|integer',
        'gender' => 'required|integer',
        'composition' => 'required|integer',
        'colour' => 'required|integer',
        'sizes' => 'required|integer',

        'yarn' => 'nullable|integer',
        'embellishment' => 'nullable|integer',
        'manufacturing_process' => 'nullable|integer',
        'craftsman' => 'nullable|integer',
        'craftsman_code' => 'nullable|string|max:100',
        'manufecture' => 'nullable|integer',
        'client' => 'nullable|integer',

        'clientreference' => 'nullable|string|max:500',

        'item_name_code' => 'nullable|string|max:100',
        'item_type_code' => 'nullable|string|max:100',
        'designer_code' => 'nullable|string|max:100',
        'gender_code' => 'nullable|string|max:100',
        'composition_code' => 'nullable|string|max:100',
        'colour_code' => 'nullable|string|max:100',
        'size_code' => 'nullable|string|max:100',

        'price' => 'nullable|string',
        'minprice' => 'nullable|string',
        'saleprice' => 'nullable|string',
    ]);

    /*
    |--------------------------------------------------------------------------
    | REQUIRED MASTER VALIDATION
    |--------------------------------------------------------------------------
    */

    $requiredMasters = [
        'auto_designer_master' => 'designer_name',
        'auto_itemtype_master' => 'item_type',
        'auto_gender_master' => 'gender',
        'auto_itemname_master' => 'item_name',
        'auto_composition_master_stock' => 'composition',
        'auto_colour_master' => 'colour',
        'auto_size_master' => 'sizes',
    ];

    foreach ($requiredMasters as $table => $field) {
        $this->validateProductMasterRecord(
            $table,
            (int) $validated[$field],
            $companyId,
            $subCompanyId,
            $projectId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL MASTER VALIDATION
    |--------------------------------------------------------------------------
    */

    $optionalMasters = [
        'auto_yarn_master' => 'yarn',
        'auto_embellishment_master' => 'embellishment',
        'auto_manufacturing_process_master' => 'manufacturing_process',
        'auto_craftsman_master' => 'craftsman',
        'auto_manufacture_master' => 'manufecture',
        'auto_client_master' => 'client',
    ];

    foreach ($optionalMasters as $table => $field) {
        if (
            isset($validated[$field]) &&
            $validated[$field] !== '' &&
            $validated[$field] !== null
        ) {
            $this->validateProductMasterRecord(
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
    | CRAFTSMAN CODE FROM MASTER
    |--------------------------------------------------------------------------
    */

    $craftsmanCode =
        $validated['craftsman_code']
        ?? $specification->craftsman_code
        ?? null;

    if (!empty($validated['craftsman'])) {
        $craftsman = DB::table('auto_craftsman_master')
            ->where(function ($query) use ($validated) {
                $query->where('id', $validated['craftsman'])
                    ->orWhere('sno', $validated['craftsman']);
            })
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
            ->first(['code']);

        if ($craftsman) {
            $craftsmanCode = $craftsman->code;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NEW BARCODE
    |--------------------------------------------------------------------------
    */

    $newBarcode = $this->generateProductBarcode(
        $projectId,
        $validated
    );

    $oldBarcode = (string) ($specification->barcode ?? '');

    $barcodeChanged =
        $oldBarcode !== (string) $newBarcode;

    /*
    |--------------------------------------------------------------------------
    | DUPLICATE BARCODE CHECK
    |--------------------------------------------------------------------------
    */

    if ($barcodeChanged) {
        $barcodeExists = DB::table(
            'auto_designer_specification_master'
        )
            ->where('barcode', $newBarcode)
            ->where('sno', '!=', $specification->sno)
            ->where('companyid', $companyId)
            ->where('subcompanyid', $subCompanyId)
            ->where('projectid', $projectId)
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
    | PRICE / OTHER VALUES
    |--------------------------------------------------------------------------
    */

    $price = $request->has('price')
        ? ($validated['price'] ?? null)
        : ($specification->price ?? null);

    $minPrice = $request->has('minprice')
        ? ($validated['minprice'] ?? null)
        : ($specification->min_price ?? null);

    $salePrice = $request->has('saleprice')
        ? ($validated['saleprice'] ?? null)
        : ($specification->sale_price ?? null);

    $yarn = $request->has('yarn')
        ? ($validated['yarn'] ?? null)
        : ($specification->yarn ?? null);

    /*
    |--------------------------------------------------------------------------
    | COMMON DATA
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

        'price' =>
            $price,

        'min_price' =>
            $minPrice,

        'sale_price' =>
            $salePrice,

        'loginid' =>
            $user->username,

        'edatetime' =>
            now(),
    ];

    /*
    |--------------------------------------------------------------------------
    | SAME BARCODE
    |--------------------------------------------------------------------------
    */

    if (!$barcodeChanged) {

        $commonData['barcode'] =
            $oldBarcode;

        $commonData['qrcode'] =
            $specification->qrcode
            ?? $oldBarcode;

        $commonData['sku'] =
            $specification->sku;

        $commonData['sku_supplier'] =
            $specification->sku_supplier
            ?? null;

        DB::table(
            'auto_designer_specification_master'
        )
            ->where('sno', $specification->sno)
            ->update($commonData);

        return response()->json([
            'success' => true,
            'message' =>
                'Product specification updated successfully.',
            'id' =>
                $specification->sno,
            'old_id' =>
                $specification->sno,
            'barcode' =>
                $oldBarcode,
            'old_barcode' =>
                $oldBarcode,
            'barcode_changed' =>
                false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BARCODE CHANGED
    |--------------------------------------------------------------------------
    */

    DB::table(
        'auto_designer_specification_master'
    )
        ->where('sno', $specification->sno)
        ->update([
            'status' => '',
            'tedit' => now(),
        ]);

    $nextId =
        ((int) DB::table(
            'auto_designer_specification_master'
        )->max('id')) + 1;

    /*
    |--------------------------------------------------------------------------
    | INTERNAL SKU
    |--------------------------------------------------------------------------
    */

    $itemNameCode =
        trim((string) ($validated['item_name_code'] ?? ''));

    $projectMaster = DB::table('tbl_project_master')
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->first(['projectname']);

    $projectNickname = '';

    if ($projectMaster && !empty($projectMaster->projectname)) {
        $projectName =
            preg_replace(
                '/[^A-Za-z0-9]/',
                '',
                $projectMaster->projectname
            );

        $projectNickname =
            strtoupper(
                substr($projectName, 0, 3)
            );
    }

    $generatedSku =
        $itemNameCode .
        '-' .
        $projectNickname .
        '-' .
        $nextId;

    /*
    |--------------------------------------------------------------------------
    | NEW VERSION
    |--------------------------------------------------------------------------
    */

    $newRowData = $commonData;

    $newRowData['id'] =
        $nextId;

    $newRowData['barcode'] =
        $newBarcode;

    $newRowData['qrcode'] =
        $newBarcode;

    $newRowData['sku'] =
        $generatedSku;

    $newRowData['sku_supplier'] =
        $specification->sku_supplier
        ?? null;

    $newRowData['companyid'] =
        $companyId;

    $newRowData['subcompanyid'] =
        $subCompanyId;

    $newRowData['projectid'] =
        $projectId;

    $newRowData['supplier_id'] =
        $specification->supplier_id
        ?? null;

    $newRowData['supplier_person_id'] =
        $specification->supplier_person_id
        ?? null;

    $newRowData['supplier_product_id'] =
        $specification->supplier_product_id
        ?? null;

    $newRowData['img_path'] =
        $specification->img_path
        ?? null;

    $newRowData['subimg_path'] =
        $specification->subimg_path
        ?? null;

    $newRowData['status'] =
        '';

    $newRowData['box_assign'] =
        $specification->box_assign
        ?? '';

    $newRowData['print_status'] =
        $specification->print_status
        ?? null;

    $newRowData['description_id'] =
        $specification->description_id
        ?? null;

    $newRowData['oc_product_id'] =
        $specification->oc_product_id
        ?? null;

    $newRowData['oc_main_img'] =
        $specification->oc_main_img
        ?? null;

    $newSno =
        DB::table(
            'auto_designer_specification_master'
        )->insertGetId($newRowData);

    /*
    |--------------------------------------------------------------------------
    | BARCODE RELATION
    |--------------------------------------------------------------------------
    |
    | Keep older aliases pointing to the newest barcode.
    | Example:
    |     A -> B
    |     B -> C
    | becomes:
    |     A -> C
    |     B -> C
    |
    */

    DB::table(
        'product_specification_barcode_history'
    )
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->where('new_barcode', $oldBarcode)
        ->update([
            'new_barcode' => $newBarcode,
            'new_sno' => $newSno,
            'updated_at' => now(),
        ]);

    DB::table(
        'product_specification_barcode_history'
    )->insert([
        'companyid' =>
            $companyId,

        'subcompanyid' =>
            $subCompanyId,

        'projectid' =>
            $projectId,

        'original_sno' =>
            $specification->sno,

        'new_sno' =>
            $newSno,

        'old_barcode' =>
            $oldBarcode,

        'new_barcode' =>
            $newBarcode,

        'modified_by' =>
            $user->username,

        'modified_at' =>
            now(),

        'created_at' =>
            now(),

        'updated_at' =>
            now(),
    ]);

    return response()->json([
        'success' => true,
        'message' =>
            'Product specification updated successfully. Old barcode relation has been saved.',
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


/*
|--------------------------------------------------------------------------
| GENERATE PRODUCT BARCODE
|--------------------------------------------------------------------------
*/

private function generateProductBarcode(
    int $projectId,
    array $data
): string {
    return implode('', [
        $projectId,
        $data['item_name'] ?? 0,
        $data['item_type'] ?? 0,
        $data['designer_name'] ?? 0,
        $data['colour'] ?? 0,
        $data['sizes'] ?? 0,
        $data['client'] ?? 0,
    ]);
}


/*
|--------------------------------------------------------------------------
| VALIDATE MASTER RECORD
|--------------------------------------------------------------------------
*/

private function validateProductMasterRecord(
    string $table,
    int $id,
    int $companyId,
    int $subCompanyId,
    int $projectId
): void {
    $exists =
        DB::table($table)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('sno', $id);
            })
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


/*
|--------------------------------------------------------------------------
| RESOLVE OLD BARCODE TO CURRENT BARCODE
|--------------------------------------------------------------------------
*/

private function resolveProductBarcode(
    $barcode,
    int $companyId,
    int $subCompanyId,
    int $projectId
): string {
    $current = trim((string) $barcode);

    if ($current === '') {
        return $current;
    }

    $seen = [];

    for ($i = 0; $i < 20; $i++) {

        if (isset($seen[$current])) {
            break;
        }

        $seen[$current] = true;

        $next =
            DB::table(
                'product_specification_barcode_history'
            )
                ->where('companyid', $companyId)
                ->where('subcompanyid', $subCompanyId)
                ->where('projectid', $projectId)
                ->where('old_barcode', $current)
                ->orderByDesc('id')
                ->value('new_barcode');

        if (!$next || (string) $next === $current) {
            break;
        }

        $current = (string) $next;
    }

    return $current;
}

/*
|--------------------------------------------------------------------------
| GET FIRST PRODUCT IMAGE
|--------------------------------------------------------------------------
*/

private function getImages($imgPath)
{
    if (!$imgPath) {
        return [];
    }

    $images = [];

    if (is_string($imgPath)) {
        $decoded = json_decode($imgPath, true);
        $images = is_array($decoded) ? $decoded : [$imgPath];
    } elseif (is_array($imgPath)) {
        $images = $imgPath;
    }

    $result = [];

    foreach ($images as $imagePath) {
        if (!is_string($imagePath)) {
            continue;
        }

        $imagePath = str_replace('\\', '/', trim($imagePath));
        if ($imagePath === '') {
            continue;
        }

        if (preg_match('/^https?:\\/\\//i', $imagePath)) {
            $result[] = $imagePath;
            continue;
        }

        $marker = 'ItemsDesigner_Masterwithbarcode/';
        $position = strpos($imagePath, $marker);
        if ($position === false) {
            continue;
        }

        $relativePath = substr($imagePath, $position);
        $result[] = asset($relativePath);
    }

    return array_values(array_unique($result));
}

private function getFirstImage($imgPath)
{
    if (!$imgPath) {
        return null;
    }

    $images = [];

    /*
    |--------------------------------------------------------------------------
    | IMG PATH CAN BE JSON ARRAY OR A NORMAL PATH
    |--------------------------------------------------------------------------
    */

    if (is_string($imgPath)) {

        $decoded = json_decode($imgPath, true);

        if (is_array($decoded)) {
            $images = $decoded;
        } else {
            $images = [$imgPath];
        }
    } elseif (is_array($imgPath)) {
        $images = $imgPath;
    }

    foreach ($images as $imagePath) {

        if (!is_string($imagePath)) {
            continue;
        }

        $imagePath = str_replace('\\', '/', trim($imagePath));

        if ($imagePath === '') {
            continue;
        }

        /* Already a full URL */
        if (preg_match('/^https?:\\/\\//i', $imagePath)) {
            return $imagePath;
        }

        /*
        |----------------------------------------------------------------------
        | Find ItemsDesigner_Masterwithbarcode anywhere in the stored path.
        | This also supports old values such as:
        | ../../ItemsDesigner_Masterwithbarcode/{barcode}/
        |----------------------------------------------------------------------
        */

        $marker = 'ItemsDesigner_Masterwithbarcode/';
        $position = strpos($imagePath, $marker);

        if ($position === false) {
            continue;
        }

        $relativePath = substr(
            $imagePath,
            $position
        );

        $relativePath = trim($relativePath, '/');

        if ($relativePath === '') {
            continue;
        }

        $physicalPath = public_path($relativePath);

        /*
        |----------------------------------------------------------------------
        | If img_path contains the exact image filename
        |----------------------------------------------------------------------
        */

        if (is_file($physicalPath)) {
            return asset($relativePath);
        }

        /*
        |----------------------------------------------------------------------
        | If img_path contains the product folder, find the first image inside
        |----------------------------------------------------------------------
        */

        if (is_dir($physicalPath)) {

            $files = scandir($physicalPath);

            foreach ($files as $file) {

                if ($file === '.' || $file === '..') {
                    continue;
                }

                $extension = strtolower(
                    pathinfo($file, PATHINFO_EXTENSION)
                );

                if (in_array(
                    $extension,
                    ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                    true
                )) {
                    return asset(
                        $relativePath . '/' . $file
                    );
                }
            }
        }
    }

    return null;
}


}