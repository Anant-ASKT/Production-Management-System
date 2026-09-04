<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WooCommerceService
{
    /**
     * Publish or update a product on WooCommerce store using Target Supplier credentials.
     *
     * @param int $specificationId
     * @param int|null $targetSupplierId
     * @param int|null $categoryId
     * @return array
     */
    public function publishProduct($specificationId, $targetSupplierId = null, $categoryId = null)
    {
        // 1. Fetch specification details with master attributes & AI content
        $product = DB::table('auto_designer_specification_master as dsm')
            ->join('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'dsm.supplier_id')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'dsm.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'dsm.manufacturing_process')
            ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
            ->where('dsm.sno', $specificationId)
            ->select([
                'dsm.sno as spec_id',
                'dsm.sku',
                'dsm.barcode',
                'dsm.oc_product_id',
                'dsm.supplier_id as origin_supplier_id',
                'dsm.supplier_product_id',
                'dsm.item_type',
                'dsm.price',
                'dsm.sale_price',
                'dsm.min_price',
                'origin_supplier.name as origin_supplier_name',
                'aipd.AI_product_name',
                'aipd.AI_product_description',
                'aipd.AI_Metatitle',
                'aipd.AI_Metakeywards',
                'aipd.AI_Metadescription',
                'aipd.AI_Producttag',
                'aipd.AI_Imagealttext',
                'itemname.itemname as master_product_name',
                'itemtype.itemtype as product_type',
                'gender.name as gender_name',
                'composition.composition_details as composition_name',
                'colour.colourname as colour_name',
                'size.size as size_name',
                'designer.designername as designer_name',
                'embellishment.embellishmentname as embellishment_name',
                'manufacturing.manufacturing_process as manufacturing_process_name',
                'craftsman.name as craftsman_name'
            ])
            ->first();

        if (!$product) {
            return ['success' => false, 'message' => 'Product specification not found.'];
        }

        // 2. Resolve Target Supplier
        $targetSupplierId = $targetSupplierId ?: $product->origin_supplier_id;
        if (!$targetSupplierId) {
            return ['success' => false, 'message' => 'No target supplier specified for publishing.'];
        }

        $targetSupplier = DB::table('suppliers')->where('sno', $targetSupplierId)->first();
        if (!$targetSupplier) {
            return ['success' => false, 'message' => 'Target supplier not found.'];
        }

        // 3. Validate Target Supplier WooCommerce Credentials
        $storeUrl = rtrim($targetSupplier->store_url ?? '', '/');
        $consumerKey = trim($targetSupplier->consumer_key ?? '');
        $consumerSecret = trim($targetSupplier->consumer_secret ?? '');

        if (empty($storeUrl) || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => false,
                'message' => 'Target supplier "' . $targetSupplier->name . '" does not have valid WooCommerce API credentials (Store URL, Consumer Key, or Consumer Secret missing).'
            ];
        }

        // 4. Clean Product Title
        $cleanTitle = $this->cleanTitle($product->AI_product_name ?: $product->master_product_name);
        if (empty($cleanTitle)) {
            $cleanTitle = 'Handcrafted Garment #' . $product->spec_id;
        }

        // 5. Retrieve approved AI enhanced images
        $approvedImages = DB::table('approved_enhanced_images')
            ->where('specification_id', $specificationId)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get();

        // 6. Upload/Attach Images to WordPress Media Library
        $wcImages = $this->prepareProductImages($storeUrl, $consumerKey, $consumerSecret, $approvedImages, $cleanTitle, $product->AI_Imagealttext);

        // 7. Build Attributes
        $attributes = [];
        if (!empty($product->size_name)) {
            $attributes[] = [
                'name' => 'Size',
                'visible' => true,
                'variation' => false,
                'options' => [$product->size_name]
            ];
        }
        if (!empty($product->colour_name)) {
            $attributes[] = [
                'name' => 'Colour',
                'visible' => true,
                'variation' => false,
                'options' => [$product->colour_name]
            ];
        }
        if (!empty($product->gender_name)) {
            $attributes[] = [
                'name' => 'Gender',
                'visible' => true,
                'variation' => false,
                'options' => [$product->gender_name]
            ];
        }
        if (!empty($product->composition_name)) {
            $attributes[] = [
                'name' => 'Composition',
                'visible' => true,
                'variation' => false,
                'options' => [$product->composition_name]
            ];
        }
        if (!empty($product->designer_name)) {
            $attributes[] = [
                'name' => 'Designer',
                'visible' => true,
                'variation' => false,
                'options' => [$product->designer_name]
            ];
        }
        if (!empty($product->manufacturing_process_name)) {
            $attributes[] = [
                'name' => 'Manufacturing Process',
                'visible' => true,
                'variation' => false,
                'options' => [$product->manufacturing_process_name]
            ];
        }

        // 8. Resolve Category
        $targetCategoryName = $product->product_type;
        if (!empty($categoryId)) {
            $catRecord = DB::table('categories')->where('sno', $categoryId)->first();
            if ($catRecord) {
                $targetCategoryName = $catRecord->name;
            }
        }

        $categories = $this->resolveCategory($storeUrl, $consumerKey, $consumerSecret, $targetCategoryName);

        // Tags
        $tags = [];
        if (!empty($product->AI_Producttag)) {
            $rawTagString = $this->cleanMetaText($product->AI_Producttag);
            $rawTags = explode(',', $rawTagString);
            foreach ($rawTags as $tag) {
                $tagClean = $this->cleanMetaText($tag);
                if (!empty($tagClean) && strlen($tagClean) <= 40 && !str_contains(strtolower($tagClean), 'meta') && !str_contains(strtolower($tagClean), 'image alt')) {
                    $tags[] = ['name' => $tagClean];
                }
            }
        }

        // 9. Description formatting
        $fullDescHtml = $this->formatDescriptionHtml($product);
        
        $cleanDescText = trim($product->AI_product_description ?? '');
        $cleanDescText = preg_replace('/\*+/', '', $cleanDescText);
        $paragraphs = array_filter(array_map('trim', explode("\n", $cleanDescText)));
        $firstPara = count($paragraphs) > 0 ? reset($paragraphs) : '';

        $shortDescText = $this->cleanMetaText($product->AI_Metadescription);
        if (empty($shortDescText) || strlen($shortDescText) < 10) {
            $shortDescText = $firstPara ?: $cleanTitle;
        }

        $shortDescHtml = '<p>' . htmlspecialchars($shortDescText) . '</p>';

        // 10. Meta data
        $metaData = [
            ['key' => 'barcode', 'value' => $product->barcode ?: ''],
            ['key' => 'erp_specification_id', 'value' => (string) $product->spec_id],
            ['key' => 'origin_supplier_name', 'value' => $product->origin_supplier_name ?: ''],
            ['key' => 'target_supplier_name', 'value' => $targetSupplier->name]
        ];

        if (!empty($product->AI_Metatitle)) {
            $cleanMetaTitle = $this->cleanMetaText($product->AI_Metatitle);
            $metaData[] = ['key' => '_yoast_wpseo_title', 'value' => $cleanMetaTitle];
            $metaData[] = ['key' => 'rank_math_title', 'value' => $cleanMetaTitle];
        }
        if (!empty($product->AI_Metadescription)) {
            $cleanMetaDesc = $this->cleanMetaText($product->AI_Metadescription);
            $metaData[] = ['key' => '_yoast_wpseo_metadesc', 'value' => $cleanMetaDesc];
            $metaData[] = ['key' => 'rank_math_description', 'value' => $cleanMetaDesc];
        }
        if (!empty($product->AI_Metakeywards)) {
            $cleanKeywords = $this->cleanMetaText($product->AI_Metakeywards);
            $metaData[] = ['key' => '_yoast_wpseo_focuskw', 'value' => $cleanKeywords];
            $metaData[] = ['key' => 'rank_math_focus_keyword', 'value' => $cleanKeywords];
        }

        // 11. Fetch price and stock
        $sp = null;
        if (!empty($product->supplier_product_id)) {
            $sp = DB::table('supplier_products')->where('sno', $product->supplier_product_id)->first();
        }
        if (!$sp && !empty($targetSupplierId)) {
            $sp = DB::table('supplier_products')
                ->where('supplier_id', $targetSupplierId)
                ->where(function($q) use ($product) {
                    if (!empty($product->item_type)) {
                        $q->where('item_type', $product->item_type);
                    }
                })
                ->first();

            if (!$sp) {
                $sp = DB::table('supplier_products')->where('supplier_id', $targetSupplierId)->first();
            }
        }
        if (!$sp && !empty($product->origin_supplier_id)) {
            $sp = DB::table('supplier_products')
                ->where('supplier_id', $product->origin_supplier_id)
                ->where(function($q) use ($product) {
                    if (!empty($product->item_type)) {
                        $q->where('item_type', $product->item_type);
                    }
                })
                ->first();

            if (!$sp) {
                $sp = DB::table('supplier_products')->where('supplier_id', $product->origin_supplier_id)->first();
            }
        }

        $regularPrice = !empty($product->price) ? (string) $product->price : (!empty($sp->price) ? (string) $sp->price : '5999');
        $salePrice = !empty($product->sale_price) ? (string) $product->sale_price : (!empty($sp->sale_price) ? (string) $sp->sale_price : '');
        $minPrice = !empty($product->min_price) ? (string) $product->min_price : (!empty($sp->min_price) ? (string) $sp->min_price : '');
        $stockQty = !empty($sp->stock) ? (int) $sp->stock : 50;

        if (!empty($minPrice)) {
            $metaData[] = ['key' => '_min_price', 'value' => (string) $minPrice];
        }

        // 12. Check if already published to this target supplier
        $publishedRecord = DB::table('published_products')
            ->where('specification_id', $specificationId)
            ->where('target_supplier_id', $targetSupplierId)
            ->first();

        $existingWcId = $publishedRecord->woocommerce_product_id ?? null;
        if (!$existingWcId && $targetSupplierId == $product->origin_supplier_id) {
            $existingWcId = $product->oc_product_id;
        }

        // 13. Construct WooCommerce Payload
        $payload = [
            'name' => $cleanTitle,
            'type' => 'simple',
            'status' => 'publish',
            'sku' => $product->sku ?: ('SPEC-' . $product->spec_id),
            'regular_price' => $regularPrice,
            'description' => $fullDescHtml,
            'short_description' => $shortDescHtml,
            'categories' => $categories,
            'tags' => $tags,
            'attributes' => $attributes,
            'meta_data' => $metaData,
            'manage_stock' => true,
            'stock_quantity' => $stockQty,
            'stock_status' => 'instock'
        ];

        if (!empty($salePrice)) {
            $payload['sale_price'] = $salePrice;
        }

        if (!empty($wcImages)) {
            $payload['images'] = $wcImages;
        }

        // 14. Send Request to WooCommerce REST API
        $endpoint = $storeUrl . '/wp-json/wc/v3/products' . ($existingWcId ? '/' . $existingWcId : '');
        $method = $existingWcId ? 'put' : 'post';

        try {
            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(45)
                ->acceptJson()
                ->asJson()
                ->$method($endpoint, $payload);

            // If updating failed because product no longer exists on remote store, retry with create
            if ($existingWcId && $response->status() === 404) {
                $endpoint = $storeUrl . '/wp-json/wc/v3/products';
                $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                    ->timeout(45)
                    ->acceptJson()
                    ->asJson()
                    ->post($endpoint, $payload);
            }

            if ($response->successful()) {
                $responseData = $response->json();
                $wcProductId = $responseData['id'] ?? null;
                $permalink = $responseData['permalink'] ?? ($storeUrl . '/?p=' . $wcProductId);

                $user = auth()->user();

                // Save or update in published_products table
                DB::table('published_products')->updateOrInsert(
                    [
                        'specification_id' => $specificationId,
                        'target_supplier_id' => $targetSupplierId,
                    ],
                    [
                        'origin_supplier_id' => $product->origin_supplier_id,
                        'category_id' => $categoryId ?: null,
                        'category_name' => $targetCategoryName,
                        'woocommerce_product_id' => $wcProductId,
                        'permalink' => $permalink,
                        'status' => 'published',
                        'published_by' => $user ? $user->id : null,
                        'countryid' => $user->country_id ?? null,
                        'companyid' => $user->company_id ?? null,
                        'subcompanyid' => $user->sub_company_id ?? null,
                        'projectid' => $user->project_id ?? null,
                        'subprojectid' => $user->sub_project_id ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                // Update specification master
                DB::table('auto_designer_specification_master')
                    ->where('sno', $specificationId)
                    ->update([
                        'oc_product_id' => $wcProductId,
                        'status' => 'Published'
                    ]);

                return [
                    'success' => true,
                    'message' => 'Product published successfully to ' . $targetSupplier->name . '!',
                    'product_id' => $wcProductId,
                    'permalink' => $permalink,
                    'target_supplier_name' => $targetSupplier->name,
                    'category_name' => $targetCategoryName,
                    'data' => $responseData
                ];
            } else {
                $err = $response->json();
                $errMsg = $err['message'] ?? ('HTTP Error ' . $response->status() . ': ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'WooCommerce API Error: ' . $errMsg
                ];
            }
        } catch (\Exception $e) {
            Log::error('WooCommerce Publish Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception during publishing: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare product images for WooCommerce REST API.
     */
    private function prepareProductImages($storeUrl, $consumerKey, $consumerSecret, $approvedImages, $title, $altText)
    {
        $wcImages = [];

        foreach ($approvedImages as $idx => $img) {
            $relativePath = ltrim(str_replace('\\', '/', $img->enhanced_image_path), '/');
            $localFullPath = public_path($relativePath);

            if (!file_exists($localFullPath)) {
                continue;
            }

            $fileName = basename($localFullPath);
            $imgAlt = ($idx === 0 && !empty($altText)) 
                ? $this->cleanMetaText($altText) 
                : ($title . ' - Image ' . ($idx + 1));

            $directUrl = null;
            $fileContent = file_get_contents($localFullPath);

            // 1. Try Catbox
            try {
                $catRes = Http::timeout(25)
                    ->attach('fileToUpload', $fileContent, $fileName)
                    ->post('https://catbox.moe/user/api.php', [
                        'reqtype' => 'fileupload'
                    ]);
                if ($catRes->successful() && str_starts_with(trim($catRes->body()), 'https://')) {
                    $directUrl = trim($catRes->body());
                }
            } catch (\Exception $e) {
                Log::warning('Catbox upload failed: ' . $e->getMessage());
            }

            // 2. Fallback to FreeImage
            if (!$directUrl) {
                try {
                    $freeRes = Http::timeout(25)->post('https://freeimage.host/api/1/upload', [
                        'key' => '6d207e02198a847aa98d0a2a901485a5',
                        'action' => 'upload',
                        'source' => base64_encode($fileContent),
                        'format' => 'json'
                    ]);
                    if ($freeRes->successful()) {
                        $directUrl = $freeRes->json()['image']['url'] ?? null;
                    }
                } catch (\Exception $e) {
                    Log::warning('FreeImage upload failed: ' . $e->getMessage());
                }
            }

            if ($directUrl) {
                $wcImages[] = [
                    'src' => $directUrl,
                    'name' => $fileName,
                    'alt' => $imgAlt,
                    'position' => $idx
                ];
            }
        }

        return $wcImages;
    }

    private function resolveCategory($storeUrl, $consumerKey, $consumerSecret, $categoryName)
    {
        if (empty($categoryName)) return [];

        try {
            $catRes = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(15)
                ->get($storeUrl . '/wp-json/wc/v3/products/categories', [
                    'search' => $categoryName,
                    'per_page' => 10
                ]);

            if ($catRes->successful()) {
                $categories = $catRes->json();
                foreach ($categories as $cat) {
                    if (strcasecmp($cat['name'], $categoryName) === 0) {
                        return [['id' => $cat['id']]];
                    }
                }
            }

            // Create new category in WooCommerce if not found
            $createRes = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(15)
                ->post($storeUrl . '/wp-json/wc/v3/products/categories', [
                    'name' => $categoryName
                ]);

            if ($createRes->successful()) {
                $newCat = $createRes->json();
                if (!empty($newCat['id'])) {
                    return [['id' => $newCat['id']]];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Category resolution failed: ' . $e->getMessage());
        }

        return [['name' => $categoryName]];
    }

    private function cleanTitle($title)
    {
        if (empty($title)) return '';
        $clean = trim($title);
        $clean = preg_replace('/^\*+|\*+$/', '', $clean);
        $clean = trim($clean);

        $markers = ['**Meta', '**Description', '**Tag', 'Meta Tag Keywords:', 'Meta Tag Description:', 'Meta Tag Title:', 'Meta:', '**Image Alt', 'Image Alt Text:'];
        foreach ($markers as $marker) {
            if (stripos($clean, $marker) !== false) {
                $parts = preg_split('/' . preg_quote($marker, '/') . '/i', $clean);
                if (!empty($parts[0])) {
                    $clean = trim($parts[0]);
                }
            }
        }

        $clean = preg_replace('/\*+/', '', $clean);
        return trim($clean, " \t\n\r\0\x0B*:-");
    }

    private function cleanMetaText($text)
    {
        if (empty($text)) return '';
        $clean = trim($text);
        $clean = preg_replace('/^\*+|\*+$/', '', $clean);
        $clean = trim($clean);

        $markers = ['**Meta', '**Description', '**Tag', 'Meta Tag Keywords:', 'Meta Tag Description:', 'Meta Tag Title:', 'Meta:', '**Image Alt', 'Image Alt Text:'];
        foreach ($markers as $marker) {
            if (stripos($clean, $marker) !== false) {
                $parts = preg_split('/' . preg_quote($marker, '/') . '/i', $clean);
                if (!empty($parts[0])) {
                    $clean = trim($parts[0]);
                }
            }
        }

        $clean = preg_replace('/\*+/', '', $clean);
        return trim($clean, " \t\n\r\0\x0B*:-");
    }

    private function formatDescriptionHtml($product)
    {
        $desc = trim($product->AI_product_description ?? '');
        $desc = preg_replace('/^\*+|\*+$/', '', $desc);
        $desc = trim($desc);
        
        $paragraphs = array_filter(array_map('trim', explode("\n", $desc)));
        $html = '';
        foreach ($paragraphs as $p) {
            $pClean = trim(preg_replace('/^\*+|\*+$/', '', $p));
            if (!empty($pClean) && !str_starts_with($pClean, '**')) {
                $html .= '<p>' . nl2br(htmlspecialchars($pClean)) . '</p>';
            }
        }

        return $html ?: '<p>' . htmlspecialchars($product->master_product_name ?: 'Handcrafted garment.') . '</p>';
    }
}
