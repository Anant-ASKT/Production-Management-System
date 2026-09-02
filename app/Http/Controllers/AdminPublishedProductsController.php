<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPublishedProductsController extends Controller
{
    /**
     * Display the Published Products list view.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $suppliers = DB::table('suppliers')->orderBy('name', 'asc')->get();

        return view('admin.published_products.index', compact('suppliers'));
    }

    /**
     * Fetch paginated JSON data for published products.
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

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 50], true)) {
            $perPage = 20;
        }

        $search = trim($request->input('search', ''));
        $targetSupplierId = $request->input('target_supplier_id');
        $originSupplierId = $request->input('origin_supplier_id');

        $query = DB::table('published_products as pp')
            ->join('auto_designer_specification_master as dsm', 'dsm.sno', '=', 'pp.specification_id')
            ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('suppliers as target_supplier', 'target_supplier.sno', '=', 'pp.target_supplier_id')
            ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'pp.origin_supplier_id')
            ->leftJoin('categories as cat', 'cat.sno', '=', 'pp.category_id')
            ->select([
                'pp.sno as published_id',
                'pp.specification_id',
                'pp.woocommerce_product_id',
                'pp.permalink',
                'pp.category_name as published_category_name',
                'pp.status as publish_status',
                'pp.created_at as published_at',
                'pp.updated_at as last_updated_at',

                'dsm.sku',
                'dsm.barcode',
                'itemtype.itemtype as product_type',

                DB::raw("COALESCE(NULLIF(aipd.AI_product_name, ''), itemname.itemname, 'Untitled Product') as product_name"),
                'aipd.AI_product_name',
                'aipd.AI_Producttag',

                'target_supplier.name as target_supplier_name',
                'target_supplier.store_url as target_store_url',
                'origin_supplier.name as origin_supplier_name',
                'cat.name as category_name'
            ]);

        // Filter by target supplier
        if (!empty($targetSupplierId)) {
            $query->where('pp.target_supplier_id', $targetSupplierId);
        }

        // Filter by origin supplier
        if (!empty($originSupplierId)) {
            $query->where('pp.origin_supplier_id', $originSupplierId);
        }

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dsm.sku', 'like', "%{$search}%")
                  ->orWhere('dsm.barcode', 'like', "%{$search}%")
                  ->orWhere('aipd.AI_product_name', 'like', "%{$search}%")
                  ->orWhere('pp.category_name', 'like', "%{$search}%")
                  ->orWhere('target_supplier.name', 'like', "%{$search}%")
                  ->orWhere('origin_supplier.name', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('pp.updated_at');

        $paginated = $query->paginate($perPage);

        // Fetch approved images for these specifications
        $specIds = collect($paginated->items())->pluck('specification_id')->unique()->toArray();

        $images = DB::table('approved_enhanced_images')
            ->whereIn('specification_id', $specIds)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get()
            ->groupBy('specification_id');

        $items = collect($paginated->items())->map(function ($item) use ($images) {
            $specImages = $images->get($item->specification_id, collect());
            $mainImg = $specImages->firstWhere('image_type', 'main') ?? $specImages->first();
            $item->main_image = $mainImg ? $mainImg->enhanced_image_path : null;
            $item->total_images = $specImages->count();
            
            // Clean product title
            $item->clean_title = $this->cleanAiTitle($item->AI_product_name ?: $item->product_name);

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem()
        ]);
    }

    private function cleanAiTitle($title)
    {
        if (empty($title)) return '';
        $clean = trim($title);
        if (preg_match('/^(.*?)(?:\*\*Meta|\*\*Description|\*\*Tag|Meta Tag|Meta:)/i', $clean, $m)) {
            $clean = trim($m[1]);
        }
        $clean = preg_replace('/^\*+|\*+$/', '', $clean);
        return trim($clean, " \t\n\r\0\x0B*:-");
    }
}
