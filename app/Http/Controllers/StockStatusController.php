<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockStatusController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | VENDOR STOCK SUMMARY
        |--------------------------------------------------------------------------
        */

        $stockSummary = DB::table('vendor_stock as vs')
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
                ),
            ])
            ->whereNotNull('vs.barcode')
            ->where('vs.barcode', '!=', '')
            ->groupBy(
                'vs.barcode',
                'vs.warehouse_id',
                'vs.warehouse_location'
            );


        /*
        |--------------------------------------------------------------------------
        | PUBLISHED PRODUCTS
        |--------------------------------------------------------------------------
        */

        $query = DB::table('published_products as pp')

            ->join(
                'auto_designer_specification_master as dsm',
                'dsm.sno',
                '=',
                'pp.specification_id'
            )

            /*
            | LEFT JOIN is intentional.
            | A published barcode without vendor stock
            | will still appear as Out of Stock.
            */

            ->leftJoinSub(
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

            ->whereNotNull('pp.specification_id')
            ->where(
                'pp.specification_id',
                '>',
                0
            );


        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $query->select([

            // Published product

            'pp.sno as published_id',

            'pp.specification_id',

            'pp.origin_supplier_id',

            'pp.target_supplier_id',

            'pp.category_id',

            'pp.category_name',

            'pp.status as published_status',

            'pp.created_at as published_at',

            'pp.updated_at as published_updated_at',


            // Specification

            'dsm.sno as specification_sno',

            'dsm.barcode',

            'dsm.sku',

            'dsm.sku_supplier',

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


            // Vendor stock

            'vs.warehouse_id',

            'vs.warehouse_location',

            DB::raw(
                'COALESCE(vs.quantity_received, 0) as quantity_received'
            ),

            DB::raw(
                'COALESCE(vs.send_qty, 0) as send_qty'
            ),

            DB::raw(
                'COALESCE(vs.available_qty, 0) as available_qty'
            ),

            DB::raw(
                'COALESCE(vs.quantity_given, 0) as quantity_given'
            ),

            'vs.stock_date',
        ]);


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $query->orderBy('dsm.sku', 'asc');

        $query->orderBy('dsm.barcode', 'asc');


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summaryRows = (clone $query)->get();

        $summary = [

            'total_products' =>
                $summaryRows->count(),

            'quantity_received' =>
                (float) $summaryRows->sum(
                    fn ($row) =>
                        (float) $row->quantity_received
                ),

            'send_qty' =>
                (float) $summaryRows->sum(
                    fn ($row) =>
                        (float) $row->send_qty
                ),

            'available_qty' =>
                (float) $summaryRows->sum(
                    fn ($row) =>
                        (float) $row->available_qty
                ),

            'quantity_given' =>
                (float) $summaryRows->sum(
                    fn ($row) =>
                        (float) $row->quantity_given
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $report = $query->paginate(50);


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'stock-status.index',
            [
                'report' => $report,
                'summary' => $summary,
            ]
        );
    }
}