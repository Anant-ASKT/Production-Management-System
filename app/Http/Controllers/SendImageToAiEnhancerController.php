<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class SendImageToAiEnhancerController extends Controller
{
    public function index()
    {
        $itemTypes = DB::table('auto_itemtype_master')
            ->orderBy('itemtype', 'asc')
            ->get(['id', 'itemtype']);

        $itemNames = DB::table('auto_itemname_master')
            ->orderBy('itemname', 'asc')
            ->get(['id', 'itemname']);

        $compositions = DB::table('auto_composition_master_stock')
            ->orderBy('composition_details', 'asc')
            ->get(['id', 'composition_details']);

        $genders = DB::table('auto_gender_master')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return view(
            'all-garments.send-image-to-ai-enhancer',
            compact(
                'itemTypes',
                'itemNames',
                'compositions',
                'genders'
            )
        );
    }

    public function data(Request $request)
    {
        $perPage = max(1, min((int) $request->get('per_page', 20), 100));
        $search = trim((string) $request->get('search', ''));
        $sent = $request->get('sent', 'all');
        $itemType = $request->get('item_type');
        $itemName = $request->get('item_name');
        $composition = $request->get('composition');
        $gender = $request->get('gender');

        $query = DB::table('auto_designer_specification_master as dsm')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->select([
                'dsm.sno',
                'dsm.barcode',
                'dsm.sku',
                'dsm.item_type',
                'dsm.item_name',
                'dsm.gender',
                'dsm.composition',
                'dsm.img_path',
                'dsm.subimg_path',
                'dsm.companyid',
                'dsm.subcompanyid',
                'dsm.projectid',
                'itemtype.itemtype as item_type_text',
                'itemname.itemname as item_name_text',
                'gender.name as gender_text',
                'composition.composition_details as composition_text',
                DB::raw("CASE WHEN EXISTS (SELECT 1 FROM ai_enhancer_sent_products asp WHERE asp.garment_id = dsm.sno AND asp.barcode COLLATE utf8mb4_unicode_ci = dsm.barcode COLLATE utf8mb4_unicode_ci) THEN 1 ELSE 0 END AS ai_sent"),
            ])
            ->where(function ($q) {
                $q->whereNull('dsm.tedit')->orWhere('dsm.tedit', '');
            });

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('dsm.barcode', 'like', $like)
                    ->orWhere('dsm.sku', 'like', $like)
                    ->orWhere('itemname.itemname', 'like', $like)
                    ->orWhere('itemtype.itemtype', 'like', $like)
                    ->orWhere('gender.name', 'like', $like)
                    ->orWhere('composition.composition_details', 'like', $like);
            });
        }

        if ($itemType !== null && $itemType !== '') {
            $query->where('dsm.item_type', $itemType);
        }

        if ($itemName !== null && $itemName !== '') {
            $query->where('dsm.item_name', $itemName);
        }

        if ($composition !== null && $composition !== '') {
            $query->where('dsm.composition', $composition);
        }

        if ($gender !== null && $gender !== '') {
            $query->where('dsm.gender', $gender);
        }

        if ($sent === 'yes') {
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('ai_enhancer_sent_products as asp')
                    ->whereColumn('asp.garment_id', 'dsm.sno')
                    ->whereRaw('asp.barcode COLLATE utf8mb4_unicode_ci = dsm.barcode COLLATE utf8mb4_unicode_ci');
            });
        } elseif ($sent === 'no') {
            $query->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('ai_enhancer_sent_products as asp')
                    ->whereColumn('asp.garment_id', 'dsm.sno')
                    ->whereRaw('asp.barcode COLLATE utf8mb4_unicode_ci = dsm.barcode COLLATE utf8mb4_unicode_ci');
            });
        }

        $products = $query->orderByDesc('dsm.sno')->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | RESOLVE PRODUCT IMAGES
        |--------------------------------------------------------------------------
        | Use the SAME image-resolution logic as All Garments.
        |
        | Supports both database formats:
        |
        | OLD:
        | ../../ItemsDesigner_Masterwithbarcode/1479293301111/
        |
        | NEW:
        | ["ItemsDesigner_Masterwithbarcode/91350782350210/file.jpeg"]
        |
        | IMPORTANT:
        | Old records can contain only a directory. We scan that directory
        | under public/ and return the real image filename(s).
        |--------------------------------------------------------------------------
        */
        $products->getCollection()->transform(function ($product) {

            $rawValues = [
                $product->img_path ?? null,
                $product->subimg_path ?? null,
            ];

            $imageUrls = [];
            $seen = [];

            $addImage = function ($relativePath) use (&$imageUrls, &$seen) {
                $relativePath = trim(
                    str_replace('\\', '/', (string) $relativePath)
                );

                if ($relativePath === '') {
                    return;
                }

                $marker = 'ItemsDesigner_Masterwithbarcode/';
                $markerPosition = stripos($relativePath, $marker);

                if ($markerPosition !== false) {
                    $relativePath = substr(
                        $relativePath,
                        $markerPosition
                    );
                } else {
                    $relativePath = preg_replace(
                        '#^(?:\.\./|\.\/|/|public/)+#i',
                        '',
                        $relativePath
                    );
                }

                $relativePath = ltrim($relativePath, '/');

                if ($relativePath === '') {
                    return;
                }

                $publicAbsolute = public_path($relativePath);

                $allowedExtensions = [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                    'gif',
                    'bmp',
                    'svg',
                    'avif',
                    'heic',
                ];

                // OLD FORMAT: database contains the directory only.
                if (is_dir($publicAbsolute)) {
                    foreach (File::files($publicAbsolute) as $file) {
                        $extension = strtolower($file->getExtension());

                        if (!in_array($extension, $allowedExtensions, true)) {
                            continue;
                        }

                        $fileRelative = str_replace(
                            '\\',
                            '/',
                            $file->getRelativePathname()
                        );

                        $fullRelativePath =
                            $relativePath . $fileRelative;

                        $key = strtolower($fullRelativePath);

                        if (!isset($seen[$key])) {
                            $seen[$key] = true;
                            $imageUrls[] = asset($fullRelativePath);
                        }
                    }

                    return;
                }

                // NEW FORMAT: database contains the actual image filename.
                $extension = strtolower(
                    pathinfo($relativePath, PATHINFO_EXTENSION)
                );

                if (in_array($extension, $allowedExtensions, true)) {
                    $key = strtolower($relativePath);

                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $imageUrls[] = asset($relativePath);
                    }
                }
            };

            $walk = function ($value) use (&$walk, $addImage) {
                if ($value === null || $value === '') {
                    return;
                }

                if (is_array($value)) {
                    foreach ($value as $item) {
                        $walk($item);
                    }
                    return;
                }

                if (is_object($value)) {
                    foreach (['path', 'url', 'image'] as $key) {
                        if (isset($value->{$key})) {
                            $walk($value->{$key});
                            return;
                        }
                    }
                    return;
                }

                $text = trim((string) $value);

                if (
                    strlen($text) >= 2 &&
                    $text[0] === '[' &&
                    substr($text, -1) === ']'
                ) {
                    $decoded = json_decode($text, true);

                    if (
                        json_last_error() === JSON_ERROR_NONE &&
                        is_array($decoded)
                    ) {
                        foreach ($decoded as $item) {
                            $walk($item);
                        }
                        return;
                    }
                }

                $addImage($text);
            };

            foreach ($rawValues as $value) {
                $walk($value);
            }

            // Same field used by All Garments Blade image handling.
            $product->image_urls = array_values($imageUrls);

            // Keep image_url for compatibility with the existing Send page.
            $product->image_url = $product->image_urls[0] ?? null;

            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*.garment_id' => ['required', 'integer'],
            'products.*.barcode' => ['required', 'string'],
        ]);

        $userId = Auth::id();
        $inserted = 0;
        $alreadySent = 0;
        $invalid = 0;

        DB::transaction(function () use ($validated, $userId, &$inserted, &$alreadySent, &$invalid) {
            foreach ($validated['products'] as $item) {
                $garmentId = (int) $item['garment_id'];
                $barcode = trim((string) $item['barcode']);

                $product = DB::table('auto_designer_specification_master')
                    ->select('sno', 'barcode', 'companyid', 'subcompanyid', 'projectid')
                    ->where('sno', $garmentId)
                    ->first();

                if (!$product || trim((string) $product->barcode) !== $barcode) {
                    $invalid++;
                    continue;
                }

                $exists = DB::table('ai_enhancer_sent_products')
                    ->where('garment_id', $garmentId)
                    ->whereRaw('BINARY barcode = BINARY ?', [$barcode])
                    ->exists();

                if ($exists) {
                    $alreadySent++;
                    continue;
                }

                DB::table('ai_enhancer_sent_products')->insert([
                    'garment_id' => $garmentId,
                    'barcode' => $barcode,
                    'user_id' => $userId,
                    'company_id' => $product->companyid,
                    'sub_company_id' => $product->subcompanyid,
                    'project_id' => $product->projectid,
                    'sent_at' => now(),
                ]);

                $inserted++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Products processed successfully.',
            'inserted' => $inserted,
            'already_sent' => $alreadySent,
            'invalid' => $invalid,
        ]);
    }

}
