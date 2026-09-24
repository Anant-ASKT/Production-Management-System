<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockStatusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS FILTERS
    |--------------------------------------------------------------------------
    |
    | Data flow:
    |
    | published_product.specification_id
    |          ↓
    | auto_designer_specification_master.sno
    |          ↓
    | auto_designer_specification_master.barcode
    |          ↓
    | vendor_stock.barcode
    |
    */

    public function filters()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $base = DB::table('published_product as pp')
            ->join(
                'auto_designer_specification_master as dsm',
                'dsm.sno',
                '=',
                'pp.specification_id'
            )
            ->join(
                'vendor_stock as vs',
                'vs.barcode',
                '=',
                'dsm.barcode'
            )
            ->whereNotNull('pp.specification_id')
            ->where('pp.specification_id', '>', 0);


        /*
        |--------------------------------------------------------------------------
        | SUPPLIERS
        |--------------------------------------------------------------------------
        */

        $suppliers = (clone $base)
            ->whereNotNull('pp.origin_supplier_id')
            ->where('pp.origin_supplier_id', '!=', '')
            ->select(
                'pp.origin_supplier_id as id'
            )
            ->distinct()
            ->orderBy(
                'pp.origin_supplier_id'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PRODUCT TYPES
        |--------------------------------------------------------------------------
        */

        $productTypes = (clone $base)
            ->leftJoin(
                'auto_itemtype_master as itemtype',
                'itemtype.id',
                '=',
                'dsm.item_type'
            )
            ->whereNotNull('dsm.item_type')
            ->select([
                'dsm.item_type as id',
                'itemtype.itemtype as name'
            ])
            ->distinct()
            ->orderBy(
                'itemtype.itemtype'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PRODUCT NAMES
        |--------------------------------------------------------------------------
        */

        $productNames = (clone $base)
            ->leftJoin(
                'auto_itemname_master as itemname',
                'itemname.id',
                '=',
                'dsm.item_name'
            )
            ->whereNotNull('dsm.item_name')
            ->select([
                'dsm.item_name as id',
                'itemname.itemname as name'
            ])
            ->distinct()
            ->orderBy(
                'itemname.itemname'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | COMPOSITIONS
        |--------------------------------------------------------------------------
        */

        $compositions = (clone $base)
            ->leftJoin(
                'auto_composition_master_stock as composition',
                'composition.id',
                '=',
                'dsm.composition'
            )
            ->whereNotNull('dsm.composition')
            ->select([
                'dsm.composition as id',
                'composition.composition_details as name'
            ])
            ->distinct()
            ->orderBy(
                'composition.composition_details'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GENDERS
        |--------------------------------------------------------------------------
        */

        $genders = (clone $base)
            ->leftJoin(
                'auto_gender_master as gender',
                'gender.id',
                '=',
                'dsm.gender'
            )
            ->whereNotNull('dsm.gender')
            ->select([
                'dsm.gender as id',
                'gender.name as name'
            ])
            ->distinct()
            ->orderBy(
                'gender.name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSES
        |--------------------------------------------------------------------------
        */

        $warehouses = (clone $base)
            ->whereNotNull('vs.warehouse_id')
            ->select([
                'vs.warehouse_id as id',
                'vs.warehouse_location as name'
            ])
            ->distinct()
            ->orderBy(
                'vs.warehouse_id'
            )
            ->get();


        return response()->json([
            'success' => true,

            'data' => [
                'suppliers'      => $suppliers,
                'product_types'  => $productTypes,
                'product_names'  => $productNames,
                'compositions'   => $compositions,
                'genders'        => $genders,
                'warehouses'     => $warehouses
            ]
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK STATUS REPORT
    |--------------------------------------------------------------------------
    */

    public function report(Request $request)
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
        | REQUEST FILTERS
        |--------------------------------------------------------------------------
        */

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $sku = trim(
            (string) $request->input(
                'sku',
                ''
            )
        );

        $barcode = trim(
            (string) $request->input(
                'barcode',
                ''
            )
        );


        $supplierId = $request->filled('supplier_id')
            ? (int) $request->input('supplier_id')
            : null;


        $productType = $request->filled('product_type')
            ? (int) $request->input('product_type')
            : null;


        $productName = $request->filled('product_name')
            ? (int) $request->input('product_name')
            : null;


        $composition = $request->filled('composition')
            ? (int) $request->input('composition')
            : null;


        $gender = $request->filled('gender')
            ? (int) $request->input('gender')
            : null;


        $warehouseId = $request->filled('warehouse_id')
            ? (int) $request->input('warehouse_id')
            : null;


        $stockStatus = trim(
            (string) $request->input(
                'stock_status',
                ''
            )
        );


        $fromDate = $request->input(
            'from_date'
        );

        $toDate = $request->input(
            'to_date'
        );


        $perPage = (int) $request->input(
            'per_page',
            20
        );


        if (!in_array(
            $perPage,
            [10, 20, 30, 50, 100],
            true
        )) {
            $perPage = 20;
        }


        /*
        |--------------------------------------------------------------------------
        | VENDOR STOCK SUMMARY
        |--------------------------------------------------------------------------
        |
        | One row per:
        |
        | barcode + warehouse
        |
        */

        $stockSummary = DB::table(
            'vendor_stock as vs'
        )
            ->select([
                'vs.barcode',
                'vs.warehouse_id',
                'vs.warehouse_location',

                DB::raw(
                    'SUM(COALESCE(vs.quantity_received, 0)) as quantity_received'
                ),

                DB::raw(
                    'SUM(COALESCE(vs.send_qty, 0)) as send_qty'
                ),

                DB::raw(
                    'SUM(COALESCE(vs.avilable_qty, 0)) as available_qty'
                ),

                DB::raw(
                    'SUM(COALESCE(vs.quantity_given, 0)) as quantity_given'
                ),

                DB::raw(
                    'MAX(vs.stock_date) as stock_date'
                )
            ])
            ->whereNotNull(
                'vs.barcode'
            )
            ->where(
                'vs.barcode',
                '!=',
                ''
            );


        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($fromDate)) {

            $stockSummary->whereDate(
                'vs.stock_date',
                '>=',
                $fromDate
            );
        }


        if (!empty($toDate)) {

            $stockSummary->whereDate(
                'vs.stock_date',
                '<=',
                $toDate
            );
        }


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE FILTER
        |--------------------------------------------------------------------------
        */

        if ($warehouseId !== null) {

            $stockSummary->where(
                'vs.warehouse_id',
                $warehouseId
            );
        }


        $stockSummary->groupBy(
            'vs.barcode',
            'vs.warehouse_id',
            'vs.warehouse_location'
        );


        /*
        |--------------------------------------------------------------------------
        | MAIN REPORT
        |--------------------------------------------------------------------------
        */

        $query = DB::table(
            'published_product as pp'
        )

            ->join(
                'auto_designer_specification_master as dsm',
                'dsm.sno',
                '=',
                'pp.specification_id'
            )

            ->joinSub(
                $stockSummary,
                'vs',
                function ($join) {

                    $join->on(
                        'vs.barcode',
                        '=',
                        'dsm.barcode'
                    );
                }
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

            ->whereNotNull(
                'pp.specification_id'
            )

            ->where(
                'pp.specification_id',
                '>',
                0
            );


        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        if ($supplierId !== null) {

            $query->where(
                'pp.origin_supplier_id',
                $supplierId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT TYPE
        |--------------------------------------------------------------------------
        */

        if ($productType !== null) {

            $query->where(
                'dsm.item_type',
                $productType
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT NAME
        |--------------------------------------------------------------------------
        */

        if ($productName !== null) {

            $query->where(
                'dsm.item_name',
                $productName
            );
        }


        /*
        |--------------------------------------------------------------------------
        | COMPOSITION
        |--------------------------------------------------------------------------
        */

        if ($composition !== null) {

            $query->where(
                'dsm.composition',
                $composition
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GENDER
        |--------------------------------------------------------------------------
        */

        if ($gender !== null) {

            $query->where(
                'dsm.gender',
                $gender
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SKU
        |--------------------------------------------------------------------------
        */

        if ($sku !== '') {

            $query->where(
                'dsm.sku',
                'like',
                '%' . $sku . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BARCODE
        |--------------------------------------------------------------------------
        */

        if ($barcode !== '') {

            $query->where(
                'dsm.barcode',
                'like',
                '%' . $barcode . '%'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERAL SEARCH
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
                        'dsm.sku_supplier',
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
                    )

                    ->orWhere(
                        'composition.composition_details',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'gender.name',
                        'like',
                        '%' . $search . '%'
                    )

                    ->orWhere(
                        'pp.category_name',
                        'like',
                        '%' . $search . '%'
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STOCK STATUS
        |--------------------------------------------------------------------------
        */

        if ($stockStatus === 'in_stock') {

            $query->where(
                'vs.available_qty',
                '>',
                0
            );

        } elseif ($stockStatus === 'out_of_stock') {

            $query->where(
                'vs.available_qty',
                '<=',
                0
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $query->select([

            'pp.sno as published_id',

            'pp.specification_id',

            'pp.origin_supplier_id',

            'pp.target_supplier_id',

            'pp.category_id',

            'pp.category_name',

            'pp.woocommerce_product_id',

            'pp.permalink',

            'pp.status as published_status',

            'pp.created_at as published_at',

            'pp.updated_at as published_updated_at',


            'dsm.sno as specification_sno',

            'dsm.barcode',

            'dsm.sku',

            'dsm.sku_supplier',

            'dsm.img_path',

            'dsm.subimg_path',

            'dsm.oc_main_img',

            'dsm.item_type',

            'itemtype.itemtype as item_type_name',

            'dsm.item_name',

            'itemname.itemname as item_name_name',

            'dsm.composition',

            'composition.composition_details as composition_name',

            'dsm.gender',

            'gender.name as gender_name',

            'dsm.colour',

            'dsm.sizes',


            'vs.warehouse_id',

            'vs.warehouse_location',

            'vs.quantity_received',

            'vs.send_qty',

            'vs.available_qty',

            'vs.quantity_given',

            'vs.stock_date'

        ]);


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summaryQuery = clone $query;

        $summaryRows = $summaryQuery->get([
            'vs.quantity_received',
            'vs.send_qty',
            'vs.available_qty',
            'vs.quantity_given'
        ]);


        $summary = [

            'total_products' =>
                $summaryRows->count(),

            'quantity_received' =>
                (float) $summaryRows->sum(
                    function ($row) {
                        return (float) $row->quantity_received;
                    }
                ),

            'send_qty' =>
                (float) $summaryRows->sum(
                    function ($row) {
                        return (float) $row->send_qty;
                    }
                ),

            'available_qty' =>
                (float) $summaryRows->sum(
                    function ($row) {
                        return (float) $row->available_qty;
                    }
                ),

            'quantity_given' =>
                (float) $summaryRows->sum(
                    function ($row) {
                        return (float) $row->quantity_given;
                    }
                )
        ];


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $query->orderBy(
            'dsm.sku',
            'asc'
        );

        $query->orderBy(
            'dsm.barcode',
            'asc'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $report = $query->paginate(
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
                $report->items(),

            'current_page' =>
                $report->currentPage(),

            'last_page' =>
                $report->lastPage(),

            'per_page' =>
                $report->perPage(),

            'total' =>
                $report->total(),

            'from' =>
                $report->firstItem(),

            'to' =>
                $report->lastItem(),

            'summary' =>
                $summary

        ]);
    }
}