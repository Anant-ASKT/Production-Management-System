<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReadyToSellStockController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Ready to Sell Stock Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('inventory.ready-to-sell-stock');
    }


    /*
    |--------------------------------------------------------------------------
    | Get Warehouses
    |--------------------------------------------------------------------------
    */

    public function getWarehouses()
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


        $warehouses = DB::table(
            'strs_warehouse'
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

        ->orderBy(
            'warehousename'
        )

        ->select([
            'sno',
            'id',
            'warehousename',
            'type',
            'storagetype',
            'locationcode',
            'primaryaddress'
        ])

        ->get();


        return response()->json([

            'success' => true,

            'data' => $warehouses

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Locations By Warehouse
    |--------------------------------------------------------------------------
    */

    public function getLocations(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }


        $warehouseId =
            $request->input('warehouse_id');


        if (
            $warehouseId === null ||
            $warehouseId === ''
        ) {

            return response()->json([
                'success' => false,
                'message' => 'Warehouse is required.'
            ], 422);

        }


        $companyId =
            (int) $user->company_id;

        $subCompanyId =
            (int) $user->sub_company_id;

        $projectId =
            (int) $user->project_id;


        $locations = DB::table(
            'strs_locationmaster'
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

        /*
        |--------------------------------------------------------------------------
        | Warehouse relation
        |--------------------------------------------------------------------------
        |
        | strs_locationmaster.warehouse_id
        | points to strs_warehouse.sno.
        |
        */

        ->where(
            'warehouse_id',
            (int) $warehouseId
        )

        ->orderBy(
            'locationname'
        )

        ->select([
            'sno',
            'id',
            'locationname',
            'warehousename',
            'warehouse_id',
            'warehousesection',
            'floornumber',
            'stackno',
            'racknumber',
            'boxno'
        ])

        ->get();


        return response()->json([

            'success' => true,

            'data' => $locations

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Boxes By Location
    |--------------------------------------------------------------------------
    */

    public function getBoxes(Request $request)
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


        $warehouseId =
            (int) $request->input(
                'warehouse_id'
            );


        $locationId =
            (int) $request->input(
                'location_id'
            );


        if (!$warehouseId) {

            return response()->json([
                'success' => false,
                'message' => 'Warehouse is required.'
            ], 422);

        }


        if (!$locationId) {

            return response()->json([
                'success' => false,
                'message' => 'Location is required.'
            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | GET BOXES
        |--------------------------------------------------------------------------
        |
        | tbl_boxes:
        |
        | warehouseid = warehouse
        | location    = location
        | status      = NULL / empty
        |
        */

        $boxes = DB::table(
            'tbl_boxes'
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

        ->where(
            'warehouseid',
            $warehouseId
        )

        ->where(
            'location',
            $locationId
        )

        ->where(
            function ($query) {

                $query
                    ->whereNull('status')
                    ->orWhere('status', '');

            }
        )

        ->whereNotNull(
            'boxno'
        )

        ->where(
            'boxno',
            '<>',
            ''
        )

        ->select([
            'sno',
            'id',
            'box_title',
            'boxno',

            // ADD THIS
            'qr_code',

            'warehouseid',
            'FloorNo',
            'StackNo',
            'RackNo',
            'location',
            'status'
        ])

        ->orderBy(
            'sno',
            'asc'
        )

        ->get();


        return response()->json([

            'success' => true,

            'data' => $boxes

        ]);
    }

   public function storeWarehouse(Request $request)
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
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'warehousename' =>
            'required|string|max:500',

        'type' =>
            'required|in:warehouse,department',

        'country' =>
            'nullable|string|max:500',

        'state_id' =>
            'required|integer',

        'state' =>
            'required|string|max:500',

        'district' =>
            'nullable|string|max:500',

        'contactperson' =>
            'nullable|string|max:500',

        'phonenumber' =>
            'nullable|string|max:500',

        'emailaddress' =>
            'nullable|email|max:500',

        'primaryaddress' =>
            'nullable|string',

    ]);


    /*
    |--------------------------------------------------------------------------
    | USER COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | CHECK STATE
    |--------------------------------------------------------------------------
    */

    $state = DB::table('state_master')
        ->where(
            'state_id',
            $validated['state_id']
        )
        ->first();


    if (!$state) {

        return response()->json([
            'success' => false,
            'message' => 'Selected state is not valid.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE WAREHOUSE
    |--------------------------------------------------------------------------
    */

    $alreadyExists = DB::table(
        'strs_warehouse'
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
        ->whereRaw(
            'LOWER(TRIM(warehousename)) = LOWER(TRIM(?))',
            [
                $validated['warehousename']
            ]
        )
        ->exists();


    if ($alreadyExists) {

        return response()->json([
            'success' => false,
            'message' =>
                'This warehouse already exists.'
        ], 422);

    }


    /*
|--------------------------------------------------------------------------
| GENERATE NEXT WAREHOUSE ID
|--------------------------------------------------------------------------
*/

    $maxWarehouseId = DB::table('strs_warehouse')
        ->max('id');


    $nextWarehouseId =
        $maxWarehouseId
            ? ((int) $maxWarehouseId + 1)
            : 1;


    /*
    |--------------------------------------------------------------------------
    | INSERT WAREHOUSE
    |--------------------------------------------------------------------------
    */

    $warehouseSno = DB::table(
        'strs_warehouse'
    )->insertGetId([

         'id' =>
            $nextWarehouseId,

        'companyid' =>
            $companyId,

        'subcompanyid' =>
            $subCompanyId,

        'projectid' =>
            $projectId,

        'tedit' =>
            null,

        'loginid' =>
            (string) ($user->id ?? ''),

        'edatetime' =>
            now(),

        'warehousename' =>
            trim(
                $validated['warehousename']
            ),

        'type' =>
            $validated['type'],

        /*
        |--------------------------------------------------------------------------
        | Removed fields
        |--------------------------------------------------------------------------
        */

        'storagetype' =>
            null,

        'locationcode' =>
            null,

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */

        'primaryaddress' =>
            $validated['primaryaddress'] ?? null,

        'country' =>
            $validated['country'] ?? 'India',

        /*
        |--------------------------------------------------------------------------
        | STATE
        |--------------------------------------------------------------------------
        */

        'state_id' =>
            $state->state_id,

        'state' =>
            $state->state,

        /*
        |--------------------------------------------------------------------------
        | Other details
        |--------------------------------------------------------------------------
        */

        'district' =>
            $validated['district'] ?? null,

        'contactperson' =>
            $validated['contactperson'] ?? null,

        'phonenumber' =>
            $validated['phonenumber'] ?? null,

        'emailaddress' =>
            $validated['emailaddress'] ?? null,

        /*
        |--------------------------------------------------------------------------
        | Removed fields
        |--------------------------------------------------------------------------
        */

        'locationidofwarehouse' =>
            null,

        'locationofwarehouse' =>
            null,

    ]);


    /*
    |--------------------------------------------------------------------------
    | GET NEW RECORD
    |--------------------------------------------------------------------------
    */

    $warehouse = DB::table(
        'strs_warehouse'
    )
        ->where(
            'sno',
            $warehouseSno
        )
        ->first();


    return response()->json([

        'success' => true,

        'message' =>
            'Warehouse created successfully.',

        'data' =>
            $warehouse

    ]);
}

public function getStates()
{
    $states = DB::table('state_master')
        ->select([
            'sno',
            'state_id',
            'state',
            'state_code'
        ])
        ->whereNotNull('state_id')
        ->whereNotNull('state')
        ->where('state', '<>', '')
        ->orderBy('state', 'asc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $states
    ]);
}

public function storeLocation(Request $request)
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
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'locationname' =>
            'required|string|max:500',

        'warehouse_id' =>
            'required|integer',

        'state_id' =>
            'nullable|integer',

        'state' =>
            'nullable|string|max:200',

        'warehousename' =>
            'nullable|string|max:500',

        'warehousesection' =>
            'nullable|string|max:500',

        'floornumber' =>
            'nullable|string|max:500',

        'stackno' =>
            'nullable|string|max:500',

        'racknumber' =>
            'nullable|string|max:500',

    ]);


    /*
    |--------------------------------------------------------------------------
    | COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | GET WAREHOUSE
    |--------------------------------------------------------------------------
    */

    $warehouse = DB::table(
        'strs_warehouse'
    )
        ->where(
            'sno',
            $validated['warehouse_id']
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
        ->first();


    if (!$warehouse) {

        return response()->json([
            'success' => false,
            'message' =>
                'Selected warehouse was not found.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | DUPLICATE LOCATION CHECK
    |--------------------------------------------------------------------------
    */

    $alreadyExists = DB::table(
        'strs_locationmaster'
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
        ->where(
            'warehouse_id',
            $warehouse->sno
        )
        ->whereRaw(
            'LOWER(TRIM(locationname)) = LOWER(TRIM(?))',
            [
                $validated['locationname']
            ]
        )
        ->exists();


    if ($alreadyExists) {

        return response()->json([
            'success' => false,
            'message' =>
                'This location already exists in the selected warehouse.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE ID
    |--------------------------------------------------------------------------
    */

    $maxLocationId =
        DB::table(
            'strs_locationmaster'
        )->max('id');


    $nextLocationId =
        $maxLocationId
            ? ((int) $maxLocationId + 1)
            : 1;


    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    */

    $locationSno =
        DB::table(
            'strs_locationmaster'
        )->insertGetId([

            'companyid' =>
                $companyId,

            'subcompanyid' =>
                $subCompanyId,

            'projectid' =>
                $projectId,

            'id' =>
                $nextLocationId,

            'locationname' =>
                trim(
                    $validated['locationname']
                ),

            /*
            |--------------------------------------------------------------------------
            | Warehouse
            |--------------------------------------------------------------------------
            */

            'warehousename' =>
                $warehouse->warehousename,

            'warehouse_id' =>
                $warehouse->sno,

            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            'state' =>
                $warehouse->state,

            'state_id' =>
                $warehouse->state_id,

            /*
            |--------------------------------------------------------------------------
            | Location details
            |--------------------------------------------------------------------------
            */

            'warehousesection' =>
                $validated['warehousesection'] ?? null,

            'floornumber' =>
                $validated['floornumber'] ?? null,

            'stackno' =>
                $validated['stackno'] ?? null,

            'racknumber' =>
                $validated['racknumber'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | System
            |--------------------------------------------------------------------------
            */

            'tedit' =>
                null,

            'edatetime' =>
                now(),

            'loginid' =>
                (string) ($user->id ?? ''),

        ]);


    /*
    |--------------------------------------------------------------------------
    | GET CREATED LOCATION
    |--------------------------------------------------------------------------
    */

    $location =
        DB::table(
            'strs_locationmaster'
        )
        ->where(
            'sno',
            $locationSno
        )
        ->first();


    return response()->json([

        'success' => true,

        'message' =>
            'Location created successfully.',

        'data' =>
            $location

    ]);
}

public function getBoxTitles(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $warehouseId = (int) $request->warehouse_id;
    $locationId  = (int) $request->location_id;

    if (!$warehouseId) {
        return response()->json([
            'success' => false,
            'message' => 'Warehouse is required.'
        ], 422);
    }

    if (!$locationId) {
        return response()->json([
            'success' => false,
            'message' => 'Location is required.'
        ], 422);
    }

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | GET EXISTING BOX TITLES
    |--------------------------------------------------------------------------
    |
    | For every title:
    |
    | MAX(id_max) + 1 = next id_max
    |
    */

    $data = DB::table('tbl_boxes')
        ->where('warehouseid', $warehouseId)
        ->where('location', $locationId)
        ->where('companyid', $companyId)
        ->where('subcompanyid', $subCompanyId)
        ->where('projectid', $projectId)
        ->whereNotNull('box_title')
        ->where('box_title', '<>', '')
        ->select(
            'box_title',
            DB::raw('COALESCE(MAX(id_max), 0) + 1 AS next_id_max')
        )
        ->groupBy('box_title')
        ->orderBy('box_title')
        ->get();


    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

public function storeBox(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | USER
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
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([

        'warehouse_id' =>
            'required|integer',

        'location_id' =>
            'required|integer',

        'box_title' =>
            'required|string|max:200',

    ]);


    /*
    |--------------------------------------------------------------------------
    | USER CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | REQUEST VALUES
    |--------------------------------------------------------------------------
    */

    $warehouseId =
        (int) $validated['warehouse_id'];

    $locationId =
        (int) $validated['location_id'];

    $boxTitle =
        trim(
            $validated['box_title']
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK WAREHOUSE
    |--------------------------------------------------------------------------
    */

    $warehouse = DB::table(
        'strs_warehouse'
    )
    ->where(
        'sno',
        $warehouseId
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
    ->first();


    if (!$warehouse) {

        return response()->json([
            'success' => false,
            'message' =>
                'Selected warehouse was not found.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | CHECK LOCATION
    |--------------------------------------------------------------------------
    */

    $location = DB::table(
        'strs_locationmaster'
    )
    ->where(
        'sno',
        $locationId
    )
    ->where(
        'warehouse_id',
        $warehouseId
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
    ->first();


    if (!$location) {

        return response()->json([
            'success' => false,
            'message' =>
                'Selected location was not found for this warehouse.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE id_max
    |--------------------------------------------------------------------------
    |
    | Same logic as your existing code.
    |
    */

    $maxIdMax = DB::table(
        'tbl_boxes'
    )
    ->where(
        'warehouseid',
        $warehouseId
    )
    ->where(
        'location',
        $locationId
    )
    ->whereRaw(
        'LOWER(TRIM(box_title)) = LOWER(TRIM(?))',
        [
            $boxTitle
        ]
    )
    ->max(
        'id_max'
    );


    /*
    |--------------------------------------------------------------------------
    | NEXT id_max
    |--------------------------------------------------------------------------
    */

    $nextIdMax =
        $maxIdMax !== null
            ? ((int) $maxIdMax + 1)
            : 1;


    /*
    |--------------------------------------------------------------------------
    | GENERATE UNIQUE RECORD ID
    |--------------------------------------------------------------------------
    */

    $maxRecordId =
        DB::table(
            'tbl_boxes'
        )
        ->max(
            'id'
        );


    $nextRecordId =
        $maxRecordId !== null
            ? ((int) $maxRecordId + 1)
            : 1;


    /*
    |--------------------------------------------------------------------------
    | MAKE SURE RECORD ID IS UNIQUE
    |--------------------------------------------------------------------------
    */

    while (
        DB::table(
            'tbl_boxes'
        )
        ->where(
            'id',
            $nextRecordId
        )
        ->exists()
    ) {

        $nextRecordId++;

    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE BOX NUMBER
    |--------------------------------------------------------------------------
    */

    $boxNo =
        $boxTitle .
        '-' .
        $nextIdMax;


    /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE BOX NUMBER
    |--------------------------------------------------------------------------
    */

    $boxExists = DB::table(
        'tbl_boxes'
    )
    ->where(
        'boxno',
        $boxNo
    )
    ->exists();


    if ($boxExists) {

        return response()->json([
            'success' => false,
            'message' =>
                'Generated Box No already exists. Please try again.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | INSERT BOX
    |--------------------------------------------------------------------------
    */

    $boxSno = DB::table(
        'tbl_boxes'
    )
    ->insertGetId([

        /*
        |--------------------------------------------------------------------------
        | UNIQUE RECORD ID
        |--------------------------------------------------------------------------
        */

        'id' =>
            $nextRecordId,


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
        | BOX SEQUENCE
        |--------------------------------------------------------------------------
        */

        'id_max' =>
            $nextIdMax,


        /*
        |--------------------------------------------------------------------------
        | BOX TITLE
        |--------------------------------------------------------------------------
        */

        'box_title' =>
            $boxTitle,


        /*
        |--------------------------------------------------------------------------
        | BOX NUMBER
        |--------------------------------------------------------------------------
        */

        'boxno' =>
            $boxNo,


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE
        |--------------------------------------------------------------------------
        */

        'warehouseid' =>
            $warehouseId,


        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        'location' =>
            $locationId,


        /*
        |--------------------------------------------------------------------------
        | LOCATION DETAILS
        |--------------------------------------------------------------------------
        */

        'FloorNo' =>
            $location->floornumber,

        'StackNo' =>
            $location->stackno,

        'RackNo' =>
            $location->racknumber,


        /*
        |--------------------------------------------------------------------------
        | NEW BOX STATUS
        |--------------------------------------------------------------------------
        */

        'status' =>
            null,

        'tedit' =>
            null,

        'transferboxno' =>
            null,

        'transferboxid' =>
            null,

        'transfer_status' =>
            null,

    ]);


    /*
    |--------------------------------------------------------------------------
    | CREATE QR URL
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We create the URL AFTER the box is inserted
    | because we need $boxSno.
    |
    */

    $qrUrl = route(
        'inventory.ready-to-sell-stock.box-view',
        [

            'company_id' =>
                $companyId,

            'sub_company_id' =>
                $subCompanyId,

            'project_id' =>
                $projectId,

            'warehouse_id' =>
                $warehouseId,

            'location_id' =>
                $locationId,

            'box_id' =>
                $boxSno,

            'box_qr' =>
                $boxNo,

        ]
    );


    /*
    |--------------------------------------------------------------------------
    | SAVE QR URL
    |--------------------------------------------------------------------------
    */

    DB::table(
        'tbl_boxes'
    )
    ->where(
        'sno',
        $boxSno
    )
    ->update([

        'qr_code' =>
            $qrUrl

    ]);


    /*
    |--------------------------------------------------------------------------
    | GET CREATED BOX
    |--------------------------------------------------------------------------
    */

    $box = DB::table(
        'tbl_boxes'
    )
    ->where(
        'sno',
        $boxSno
    )
    ->first();


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' =>
            true,

        'message' =>
            'Box created successfully.',

        'data' =>
            $box

    ]);
}

public function boxView(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | GET QR VALUES
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) $request->query('company_id');

    $subCompanyId =
        (int) $request->query('sub_company_id');

    $projectId =
        (int) $request->query('project_id');

    $warehouseId =
        (int) $request->query('warehouse_id');

    $locationId =
        (int) $request->query('location_id');

    $boxId =
        (int) $request->query('box_id');

    $boxQr =
        trim(
            (string) $request->query(
                'box_qr',
                ''
            )
        );


    /*
    |--------------------------------------------------------------------------
    | BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        !$companyId ||
        !$subCompanyId ||
        !$projectId ||
        !$warehouseId ||
        !$locationId ||
        !$boxId ||
        $boxQr === ''
    ) {

        abort(
            404,
            'Invalid Box QR.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GET BOX
    |--------------------------------------------------------------------------
    */

    $box =
        DB::table('tbl_boxes')

        ->where(
            'sno',
            $boxId
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

        ->where(
            'warehouseid',
            $warehouseId
        )

        ->where(
            'location',
            $locationId
        )

        ->where(
            'boxno',
            $boxQr
        )

        ->first();


    if (!$box) {

        abort(
            404,
            'Box not found.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GET WAREHOUSE
    |--------------------------------------------------------------------------
    */

    $warehouse =
        DB::table('strs_warehouse')

        ->where(
            'sno',
            $warehouseId
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

        ->first();


    /*
    |--------------------------------------------------------------------------
    | GET LOCATION
    |--------------------------------------------------------------------------
    */

    $location =
        DB::table('strs_locationmaster')

        ->where(
            'sno',
            $locationId
        )

        ->where(
            'warehouse_id',
            $warehouseId
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

        ->first();


    /*
    |--------------------------------------------------------------------------
    | GET STOCK IN THIS BOX
    |--------------------------------------------------------------------------
    |
    | Your Ready-to-Sell save process stores stock
    | in vendor_stock and uses boxid.
    |
    */

    $stock =
        DB::table('vendor_stock as vs')

        ->leftJoin(
            'auto_designer_specification_master as p',
            function ($join) {

                $join
                    ->on(
                        'p.barcode',
                        '=',
                        'vs.barcode'
                    )

                    ->on(
                        'p.companyid',
                        '=',
                        'vs.companyid'
                    )

                    ->on(
                        'p.subcompanyid',
                        '=',
                        'vs.subcompanyid'
                    )

                    ->on(
                        'p.projectid',
                        '=',
                        'vs.projectid'
                    );

            }
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

        ->where(
            'vs.warehouse_id',
            $warehouseId
        )

        ->where(
            'vs.location_id',
            $locationId
        )

        ->where(
            'vs.boxid',
            $boxId
        )

        ->select([

            /*
            |--------------------------------------------------------------------------
            | STOCK
            |--------------------------------------------------------------------------
            */

            'vs.id as stock_id',

            'vs.barcode',

            'vs.item_id',

            'vs.quantity_received',

            'vs.send_qty',

            'vs.boxid',

            'vs.warehouse_id',

            'vs.location_id',


            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            'p.sno as product_id',

            'p.sku',

            'p.item_name',

            'p.item_type',

            'p.designer_name',

            'p.colour',

            'p.sizes',

            'p.gender',

            'p.composition',

            'p.manufacturing_process',

            'p.img_path'

        ])

        ->orderBy(
            'vs.id',
            'asc'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | TOTAL QUANTITY
    |--------------------------------------------------------------------------
    */

    $totalQuantity =
        $stock->sum(
            function ($item) {

                return (int)
                    ($item->quantity_received ?? 0);

            }
        );


    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'inventory.box-view',
        [

            'box' =>
                $box,

            'warehouse' =>
                $warehouse,

            'location' =>
                $location,

            'stock' =>
                $stock,

            'totalQuantity' =>
                $totalQuantity,

        ]
    );
}

public function saveReadyToSellStock(Request $request)
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
    | VALIDATE BASIC REQUEST
    |--------------------------------------------------------------------------
    */

    $request->validate([
        'items' =>
            'required|array|min:1',

        'items.*.barcode' =>
            'required|string',

        'items.*.product_id' =>
            'required|integer',

        'items.*.warehouse_id' =>
            'required|integer',

        'items.*.location_id' =>
            'required|integer',

        'items.*.box_id' =>
            'required|integer',

        'items.*.quantity' =>
            'required|integer|min:1',

    ]);


    /*
    |--------------------------------------------------------------------------
    | USER CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);

    $loginId =
        (string) ($user->id ?? '');


    $items =
        $request->input(
            'items',
            []
        );


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */

    try {

        $result = DB::transaction(
            function () use (
                $items,
                $companyId,
                $subCompanyId,
                $projectId,
                $loginId
            ) {


                /*
                |--------------------------------------------------------------------------
                | GET NEXT vendor_stock.id
                |--------------------------------------------------------------------------
                */

                $maxStockId =
                    DB::table(
                        'vendor_stock'
                    )
                    ->lockForUpdate()
                    ->max('id');


                $nextStockId =
                    $maxStockId
                        ? ((int) $maxStockId + 1)
                        : 1;


                $savedItems = [];

                $boxesUsed = [];


                /*
                |--------------------------------------------------------------------------
                | PROCESS EACH STOCK ITEM
                |--------------------------------------------------------------------------
                */

                foreach (
                    $items as $item
                ) {


                    $barcode =
                        trim(
                            (string)
                            ($item['barcode'] ?? '')
                        );


                    $productId =
                        (int)
                        ($item['product_id'] ?? 0);


                    $warehouseId =
                        (int)
                        ($item['warehouse_id'] ?? 0);


                    $locationId =
                        (int)
                        ($item['location_id'] ?? 0);


                    $boxId =
                        (int)
                        ($item['box_id'] ?? 0);


                    $quantity =
                        (int)
                        ($item['quantity'] ?? 0);


                    if (
                        $barcode === '' ||
                        $productId <= 0 ||
                        $warehouseId <= 0 ||
                        $locationId <= 0 ||
                        $boxId <= 0 ||
                        $quantity <= 0
                    ) {

                        throw new \Exception(
                            'Invalid stock item data.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    $product =
                        DB::table(
                            'auto_designer_specification_master'
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
                        ->where(
                            'barcode',
                            $barcode
                        )
                        ->first();


                    if (!$product) {

                        throw new \Exception(
                            'Product with barcode ' .
                            $barcode .
                            ' was not found.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK BOX
                    |--------------------------------------------------------------------------
                    */

                    $box =
                        DB::table(
                            'tbl_boxes'
                        )
                        ->where(
                            'sno',
                            $boxId
                        )
                        ->where(
                            'warehouseid',
                            $warehouseId
                        )
                        ->where(
                            'location',
                            $locationId
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
                        ->first();


                    if (!$box) {

                        throw new \Exception(
                            'Selected box was not found for barcode ' .
                            $barcode .
                            '.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK BOX STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        strtolower(
                            trim(
                                (string)
                                ($box->status ?? '')
                            )
                        ) === 'closed'
                    ) {

                        throw new \Exception(
                            'Box ' .
                            $box->boxno .
                            ' is already closed.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GET LOCATION
                    |--------------------------------------------------------------------------
                    */

                    $location =
                        DB::table(
                            'strs_locationmaster'
                        )
                        ->where(
                            'sno',
                            $locationId
                        )
                        ->where(
                            'warehouse_id',
                            $warehouseId
                        )
                        ->first();


                    if (!$location) {

                        throw new \Exception(
                            'Selected location was not found.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | INSERT ONE STOCK ROW FOR EACH QUANTITY
                    |--------------------------------------------------------------------------
                    |
                    | Example:
                    | quantity = 3
                    |
                    | Creates:
                    | row 1 -> quantity_received = 1
                    | row 2 -> quantity_received = 1
                    | row 3 -> quantity_received = 1
                    |
                    */

            for ($qtyIndex = 1; $qtyIndex <= $quantity; $qtyIndex++) {

                DB::table('vendor_stock')->insert([

                    /*
                    |--------------------------------------------------------------------------
                    | UNIQUE STOCK ID
                    |--------------------------------------------------------------------------
                    */

                    'id' =>
                        $nextStockId++,


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
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    'vendor_id' =>
                        $companyId,


                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    'item_id' =>
                        $productId,

                    'barcode' =>
                        $barcode,


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK DATE
                    |--------------------------------------------------------------------------
                    */

                    'stock_date' =>
                        now()->toDateString(),

                    'stockentrydate' =>
                        now()->toDateString(),


                    /*
                    |--------------------------------------------------------------------------
                    | IMPORTANT
                    |--------------------------------------------------------------------------
                    | Every physical stock item gets quantity = 1
                    */

                    'quantity_received' =>
                        1,

                    'send_qty' =>
                        0,


                    /*
                    |--------------------------------------------------------------------------
                    | WAREHOUSE
                    |--------------------------------------------------------------------------
                    */

                    'warehouse_id' =>
                        $warehouseId,

                    'warehouse_location' =>
                        $location->locationname ??
                        ($item['location_name'] ?? ''),

                    'location_id' =>
                        $locationId,


                    /*
                    |--------------------------------------------------------------------------
                    | BOX
                    |--------------------------------------------------------------------------
                    */

                    'boxid' =>
                        $boxId,


                    /*
                    |--------------------------------------------------------------------------
                    | PRODUCT REFERENCE
                    |--------------------------------------------------------------------------
                    */

                    'g_id' =>
                        $product->sno ?? null,


                    /*
                    |--------------------------------------------------------------------------
                    | REMARKS
                    |--------------------------------------------------------------------------
                    */

                    'remarks' =>
                        'Ready to Sell Stock',


                    /*
                    |--------------------------------------------------------------------------
                    | OTHER FIELDS
                    |--------------------------------------------------------------------------
                    */

                    'tedit' =>
                        null,

                    'flag_sendtointernal' =>
                        null,

                    'stock_transfer' =>
                        null,

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),

                ]);

            }


                    /*
                    |--------------------------------------------------------------------------
                    | MARK PRODUCT AS ASSIGNED
                    |--------------------------------------------------------------------------
                    */

                    DB::table(
                        'auto_designer_specification_master'
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
                    ->where(
                        'barcode',
                        $barcode
                    )
                    ->update([

                        'box_assign' =>
                            'done',

                        'tedit' =>
                            null,

                        'loginid' =>
                            $loginId,

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | REMEMBER BOXES USED
                    |--------------------------------------------------------------------------
                    */

                    $boxesUsed[
                        $boxId
                    ] = true;


                    $savedItems[] = [

                        'barcode' =>
                            $barcode,

                        'quantity' =>
                            $quantity,

                        'box_id' =>
                            $boxId,

                        'box_no' =>
                            $box->boxno,

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | RETURN RESULT
                |--------------------------------------------------------------------------
                */

                return [

                    'saved_items' =>
                        $savedItems,

                    'boxes_used' =>
                        array_keys(
                            $boxesUsed
                        ),

                ];

            }
        );


        return response()->json([

            'success' =>
                true,

            'message' =>
                'Stock saved successfully.',

            'data' =>
                $result,

        ]);


     } catch (\Throwable $e) {

    Log::error(
        'Ready to Sell Stock Save Error',
        [
            'message' => $e->getMessage(),
            'user_id' => $user->id ?? null,
            'items'   => $items,
        ]
    );

    return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 422);
}
}

public function viewStock(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        abort(401);
    }

    /*
    |--------------------------------------------------------------------------
    | USER CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | STOCK QUERY
    |--------------------------------------------------------------------------
    */

    $query = DB::table('vendor_stock as vs')


        /*
        |--------------------------------------------------------------------------
        | PRODUCT SPECIFICATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_specification_master as sm',
            function ($join) {

                $join->on(
                    'sm.barcode',
                    '=',
                    'vs.barcode'
                )

                ->on(
                    'sm.companyid',
                    '=',
                    'vs.companyid'
                )

                ->on(
                    'sm.subcompanyid',
                    '=',
                    'vs.subcompanyid'
                )

                ->on(
                    'sm.projectid',
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
            'auto_designer_master as dm',
            function ($join) {

                $join->on(
                    'dm.id',
                    '=',
                    'sm.designer_name'
                )

                ->on(
                    'dm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'dm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'dm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | ITEM TYPE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemtype_master as itm',
            function ($join) {

                $join->on(
                    'itm.id',
                    '=',
                    'sm.item_type'
                )

                ->on(
                    'itm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'itm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'itm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | GENDER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_gender_master as gm',
            function ($join) {

                $join->on(
                    'gm.id',
                    '=',
                    'sm.gender'
                )

                ->on(
                    'gm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'gm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'gm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | ITEM NAME
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemname_master as inm',
            function ($join) {

                $join->on(
                    'inm.id',
                    '=',
                    'sm.item_name'
                )

                ->on(
                    'inm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'inm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'inm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | COMPOSITION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_composition_master_stock as cm',
            function ($join) {

                $join->on(
                    'cm.id',
                    '=',
                    'sm.composition'
                )

                ->on(
                    'cm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'cm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'cm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | COLOUR
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_colour_master as colm',
            function ($join) {

                $join->on(
                    'colm.id',
                    '=',
                    'sm.colour'
                )

                ->on(
                    'colm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'colm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'colm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | SIZE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_size_master as szm',
            function ($join) {

                $join->on(
                    'szm.id',
                    '=',
                    'sm.sizes'
                )

                ->on(
                    'szm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'szm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'szm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | MANUFACTURING PROCESS
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_manufacturing_process_master as mpm',
            function ($join) {

                $join->on(
                    'mpm.id',
                    '=',
                    'sm.manufacturing_process'
                )

                ->on(
                    'mpm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'mpm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'mpm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_warehouse as wh',
            'wh.sno',
            '=',
            'vs.warehouse_id'
        )


        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_locationmaster as lm',
            'lm.sno',
            '=',
            'vs.location_id'
        )


        /*
        |--------------------------------------------------------------------------
        | BOX
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_boxes as bx',
            'bx.sno',
            '=',
            'vs.boxid'
        )


        /*
        |--------------------------------------------------------------------------
        | COMPANY FILTER
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
        );


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    $search =
        trim(
            $request->input(
                'search',
                ''
            )
        );


    if ($search !== '') {

        $query->where(
            function ($q) use ($search) {

                $q->where(
                    'vs.barcode',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'sm.sku',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'inm.itemname',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'itm.itemtype',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'gm.name',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'colm.colourname',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'cm.composition_details',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'wh.warehousename',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'lm.locationname',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'bx.boxno',
                    'like',
                    '%' . $search . '%'
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | WAREHOUSE FILTER
    |--------------------------------------------------------------------------
    */

    if (
        $request->filled('warehouse_id')
    ) {

        $query->where(
            'vs.warehouse_id',
            $request->warehouse_id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | LOCATION FILTER
    |--------------------------------------------------------------------------
    */

    if (
        $request->filled('location_id')
    ) {

        $query->where(
            'vs.location_id',
            $request->location_id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BOX FILTER
    |--------------------------------------------------------------------------
    */

    if (
        $request->filled('box_id')
    ) {

        $query->where(
            'vs.boxid',
            $request->box_id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | GROUP STOCK
    |--------------------------------------------------------------------------
    |
    | Same barcode + warehouse + location + box
    | will become ONE row.
    |
    | Example:
    |
    | Barcode ABC
    | 1
    | 1
    | 1
    |
    | becomes:
    |
    | ABC = 3
    |
    |--------------------------------------------------------------------------
    */

    $stocks = $query

        ->select([

            /*
            |--------------------------------------------------------------
            | STOCK
            |--------------------------------------------------------------
            */

            'vs.barcode',

            'vs.warehouse_id',

            'vs.location_id',

            'vs.boxid',


            /*
            |--------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------
            */

            'sm.sku',

            'sm.img_path',


            /*
            |--------------------------------------------------------------
            | PRODUCT MASTER NAMES
            |--------------------------------------------------------------
            */

            'inm.itemname as product_name',

            'itm.itemtype as item_type_name',

            'gm.name as gender_name',

            'cm.composition_details as composition_name',

            'colm.colourname as colour_name',

            'szm.size as size_name',

            'dm.designername as designer_name',

            'mpm.manufacturing_process as manufacturing_process_name',


            /*
            |--------------------------------------------------------------
            | WAREHOUSE / LOCATION / BOX
            |--------------------------------------------------------------
            */

            'wh.warehousename',

            'lm.locationname',

            'bx.boxno',

        ])


        /*
        |--------------------------------------------------------------------------
        | QUANTITY
        |--------------------------------------------------------------------------
        */

        ->selectRaw(
            'SUM(vs.quantity_received) AS total_received'
        )

        ->selectRaw(
            'SUM(vs.send_qty) AS total_sent'
        )

        ->selectRaw(
            'SUM(vs.quantity_received - vs.send_qty) AS total_available'
        )


        /*
        |--------------------------------------------------------------------------
        | GROUP BY
        |--------------------------------------------------------------------------
        */

        ->groupBy([

            'vs.barcode',

            'vs.warehouse_id',

            'vs.location_id',

            'vs.boxid',

            'sm.sku',

            'sm.img_path',

            'inm.itemname',

            'itm.itemtype',

            'gm.name',

            'cm.composition_details',

            'colm.colourname',

            'szm.size',

            'dm.designername',

            'mpm.manufacturing_process',

            'wh.warehousename',

            'lm.locationname',

            'bx.boxno',

        ])


        ->orderBy(
            'vs.barcode'
        )

        ->paginate(25)

        ->withQueryString();


    /*
    |--------------------------------------------------------------------------
    | PREPARE PRODUCT IMAGE URL
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| PREPARE PRODUCT IMAGE URL
|--------------------------------------------------------------------------
*/

$stocks->getCollection()->transform(
    function ($stock) {

        $stock->image_url = null;

        $rawImagePath =
            trim(
                (string) ($stock->img_path ?? '')
            );


        /*
        |--------------------------------------------------------------------------
        | NO IMAGE PATH
        |--------------------------------------------------------------------------
        */

        if ($rawImagePath === '') {
            return $stock;
        }


        /*
        |--------------------------------------------------------------------------
        | 1. HANDLE JSON ARRAY
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | ["ItemsDesigner_Masterwithbarcode/14794028211221022/image.jpg"]
        |
        */

        if (
            str_starts_with(
                $rawImagePath,
                '['
            )
        ) {

            $decoded =
                json_decode(
                    $rawImagePath,
                    true
                );


            if (
                is_array($decoded) &&
                !empty($decoded)
            ) {

                $rawImagePath =
                    trim(
                        (string) $decoded[0]
                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | 2. CLEAN PATH
        |--------------------------------------------------------------------------
        */

        $rawImagePath =
            trim(
                $rawImagePath,
                "\"' "
            );


        /*
        |--------------------------------------------------------------------------
        | WINDOWS SLASH -> NORMAL SLASH
        |--------------------------------------------------------------------------
        */

        $rawImagePath =
            str_replace(
                '\\',
                '/',
                $rawImagePath
            );


        /*
        |--------------------------------------------------------------------------
        | 3. FIND ItemsDesigner_Masterwithbarcode
        |--------------------------------------------------------------------------
        |
        | This handles:
        |
        | ../../ItemsDesigner_Masterwithbarcode/...
        |
        | ItemsDesigner_Masterwithbarcode/...
        |
        */

        $position =
            strpos(
                $rawImagePath,
                'ItemsDesigner_Masterwithbarcode/'
            );


        if (
            $position !== false
        ) {

            $rawImagePath =
                substr(
                    $rawImagePath,
                    $position
                );

        }


        /*
        |--------------------------------------------------------------------------
        | 4. REMOVE EXTRA ../ AND ./ 
        |--------------------------------------------------------------------------
        */

        $rawImagePath =
            preg_replace(
                '#^(\.\./)+#',
                '',
                $rawImagePath
            );


        $rawImagePath =
            preg_replace(
                '#^\./+?#',
                '',
                $rawImagePath
            );


        /*
        |--------------------------------------------------------------------------
        | 5. REMOVE public/
        |--------------------------------------------------------------------------
        */

        $rawImagePath =
            preg_replace(
                '#^public/#i',
                '',
                $rawImagePath
            );


        /*
        |--------------------------------------------------------------------------
        | 6. REMOVE LEADING SLASH
        |--------------------------------------------------------------------------
        */

        $rawImagePath =
            ltrim(
                $rawImagePath,
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | 7. CHECK IF IT IS AN IMAGE FILE
        |--------------------------------------------------------------------------
        */

        $extension =
            strtolower(
                pathinfo(
                    $rawImagePath,
                    PATHINFO_EXTENSION
                )
            );


        $imageExtensions = [

            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'bmp'

        ];


        /*
        |--------------------------------------------------------------------------
        | 8. IF IMAGE FILE EXISTS
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $extension,
                $imageExtensions,
                true
            )
        ) {

            $fullImagePath =
                public_path(
                    $rawImagePath
                );


            if (
                is_file(
                    $fullImagePath
                )
            ) {

                $stock->image_url =
                    asset(
                        $rawImagePath
                    );

                return $stock;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | 9. PATH IS A BARCODE FOLDER
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | ItemsDesigner_Masterwithbarcode/
        | 0091019414116011109/
        |
        */

        $folderPath =
            public_path(
                rtrim(
                    $rawImagePath,
                    '/'
                )
            );


        /*
        |--------------------------------------------------------------------------
        | 10. FIND IMAGE INSIDE BARCODE FOLDER
        |--------------------------------------------------------------------------
        */

        if (
            is_dir(
                $folderPath
            )
        ) {

            $files =
                glob(
                    $folderPath . '/*'
                );


            if (
                $files !== false
            ) {

                foreach (
                    $files as $file
                ) {

                    if (
                        !is_file($file)
                    ) {
                        continue;
                    }


                    $fileExtension =
                        strtolower(
                            pathinfo(
                                $file,
                                PATHINFO_EXTENSION
                            )
                        );


                    if (
                        !in_array(
                            $fileExtension,
                            $imageExtensions,
                            true
                        )
                    ) {

                        continue;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GET FILE NAME
                    |--------------------------------------------------------------------------
                    */

                    $fileName =
                        basename(
                            $file
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | BUILD BROWSER PATH
                    |--------------------------------------------------------------------------
                    */

                    $browserPath =
                        rtrim(
                            $rawImagePath,
                            '/'
                        )
                        . '/'
                        . $fileName;


                    /*
                    |--------------------------------------------------------------------------
                    | FINAL IMAGE URL
                    |--------------------------------------------------------------------------
                    */

                    $stock->image_url =
                        asset(
                            $browserPath
                        );


                    break;

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | RETURN STOCK
        |--------------------------------------------------------------------------
        */

        return $stock;

    }
);

    /*
    |--------------------------------------------------------------------------
    | WAREHOUSE MASTER
    |--------------------------------------------------------------------------
    */

    $warehouses =
        DB::table(
            'strs_warehouse'
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

        ->orderBy(
            'warehousename'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'inventory.view-stock',
        compact(
            'stocks',
            'warehouses',
            'search'
        )
    );
}

public function getProductStockDetails(
    Request $request,
    $barcode
) {
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }


    /*
    |--------------------------------------------------------------------------
    | USER CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    $barcode =
        trim(
            (string) $barcode
        );


    if ($barcode === '') {

        return response()->json([
            'success' => false,
            'message' => 'Barcode is required.'
        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT INFORMATION
    |--------------------------------------------------------------------------
    */

    $product = DB::table(
        'auto_designer_specification_master as sm'
    )


        /*
        |--------------------------------------------------------------------------
        | DESIGNER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as dm',
            function ($join) {

                $join->on(
                    'dm.id',
                    '=',
                    'sm.designer_name'
                )

                ->on(
                    'dm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'dm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'dm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | ITEM TYPE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemtype_master as itm',
            function ($join) {

                $join->on(
                    'itm.id',
                    '=',
                    'sm.item_type'
                )

                ->on(
                    'itm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'itm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'itm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | GENDER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_gender_master as gm',
            function ($join) {

                $join->on(
                    'gm.id',
                    '=',
                    'sm.gender'
                )

                ->on(
                    'gm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'gm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'gm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | ITEM NAME
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_itemname_master as inm',
            function ($join) {

                $join->on(
                    'inm.id',
                    '=',
                    'sm.item_name'
                )

                ->on(
                    'inm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'inm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'inm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | COMPOSITION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_composition_master_stock as cm',
            function ($join) {

                $join->on(
                    'cm.id',
                    '=',
                    'sm.composition'
                )

                ->on(
                    'cm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'cm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'cm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | COLOUR
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_colour_master as colm',
            function ($join) {

                $join->on(
                    'colm.id',
                    '=',
                    'sm.colour'
                )

                ->on(
                    'colm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'colm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'colm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | SIZE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_size_master as szm',
            function ($join) {

                $join->on(
                    'szm.id',
                    '=',
                    'sm.sizes'
                )

                ->on(
                    'szm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'szm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'szm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | MANUFACTURING
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_manufacturing_process_master as mpm',
            function ($join) {

                $join->on(
                    'mpm.id',
                    '=',
                    'sm.manufacturing_process'
                )

                ->on(
                    'mpm.companyid',
                    '=',
                    'sm.companyid'
                )

                ->on(
                    'mpm.subcompanyid',
                    '=',
                    'sm.subcompanyid'
                )

                ->on(
                    'mpm.projectid',
                    '=',
                    'sm.projectid'
                );

            }
        )


        /*
        |--------------------------------------------------------------------------
        | PRODUCT FILTER
        |--------------------------------------------------------------------------
        */

        ->where(
            'sm.barcode',
            $barcode
        )

        ->where(
            'sm.companyid',
            $companyId
        )

        ->where(
            'sm.subcompanyid',
            $subCompanyId
        )

        ->where(
            'sm.projectid',
            $projectId
        )

        ->select([

            'sm.barcode',

            'sm.sku',

            'sm.img_path',

            'inm.itemname as product_name',

            'itm.itemtype as item_type_name',

            'gm.name as gender_name',

            'cm.composition_details as composition_name',

            'colm.colourname as colour_name',

            'szm.size as size_name',

            'dm.designername as designer_name',

            'mpm.manufacturing_process as manufacturing_process_name',

        ])

        ->first();


    if (!$product) {

        return response()->json([
            'success' => false,
            'message' => 'Product not found.'
        ], 404);

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

    $product->image_url = null;

    $rawImagePath =
        trim(
            (string) ($product->img_path ?? '')
        );


    if ($rawImagePath !== '') {

        /*
        |--------------------------------------------------------------------------
        | JSON IMAGE PATH
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $rawImagePath,
                '['
            )
        ) {

            $decoded =
                json_decode(
                    $rawImagePath,
                    true
                );

            if (
                is_array($decoded) &&
                !empty($decoded)
            ) {

                $rawImagePath =
                    trim(
                        (string) $decoded[0]
                    );

            }

        }


        $rawImagePath =
            trim(
                $rawImagePath,
                "\"' "
            );


        $rawImagePath =
            str_replace(
                '\\',
                '/',
                $rawImagePath
            );


        /*
        |--------------------------------------------------------------------------
        | FIND ACTUAL IMAGE DIRECTORY
        |--------------------------------------------------------------------------
        */

        $position =
            strpos(
                $rawImagePath,
                'ItemsDesigner_Masterwithbarcode/'
            );


        if (
            $position !== false
        ) {

            $rawImagePath =
                substr(
                    $rawImagePath,
                    $position
                );

        }


        $rawImagePath =
            preg_replace(
                '#^(\.\./)+#',
                '',
                $rawImagePath
            );


        $rawImagePath =
            ltrim(
                $rawImagePath,
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | DIRECT IMAGE
        |--------------------------------------------------------------------------
        */

        $extension =
            strtolower(
                pathinfo(
                    $rawImagePath,
                    PATHINFO_EXTENSION
                )
            );


        $imageExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'bmp'
        ];


        if (
            in_array(
                $extension,
                $imageExtensions,
                true
            )
        ) {

            if (
                is_file(
                    public_path(
                        $rawImagePath
                    )
                )
            ) {

                $product->image_url =
                    asset(
                        $rawImagePath
                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | BARCODE DIRECTORY
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $product->image_url
            )
        ) {

            $folderPath =
                public_path(
                    rtrim(
                        $rawImagePath,
                        '/'
                    )
                );


            if (
                is_dir($folderPath)
            ) {

                $files =
                    glob(
                        $folderPath . '/*'
                    );


                if (
                    $files !== false
                ) {

                    foreach (
                        $files as $file
                    ) {

                        if (
                            !is_file($file)
                        ) {
                            continue;
                        }


                        $fileExtension =
                            strtolower(
                                pathinfo(
                                    $file,
                                    PATHINFO_EXTENSION
                                )
                            );


                        if (
                            !in_array(
                                $fileExtension,
                                $imageExtensions,
                                true
                            )
                        ) {
                            continue;
                        }


                        $relativeFile =
                            rtrim(
                                $rawImagePath,
                                '/'
                            )
                            . '/'
                            . basename(
                                $file
                            );


                        $product->image_url =
                            asset(
                                $relativeFile
                            );


                        break;

                    }

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | STOCK DISTRIBUTION
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Do NOT restrict this query to the clicked table row.
    |
    | We get ALL stock for this barcode.
    |--------------------------------------------------------------------------
    */

    $stockLocations = DB::table(
        'vendor_stock as vs'
    )

        ->leftJoin(
            'strs_warehouse as wh',
            'wh.sno',
            '=',
            'vs.warehouse_id'
        )

        ->leftJoin(
            'strs_locationmaster as lm',
            'lm.sno',
            '=',
            'vs.location_id'
        )

        ->leftJoin(
            'tbl_boxes as bx',
            'bx.sno',
            '=',
            'vs.boxid'
        )

        ->where(
            'vs.barcode',
            $barcode
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

        ->select([

            'vs.warehouse_id',

            'vs.location_id',

            'vs.boxid',

            'wh.warehousename',

            'lm.locationname',

            'bx.boxno',

        ])

        ->selectRaw(
            'SUM(vs.quantity_received) AS total_received'
        )

        ->selectRaw(
            'SUM(vs.send_qty) AS total_sent'
        )

        ->selectRaw(
            'SUM(vs.quantity_received - vs.send_qty) AS total_available'
        )

        ->groupBy([

            'vs.warehouse_id',

            'vs.location_id',

            'vs.boxid',

            'wh.warehousename',

            'lm.locationname',

            'bx.boxno',

        ])

        ->orderBy(
            'wh.warehousename'
        )

        ->orderBy(
            'lm.locationname'
        )

        ->orderBy(
            'bx.boxno'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | TOTAL STOCK
    |--------------------------------------------------------------------------
    */

    $totalStock =
        $stockLocations->sum(
            'total_available'
        );


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,

        'product' => $product,

        'stock' => $stockLocations,

        'total_stock' => $totalStock

    ]);
}

public function patternTestFitStock()
{
    $user = Auth::user();

    if (!$user) {
        abort(401);
    }


    /*
    |--------------------------------------------------------------------------
    | USER CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | PATTERN & TEST FIT
    |--------------------------------------------------------------------------
    |
    | Manufacturing Process:
    |
    | 1 = Hand Knit
    | 2 = Studio Sowing Machine
    |
    */

    $manufacturingProcessId = 2;


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS PER PAGE
    |--------------------------------------------------------------------------
    |
    | 12 products
    |
    | 3 products per row
    | = 4 rows
    |
    */

    $perPage = 12;


    /*
    |--------------------------------------------------------------------------
    | SPECIFICATION QUERY
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | This is a separate query only for Pattern & Test Fit.
    |
    */

    $query = DB::table(
        'auto_designer_specification_master as dsm'
    )


        /*
        |--------------------------------------------------------------------------
        | DESIGNER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as designer',
            'designer.sno',
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
            'itemtype.sno',
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
            'gender.sno',
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
            'itemname.sno',
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
            'composition.sno',
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
            'colour.sno',
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
            'size.sno',
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
            'embellishment.sno',
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
            'manufacturing.sno',
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
            'craftsman.sno',
            '=',
            'dsm.craftsman'
        )


        /*
        |--------------------------------------------------------------------------
        | MANUFACTURER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_manufacture_master as manufacture',
            'manufacture.sno',
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
            'client.sno',
            '=',
            'dsm.client'
        )


        /*
        |--------------------------------------------------------------------------
        | FILTER
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
        | ONLY MANUFACTURING PROCESS = 2
        |--------------------------------------------------------------------------
        */

        ->where(
            'dsm.manufacturing_process',
            $manufacturingProcessId
        )


        /*
        |--------------------------------------------------------------------------
        | ONLY NOT-YET-ASSIGNED PRODUCTS
        |--------------------------------------------------------------------------
        */

        ->where(function ($q) {

            $q
                ->whereNull('dsm.status')
                ->orWhere(
                    'dsm.status',
                    ''
                );

        })


        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        ->select([

            /*
            |--------------------------------------------------------------------------
            | IDs / INTERNAL VALUES
            |--------------------------------------------------------------------------
            */

            'dsm.sno',

            'dsm.id',

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
            | PRODUCT DATA
            |--------------------------------------------------------------------------
            */

            'dsm.barcode',

            'dsm.sku',

            'dsm.img_path',

            'dsm.status',

            'dsm.clientreference',

            'dsm.edatetime',


            /*
            |--------------------------------------------------------------------------
            | MASTER NAMES
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

        ])


        /*
        |--------------------------------------------------------------------------
        | NEWEST FIRST
        |--------------------------------------------------------------------------
        */

        ->orderBy(
            'dsm.sno',
            'desc'
        );


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $products =
        $query
            ->paginate($perPage)
            ->withQueryString();


    /*
    |--------------------------------------------------------------------------
    | IMAGE URL
    |--------------------------------------------------------------------------
    |
    | Handles:
    |
    | ../../ItemsDesigner_Masterwithbarcode/147922111/
    |
    | OR
    |
    | ["ItemsDesigner_Masterwithbarcode/147940.../image.jpg"]
    |
    */

    $products
        ->getCollection()
        ->transform(function ($item) {

            $item->image_url = null;


            if (
                empty(
                    $item->img_path
                )
            ) {

                return $item;

            }


            /*
            |--------------------------------------------------------------------------
            | NORMALIZE PATH
            |--------------------------------------------------------------------------
            */

            $imgPath =
                str_replace(
                    '\\',
                    '/',
                    trim(
                        $item->img_path
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | JSON ARRAY
            |--------------------------------------------------------------------------
            */

            if (
                str_starts_with(
                    $imgPath,
                    '['
                )
            ) {

                $decoded =
                    json_decode(
                        $imgPath,
                        true
                    );


                if (
                    is_array($decoded) &&
                    !empty($decoded)
                ) {

                    $imgPath =
                        trim(
                            (string)
                            $decoded[0]
                        );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | FIND ITEMS DESIGNER FOLDER
            |--------------------------------------------------------------------------
            */

            $marker =
                'ItemsDesigner_Masterwithbarcode/';


            $position =
                strpos(
                    $imgPath,
                    $marker
                );


            if (
                $position === false
            ) {

                return $item;

            }


            /*
            |--------------------------------------------------------------------------
            | GET BARCODE FOLDER
            |--------------------------------------------------------------------------
            */

            $barcodeFolder =
                substr(
                    $imgPath,
                    $position +
                    strlen($marker)
                );


            $barcodeFolder =
                trim(
                    $barcodeFolder,
                    '/'
                );


            if (
                $barcodeFolder === ''
            ) {

                return $item;

            }


            /*
            |--------------------------------------------------------------------------
            | PHYSICAL FOLDER
            |--------------------------------------------------------------------------
            */

            $folderPath =
                public_path(
                    'ItemsDesigner_Masterwithbarcode/' .
                    $barcodeFolder
                );


            if (
                !is_dir(
                    $folderPath
                )
            ) {

                return $item;

            }


            /*
            |--------------------------------------------------------------------------
            | GET FIRST IMAGE
            |--------------------------------------------------------------------------
            */

            $files =
                scandir(
                    $folderPath
                );


            if (
                $files === false
            ) {

                return $item;

            }


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
                    !in_array(
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

                    continue;

                }


                /*
                |--------------------------------------------------------------------------
                | BROWSER URL
                |--------------------------------------------------------------------------
                */

                $item->image_url =
                    asset(
                        'ItemsDesigner_Masterwithbarcode/' .
                        $barcodeFolder .
                        '/' .
                        $file
                    );


                break;

            }


            return $item;

        });


    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'inventory.pattern-test-fit-stock',
        [
            'products' =>
                $products,

            'manufacturingProcessId' =>
                $manufacturingProcessId
        ]
    );
}

public function savePatternTestFitStock(Request $request)
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
    | VALIDATION
    |--------------------------------------------------------------------------
    |
    | Digital files are OPTIONAL.
    |
    */

    $request->validate([

        'type' =>
            'required|in:pattern,testfit',

        'product_id' =>
            'required|integer',

        'product_barcode' =>
            'required|string',

        'image_count' =>
            'required|integer|min:1|max:50',

        'images' =>
            'required|array|min:1',

        'images.*' =>
            'required|image|mimes:jpg,jpeg,png,webp|max:10240',

        'warehouse_id' =>
            'required|integer',

        'location_id' =>
            'required|integer',

        'box_id' =>
            'required|integer',

        'remarks' =>
            'nullable|string',

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL DIGITAL FILES
        |--------------------------------------------------------------------------
        */

        'digital_files' =>
            'nullable|array',

        'digital_files.*' =>
            'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,zip,rar,txt|max:51200',

    ]);


    /*
    |--------------------------------------------------------------------------
    | USER COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId =
        (int) ($user->company_id ?? 0);

    $subCompanyId =
        (int) ($user->sub_company_id ?? 0);

    $projectId =
        (int) ($user->project_id ?? 0);


    /*
    |--------------------------------------------------------------------------
    | IMAGE COUNT
    |--------------------------------------------------------------------------
    */

    $imageCount =
        (int) $request->image_count;


    $images =
        $request->file(
            'images',
            []
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK EXACT IMAGE COUNT
    |--------------------------------------------------------------------------
    */

    if (
        count($images) !==
        $imageCount
    ) {

        return response()->json([

            'success' =>
                false,

            'message' =>
                "Please attach exactly {$imageCount} images."

        ], 422);

    }


    /*
    |--------------------------------------------------------------------------
    | TYPE
    |--------------------------------------------------------------------------
    */

    $type =
        $request->input('type');


    /*
    |--------------------------------------------------------------------------
    | SELECT TABLE
    |--------------------------------------------------------------------------
    */

    $table =
        $type === 'pattern'
            ? 'vendor_patternstock'
            : 'vendor_testfitstock';


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */

    try {

        $result =
            DB::transaction(
                function () use (

                    $request,

                    $user,

                    $companyId,

                    $subCompanyId,

                    $projectId,

                    $type,

                    $table,

                    $images,

                    $imageCount

                ) {


                    /*
                    |--------------------------------------------------------------------------
                    | GET NEXT STOCK ID
                    |--------------------------------------------------------------------------
                    |
                    | We use the selected stock table's ID.
                    |
                    */

                    $maxId =
                        DB::table($table)
                            ->lockForUpdate()
                            ->max('id');


                    $stockId =
                        $maxId
                            ? ((int) $maxId + 1)
                            : 1;


                    /*
                    |--------------------------------------------------------------------------
                    | MAKE SURE ID IS UNIQUE
                    |--------------------------------------------------------------------------
                    */

                    while (
                        DB::table($table)
                            ->where(
                                'id',
                                $stockId
                            )
                            ->exists()
                    ) {

                        $stockId++;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ORIGINAL PRODUCT BARCODE
                    |--------------------------------------------------------------------------
                    */

                    $productBarcode =
                        trim(
                            (string)
                            $request->product_barcode
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | MAIN BARCODE
                    |--------------------------------------------------------------------------
                    |
                    | Pattern:
                    |
                    | PAT-productbarcode-stockid
                    |
                    | Test Fit:
                    |
                    | FIT-productbarcode-stockid
                    |
                    */

                    $prefix =
                        $type === 'pattern'
                            ? 'PAT'
                            : 'FIT';


                    $mainBarcode =
                        $prefix .
                        '-' .
                        $productBarcode .
                        '-' .
                        $stockId;


                    /*
                    |--------------------------------------------------------------------------
                    | BASE PUBLIC FOLDER
                    |--------------------------------------------------------------------------
                    */

                    $baseFolder =
                        $type === 'pattern'
                            ? 'PatternStock'
                            : 'TestFitStock';


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK FOLDER
                    |--------------------------------------------------------------------------
                    */

                    $stockFolder =
                        $baseFolder .
                        '/' .
                        $mainBarcode;


                    /*
                    |--------------------------------------------------------------------------
                    | ARRAYS
                    |--------------------------------------------------------------------------
                    */

                    $imagePaths = [];

                    $barcodePictures = [];

                    $digitalFilePaths = [];


                    /*
                    |--------------------------------------------------------------------------
                    | DIGITAL FILES
                    |--------------------------------------------------------------------------
                    |
                    | Optional.
                    |
                    */

                    $digitalFiles =
                        $request->file(
                            'digital_files',
                            []
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | INSERT STOCK RECORD
                    |--------------------------------------------------------------------------
                    */

                    DB::table($table)->insert([

                        'id' =>
                            $stockId,

                        'companyid' =>
                            $companyId,

                        'subcompanyid' =>
                            $subCompanyId,

                        'projectid' =>
                            $projectId,


                        /*
                        |--------------------------------------------------------------------------
                        | COMPANY IS VENDOR
                        |--------------------------------------------------------------------------
                        */

                        'vendor_id' =>
                            $companyId,


                        /*
                        |--------------------------------------------------------------------------
                        | PRODUCT
                        |--------------------------------------------------------------------------
                        */

                        'item_id' =>
                            (int)
                            $request->product_id,


                        /*
                        |--------------------------------------------------------------------------
                        | STOCK DATE
                        |--------------------------------------------------------------------------
                        */

                        'stock_date' =>
                            now()->toDateString(),


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE COUNT
                        |--------------------------------------------------------------------------
                        */

                        'qty_img' =>
                            $imageCount,


                        /*
                        |--------------------------------------------------------------------------
                        | REMARKS
                        |--------------------------------------------------------------------------
                        */

                        'remarks' =>
                            $request->remarks,


                        /*
                        |--------------------------------------------------------------------------
                        | BOX
                        |--------------------------------------------------------------------------
                        */

                        'boxid' =>
                            (int)
                            $request->box_id,


                        /*
                        |--------------------------------------------------------------------------
                        | MAIN BARCODE
                        |--------------------------------------------------------------------------
                        */

                        'barcode' =>
                            $mainBarcode,


                        /*
                        |--------------------------------------------------------------------------
                        | ORIGINAL DESIGN BARCODE
                        |--------------------------------------------------------------------------
                        */

                        'barcodeofdesign' =>
                            $productBarcode,


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE PATH
                        |--------------------------------------------------------------------------
                        */

                        'img_path' =>
                            null,


                        /*
                        |--------------------------------------------------------------------------
                        | LOCATION
                        |--------------------------------------------------------------------------
                        */

                        'location_id' =>
                            (int)
                            $request->location_id,


                        /*
                        |--------------------------------------------------------------------------
                        | EDIT
                        |--------------------------------------------------------------------------
                        */

                        'tedit' =>
                            null,


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE BARCODE JSON
                        |--------------------------------------------------------------------------
                        */

                        'barcode_pics' =>
                            null,


                        /*
                        |--------------------------------------------------------------------------
                        | DIGITAL FILE STATUS
                        |--------------------------------------------------------------------------
                        */

                        'digital_files_status' =>
                            null,


                        /*
                        |--------------------------------------------------------------------------
                        | DIGITAL FILE PATH
                        |--------------------------------------------------------------------------
                        */

                        'digital_files_path' =>
                            null,


                        /*
                        |--------------------------------------------------------------------------
                        | TIMESTAMPS
                        |--------------------------------------------------------------------------
                        */

                        'created_at' =>
                            now(),

                        'updated_at' =>
                            now(),

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | SAVE PRODUCT IMAGES
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $images as $index => $image
                    ) {


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE NUMBER
                        |--------------------------------------------------------------------------
                        */

                        $imageNumber =
                            $index + 1;


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE BARCODE
                        |--------------------------------------------------------------------------
                        |
                        | Example:
                        |
                        | PAT-14794028211221022-15-01
                        | PAT-14794028211221022-15-02
                        |
                        */

                        $imageBarcode =
                            $mainBarcode .
                            '-' .
                            str_pad(
                                $imageNumber,
                                2,
                                '0',
                                STR_PAD_LEFT
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE EXTENSION
                        |--------------------------------------------------------------------------
                        */

                        $extension =
                            strtolower(
                                $image->getClientOriginalExtension()
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | IMAGE FILE NAME
                        |--------------------------------------------------------------------------
                        */

                        $fileName =
                            'image.' .
                            $extension;


                        /*
                        |--------------------------------------------------------------------------
                        | PHYSICAL IMAGE FOLDER
                        |--------------------------------------------------------------------------
                        */

                        $physicalFolder =
                            public_path(
                                $baseFolder .
                                '/' .
                                $mainBarcode .
                                '/' .
                                $imageBarcode
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CREATE DIRECTORY
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !is_dir(
                                $physicalFolder
                            )
                        ) {

                            mkdir(
                                $physicalFolder,
                                0775,
                                true
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | MOVE IMAGE
                        |--------------------------------------------------------------------------
                        */

                        $image->move(
                            $physicalFolder,
                            $fileName
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | DATABASE IMAGE PATH
                        |--------------------------------------------------------------------------
                        */

                        $imagePath =
                            $baseFolder .
                            '/' .
                            $mainBarcode .
                            '/' .
                            $imageBarcode .
                            '/' .
                            $fileName;


                        /*
                        |--------------------------------------------------------------------------
                        | ADD IMAGE PATH
                        |--------------------------------------------------------------------------
                        */

                        $imagePaths[] =
                            $imagePath;


                        /*
                        |--------------------------------------------------------------------------
                        | ADD BARCODE INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        $barcodePictures[] = [

                            'sr' =>
                                $imageNumber,

                            'total' =>
                                $imageCount,

                            'barcode' =>
                                $imageBarcode,

                            'image' =>
                                $imagePath,

                        ];

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DIGITAL FILES
                    |--------------------------------------------------------------------------
                    |
                    | OPTIONAL
                    |
                    */

                    if (
                        !empty(
                            $digitalFiles
                        )
                    ) {


                        /*
                        |--------------------------------------------------------------------------
                        | DIGITAL FILE PHYSICAL FOLDER
                        |--------------------------------------------------------------------------
                        */

                        $digitalPhysicalFolder =
                            public_path(
                                $baseFolder .
                                '/' .
                                $mainBarcode .
                                '/digital'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CREATE DIGITAL DIRECTORY
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !is_dir(
                                $digitalPhysicalFolder
                            )
                        ) {

                            mkdir(
                                $digitalPhysicalFolder,
                                0775,
                                true
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SAVE EACH DIGITAL FILE
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $digitalFiles as $digitalFile
                        ) {


                            /*
                            |--------------------------------------------------------------------------
                            | CHECK FILE
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !$digitalFile ||
                                !$digitalFile->isValid()
                            ) {

                                continue;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | ORIGINAL FILE NAME
                            |--------------------------------------------------------------------------
                            */

                            $originalName =
                                $digitalFile
                                    ->getClientOriginalName();


                            /*
                            |--------------------------------------------------------------------------
                            | EXTENSION
                            |--------------------------------------------------------------------------
                            */

                            $extension =
                                strtolower(
                                    $digitalFile
                                        ->getClientOriginalExtension()
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | SAFE FILE NAME
                            |--------------------------------------------------------------------------
                            */

                            $safeName =
                                pathinfo(
                                    $originalName,
                                    PATHINFO_FILENAME
                                );


                            $safeName =
                                preg_replace(
                                    '/[^A-Za-z0-9_-]/',
                                    '_',
                                    $safeName
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | PREVENT EMPTY NAME
                            |--------------------------------------------------------------------------
                            */

                            if (
                                empty($safeName)
                            ) {

                                $safeName =
                                    'digital_file';

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | UNIQUE FILE NAME
                            |--------------------------------------------------------------------------
                            */

                            $fileName =
                                $safeName .
                                '_' .
                                uniqid() .
                                '.' .
                                $extension;


                            /*
                            |--------------------------------------------------------------------------
                            | MOVE DIGITAL FILE
                            |--------------------------------------------------------------------------
                            */

                            $digitalFile->move(
                                $digitalPhysicalFolder,
                                $fileName
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | DATABASE DIGITAL PATH
                            |--------------------------------------------------------------------------
                            */

                            $digitalPath =
                                $baseFolder .
                                '/' .
                                $mainBarcode .
                                '/digital/' .
                                $fileName;


                            /*
                            |--------------------------------------------------------------------------
                            | ADD DIGITAL FILE PATH
                            |--------------------------------------------------------------------------
                            */

                            $digitalFilePaths[] =
                                $digitalPath;

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DIGITAL FILE STATUS
                    |--------------------------------------------------------------------------
                    */

                    $digitalFilesStatus =
                        !empty(
                            $digitalFilePaths
                        )
                            ? 'done'
                            : null;


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE STOCK RECORD
                    |--------------------------------------------------------------------------
                    */

                    DB::table($table)
                        ->where(
                            'id',
                            $stockId
                        )
                        ->update([


                            /*
                            |--------------------------------------------------------------------------
                            | IMAGE PATHS
                            |--------------------------------------------------------------------------
                            */

                            'img_path' =>
                                json_encode(
                                    $imagePaths,
                                    JSON_UNESCAPED_SLASHES
                                ),


                            /*
                            |--------------------------------------------------------------------------
                            | IMAGE BARCODE DATA
                            |--------------------------------------------------------------------------
                            */

                            'barcode_pics' =>
                                json_encode(
                                    $barcodePictures,
                                    JSON_UNESCAPED_SLASHES
                                ),


                            /*
                            |--------------------------------------------------------------------------
                            | DIGITAL FILE STATUS
                            |--------------------------------------------------------------------------
                            */

                            'digital_files_status' =>
                                $digitalFilesStatus,


                            /*
                            |--------------------------------------------------------------------------
                            | DIGITAL FILE PATHS
                            |--------------------------------------------------------------------------
                            */

                            'digital_files_path' =>
                                !empty(
                                    $digitalFilePaths
                                )
                                    ? json_encode(
                                        $digitalFilePaths,
                                        JSON_UNESCAPED_SLASHES
                                    )
                                    : null,


                            /*
                            |--------------------------------------------------------------------------
                            | UPDATED
                            |--------------------------------------------------------------------------
                            */

                            'updated_at' =>
                                now(),

                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | RETURN RESULT
                    |--------------------------------------------------------------------------
                    */

                    return [

                        'id' =>
                            $stockId,

                        'main_barcode' =>
                            $mainBarcode,

                        'product_barcode' =>
                            $productBarcode,

                        'image_count' =>
                            $imageCount,

                        'images' =>
                            $barcodePictures,

                        'digital_files_status' =>
                            $digitalFilesStatus,

                        'digital_files' =>
                            $digitalFilePaths,

                        'warehouse_id' =>
                            (int)
                            $request->warehouse_id,

                        'location_id' =>
                            (int)
                            $request->location_id,

                        'box_id' =>
                            (int)
                            $request->box_id,

                    ];

                }
            );


        /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' =>
                true,

            'message' =>
                ucfirst($type) .
                ' saved successfully.',

            'data' =>
                $result,

        ]);


    } catch (\Throwable $e) {


        /*
        |--------------------------------------------------------------------------
        | LOG ERROR
        |--------------------------------------------------------------------------
        */

        Log::error(
            'Pattern/TestFit Stock Save Error',
            [

                'message' =>
                    $e->getMessage(),

                'user_id' =>
                    $user->id ?? null,

                'company_id' =>
                    $companyId,

                'subcompany_id' =>
                    $subCompanyId,

                'project_id' =>
                    $projectId,

                'type' =>
                    $type,

                'trace' =>
                    $e->getTraceAsString(),

            ]
        );


        /*
        |--------------------------------------------------------------------------
        | ERROR RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' =>
                false,

            'message' =>
                'Unable to save Pattern/Test Fit stock.',

            'error' =>
                $e->getMessage(),

        ], 500);

    }
}


public function getPatternTestFitAssignments(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    $companyId = (int) ($user->company_id ?? 0);
    $subCompanyId = (int) ($user->sub_company_id ?? 0);
    $projectId = (int) ($user->project_id ?? 0);

    $itemId = (int) $request->input('item_id');

    if ($itemId <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid product ID.'
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | PATTERN STOCK
    |--------------------------------------------------------------------------
    */

    $patternStock = DB::table('vendor_patternstock as ps')

        ->leftJoin(
            'strs_locationmaster as l',
            'l.sno',
            '=',
            'ps.location_id'
        )

        ->leftJoin(
            'strs_warehouse as w',
            'w.sno',
            '=',
            'l.warehouse_id'
        )

        ->leftJoin(
            'tbl_boxes as b',
            'b.sno',
            '=',
            'ps.boxid'
        )

        ->where(
            'ps.companyid',
            $companyId
        )

        ->where(
            'ps.subcompanyid',
            $subCompanyId
        )

        ->where(
            'ps.projectid',
            $projectId
        )

        ->where(
            'ps.item_id',
            $itemId
        )

        ->select([

            'ps.sno',
            'ps.id',
            'ps.item_id',
            'ps.qty_img',
            'ps.barcode',
            'ps.barcodeofdesign',
            'ps.img_path',
            'ps.barcode_pics',
            'ps.boxid',
            'ps.location_id',

            DB::raw(
                "COALESCE(w.warehousename, 'N/A') AS warehouse_name"
            ),

            DB::raw(
                "COALESCE(l.locationname, 'N/A') AS location_name"
            ),

            DB::raw(
                "COALESCE(b.boxno, 'N/A') AS box_no"
            )

        ])

        ->orderBy(
            'ps.sno',
            'desc'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | TEST FIT STOCK
    |--------------------------------------------------------------------------
    */

    $testFitStock = DB::table('vendor_testfitstock as ts')

        ->leftJoin(
            'strs_locationmaster as l',
            'l.sno',
            '=',
            'ts.location_id'
        )

        ->leftJoin(
            'strs_warehouse as w',
            'w.sno',
            '=',
            'l.warehouse_id'
        )

        ->leftJoin(
            'tbl_boxes as b',
            'b.sno',
            '=',
            'ts.boxid'
        )

        ->where(
            'ts.companyid',
            $companyId
        )

        ->where(
            'ts.subcompanyid',
            $subCompanyId
        )

        ->where(
            'ts.projectid',
            $projectId
        )

        ->where(
            'ts.item_id',
            $itemId
        )

        ->select([

            'ts.sno',
            'ts.id',
            'ts.item_id',
            'ts.qty_img',
            'ts.barcode',
            'ts.barcodeofdesign',
            'ts.img_path',
            'ts.barcode_pics',
            'ts.boxid',
            'ts.location_id',

            DB::raw(
                "COALESCE(w.warehousename, 'N/A') AS warehouse_name"
            ),

            DB::raw(
                "COALESCE(l.locationname, 'N/A') AS location_name"
            ),

            DB::raw(
                "COALESCE(b.boxno, 'N/A') AS box_no"
            )

        ])

        ->orderBy(
            'ts.sno',
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

        'pattern' => $patternStock,

        'testfit' => $testFitStock,

        'pattern_count' =>
            $patternStock->count(),

        'testfit_count' =>
            $testFitStock->count()

    ]);
}

public function viewPatternTestFitStock()
{
    return view(
        'inventory.view-pattern-test-fit-stock'
    );
}

public function getPatternTestFitStock(Request $request)
{
    try {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | USER CONTEXT
        |--------------------------------------------------------------------------
        */

        $companyId =
            (int) ($user->company_id ?? 0);

        $subCompanyId =
            (int) ($user->sub_company_id ?? 0);

        $projectId =
            (int) ($user->project_id ?? 0);

        $type =
            strtolower(
                trim(
                    $request->input(
                        'type',
                        'all'
                    )
                )
            );

        $search =
            trim(
                $request->input(
                    'search',
                    ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | PATTERN STOCK
        |--------------------------------------------------------------------------
        */

        $patternQuery = DB::table(
            'vendor_patternstock as ps'
        )

        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_locationmaster as l',
            'l.sno',
            '=',
            'ps.location_id'
        )

        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_warehouse as w',
            'w.sno',
            '=',
            'l.warehouse_id'
        )

        /*
        |--------------------------------------------------------------------------
        | BOX
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_boxes as b',
            'b.sno',
            '=',
            'ps.boxid'
        )

        /*
        |--------------------------------------------------------------------------
        | DESIGN SPECIFICATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_specification_master as dsm',
            'dsm.barcode',
            '=',
            'ps.barcodeofdesign'
        )

        /*
        |--------------------------------------------------------------------------
        | DESIGNER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as designer',
            'designer.sno',
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
            'itemtype.sno',
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
            'gender.sno',
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
            'itemname.sno',
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
            'composition.sno',
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
            'colour.sno',
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
            'size.sno',
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
            'embellishment.sno',
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
            'manufacturing.sno',
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
            'craftsman.sno',
            '=',
            'dsm.craftsman'
        )

        /*
        |--------------------------------------------------------------------------
        | MANUFACTURER
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_manufacture_master as manufacture',
            'manufacture.sno',
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
            'client.sno',
            '=',
            'dsm.client'
        )

        /*
        |--------------------------------------------------------------------------
        | COMPANY FILTER
        |--------------------------------------------------------------------------
        */

        ->where(
            'ps.companyid',
            $companyId
        )

        ->where(
            'ps.subcompanyid',
            $subCompanyId
        )

        ->where(
            'ps.projectid',
            $projectId
        );


        /*
        |--------------------------------------------------------------------------
        | TEST FIT STOCK
        |--------------------------------------------------------------------------
        */

        $testFitQuery = DB::table(
            'vendor_testfitstock as ts'
        )

        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_locationmaster as l',
            'l.sno',
            '=',
            'ts.location_id'
        )

        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'strs_warehouse as w',
            'w.sno',
            '=',
            'l.warehouse_id'
        )

        /*
        |--------------------------------------------------------------------------
        | BOX
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'tbl_boxes as b',
            'b.sno',
            '=',
            'ts.boxid'
        )

        /*
        |--------------------------------------------------------------------------
        | DESIGN SPECIFICATION
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_specification_master as dsm',
            'dsm.barcode',
            '=',
            'ts.barcodeofdesign'
        )

        /*
        |--------------------------------------------------------------------------
        | SAME MASTERS
        |--------------------------------------------------------------------------
        */

        ->leftJoin(
            'auto_designer_master as designer',
            'designer.sno',
            '=',
            'dsm.designer_name'
        )

        ->leftJoin(
            'auto_itemtype_master as itemtype',
            'itemtype.sno',
            '=',
            'dsm.item_type'
        )

        ->leftJoin(
            'auto_gender_master as gender',
            'gender.sno',
            '=',
            'dsm.gender'
        )

        ->leftJoin(
            'auto_itemname_master as itemname',
            'itemname.sno',
            '=',
            'dsm.item_name'
        )

        ->leftJoin(
            'auto_composition_master_stock as composition',
            'composition.sno',
            '=',
            'dsm.composition'
        )

        ->leftJoin(
            'auto_colour_master as colour',
            'colour.sno',
            '=',
            'dsm.colour'
        )

        ->leftJoin(
            'auto_size_master as size',
            'size.sno',
            '=',
            'dsm.sizes'
        )

        ->leftJoin(
            'auto_embellishment_master as embellishment',
            'embellishment.sno',
            '=',
            'dsm.embellishment'
        )

        ->leftJoin(
            'auto_manufacturing_process_master as manufacturing',
            'manufacturing.sno',
            '=',
            'dsm.manufacturing_process'
        )

        ->leftJoin(
            'auto_craftsman_master as craftsman',
            'craftsman.sno',
            '=',
            'dsm.craftsman'
        )

        ->leftJoin(
            'auto_manufacture_master as manufacture',
            'manufacture.sno',
            '=',
            'dsm.manufecture'
        )

        ->leftJoin(
            'auto_client_master as client',
            'client.sno',
            '=',
            'dsm.client'
        )

        /*
        |--------------------------------------------------------------------------
        | COMPANY FILTER
        |--------------------------------------------------------------------------
        */

        ->where(
            'ts.companyid',
            $companyId
        )

        ->where(
            'ts.subcompanyid',
            $subCompanyId
        )

        ->where(
            'ts.projectid',
            $projectId
        );


        /*
        |--------------------------------------------------------------------------
        | SEARCH PATTERN
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $patternQuery->where(
                function ($q) use ($search) {

                    $like =
                        '%' . $search . '%';

                    $q->where(
                        'ps.barcode',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'ps.barcodeofdesign',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'dsm.sku',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'itemname.itemname',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'designer.designername',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'gender.name',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'w.warehousename',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'l.locationname',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'b.boxno',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'b.box_title',
                        'like',
                        $like
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH TEST FIT
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $testFitQuery->where(
                function ($q) use ($search) {

                    $like =
                        '%' . $search . '%';

                    $q->where(
                        'ts.barcode',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'ts.barcodeofdesign',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'dsm.sku',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'itemname.itemname',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'designer.designername',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'gender.name',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'w.warehousename',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'l.locationname',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'b.boxno',
                        'like',
                        $like
                    )

                    ->orWhere(
                        'b.box_title',
                        'like',
                        $like
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PATTERN SELECT
        |--------------------------------------------------------------------------
        */

        $patternQuery->select([

            'ps.sno',
            'ps.id',

            DB::raw(
                "'Pattern' as stock_type"
            ),

            'ps.companyid',
            'ps.subcompanyid',
            'ps.projectid',

            'ps.vendor_id',
            'ps.item_id',

            'ps.stock_date',
            'ps.qty_img',
            'ps.remarks',

            'ps.boxid',
            'ps.location_id',

            'ps.barcode',
            'ps.barcodeofdesign',

            'ps.img_path',
            'ps.barcode_pics',

            'ps.tedit',

            'ps.created_at',
            'ps.updated_at',

            /*
            |--------------------------------------------------------------------------
            | DESIGN
            |--------------------------------------------------------------------------
            */

            'dsm.sno as design_sno',
            'dsm.id as design_id',
            'dsm.barcode as design_barcode',
            'dsm.sku',

            /*
            |--------------------------------------------------------------------------
            | MASTER DISPLAY VALUES
            |--------------------------------------------------------------------------
            */

            'designer.designername as designer_name',

            'itemtype.itemtype as item_type_name',

            'gender.name as gender_name',

            'itemname.itemname as item_name',

            'composition.composition_details as composition_name',

            'colour.colourname as colour_name',

            'size.size as size_name',

            'embellishment.embellishmentname as embellishment_name',

            'manufacturing.manufacturing_process as manufacturing_process_name',

            'craftsman.name as craftsman_name',

            'manufacture.name as manufacturer_name',

            'client.name as client_name',

            /*
            |--------------------------------------------------------------------------
            | IDS
            |--------------------------------------------------------------------------
            */

            'dsm.designer_name as designer_id',
            'dsm.item_type as item_type_id',
            'dsm.gender as gender_id',
            'dsm.item_name as item_name_id',
            'dsm.composition as composition_id',
            'dsm.colour as colour_id',
            'dsm.sizes as size_id',
            'dsm.embellishment as embellishment_id',
            'dsm.manufacturing_process as manufacturing_process_id',
            'dsm.craftsman as craftsman_id',
            'dsm.manufecture as manufacturer_id',
            'dsm.client as client_id',

            /*
            |--------------------------------------------------------------------------
            | WAREHOUSE
            |--------------------------------------------------------------------------
            */

            'w.sno as warehouse_id',
            'w.warehousename as warehouse_name',

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            'l.sno as location_master_id',
            'l.locationname as location_name',
            'l.warehousesection',
            'l.floornumber',
            'l.stackno',
            'l.racknumber',

            /*
            |--------------------------------------------------------------------------
            | BOX
            |--------------------------------------------------------------------------
            */

            'b.sno as box_id',
            'b.box_title',
            'b.boxno',
            'b.warehouseid as box_warehouse_id',
            'b.location as box_location_id',
            'b.status as box_status'

        ]);


        /*
        |--------------------------------------------------------------------------
        | TEST FIT SELECT
        |--------------------------------------------------------------------------
        */

        $testFitQuery->select([

            'ts.sno',
            'ts.id',

            DB::raw(
                "'Test Fit' as stock_type"
            ),

            'ts.companyid',
            'ts.subcompanyid',
            'ts.projectid',

            'ts.vendor_id',
            'ts.item_id',

            'ts.stock_date',
            'ts.qty_img',
            'ts.remarks',

            'ts.boxid',
            'ts.location_id',

            'ts.barcode',
            'ts.barcodeofdesign',

            'ts.img_path',
            'ts.barcode_pics',

            'ts.tedit',

            'ts.created_at',
            'ts.updated_at',

            /*
            |--------------------------------------------------------------------------
            | DESIGN
            |--------------------------------------------------------------------------
            */

            'dsm.sno as design_sno',
            'dsm.id as design_id',
            'dsm.barcode as design_barcode',
            'dsm.sku',

            /*
            |--------------------------------------------------------------------------
            | MASTER DISPLAY VALUES
            |--------------------------------------------------------------------------
            */

            'designer.designername as designer_name',

            'itemtype.itemtype as item_type_name',

            'gender.name as gender_name',

            'itemname.itemname as item_name',

            'composition.composition_details as composition_name',

            'colour.colourname as colour_name',

            'size.size as size_name',

            'embellishment.embellishmentname as embellishment_name',

            'manufacturing.manufacturing_process as manufacturing_process_name',

            'craftsman.name as craftsman_name',

            'manufacture.name as manufacturer_name',

            'client.name as client_name',

            /*
            |--------------------------------------------------------------------------
            | IDS
            |--------------------------------------------------------------------------
            */

            'dsm.designer_name as designer_id',
            'dsm.item_type as item_type_id',
            'dsm.gender as gender_id',
            'dsm.item_name as item_name_id',
            'dsm.composition as composition_id',
            'dsm.colour as colour_id',
            'dsm.sizes as size_id',
            'dsm.embellishment as embellishment_id',
            'dsm.manufacturing_process as manufacturing_process_id',
            'dsm.craftsman as craftsman_id',
            'dsm.manufecture as manufacturer_id',
            'dsm.client as client_id',

            /*
            |--------------------------------------------------------------------------
            | WAREHOUSE
            |--------------------------------------------------------------------------
            */

            'w.sno as warehouse_id',
            'w.warehousename as warehouse_name',

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            'l.sno as location_master_id',
            'l.locationname as location_name',
            'l.warehousesection',
            'l.floornumber',
            'l.stackno',
            'l.racknumber',

            /*
            |--------------------------------------------------------------------------
            | BOX
            |--------------------------------------------------------------------------
            */

            'b.sno as box_id',
            'b.box_title',
            'b.boxno',
            'b.warehouseid as box_warehouse_id',
            'b.location as box_location_id',
            'b.status as box_status'

        ]);


        /*
        |--------------------------------------------------------------------------
        | GET DATA
        |--------------------------------------------------------------------------
        */

        $patternStock =
            collect();

        $testFitStock =
            collect();


        /*
        |--------------------------------------------------------------------------
        | PATTERN
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'pattern'
        ) {

            $patternStock =
                $patternQuery
                    ->orderByDesc(
                        'ps.sno'
                    )
                    ->get();

        }


        /*
        |--------------------------------------------------------------------------
        | TEST FIT
        |--------------------------------------------------------------------------
        */

        elseif (
            $type === 'testfit' ||
            $type === 'test_fit'
        ) {

            $testFitStock =
                $testFitQuery
                    ->orderByDesc(
                        'ts.sno'
                    )
                    ->get();

        }


        /*
        |--------------------------------------------------------------------------
        | ALL
        |--------------------------------------------------------------------------
        */

        else {

            $patternStock =
                $patternQuery
                    ->orderByDesc(
                        'ps.sno'
                    )
                    ->get();

            $testFitStock =
                $testFitQuery
                    ->orderByDesc(
                        'ts.sno'
                    )
                    ->get();

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'pattern' =>
                $patternStock,

            'testfit' =>
                $testFitStock,

            'pattern_count' =>
                $patternStock->count(),

            'testfit_count' =>
                $testFitStock->count(),

            'total' =>
                $patternStock->count()
                +
                $testFitStock->count()

        ]);

    } catch (\Throwable $e) {

        Log::error(
            'Pattern/Test Fit stock loading error',
            [
                'message' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine(),

                'trace' =>
                    $e->getTraceAsString()
            ]
        );

        return response()->json([

            'success' => false,

            'message' =>
                $e->getMessage(),

            'file' =>
                $e->getFile(),

            'line' =>
                $e->getLine()

        ], 500);
    }
}


}