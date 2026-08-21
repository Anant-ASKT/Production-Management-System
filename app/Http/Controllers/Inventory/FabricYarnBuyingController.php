<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Validator;

class FabricYarnBuyingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW PAGE
    |--------------------------------------------------------------------------
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
        | MARKET MASTER
        |--------------------------------------------------------------------------
        */

        $markets = DB::table('tbl_market')
            ->orderBy('market_name')
            ->get([
                'sno',
                'id',
                'market_name'
            ]);

        return view(
            'inventory.fabric-yarn-buying',
            compact(
                'companyId',
                'subCompanyId',
                'projectId',
                'markets'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET SHOPS BY MARKET
    |--------------------------------------------------------------------------
    */

    public function getShops(Request $request)
    {
        $marketId = $request->input('market_id');

        if (!$marketId) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $shops = DB::table('tbl_shop_masterfabric')
            ->where('market_id', $marketId)
            ->orderBy('shop_name')
            ->get([
                'sno',
                'id',
                'market_id',
                'shop_name',
                'mobileno',
                'contact_name',
                'emailid',
                'address',
                'place'
            ]);

        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET SHOP DETAILS
    |--------------------------------------------------------------------------
    */

    public function getShopDetails(Request $request)
    {
        $shopId = $request->input('shop_id');

        if (!$shopId) {
            return response()->json([
                'success' => false,
                'message' => 'Shop ID is required.'
            ], 422);
        }

        $shop = DB::table('tbl_shop_masterfabric')
            ->where('id', $shopId)
            ->first([
                'sno',
                'id',
                'market_id',
                'shop_name',
                'mobileno',
                'contact_name',
                'emailid',
                'address',
                'place'
            ]);

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET MARKETS
    |--------------------------------------------------------------------------
    */

    public function getMarkets()
    {
        $markets = DB::table('tbl_market')
            ->orderBy('market_name')
            ->get([
                'sno',
                'id',
                'market_name'
            ]);

        return response()->json([
            'success' => true,
            'data' => $markets
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE PKU
    |--------------------------------------------------------------------------
    */

    public function generatePku(Request $request)
    {
        $type = $request->input(
            'type',
            'Fabric'
        );

        $pku = $this->generateUniquePku(
            $type
        );

        return response()->json([
            'success' => true,
            'pku' => $pku
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE COMPLETE PURCHASE
    |--------------------------------------------------------------------------
    */

   public function save(Request $request)
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
    | LOGIN CONTEXT
    |--------------------------------------------------------------------------
    */

    $companyId = (int) $user->company_id;
    $subCompanyId = (int) $user->sub_company_id;
    $projectId = (int) $user->project_id;
    $loginId = $user->id ?? null;


    /*
    |--------------------------------------------------------------------------
    | BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    $request->validate([

        'purchase_type' => [
            'required',
            'in:Fabric,Yarn'
        ],

        'purchase_date' => [
            'required',
            'date'
        ],

        'market_id' => [
            'nullable'
        ],

        'market_text' => [
            'required',
            'string',
            'max:200'
        ],

        'shop_id' => [
            'nullable'
        ],

        'shop_text' => [
            'required',
            'string',
            'max:200'
        ],

        'contact_no' => [
            'required',
            'string',
            'max:100'
        ],

        'contact_person' => [
            'required',
            'string',
            'max:100'
        ],

        'place_name' => [
            'required',
            'string',
            'max:200'
        ],

        'email' => [
            'nullable',
            'email',
            'max:100'
        ],

        'address' => [
            'required',
            'string'
        ],

        'fabric_name' => [
            'required',
            'array',
            'min:1'
        ],

        'composition' => [
            'nullable',
            'array'
        ],

        'fabric_width' => [
            'nullable',
            'array'
        ],

        'minimum_order_qty' => [
            'nullable',
            'array'
        ],

        'price_per_meter' => [
            'nullable',
            'array'
        ],

        'price_per_roll' => [
            'nullable',
            'array'
        ],

        'pku_number' => [
            'nullable',
            'array'
        ],

        'sku_number' => [
            'nullable',
            'array'
        ],

        'sample_purchase' => [
            'nullable',
            'array'
        ],

        'physical_number' => [
            'nullable',
            'array'
        ],

        'physical_boxno' => [
            'nullable',
            'array'
        ],

        'physical_location' => [
            'nullable',
            'array'
        ],

        'comments' => [
            'nullable',
            'array'
        ],

    ]);


    /*
    |--------------------------------------------------------------------------
    | PURCHASE TYPE
    |--------------------------------------------------------------------------
    */

    $purchaseType = trim(
        $request->input(
            'purchase_type',
            'Fabric'
        )
    );


    /*
    |--------------------------------------------------------------------------
    | FABRIC / YARN SECTIONS
    |--------------------------------------------------------------------------
    */

    $fabricNames = $request->input(
        'fabric_name',
        []
    );

    if (
        !is_array($fabricNames) ||
        count($fabricNames) < 1
    ) {

        return response()->json([
            'success' => false,
            'message' =>
                'At least one Fabric/Yarn section is required.'
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL UPLOADED FILES
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Do this ONCE.
    |
    | Your current request contains:
    |
    | shop_photos
    | shop_card_photos
    | fabric_photos_0
    | fabric_photos_1
    | fabric_photos_2
    |
    */

    $allFiles = $request->allFiles();


    /*
    |--------------------------------------------------------------------------
    | SHOP IMAGE VALIDATION
    |--------------------------------------------------------------------------
    |
    | At least ONE image is required from:
    |
    | Visiting Card
    | OR
    | Shop Photos
    |
    */

    $shopPhotos = [];

    if (
        isset($allFiles['shop_photos']) &&
        is_array($allFiles['shop_photos'])
    ) {
        $shopPhotos = $allFiles['shop_photos'];
    }


    $shopCardPhotos = [];

    if (
        isset($allFiles['shop_card_photos']) &&
        is_array($allFiles['shop_card_photos'])
    ) {
        $shopCardPhotos =
            $allFiles['shop_card_photos'];
    }


    if (
        count($shopPhotos) === 0 &&
        count($shopCardPhotos) === 0
    ) {

        return response()->json([
            'success' => false,
            'message' =>
                'Please upload at least one Visiting Card or Shop Photo.'
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | FABRIC / YARN IMAGE VALIDATION
    |--------------------------------------------------------------------------
    |
    | Every section must contain at least ONE image.
    |
    | Example:
    |
    | fabric_photos_0[]
    | fabric_photos_1[]
    | fabric_photos_2[]
    |
    */

    foreach (
        $fabricNames as $index => $fabricName
    ) {

        $fabricName = trim(
            (string) $fabricName
        );


        /*
        | Skip completely empty deleted sections.
        */

        if ($fabricName === '') {
            continue;
        }


        $inputName =
            'fabric_photos_' . $index;


        $fabricImages = [];


        if (
            isset($allFiles[$inputName]) &&
            is_array($allFiles[$inputName])
        ) {

            $fabricImages =
                $allFiles[$inputName];
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK VALID IMAGE FILES
        |--------------------------------------------------------------------------
        */

        $validImageCount = 0;


        foreach (
            $fabricImages as $image
        ) {

            if (
                $image instanceof
                \Illuminate\Http\UploadedFile &&
                $image->isValid()
            ) {

                $extension =
                    strtolower(
                        $image
                            ->getClientOriginalExtension()
                    );


                if (
                    in_array(
                        $extension,
                        [
                            'jpg',
                            'jpeg',
                            'png',
                            'webp'
                        ],
                        true
                    )
                ) {

                    $validImageCount++;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE REQUIRED
        |--------------------------------------------------------------------------
        */

        if ($validImageCount === 0) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Please upload at least one image for '
                    . $purchaseType
                    . ' '
                    . ($index + 1)
                    . '.'
            ], 422);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVERYTHING SAVE TOGETHER
    |--------------------------------------------------------------------------
    */

    DB::beginTransaction();


    try {

        /*
        |--------------------------------------------------------------------------
        | MARKET
        |--------------------------------------------------------------------------
        */

        $marketText = trim(
            $request->input(
                'market_text',
                ''
            )
        );


        $marketId =
            $request->input(
                'market_id'
            );


        /*
        | Check existing market by name.
        */

        $market = DB::table(
            'tbl_market'
        )
        ->whereRaw(
            'LOWER(market_name) = ?',
            [
                strtolower($marketText)
            ]
        )
        ->first();


        if ($market) {

            $marketId =
                $market->id;

        } else {

            /*
            | Create new market.
            */

            $marketId =
                ((int) DB::table(
                    'tbl_market'
                )->max('id')) + 1;


            if ($marketId <= 0) {
                $marketId = 1;
            }


            DB::table(
                'tbl_market'
            )->insert([

                'id' =>
                    $marketId,

                'market_name' =>
                    $marketText,

                'tedit' =>
                    null

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SHOP
        |--------------------------------------------------------------------------
        */

        $shopText = trim(
            $request->input(
                'shop_text',
                ''
            )
        );


        /*
        | Find shop under selected market.
        */

        $shop = DB::table(
            'tbl_shop_masterfabric'
        )
        ->where(
            'market_id',
            $marketId
        )
        ->whereRaw(
            'LOWER(shop_name) = ?',
            [
                strtolower($shopText)
            ]
        )
        ->first();


        if ($shop) {

            /*
            | Existing shop.
            */

            $shopMasterId =
                $shop->id;


            /*
            | Update latest shop information.
            */

            DB::table(
                'tbl_shop_masterfabric'
            )
            ->where(
                'id',
                $shopMasterId
            )
            ->update([

                'mobileno' =>
                    $request->input(
                        'contact_no'
                    ),

                'contact_name' =>
                    $request->input(
                        'contact_person'
                    ),

                'emailid' =>
                    $request->input(
                        'email'
                    ),

                'address' =>
                    $request->input(
                        'address'
                    ),

                'place' =>
                    $request->input(
                        'place_name'
                    )

            ]);

        } else {

            /*
            | Create new shop.
            */

            $shopMasterId =
                ((int) DB::table(
                    'tbl_shop_masterfabric'
                )->max('id')) + 1;


            if ($shopMasterId <= 0) {
                $shopMasterId = 1;
            }


            DB::table(
                'tbl_shop_masterfabric'
            )->insert([

                'id' =>
                    $shopMasterId,

                'market_id' =>
                    $marketId,

                'shop_name' =>
                    $shopText,

                'mobileno' =>
                    $request->input(
                        'contact_no'
                    ),

                'contact_name' =>
                    $request->input(
                        'contact_person'
                    ),

                'emailid' =>
                    $request->input(
                        'email'
                    ),

                'address' =>
                    $request->input(
                        'address'
                    ),

                'place' =>
                    $request->input(
                        'place_name'
                    ),

                'tedit' =>
                    null

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | PURCHASE ID
        |--------------------------------------------------------------------------
        */

        $purchaseId =
            ((int) DB::table(
                'fabric_shops'
            )->max('id')) + 1;


        if ($purchaseId <= 0) {
            $purchaseId = 1;
        }


        /*
        |--------------------------------------------------------------------------
        | MAIN PURCHASE
        |--------------------------------------------------------------------------
        */

        DB::table(
            'fabric_shops'
        )->insert([

            'id' =>
                $purchaseId,

            'companyid' =>
                $companyId,

            'subcompanyid' =>
                $subCompanyId,

            'projectid' =>
                $projectId,

            'market_name' =>
                $marketText,

            'shop_name' =>
                $shopText,

            'contact_no' =>
                $request->input(
                    'contact_no'
                ),

            'contact_person' =>
                $request->input(
                    'contact_person'
                ),

            'place_name' =>
                $request->input(
                    'place_name'
                ),

            'address' =>
                $request->input(
                    'address'
                ),

            'email' =>
                $request->input(
                    'email'
                ),

            'purchase_date' =>
                $request->input(
                    'purchase_date'
                ),

            'loginid' =>
                $loginId,

            'type' =>
                $purchaseType,

            'market_id' =>
                $marketId,

            'shop_master_id' =>
                $shopMasterId

        ]);


        /*
        |--------------------------------------------------------------------------
        | MAIN UPLOAD FOLDER
        |--------------------------------------------------------------------------
        |
        | REQUIRED PATH:
        |
        | C:\xampp\htdocs\Production-Management-System\public\fabric_buying\
        |
        */

        $mainFolder =
            public_path(
                'fabric_buying/'
                . $companyId
                . '/'
                . $subCompanyId
                . '/'
                . $projectId
                . '/'
            );


        if (
            !File::exists(
                $mainFolder
            )
        ) {

            File::makeDirectory(
                $mainFolder,
                0777,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SHOP FOLDER
        |--------------------------------------------------------------------------
        */

        $safeShopName =
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '_',
                $shopText
            );


        $shopFolderName =
            time()
            . '_'
            . $safeShopName;


        $shopFolder =
            $mainFolder
            . $shopFolderName
            . '/';


        if (
            !File::exists(
                $shopFolder
            )
        ) {

            File::makeDirectory(
                $shopFolder,
                0777,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SHOP PHOTOS FOLDER
        |--------------------------------------------------------------------------
        */

        $shopPhotoFolder =
            $shopFolder
            . 'shop_photos/';


        if (
            !File::exists(
                $shopPhotoFolder
            )
        ) {

            File::makeDirectory(
                $shopPhotoFolder,
                0777,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VISITING CARD FOLDER
        |--------------------------------------------------------------------------
        */

        $shopCardFolder =
            $shopFolder
            . 'shop_card/';


        if (
            !File::exists(
                $shopCardFolder
            )
        ) {

            File::makeDirectory(
                $shopCardFolder,
                0777,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE SHOP PHOTOS
        |--------------------------------------------------------------------------
        */

        $this->saveShopImages(
            $request,
            'shop_photos',
            $shopPhotoFolder,
            $purchaseId,
            $companyId,
            $subCompanyId,
            $projectId,
            $loginId,
            'Shop'
        );


        /*
        |--------------------------------------------------------------------------
        | SAVE VISITING CARD
        |--------------------------------------------------------------------------
        */

        $this->saveShopImages(
            $request,
            'shop_card_photos',
            $shopCardFolder,
            $purchaseId,
            $companyId,
            $subCompanyId,
            $projectId,
            $loginId,
            'Card'
        );


        /*
        |--------------------------------------------------------------------------
        | SAVE EVERY FABRIC / YARN SECTION
        |--------------------------------------------------------------------------
        */

        foreach (
            $fabricNames as $index => $fabricName
        ) {

            $fabricName =
                trim(
                    (string) $fabricName
                );


            /*
            | Ignore empty deleted section.
            */

            if ($fabricName === '') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PKU
            |--------------------------------------------------------------------------
            */

            $pku =
                trim(
                    $request->input(
                        "pku_number.$index",
                        ''
                    )
                );


            /*
            | Generate if empty.
            */

            if ($pku === '') {

                $pku =
                    $this->generateUniquePku(
                        $purchaseType
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK PKU DUPLICATE
            |--------------------------------------------------------------------------
            */

            $pkuExists =
                DB::table(
                    'fabrics_purchased'
                )
                ->where(
                    'pku_number',
                    $pku
                )
                ->exists();


            if ($pkuExists) {

                throw new \Exception(
                    'PKU Number '
                    . $pku
                    . ' already exists. Please generate another PKU.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | INSERT FABRIC / YARN
            |--------------------------------------------------------------------------
            */

            $materialId =
                DB::table(
                    'fabrics_purchased'
                )->insertGetId([

                    'fabric_shop_id' =>
                        $purchaseId,

                    'fabric_name' =>
                        $fabricName,

                    'composition' =>
                        $request->input(
                            "composition.$index"
                        ),

                    'fabric_width' =>
                        $request->input(
                            "fabric_width.$index"
                        ),

                    'minimum_order_qty' =>
                        $request->input(
                            "minimum_order_qty.$index"
                        ),

                    'price_per_meter' =>
                        $request->input(
                            "price_per_meter.$index"
                        ),

                    'price_per_roll' =>
                        $request->input(
                            "price_per_roll.$index"
                        ),

                    'pku_number' =>
                        $pku,

                    'sku_number' =>
                        $request->input(
                            "sku_number.$index"
                        ),

                    'physical_number' =>
                        $request->input(
                            "physical_number.$index"
                        ),

                    'physical_boxnumber' =>
                        $request->input(
                            "physical_boxno.$index"
                        ),

                    'physical_location' =>
                        $request->input(
                            "physical_location.$index"
                        ),

                    'comments' =>
                        $request->input(
                            "comments.$index"
                        ),

                    'sample_purchase' =>
                        $request->input(
                            "sample_purchase.$index"
                        ),

                    'companyid' =>
                        $companyId,

                    'subcompanyid' =>
                        $subCompanyId,

                    'projectid' =>
                        $projectId,

                    'loginid' =>
                        $loginId,

                    'type' =>
                        $purchaseType

                ]);


            /*
            |--------------------------------------------------------------------------
            | FABRIC / YARN FOLDER
            |--------------------------------------------------------------------------
            */

            $materialFolder =
                $shopFolder
                . 'fabric_'
                . $materialId
                . '/';


            if (
                !File::exists(
                    $materialFolder
                )
            ) {

                File::makeDirectory(
                    $materialFolder,
                    0777,
                    true
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GET MATERIAL IMAGES
            |--------------------------------------------------------------------------
            |
            | IMPORTANT FIX:
            |
            | Use $allFiles instead of
            | $request->file() for checking.
            |
            */

            $inputName =
                'fabric_photos_' . $index;


            $images = [];


            if (
                isset(
                    $allFiles[$inputName]
                ) &&
                is_array(
                    $allFiles[$inputName]
                )
            ) {

                $images =
                    $allFiles[$inputName];
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE MATERIAL IMAGES
            |--------------------------------------------------------------------------
            */

            foreach (
                $images as $image
            ) {

                if (
                    !(
                        $image instanceof
                        \Illuminate\Http\UploadedFile
                    )
                ) {
                    continue;
                }


                if (
                    !$image->isValid()
                ) {
                    continue;
                }


                $extension =
                    strtolower(
                        $image
                            ->getClientOriginalExtension()
                    );


                if (
                    !in_array(
                        $extension,
                        [
                            'jpg',
                            'jpeg',
                            'png',
                            'webp'
                        ],
                        true
                    )
                ) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | UNIQUE IMAGE NAME
                |--------------------------------------------------------------------------
                */

                $fileName =
                    time()
                    . '_'
                    . random_int(
                        1111,
                        9999
                    )
                    . '.'
                    . $extension;


                /*
                |--------------------------------------------------------------------------
                | MOVE IMAGE
                |--------------------------------------------------------------------------
                */

                $image->move(
                    $materialFolder,
                    $fileName
                );


                /*
                |--------------------------------------------------------------------------
                | DATABASE RELATIVE PATH
                |--------------------------------------------------------------------------
                */

                $relativePath =
                    'fabric_buying/'
                    . $companyId
                    . '/'
                    . $subCompanyId
                    . '/'
                    . $projectId
                    . '/'
                    . $shopFolderName
                    . '/'
                    . 'fabric_'
                    . $materialId
                    . '/'
                    . $fileName;


                /*
                |--------------------------------------------------------------------------
                | SAVE IMAGE DATABASE RECORD
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'fabric_photos'
                )->insert([

                    'companyid' =>
                        $companyId,

                    'subcompanyid' =>
                        $subCompanyId,

                    'projectid' =>
                        $projectId,

                    'fabric_id' =>
                        $materialId,

                    'photo_path' =>
                        $relativePath,

                    'loginid' =>
                        $loginId,

                    'type' =>
                        $purchaseType

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        DB::commit();


        return response()->json([

            'success' =>
                true,

            'message' =>
                $purchaseType
                . ' purchase saved successfully.',

            'purchase_id' =>
                $purchaseId,

            'market_id' =>
                $marketId,

            'shop_id' =>
                $shopMasterId

        ]);


    } catch (\Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | ROLLBACK
        |--------------------------------------------------------------------------
        */

        DB::rollBack();

        report($e);


        return response()->json([

            'success' =>
                false,

            'message' =>
                $e->getMessage()

        ], 500);
    }
}


    /*
    |--------------------------------------------------------------------------
    | SAVE SHOP IMAGES
    |--------------------------------------------------------------------------
    */

    private function saveShopImages(
        Request $request,
        string $inputName,
        string $folder,
        int $purchaseId,
        int $companyId,
        int $subCompanyId,
        int $projectId,
        $loginId,
        string $photoType
    ) {

        if (
            !$request->hasFile(
                $inputName
            )
        ) {
            return;
        }


        foreach (
            $request->file(
                $inputName
            )
            as $image
        ) {

            if (
                !$image->isValid()
            ) {
                continue;
            }


            $extension =
                strtolower(
                    $image
                        ->getClientOriginalExtension()
                );


            if (
                !in_array(
                    $extension,
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp'
                    ],
                    true
                )
            ) {
                continue;
            }


            $fileName =
                time()
                . '_'
                . random_int(
                    1111,
                    9999
                )
                . '.'
                . $extension;


            $image->move(
                $folder,
                $fileName
            );


            /*
            |--------------------------------------------------------------------------
            | SHOP FOLDER NAME
            |--------------------------------------------------------------------------
            */

            $shopFolderName =
                basename(
                    dirname(
                        rtrim(
                            $folder,
                            '/\\'
                        )
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | DETERMINE FOLDER TYPE
            |--------------------------------------------------------------------------
            */

            $folderName =
                basename(
                    rtrim(
                        $folder,
                        '/\\'
                    )
                );


            if (
                $folderName ===
                'shop_photos'
            ) {

                $subFolder =
                    'shop_photos';

            } else {

                $subFolder =
                    'shop_card';
            }


            /*
            |--------------------------------------------------------------------------
            | DATABASE PATH
            |--------------------------------------------------------------------------
            */

            $relativePath =
                'fabric_buying/'
                . $companyId
                . '/'
                . $subCompanyId
                . '/'
                . $projectId
                . '/'
                . $shopFolderName
                . '/'
                . $subFolder
                . '/'
                . $fileName;


            /*
            |--------------------------------------------------------------------------
            | SAVE PHOTO RECORD
            |--------------------------------------------------------------------------
            */

            DB::table(
                'fabric_shop_photos'
            )->insert([

                'fabric_shop_id' =>
                    $purchaseId,

                'photo_path' =>
                    $relativePath,

                'photo_type' =>
                    $photoType,

                'companyid' =>
                    $companyId,

                'subcompanyid' =>
                    $subCompanyId,

                'projectid' =>
                    $projectId,

                'loginid' =>
                    $loginId,

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UNIQUE PKU
    |--------------------------------------------------------------------------
    */

    private function generateUniquePku(
        string $type
    ): string {

        $prefix =
            strtolower($type) === 'yarn'
                ? 'YRN'
                : 'FAB';


        do {

            $number =
                random_int(
                    1000,
                    9999
                );


            $pku =
                $prefix
                . $number;


            $exists =
                DB::table(
                    'fabrics_purchased'
                )
                ->where(
                    'pku_number',
                    $pku
                )
                ->exists();


        } while ($exists);


        return $pku;
    }
}