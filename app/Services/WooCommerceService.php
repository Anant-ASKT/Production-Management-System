<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Category;

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

        // 5. Retrieve approved AI enhanced images (only main and sub, exclude extra)
        $approvedImages = DB::table('approved_enhanced_images')
            ->where('specification_id', $specificationId)
            ->where('status', 'approved')
            ->whereIn('image_type', ['main', 'sub'])
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

        // 8. Resolve Category & Hierarchy for WooCommerce (Strict check: do not create new category in WooCommerce)
        $categoryChain = [];
        $targetCategoryName = $product->product_type ?: 'Garments';

        if (!empty($categoryId)) {
            $catModel = Category::with('parent')->find($categoryId);
            if ($catModel) {
                $categoryChain = $catModel->getLineage();
                $targetCategoryName = $catModel->name;
            }
        }

        $categoryCheck = $this->resolveCategoryHierarchy(
            $storeUrl,
            $consumerKey,
            $consumerSecret,
            $categoryChain,
            $targetCategoryName,
            $targetSupplier->name
        );

        if (!$categoryCheck['success']) {
            return [
                'success' => false,
                'message' => $categoryCheck['message']
            ];
        }

        $categories = $categoryCheck['categories'];

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

        // 11. Fetch price and stock from vendor_stock_web according to which supplier uploaded this product
        $vswBaseQuery = DB::table('vendor_stock_web')
            ->where(function($q) use ($product) {
                $q->where('item_id', $product->spec_id);
                if (!empty($product->sku)) {
                    $q->orWhere('batch_no', $product->sku);
                }
                if (!empty($product->barcode)) {
                    $q->orWhere('barcode', $product->barcode);
                }
            });

        $uploadSupplierId = null;
        $vswCandidate = (clone $vswBaseQuery)
            ->whereNotNull('vendor_id')
            ->where('vendor_id', '>', 0)
            ->orderBy('sno', 'desc')
            ->first();

        if ($vswCandidate && !empty($vswCandidate->vendor_id)) {
            $uploadSupplierId = (int) $vswCandidate->vendor_id;
        } elseif (!empty($product->origin_supplier_id)) {
            $uploadSupplierId = (int) $product->origin_supplier_id;
        } elseif (!empty($product->supplier_product_id)) {
            $sp = DB::table('supplier_products')->where('sno', $product->supplier_product_id)->first();
            if ($sp && !empty($sp->supplier_id)) {
                $uploadSupplierId = (int) $sp->supplier_id;
            }
        }

        $vswQuery = clone $vswBaseQuery;
        if (!empty($uploadSupplierId)) {
            $vswSupplierQuery = (clone $vswBaseQuery)->where('vendor_id', $uploadSupplierId);
            if ($vswSupplierQuery->exists()) {
                $vswQuery = $vswSupplierQuery;
            }
        }

        $vswLatest = (clone $vswQuery)->orderBy('sno', 'desc')->first();
        $vswAvailableStock = (clone $vswQuery)->where(function($q) {
            $q->where('send_qty', 0)->orWhereNull('send_qty');
        })->where(function($q) {
            $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
        })->count();

        if ($vswLatest) {
            $vswPurchase = (!empty($vswLatest->purchase_price) && (float) $vswLatest->purchase_price > 0) ? (float) $vswLatest->purchase_price : null;
            $vswSale = (!empty($vswLatest->sale_price) && (float) $vswLatest->sale_price > 0) ? (float) $vswLatest->sale_price : null;

            if ($vswPurchase && $vswSale && $vswSale < $vswPurchase) {
                $regularPrice = (string) $vswPurchase;
                $salePrice = (string) $vswSale;
            } elseif ($vswSale) {
                $regularPrice = (string) $vswSale;
                $salePrice = '';
            } elseif ($vswPurchase) {
                $regularPrice = (string) $vswPurchase;
                $salePrice = '';
            } else {
                $regularPrice = !empty($product->price) ? (string) $product->price : '5999';
                $salePrice = !empty($product->sale_price) ? (string) $product->sale_price : '';
            }
            $minPrice = !empty($product->min_price) ? (string) $product->min_price : '';
            $stockQty = $vswAvailableStock;
        } else {
            // Fallback to supplier_products if not found in vendor_stock_web
            $sp = null;
            if (!empty($product->supplier_product_id)) {
                $sp = DB::table('supplier_products')->where('sno', $product->supplier_product_id)->first();
            }
            if (!$sp && !empty($uploadSupplierId)) {
                $sp = DB::table('supplier_products')
                    ->where('supplier_id', $uploadSupplierId)
                    ->where(function($q) use ($product) {
                        if (!empty($product->item_type)) {
                            $q->where('item_type', $product->item_type);
                        }
                    })
                    ->first();

                if (!$sp) {
                    $sp = DB::table('supplier_products')->where('supplier_id', $uploadSupplierId)->first();
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
        }

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
            $localFullPath = $this->findLocalImageFile($img->enhanced_image_path);

            if (!$localFullPath || !file_exists($localFullPath)) {
                Log::warning("Product image file not found locally: {$img->enhanced_image_path}");
                continue;
            }

            $relativePath = ltrim(str_replace(['\\', '/'], '/', $img->enhanced_image_path), '/');
            $fileName = basename($localFullPath);
            $imgAlt = ($idx === 0 && !empty($altText)) 
                ? $this->cleanMetaText($altText) 
                : ($title . ' - Image ' . ($idx + 1));

            $directUrl = null;

            // 1. If running on a live public domain, provide direct public asset URL
            $currentHost = request()->getHost() ?: parse_url(config('app.url'), PHP_URL_HOST);
            if ($this->isPublicHost($currentHost)) {
                $publicUrl = asset($relativePath);
                if (filter_var($publicUrl, FILTER_VALIDATE_URL)) {
                    $directUrl = $publicUrl;
                }
            }

            // 2. Fallback to external uploads if on localhost / private IP or public URL not available
            if (!$directUrl) {
                $fileContent = file_get_contents($localFullPath);

                // Option A: FreeImage Host (official API with multipart attachment, returns direct image CDN URL)
                try {
                    $freeRes = Http::timeout(25)
                        ->attach('source', $fileContent, $fileName)
                        ->post('https://freeimage.host/api/1/upload', [
                            'key' => '6d207e02198a847aa98d0a2a901485a5',
                            'action' => 'upload',
                            'format' => 'json'
                        ]);
                    if ($freeRes->successful()) {
                        $freeJson = $freeRes->json();
                        $candUrl = $freeJson['image']['url'] ?? ($freeJson['image']['display_url'] ?? null);
                        if (!empty($candUrl) && filter_var($candUrl, FILTER_VALIDATE_URL)) {
                            $directUrl = $candUrl;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('FreeImage upload failed: ' . $e->getMessage());
                }

                // Option B: Catbox (fallback)
                if (!$directUrl) {
                    try {
                        $catRes = Http::timeout(8)
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
                }
            }

            if ($directUrl) {
                $wcImages[] = [
                    'src' => $directUrl,
                    'name' => $fileName,
                    'alt' => $imgAlt,
                    'position' => $idx
                ];
            } else {
                Log::error("Failed to generate a public/valid URL for image {$fileName} (spec ID: {$img->specification_id})");
            }
        }

        return $wcImages;
    }

    /**
     * Locate local image file across common Laravel and cPanel directory structures.
     */
    private function findLocalImageFile($rawPath)
    {
        if (empty($rawPath)) return null;

        $cleanPath = ltrim(str_replace(['\\', '/'], '/', $rawPath), '/');

        $candidates = [
            public_path($cleanPath),
            base_path('public_html/' . $cleanPath),
            base_path('public/' . $cleanPath),
            base_path($cleanPath),
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $cleanPath),
        ];

        if (str_starts_with($cleanPath, 'public/')) {
            $sub = substr($cleanPath, 7);
            $candidates[] = public_path($sub);
            $candidates[] = base_path('public_html/' . $sub);
        }

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_file($cand)) {
                return $cand;
            }
        }

        return null;
    }

    /**
     * Check if the given hostname is a public, internet-accessible host.
     */
    private function isPublicHost($host)
    {
        if (empty($host)) return false;

        $host = strtolower(trim($host));

        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0'], true)) {
            return false;
        }

        if (preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|127\.)/', $host)) {
            return false;
        }

        if (preg_match('/\.(test|local|localhost|invalid|example)$/i', $host)) {
            return false;
        }

        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    /**
     * Resolve single or hierarchical categories on the WooCommerce store.
     * STRICT: Does NOT create new categories on WooCommerce.
     * If the category or subcategory does not exist on the WooCommerce store, returns an error.
     *
     * @param string $storeUrl
     * @param string $consumerKey
     * @param string $consumerSecret
     * @param array $categoryChain Array of Category models from root to leaf
     * @param string $fallbackName Default name if chain is empty
     * @param string $storeName Target store name for error messages
     * @return array ['success' => bool, 'categories' => array, 'message' => string]
     */
    private function resolveCategoryHierarchy($storeUrl, $consumerKey, $consumerSecret, array $categoryChain, $fallbackName = 'Garments', $storeName = 'the store')
    {
        // If no category chain provided, check fallback name
        if (empty($categoryChain)) {
            $catName = trim($fallbackName ?: 'Garments');
            $wcId = $this->findWooCategory($storeUrl, $consumerKey, $consumerSecret, $catName, 0);
            if (!$wcId) {
                return [
                    'success' => false,
                    'message' => "Category '{$catName}' does not exist on the WooCommerce store ({$storeName}). Please create it in WooCommerce first."
                ];
            }
            return [
                'success' => true,
                'categories' => [['id' => $wcId]]
            ];
        }

        $wcCategoryIds = [];
        $parentWcId = 0;
        $prevCatName = null;

        foreach ($categoryChain as $catModel) {
            $catName = trim($catModel->name);
            if (empty($catName)) continue;

            $currentWcId = $this->findWooCategory(
                $storeUrl,
                $consumerKey,
                $consumerSecret,
                $catName,
                $parentWcId
            );

            if (!$currentWcId) {
                $msg = ($parentWcId > 0 && $prevCatName)
                    ? "Subcategory '{$catName}' (under '{$prevCatName}') does not exist on the WooCommerce store ({$storeName}). Please create this subcategory in WooCommerce first."
                    : "Category '{$catName}' does not exist on the WooCommerce store ({$storeName}). Please create this category in WooCommerce first.";

                return [
                    'success' => false,
                    'message' => $msg
                ];
            }

            $wcCategoryIds[] = ['id' => $currentWcId];
            $parentWcId = $currentWcId;
            $prevCatName = $catName;
        }

        return [
            'success' => true,
            'categories' => $wcCategoryIds
        ];
    }

    /**
     * Search WooCommerce for an existing category by name.
     * Does NOT create any new category on WooCommerce.
     *
     * @param string $storeUrl
     * @param string $consumerKey
     * @param string $consumerSecret
     * @param string $name
     * @param int $parentWcId
     * @return int|null
     */
    private function findWooCategory($storeUrl, $consumerKey, $consumerSecret, $name, $parentWcId = 0)
    {
        if (empty($name)) return null;

        try {
            $searchRes = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(15)
                ->get($storeUrl . '/wp-json/wc/v3/products/categories', [
                    'search' => $name,
                    'per_page' => 50
                ]);

            if ($searchRes->successful()) {
                $existing = $searchRes->json();
                
                // First pass: exact name match with exact parent match
                foreach ($existing as $item) {
                    if (strcasecmp(trim($item['name']), trim($name)) === 0 && (int)($item['parent'] ?? 0) === (int)$parentWcId) {
                        return (int)$item['id'];
                    }
                }

                // Second pass: exact name match (if parent in WC was 0, update parent to link properly)
                foreach ($existing as $item) {
                    if (strcasecmp(trim($item['name']), trim($name)) === 0) {
                        $itemId = (int)$item['id'];
                        if ($parentWcId > 0 && (int)($item['parent'] ?? 0) !== (int)$parentWcId) {
                            try {
                                Http::withBasicAuth($consumerKey, $consumerSecret)
                                    ->timeout(15)
                                    ->put($storeUrl . '/wp-json/wc/v3/products/categories/' . $itemId, [
                                        'parent' => (int)$parentWcId
                                    ]);
                            } catch (\Exception $updateEx) {
                                Log::warning("Could not update parent for WC category {$itemId}: " . $updateEx->getMessage());
                            }
                        }
                        return $itemId;
                    }
                }
            } else {
                Log::warning("WooCommerce category search returned HTTP " . $searchRes->status() . " for: {$name}");
            }
        } catch (\Exception $e) {
            Log::warning("Error in findWooCategory ({$name}): " . $e->getMessage());
        }

        return null;
    }

    private function resolveCategory($storeUrl, $consumerKey, $consumerSecret, $categoryName)
    {
        if (empty($categoryName)) return [];
        $wcId = $this->findWooCategory($storeUrl, $consumerKey, $consumerSecret, $categoryName, 0);
        return $wcId ? [['id' => $wcId]] : [['name' => $categoryName]];
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

    /**
     * Update product price and stock on WooCommerce store.
     *
     * @param int $publishedProductId
     * @param array $data ['regular_price' => ..., 'sale_price' => ..., 'stock_quantity' => ...]
     * @return array
     */
    public function updateProductPriceAndStock($publishedProductId, array $data)
    {
        $published = DB::table('published_products')->where('sno', $publishedProductId)->first();
        if (!$published) {
            return ['success' => false, 'message' => 'Published product record not found.'];
        }

        $wcProductId = $published->woocommerce_product_id;
        if (!$wcProductId) {
            return ['success' => false, 'message' => 'WooCommerce Product ID is not set for this item.'];
        }

        $supplier = DB::table('suppliers')->where('sno', $published->target_supplier_id)->first();
        if (!$supplier) {
            return ['success' => false, 'message' => 'Target supplier not found.'];
        }

        $storeUrl = rtrim($supplier->store_url ?? '', '/');
        $consumerKey = trim($supplier->consumer_key ?? '');
        $consumerSecret = trim($supplier->consumer_secret ?? '');

        if (empty($storeUrl) || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => false,
                'message' => 'Target supplier "' . $supplier->name . '" does not have valid WooCommerce API credentials.'
            ];
        }

        $stockQuantity = isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : null;
        $regularPrice = isset($data['regular_price']) && $data['regular_price'] !== '' ? (string) $data['regular_price'] : null;
        $salePrice = isset($data['sale_price']) && $data['sale_price'] !== '' ? (string) $data['sale_price'] : '';

        $payload = [];
        if ($regularPrice !== null) {
            $payload['regular_price'] = $regularPrice;
        }
        // If sale price is given, set it. If explicitly empty string, clear it.
        $payload['sale_price'] = $salePrice;

        if ($stockQuantity !== null) {
            $payload['manage_stock'] = true;
            $payload['stock_quantity'] = $stockQuantity;
            $payload['stock_status'] = $stockQuantity > 0 ? 'instock' : 'outofstock';
        }

        $endpoint = $storeUrl . '/wp-json/wc/v3/products/' . $wcProductId;

        try {
            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(30)
                ->acceptJson()
                ->asJson()
                ->put($endpoint, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Product updated successfully on WooCommerce.',
                    'status_code' => $response->status(),
                    'payload' => $payload,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'WooCommerce update failed (HTTP ' . $response->status() . '): ' . $response->body(),
                'status_code' => $response->status(),
                'payload' => $payload,
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('WooCommerce updateProductPriceAndStock exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Connection to WooCommerce store failed: ' . $e->getMessage(),
                'payload' => $payload
            ];
        }
    }

    /**
     * Update order status on WooCommerce store using REST API.
     *
     * @param int|string|object|null $supplierOrUrl Target supplier ID, store URL, or supplier object
     * @param int|string $wooCommerceOrderId
     * @param string $status (e.g. 'cancelled', 'processing', 'completed', etc.)
     * @param string|null $note Optional note to record in WooCommerce order notes
     * @param bool $isCustomerNote Whether note is visible to customer
     * @return array
     */
    public function updateOrderStatus($supplierOrUrl, $wooCommerceOrderId, string $status, ?string $note = null, bool $isCustomerNote = false)
    {
        $supplier = null;
        if (is_numeric($supplierOrUrl)) {
            $supplier = DB::table('suppliers')->where('sno', $supplierOrUrl)->first();
        } elseif (is_object($supplierOrUrl)) {
            $supplier = $supplierOrUrl;
        } elseif (is_string($supplierOrUrl) && !empty($supplierOrUrl)) {
            $cleanUrl = preg_replace('#^https?://#i', '', rtrim(trim($supplierOrUrl), '/'));
            $supplier = DB::table('suppliers')
                ->where('store_url', 'like', "%{$cleanUrl}%")
                ->whereNotNull('consumer_key')
                ->whereNotNull('consumer_secret')
                ->first();
        }

        if (!$supplier) {
            // Fallback: try finding any supplier that has valid WooCommerce credentials
            $supplier = DB::table('suppliers')
                ->whereNotNull('store_url')
                ->whereNotNull('consumer_key')
                ->whereNotNull('consumer_secret')
                ->first();
        }

        if (!$supplier) {
            return [
                'success' => false,
                'message' => 'No supplier with valid WooCommerce API credentials found in system.',
            ];
        }

        $storeUrl = rtrim($supplier->store_url ?? '', '/');
        $consumerKey = trim($supplier->consumer_key ?? '');
        $consumerSecret = trim($supplier->consumer_secret ?? '');

        if (empty($storeUrl) || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => false,
                'message' => "Supplier '{$supplier->name}' does not have complete WooCommerce API credentials (Store URL, Consumer Key, or Consumer Secret).",
            ];
        }

        $orderEndpoint = "{$storeUrl}/wp-json/wc/v3/orders/{$wooCommerceOrderId}";
        $payload = ['status' => strtolower(trim($status))];

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(25)
                ->acceptJson()
                ->asJson()
                ->put($orderEndpoint, $payload);

            if ($response->successful()) {
                // If a note was provided, post it to the order notes endpoint
                if (!empty($note)) {
                    try {
                        Http::withoutVerifying()
                            ->withBasicAuth($consumerKey, $consumerSecret)
                            ->timeout(10)
                            ->acceptJson()
                            ->asJson()
                            ->post("{$storeUrl}/wp-json/wc/v3/orders/{$wooCommerceOrderId}/notes", [
                                'note' => $note,
                                'customer_note' => $isCustomerNote,
                            ]);
                    } catch (\Throwable $noteEx) {
                        Log::warning("Failed to add order note to WooCommerce order {$wooCommerceOrderId}: " . $noteEx->getMessage());
                    }
                }

                Log::info("WooCommerce order #{$wooCommerceOrderId} status successfully updated to '{$status}' on store {$supplier->name}.");

                return [
                    'success' => true,
                    'message' => "Order #{$wooCommerceOrderId} status updated to '{$status}' on WooCommerce store ({$supplier->name}).",
                    'data' => $response->json(),
                ];
            }

            $errorBody = $response->body();
            Log::error("WooCommerce order #{$wooCommerceOrderId} status update failed (HTTP {$response->status()}): {$errorBody}");

            return [
                'success' => false,
                'message' => "WooCommerce API returned HTTP {$response->status()}: " . ($response->json('message') ?? $errorBody),
                'status_code' => $response->status(),
                'response' => $errorBody,
            ];

        } catch (\Exception $e) {
            Log::error("WooCommerce updateOrderStatus exception for order #{$wooCommerceOrderId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Connection to WooCommerce store failed: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Process an automated refund on WooCommerce (and through Razorpay / gateway via api_refund).
     *
     * @param int|string|object|null $supplierOrUrl Target supplier ID, store URL, or supplier object
     * @param int|string $wooCommerceOrderId
     * @param float|string $amount
     * @param string|null $reason
     * @param bool $apiRefund (calls WordPress WooCommerce payment gateway process_refund)
     * @return array
     */
    public function createOrderRefund($supplierOrUrl, $wooCommerceOrderId, $amount, ?string $reason = null, bool $apiRefund = true)
    {
        $supplier = null;
        if (is_numeric($supplierOrUrl)) {
            $supplier = DB::table('suppliers')->where('sno', $supplierOrUrl)->first();
        } elseif (is_object($supplierOrUrl)) {
            $supplier = $supplierOrUrl;
        } elseif (is_string($supplierOrUrl) && !empty($supplierOrUrl)) {
            $cleanUrl = preg_replace('#^https?://#i', '', rtrim(trim($supplierOrUrl), '/'));
            $supplier = DB::table('suppliers')
                ->where('store_url', 'like', "%{$cleanUrl}%")
                ->whereNotNull('consumer_key')
                ->whereNotNull('consumer_secret')
                ->first();
        }

        if (!$supplier) {
            $supplier = DB::table('suppliers')
                ->whereNotNull('store_url')
                ->whereNotNull('consumer_key')
                ->whereNotNull('consumer_secret')
                ->first();
        }

        if (!$supplier) {
            return [
                'success' => false,
                'message' => 'No supplier with valid WooCommerce API credentials found in system.',
            ];
        }

        $storeUrl = rtrim($supplier->store_url ?? '', '/');
        $consumerKey = trim($supplier->consumer_key ?? '');
        $consumerSecret = trim($supplier->consumer_secret ?? '');

        if (empty($storeUrl) || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => false,
                'message' => "Supplier '{$supplier->name}' does not have complete WooCommerce API credentials.",
            ];
        }

        $refundEndpoint = "{$storeUrl}/wp-json/wc/v3/orders/{$wooCommerceOrderId}/refunds";
        $formattedAmount = number_format((float) $amount, 2, '.', '');
        $payload = [
            'amount' => (string) $formattedAmount,
            'reason' => $reason ?: 'Order cancelled by Admin via PMS',
            'api_refund' => (bool) $apiRefund,
        ];

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(35)
                ->acceptJson()
                ->asJson()
                ->post($refundEndpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $refundId = $data['id'] ?? null;
                $isRefundedPayment = !empty($data['refunded_payment']);

                Log::info("WooCommerce order #{$wooCommerceOrderId} refund of ₹{$formattedAmount} created successfully (Refund #{$refundId}).", [
                    'store' => $supplier->name,
                    'refunded_payment' => $isRefundedPayment,
                ]);

                return [
                    'success' => true,
                    'message' => "Refund of ₹{$formattedAmount} processed via WooCommerce Razorpay (Refund #{$refundId}).",
                    'refund_id' => $refundId,
                    'data' => $data,
                ];
            }

            $errorBody = $response->body();
            $errorJson = $response->json();
            $errorMsg = $errorJson['message'] ?? $errorBody;
            Log::error("WooCommerce order #{$wooCommerceOrderId} refund failed (HTTP {$response->status()}): {$errorBody}");

            return [
                'success' => false,
                'message' => "WooCommerce Refund API: " . $errorMsg,
                'status_code' => $response->status(),
                'response' => $errorBody,
            ];
        } catch (\Exception $e) {
            Log::error("WooCommerce createOrderRefund exception for order #{$wooCommerceOrderId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Connection to WooCommerce store failed: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Resolve store credentials for a supplier.
     */
    public function resolveSupplierCredentials($supplierOrId)
    {
        $supplier = null;
        if ($supplierOrId instanceof \App\Models\Supplier) {
            $supplier = $supplierOrId;
        } elseif (is_numeric($supplierOrId)) {
            $supplier = DB::table('suppliers')->where('sno', $supplierOrId)->first();
        } elseif (is_object($supplierOrId)) {
            $supplier = $supplierOrId;
        }

        if (!$supplier) {
            return [
                'success' => false,
                'message' => 'Supplier not found.',
            ];
        }

        $storeUrl = rtrim($supplier->store_url ?? '', '/');
        $consumerKey = trim($supplier->consumer_key ?? '');
        $consumerSecret = trim($supplier->consumer_secret ?? '');

        if (empty($storeUrl) || empty($consumerKey) || empty($consumerSecret)) {
            return [
                'success' => false,
                'message' => "Supplier '{$supplier->name}' does not have complete WooCommerce API credentials (Store URL, Consumer Key, and Consumer Secret are required).",
            ];
        }

        return [
            'success' => true,
            'supplier' => $supplier,
            'store_url' => $storeUrl,
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret,
        ];
    }

    /**
     * Test connection to WooCommerce store.
     */
    public function testStoreConnection($supplierOrId)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(20)
                ->acceptJson()
                ->get($creds['store_url'] . '/wp-json/wc/v3/products', ['per_page' => 1]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connected to WooCommerce store successfully.',
                    'total_products' => (int) $response->header('X-WP-Total', 0),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to connect (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch products directly from WooCommerce store.
     */
    public function getStoreProducts($supplierOrId, array $params = [])
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $queryParams = [
            'page' => max(1, (int)($params['page'] ?? 1)),
            'per_page' => min(100, max(1, (int)($params['per_page'] ?? 20))),
        ];

        if (!empty($params['search'])) {
            $queryParams['search'] = trim($params['search']);
        }

        if (!empty($params['status']) && $params['status'] !== 'all') {
            $queryParams['status'] = $params['status'];
        }

        if (!empty($params['category'])) {
            $queryParams['category'] = $params['category'];
        }

        if (!empty($params['order'])) {
            $queryParams['order'] = strtolower($params['order']) === 'asc' ? 'asc' : 'desc';
        }

        if (!empty($params['orderby'])) {
            $queryParams['orderby'] = $params['orderby'];
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(35)
                ->acceptJson()
                ->get($creds['store_url'] . '/wp-json/wc/v3/products', $queryParams);

            if ($response->successful()) {
                $total = (int) $response->header('X-WP-Total', 0);
                $totalPages = (int) $response->header('X-WP-TotalPages', 0);
                $products = $response->json();

                return [
                    'success' => true,
                    'products' => is_array($products) ? $products : [],
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'page' => $queryParams['page'],
                    'per_page' => $queryParams['per_page'],
                ];
            }

            return [
                'success' => false,
                'message' => 'WooCommerce API Error (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch categories directly from WooCommerce store.
     */
    public function getStoreCategories($supplierOrId, array $params = [])
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $queryParams = [
            'per_page' => min(100, max(1, (int)($params['per_page'] ?? 100))),
            'page' => max(1, (int)($params['page'] ?? 1)),
            'hide_empty' => false,
        ];

        if (!empty($params['search'])) {
            $queryParams['search'] = trim($params['search']);
        }

        if (isset($params['parent'])) {
            $queryParams['parent'] = (int) $params['parent'];
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(35)
                ->acceptJson()
                ->get($creds['store_url'] . '/wp-json/wc/v3/products/categories', $queryParams);

            if ($response->successful()) {
                $total = (int) $response->header('X-WP-Total', 0);
                $totalPages = (int) $response->header('X-WP-TotalPages', 0);
                $categories = $response->json();

                return [
                    'success' => true,
                    'categories' => is_array($categories) ? $categories : [],
                    'total' => $total,
                    'total_pages' => $totalPages,
                ];
            }

            return [
                'success' => false,
                'message' => 'WooCommerce API Error (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update WooCommerce product status (e.g. withdraw as 'draft', or reinstate as 'publish').
     * When hiding as draft, stock on WooCommerce is set to 0.
     * When publishing live, stock is restored from available inventory.
     */
    public function updateProductStatus($supplierOrId, $wcProductId, string $newStatus)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $endpoint = $creds['store_url'] . '/wp-json/wc/v3/products/' . (int) $wcProductId;

        $payload = ['status' => $newStatus];

        $published = DB::table('published_products')
            ->where('woocommerce_product_id', $wcProductId)
            ->first();

        if ($newStatus === 'draft') {
            // Set WooCommerce stock to 0 (Out of stock) on website
            $payload['manage_stock'] = true;
            $payload['stock_quantity'] = 0;
            $payload['stock_status'] = 'outofstock';
        } elseif ($newStatus === 'publish') {
            // When re-publishing, restore available stock from vendor_stock_web if mapped
            if ($published && !empty($published->specification_id)) {
                $availableStock = DB::table('vendor_stock_web')
                    ->where(function($q) use ($published) {
                        $q->where('item_id', $published->specification_id);
                    })
                    ->where(function($q) {
                        $q->where('send_qty', 0)->orWhereNull('send_qty');
                    })
                    ->count();

                if ($availableStock > 0) {
                    $payload['manage_stock'] = true;
                    $payload['stock_quantity'] = $availableStock;
                    $payload['stock_status'] = 'instock';
                }
            }
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(30)
                ->acceptJson()
                ->asJson()
                ->put($endpoint, $payload);

            if ($response->successful()) {
                $updatedProduct = $response->json();

                // If linked in published_products, keep local status aligned
                if ($published) {
                    DB::table('published_products')
                        ->where('woocommerce_product_id', $wcProductId)
                        ->update([
                            'status' => $newStatus === 'publish' ? 'published' : 'withdrawn',
                            'updated_at' => now(),
                        ]);
                }

                $message = $newStatus === 'draft' 
                    ? 'Product hidden and website stock set to 0 (Out of stock).' 
                    : 'Product published live on website.';

                return [
                    'success' => true,
                    'message' => $message,
                    'product' => $updatedProduct,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to update product (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete product from WooCommerce store.
     * Sets WooCommerce stock to 0 (Out of stock) without deleting ERP inventory.
     */
    public function deleteProduct($supplierOrId, $wcProductId, bool $force = false)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $endpoint = $creds['store_url'] . '/wp-json/wc/v3/products/' . (int) $wcProductId;

        // 1. First, set stock to 0 on WooCommerce so no purchases can occur
        try {
            Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(20)
                ->acceptJson()
                ->asJson()
                ->put($endpoint, [
                    'manage_stock' => true,
                    'stock_quantity' => 0,
                    'stock_status' => 'outofstock'
                ]);
        } catch (\Exception $e) {
            Log::warning("Could not zero stock before deleting WC product #{$wcProductId}: " . $e->getMessage());
        }

        // 2. Delete / trash product on WooCommerce
        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(30)
                ->acceptJson()
                ->delete($endpoint, ['force' => $force ? 'true' : 'false']);

            if ($response->successful()) {
                $publishedRecord = DB::table('published_products')
                    ->where('woocommerce_product_id', $wcProductId)
                    ->first();

                // If force deleted, remove from published_products
                if ($force) {
                    DB::table('published_products')
                        ->where('woocommerce_product_id', $wcProductId)
                        ->delete();

                    if ($publishedRecord && !empty($publishedRecord->specification_id)) {
                        $remaining = DB::table('published_products')
                            ->where('specification_id', $publishedRecord->specification_id)
                            ->count();
                        if ($remaining === 0) {
                            DB::table('auto_designer_specification_master')
                                ->where('sno', $publishedRecord->specification_id)
                                ->update(['status' => 'Approved', 'oc_product_id' => null]);
                        }
                    }
                } else {
                    DB::table('published_products')
                        ->where('woocommerce_product_id', $wcProductId)
                        ->update([
                            'status' => 'trashed',
                            'updated_at' => now(),
                        ]);

                    if ($publishedRecord && !empty($publishedRecord->specification_id)) {
                        $remaining = DB::table('published_products')
                            ->where('specification_id', $publishedRecord->specification_id)
                            ->where('status', 'published')
                            ->count();
                        if ($remaining === 0) {
                            DB::table('auto_designer_specification_master')
                                ->where('sno', $publishedRecord->specification_id)
                                ->update(['status' => 'Approved', 'oc_product_id' => null]);
                        }
                    }
                }

                return [
                    'success' => true,
                    'message' => $force ? 'Product stock set to 0 and permanently deleted from website.' : 'Product stock set to 0 and moved to Trash on website.',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete product (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create category on WooCommerce store.
     */
    public function createStoreCategory($supplierOrId, array $data)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $endpoint = $creds['store_url'] . '/wp-json/wc/v3/products/categories';
        $payload = [
            'name' => trim($data['name']),
        ];
        if (!empty($data['parent'])) {
            $payload['parent'] = (int) $data['parent'];
        }
        if (!empty($data['description'])) {
            $payload['description'] = trim($data['description']);
        }

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Category created on WooCommerce store.',
                    'category' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create category (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch a single category details from WooCommerce store.
     */
    public function getStoreCategory($supplierOrId, $wcCategoryId)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        $endpoint = $creds['store_url'] . '/wp-json/wc/v3/products/categories/' . (int) $wcCategoryId;

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(20)
                ->acceptJson()
                ->get($endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'category' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch category details (HTTP ' . $response->status() . ')',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete category on WooCommerce store (blocked if category contains products).
     */
    public function deleteStoreCategory($supplierOrId, $wcCategoryId, bool $force = true)
    {
        $creds = $this->resolveSupplierCredentials($supplierOrId);
        if (!$creds['success']) {
            return $creds;
        }

        // Safety check: Verify if category has products assigned
        $catDetails = $this->getStoreCategory($supplierOrId, $wcCategoryId);
        if ($catDetails['success'] && isset($catDetails['category']['count'])) {
            $productCount = (int) $catDetails['category']['count'];
            $catName = $catDetails['category']['name'] ?? 'This category';
            if ($productCount > 0) {
                return [
                    'success' => false,
                    'message' => "Cannot delete '{$catName}' because it currently contains {$productCount} " . ($productCount === 1 ? 'product' : 'products') . " on the website. Please move or remove those products before deleting this category.",
                    'has_products' => true,
                    'product_count' => $productCount
                ];
            }
        }

        $endpoint = $creds['store_url'] . '/wp-json/wc/v3/products/categories/' . (int) $wcCategoryId;

        try {
            $response = Http::withoutVerifying()
                ->withBasicAuth($creds['consumer_key'], $creds['consumer_secret'])
                ->timeout(30)
                ->acceptJson()
                ->delete($endpoint, ['force' => $force ? 'true' : 'false']);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Category deleted from WooCommerce store.',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete category (HTTP ' . $response->status() . '): ' . ($response->json('message') ?? $response->body()),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }
}

