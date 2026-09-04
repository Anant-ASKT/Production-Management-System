<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WooCommerceService;

class AdminPublishProductsController extends Controller
{
    /**
     * Display the Publish Products page.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('admin.publish_products.index');
    }

    /**
     * Fetch paginated JSON data for products ready to publish.
     * Criteria:
     * 1. Must have AI description saved in AI_product_description.
     * 2. Must have at least one approved image in approved_enhanced_images.
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

        $companyId = $user->companyid;
        $subCompanyId = $user->subcompanyid;
        $projectId = $user->projectid;

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 50], true)) {
            $perPage = 20;
        }

        $search = trim($request->input('search', ''));

        $query = DB::table('auto_designer_specification_master as dsm')
            ->join('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('suppliers as supplier', 'supplier.sno', '=', 'dsm.supplier_id')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
            ->where(function($q) use ($companyId, $subCompanyId, $projectId) {
                if ($companyId) $q->where('dsm.companyid', $companyId);
                if ($subCompanyId) $q->where('dsm.subcompanyid', $subCompanyId);
                if ($projectId) $q->where('dsm.projectid', $projectId);
            })
            // Must have at least one approved enhanced image
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('approved_enhanced_images as aei')
                  ->whereColumn('aei.specification_id', 'dsm.sno')
                  ->where('aei.status', 'approved');
            });

        // Search across SKU, barcode, AI product name, and master names
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dsm.sku', 'like', "%{$search}%")
                  ->orWhere('dsm.barcode', 'like', "%{$search}%")
                  ->orWhere('aipd.AI_product_name', 'like', "%{$search}%")
                  ->orWhere('aipd.AI_Producttag', 'like', "%{$search}%")
                  ->orWhere('itemname.itemname', 'like', "%{$search}%")
                  ->orWhere('itemtype.itemtype', 'like', "%{$search}%");
            });
        }

        $query->select([
            'dsm.sno as spec_id',
            'dsm.id as display_id',
            'dsm.sku',
            'dsm.sku_supplier',
            'dsm.barcode',
            'dsm.oc_product_id',
            'dsm.edatetime',

            // AI Product Data (Primary Display)
            'aipd.sno as ai_desc_id',
            DB::raw("COALESCE(NULLIF(aipd.AI_product_name, ''), itemname.itemname) as product_name"),
            'aipd.AI_product_name',
            'aipd.AI_product_description',
            'aipd.AI_Metatitle',
            'aipd.AI_Metakeywards',
            'aipd.AI_Metadescription',
            'aipd.AI_Producttag',
            'aipd.AI_Imagealttext',

            // Master Attribute Specs
            'itemtype.itemtype as product_type',
            'gender.name as gender_name',
            'composition.composition_details as composition_name',
            'colour.colourname as colour_name',
            'size.size as size_name',
            'supplier.name as supplier_name',
            'designer.designername as designer_name'
        ]);

        $query->orderByDesc('dsm.sno');

        $products = $query->paginate($perPage);

        // Attach approved AI enhanced images for each product
        $specIds = collect($products->items())->pluck('spec_id')->toArray();

        $approvedImages = DB::table('approved_enhanced_images')
            ->whereIn('specification_id', $specIds)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get()
            ->groupBy('specification_id');

        // Transform items to include images array and cleaned titles
        $items = collect($products->items())->map(function ($item) use ($approvedImages) {
            $images = $approvedImages->get($item->spec_id, collect());
            
            $mainImg = $images->firstWhere('image_type', 'main') ?? $images->first();
            $item->ai_main_image = $mainImg ? $mainImg->enhanced_image_path : null;
            $item->approved_images = $images->values()->all();
            $item->total_approved_images = $images->count();

            // Clean AI Product Title
            $rawTitle = $item->AI_product_name ?: $item->product_name;
            $cleanTitle = $this->cleanAiTitle($rawTitle);
            $item->clean_product_name = $cleanTitle ?: ($item->product_name ?: 'Untitled');

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem()
        ]);
    }

    private function cleanAiTitle($title)
    {
        if (empty($title)) return '';
        $clean = trim($title);
        // If meta tag or other markers got concatenated into title:
        if (preg_match('/^(.*?)(?:\*\*Meta|\*\*Description|\*\*Tag|Meta Tag|Meta:)/i', $clean, $m)) {
            $clean = trim($m[1]);
        }
        $clean = preg_replace('/^\*+|\*+$/', '', $clean);
        return trim($clean, " \t\n\r\0\x0B*:-");
    }

    /**
     * Get single product detail modal data.
     */
    public function detail($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $product = DB::table('auto_designer_specification_master as dsm')
            ->join('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('suppliers as supplier', 'supplier.sno', '=', 'dsm.supplier_id')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'dsm.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'dsm.manufacturing_process')
            ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
            ->where('dsm.sno', $id)
            ->select([
                'dsm.sno as spec_id',
                'dsm.id as display_id',
                'dsm.sku',
                'dsm.sku_supplier',
                'dsm.barcode',
                'dsm.clientreference',
                'dsm.oc_product_id',
                'dsm.supplier_id',
                'dsm.supplier_product_id',
                'dsm.item_type',
                'dsm.price',
                'dsm.sale_price',
                'dsm.min_price',
                'dsm.edatetime',

                // AI Data
                'aipd.AI_product_name',
                'aipd.AI_product_description',
                'aipd.AI_Metatitle',
                'aipd.AI_Metakeywards',
                'aipd.AI_Metadescription',
                'aipd.AI_Producttag',
                'aipd.AI_Imagealttext',

                // Masters
                'itemname.itemname as master_product_name',
                'itemtype.itemtype as product_type',
                'gender.name as gender_name',
                'composition.composition_details as composition_name',
                'colour.colourname as colour_name',
                'size.size as size_name',
                'supplier.name as supplier_name',
                'designer.designername as designer_name',
                'embellishment.embellishmentname as embellishment_name',
                'manufacturing.manufacturing_process as manufacturing_process_name',
                'craftsman.name as craftsman_name'
            ])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $product->clean_product_name = $this->cleanAiTitle($product->AI_product_name ?: $product->master_product_name);

        // Fetch supplier product pricing and stock
        $supplierProduct = null;
        if (!empty($product->supplier_product_id)) {
            $supplierProduct = DB::table('supplier_products')
                ->where('sno', $product->supplier_product_id)
                ->first();
        }

        if (!$supplierProduct && !empty($product->supplier_id)) {
            $supplierProduct = DB::table('supplier_products')
                ->where('supplier_id', $product->supplier_id)
                ->where(function($q) use ($product) {
                    if (!empty($product->item_type)) {
                        $q->where('item_type', $product->item_type);
                    }
                })
                ->first();

            if (!$supplierProduct) {
                $supplierProduct = DB::table('supplier_products')
                    ->where('supplier_id', $product->supplier_id)
                    ->first();
            }
        }

        $product->regular_price = !empty($product->price) ? (float) $product->price : (!empty($supplierProduct->price) ? (float) $supplierProduct->price : null);
        $product->sale_price = !empty($product->sale_price) ? (float) $product->sale_price : (!empty($supplierProduct->sale_price) ? (float) $supplierProduct->sale_price : null);
        $product->min_price = !empty($product->min_price) ? (float) $product->min_price : (!empty($supplierProduct->min_price) ? (float) $supplierProduct->min_price : null);
        $product->stock_qty = isset($supplierProduct->stock) ? (int) $supplierProduct->stock : 25;

        // Approved enhanced images
        $approvedImages = DB::table('approved_enhanced_images')
            ->where('specification_id', $id)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $product,
            'approved_images' => $approvedImages
        ]);
    }

    /**
     * Display the full WooCommerce-style product preview page.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $product = DB::table('auto_designer_specification_master as dsm')
            ->join('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('suppliers as supplier', 'supplier.sno', '=', 'dsm.supplier_id')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'dsm.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'dsm.manufacturing_process')
            ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
            ->leftJoin('auto_manufacture_master as manufacture', 'manufacture.id', '=', 'dsm.manufecture')
            ->leftJoin('auto_client_master as client', 'client.id', '=', 'dsm.client')
            ->where('dsm.sno', $id)
            ->select([
                'dsm.sno as spec_id',
                'dsm.id as display_id',
                'dsm.sku',
                'dsm.sku_supplier',
                'dsm.barcode',
                'dsm.clientreference',
                'dsm.oc_product_id',
                'dsm.supplier_id',
                'dsm.supplier_product_id',
                'dsm.item_type',
                'dsm.price',
                'dsm.sale_price',
                'dsm.min_price',
                'dsm.edatetime',

                // AI Data
                'aipd.AI_product_name',
                'aipd.AI_product_description',
                'aipd.AI_Metatitle',
                'aipd.AI_Metakeywards',
                'aipd.AI_Metadescription',
                'aipd.AI_Producttag',
                'aipd.AI_Imagealttext',

                // Masters
                'itemname.itemname as master_product_name',
                'itemtype.itemtype as product_type',
                'gender.name as gender_name',
                'composition.composition_details as composition_name',
                'colour.colourname as colour_name',
                'size.size as size_name',
                'supplier.name as supplier_name',
                'supplier.store_url as store_url',
                'designer.designername as designer_name',
                'embellishment.embellishmentname as embellishment_name',
                'manufacturing.manufacturing_process as manufacturing_process_name',
                'craftsman.name as craftsman_name',
                'manufacture.name as manufacture_name',
                'client.name as client_name'
            ])
            ->first();

        if (!$product) {
            abort(404);
        }

        $product->clean_product_name = $this->cleanAiTitle($product->AI_product_name ?: $product->master_product_name);

        // Fetch pricing and stock from supplier_products if not directly on specification
        $supplierProduct = null;
        if (!empty($product->supplier_product_id)) {
            $supplierProduct = DB::table('supplier_products')
                ->where('sno', $product->supplier_product_id)
                ->first();
        }

        if (!$supplierProduct && !empty($product->supplier_id)) {
            $supplierProduct = DB::table('supplier_products')
                ->where('supplier_id', $product->supplier_id)
                ->where(function($q) use ($product) {
                    if (!empty($product->item_type)) {
                        $q->where('item_type', $product->item_type);
                    }
                })
                ->first();

            if (!$supplierProduct) {
                $supplierProduct = DB::table('supplier_products')
                    ->where('supplier_id', $product->supplier_id)
                    ->first();
            }
        }

        $product->regular_price = !empty($product->price) ? (float) $product->price : (!empty($supplierProduct->price) ? (float) $supplierProduct->price : null);
        $product->sale_price = !empty($product->sale_price) ? (float) $product->sale_price : (!empty($supplierProduct->sale_price) ? (float) $supplierProduct->sale_price : null);
        $product->min_price = !empty($product->min_price) ? (float) $product->min_price : (!empty($supplierProduct->min_price) ? (float) $supplierProduct->min_price : null);
        $product->stock_qty = isset($supplierProduct->stock) ? (int) $supplierProduct->stock : 25;

        // Fetch all approved enhanced images
        $approvedImages = DB::table('approved_enhanced_images')
            ->where('specification_id', $id)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get();

        // Fetch all suppliers for target store selection
        $suppliers = DB::table('suppliers')->orderBy('name', 'asc')->get();

        // Fetch existing published records for this specification
        $publishedRecords = DB::table('published_products as pp')
            ->join('suppliers as s', 's.sno', '=', 'pp.target_supplier_id')
            ->leftJoin('categories as c', 'c.sno', '=', 'pp.category_id')
            ->where('pp.specification_id', $id)
            ->select([
                'pp.*',
                's.name as target_supplier_name',
                's.store_url as target_store_url',
                'c.name as db_category_name'
            ])
            ->orderBy('pp.updated_at', 'desc')
            ->get();

        // Fetch categories for initial target supplier (default to product origin supplier or first supplier)
        $defaultTargetSupplierId = $product->supplier_id ?? ($suppliers->first()->sno ?? null);
        $categories = $defaultTargetSupplierId 
            ? DB::table('categories')->where('supplier_id', $defaultTargetSupplierId)->orderBy('name', 'asc')->get() 
            : collect();

        return view('admin.publish_products.show', compact('product', 'approvedImages', 'suppliers', 'publishedRecords', 'categories', 'defaultTargetSupplierId'));
    }

    /**
     * Get categories by supplier ID (AJAX endpoint).
     */
    public function getCategoriesBySupplier($supplierId)
    {
        $categories = DB::table('categories')
            ->where('supplier_id', $supplierId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Publish product to selected Supplier's WooCommerce store.
     */
    public function publish(Request $request, $id, WooCommerceService $wcService)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'target_supplier_id' => 'required|exists:suppliers,sno',
            'category_id' => 'nullable|exists:categories,sno',
        ]);

        $targetSupplierId = (int) $request->input('target_supplier_id');
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;

        $result = $wcService->publishProduct((int) $id, $targetSupplierId, $categoryId);

        return response()->json($result);
    }
}

