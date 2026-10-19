<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SupplierProduct;

class AdminSupplierRawDataController extends Controller
{
    /**
     * Display a listing of raw products submitted by suppliers.
     */
    public function index(Request $request)
    {
        $companyId = session('companyid');
        $subCompanyId = session('subcompanyid');
        $projectId = session('projectid');

        $query = DB::table('supplier_products as sp')
            ->leftJoin('suppliers as s', 's.sno', '=', 'sp.supplier_id')
            ->leftJoin('supplier_users as su', 'su.sno', '=', 'sp.supplier_user_id')
            ->select(
                'sp.*',
                's.name as supplier_name',
                's.nickname as supplier_nickname',
                'su.name as supplier_user_name',
                'su.email as supplier_user_email',
                'su.phone as supplier_user_phone'
            );

        if (!empty($companyId)) {
            $query->where('sp.companyid', $companyId);
        }
        if (!empty($subCompanyId)) {
            $query->where('sp.subcompanyid', $subCompanyId);
        }
        if (!empty($projectId)) {
            $query->where('sp.projectid', $projectId);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('sp.supplier_id', $request->supplier_id);
        }

        // Search by keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('sp.name', 'like', "%{$search}%")
                  ->orWhere('sp.description', 'like', "%{$search}%")
                  ->orWhere('sp.product_sku', 'like', "%{$search}%")
                  ->orWhere('s.name', 'like', "%{$search}%")
                  ->orWhere('s.nickname', 'like', "%{$search}%");
            });
        }

        // Status filter: pending (default), done, all
        $statusFilter = $request->query('status', 'pending');
        if ($statusFilter === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('sp.status_done')
                  ->orWhere('sp.status_done', '')
                  ->orWhere('sp.status_done', 'draft')
                  ->orWhere('sp.status_done', 'sent_to_specification')
                  ->orWhere('sp.status_done', 'sent_to_photo_section');
            })->where(function ($q) {
                $q->where('sp.status_done', '!=', 'Done')
                  ->orWhereNull('sp.status_done');
            });
        } elseif ($statusFilter === 'done') {
            $query->where('sp.status_done', 'Done');
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('sp.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sp.created_at', '<=', $request->date_to);
        }

        $products = $query->orderBy('sp.sno', 'desc')->paginate(15)->withQueryString();

        // Resolve image URLs for each product
        foreach ($products as $product) {
            $product->image_url = $this->resolveImageUrl($product->main_image);
            $product->sub_image_urls = $this->resolveSubImageUrls($product->sub_images);
        }

        // Calculate summary counters
        $statsQuery = DB::table('supplier_products as sp');
        if (!empty($companyId)) $statsQuery->where('sp.companyid', $companyId);
        if (!empty($subCompanyId)) $statsQuery->where('sp.subcompanyid', $subCompanyId);
        if (!empty($projectId)) $statsQuery->where('sp.projectid', $projectId);

        $totalProducts = (clone $statsQuery)->count();
        $pendingCount = (clone $statsQuery)
            ->where(function ($q) {
                $q->whereNull('sp.status_done')
                  ->orWhere('sp.status_done', '')
                  ->orWhere('sp.status_done', 'draft')
                  ->orWhere('sp.status_done', 'sent_to_specification')
                  ->orWhere('sp.status_done', 'sent_to_photo_section');
            })
            ->where(function ($q) {
                $q->where('sp.status_done', '!=', 'Done')
                  ->orWhereNull('sp.status_done');
            })
            ->count();
        $doneCount = (clone $statsQuery)->where('sp.status_done', 'Done')->count();

        // Suppliers list for dropdown
        $suppliers = DB::table('suppliers')
            ->select('sno', 'name', 'nickname')
            ->orderBy('name')
            ->get();

        return view('admin.supplier-raw-data.index', compact(
            'products',
            'suppliers',
            'statusFilter',
            'totalProducts',
            'pendingCount',
            'doneCount'
        ));
    }

    /**
     * Show single supplier product details page or JSON for AJAX.
     */
    public function show(Request $request, $id)
    {
        $product = DB::table('supplier_products as sp')
            ->leftJoin('suppliers as s', 's.sno', '=', 'sp.supplier_id')
            ->leftJoin('supplier_users as su', 'su.sno', '=', 'sp.supplier_user_id')
            ->select(
                'sp.*',
                's.name as supplier_name',
                's.nickname as supplier_nickname',
                'su.name as supplier_user_name',
                'su.email as supplier_user_email',
                'su.phone as supplier_user_phone'
            )
            ->where('sp.sno', $id)
            ->first();

        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }
            return redirect()->route('admin.supplier-raw-data.index')->with('error', 'Product not found.');
        }

        $product->image_url = $this->resolveImageUrl($product->main_image);
        $product->sub_image_urls = $this->resolveSubImageUrls($product->sub_images);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        }

        return view('admin.supplier-raw-data.show', compact('product'));
    }

    /**
     * Helper to resolve main image URL
     */
    private function resolveImageUrl($imagePath)
    {
        if (empty($imagePath)) return '';
        $imagePath = trim($imagePath);

        if (str_starts_with($imagePath, '[') && str_ends_with($imagePath, ']')) {
            $decoded = json_decode($imagePath, true);
            if (is_array($decoded) && !empty($decoded)) {
                $imagePath = is_array($decoded[0]) ? ($decoded[0]['url'] ?? reset($decoded[0])) : $decoded[0];
            }
        }

        if (empty($imagePath)) return '';

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        $clean = ltrim($imagePath, '/');
        $clean = preg_replace('/^(\.\.\/)+/', '', $clean);

        if (file_exists(public_path($clean))) {
            return asset($clean);
        }

        return asset($clean);
    }

    /**
     * Helper to resolve sub-images URLs
     */
    private function resolveSubImageUrls($rawSubs)
    {
        if (empty($rawSubs)) return [];
        $decoded = is_string($rawSubs) ? json_decode($rawSubs, true) : $rawSubs;
        if (!is_array($decoded)) return [];

        $urls = [];
        foreach ($decoded as $item) {
            if (empty($item)) continue;
            $itemPath = is_array($item) ? ($item['url'] ?? reset($item)) : $item;
            if (empty($itemPath)) continue;

            $itemPath = trim((string) $itemPath);
            if (str_starts_with($itemPath, 'http://') || str_starts_with($itemPath, 'https://')) {
                $urls[] = $itemPath;
                continue;
            }

            $clean = ltrim($itemPath, '/');
            $clean = preg_replace('/^(\.\.\/)+/', '', $clean);
            $urls[] = asset($clean);
        }

        return $urls;
    }
}
