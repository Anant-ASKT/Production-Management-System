<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierProduct;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $query = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno);

        $selectedDate = $request->has('date') ? $request->input('date') : now()->toDateString();
        if (!empty($selectedDate)) {
            $query->whereDate('created_at', $selectedDate);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_type', 'like', "%{$search}%")
                  ->orWhere('colour', 'like', "%{$search}%");
            });
        }

        $products = $query->latest('sno')->paginate(10)->withQueryString();
        return view('supplier.products.index', compact('products', 'selectedDate'));
    }

    public function create()
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;
        $masters = $isIntegrated ? $this->getSupplierMasterData($user, $supplier) : [];
        $vendorStockSkus = $isIntegrated ? $this->getSupplierVendorStockSkus($user, $supplier) : collect();

        return view('supplier.products.create', compact('isIntegrated', 'masters', 'vendorStockSkus'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('name') && $request->filled('item_name')) {
            $itemNameRow = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                ->where('id', $request->item_name)
                ->orWhere('sno', $request->item_name)
                ->first();
            if ($itemNameRow) {
                $request->merge(['name' => $itemNameRow->itemname]);
            } else {
                $request->merge(['name' => $request->item_name]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600',
            'sub_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600',
        ]);

        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;
        $supplier = \App\Models\Supplier::find($supplierId);
        $isSupplierIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        $isProductIntegrated = false;
        if ($isSupplierIntegrated) {
            $mode = $request->input('catalog_mode', $request->input('catalog_mode_switch', 'integrated'));
            $isProductIntegrated = ($mode === 'integrated');
        }

        $data = $request->except(['main_image', 'sub_images', '_token', 'catalog_mode', 'catalog_mode_switch', 'existing_spec_image', 'existing_spec_sub_images']);
        $data['is_integrated'] = $isProductIntegrated;
        $data['stock'] = (isset($data['stock']) && $data['stock'] !== '' && is_numeric($data['stock'])) ? (int) $data['stock'] : 1;
        $data['supplier_id'] = $supplierId;
        $data['supplier_user_id'] = $user->sno;
        $data['countryid'] = $user->countryid;
        $data['companyid'] = $user->companyid;
        $data['subcompanyid'] = $user->subcompanyid;
        $data['projectid'] = $user->projectid;
        $data['subprojectid'] = $user->subprojectid;

        if ($request->filled('product_sku')) {
            $data['product_sku'] = $request->product_sku;
            $data['intregated_sku'] = $request->product_sku;
        }

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "raw_products/{$supplierId}/main_image";
            $this->saveAndCompressImage($file, $path, $filename);
            $data['main_image'] = $path . '/' . $filename;
        } elseif ($request->filled('existing_spec_image')) {
            $data['main_image'] = $request->existing_spec_image;
        }

        if ($request->hasFile('sub_images')) {
            $subImages = [];
            foreach ($request->file('sub_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = "raw_products/{$supplierId}/additional_images";
                $this->saveAndCompressImage($file, $path, $filename);
                $subImages[] = $path . '/' . $filename;
            }
            $data['sub_images'] = json_encode($subImages);
        } elseif ($request->filled('existing_spec_sub_images')) {
            $data['sub_images'] = $request->existing_spec_sub_images;
        }

        SupplierProduct::create($data);

        return redirect()->route('supplier.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;
        $masters = $isIntegrated ? $this->getSupplierMasterData($user, $supplier) : [];
        $vendorStockSkus = $isIntegrated ? $this->getSupplierVendorStockSkus($user, $supplier) : collect();

        $product = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);
            
        return view('supplier.products.edit', compact('product', 'isIntegrated', 'masters', 'vendorStockSkus'));
    }

    /**
     * AJAX endpoint to search specifications by SKU, product name, item type, color, size, gender, etc.
     */
    public function searchSpecifications(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        if (!$isIntegrated) {
            return response()->json([]);
        }

        $q = trim($request->get('q', ''));

        $itemType = $request->get('item_type');
        $itemName = $request->get('item_name');
        $composition = $request->get('composition');
        $gender = $request->get('gender');

        $stockByBarcode = \Illuminate\Support\Facades\DB::table('vendor_stock')
            ->whereNotNull('barcode')
            ->where('barcode', '<>', '')
            ->select(
                'barcode',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(vendor_id), 0) as vendor_id'),
                \Illuminate\Support\Facades\DB::raw('MAX(purchase_price) as purchase_price'),
                \Illuminate\Support\Facades\DB::raw('MAX(sale_price) as sale_price'),
                \Illuminate\Support\Facades\DB::raw('count(sno) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('coalesce(sum(case when avilable_qty > 0 then avilable_qty else 0 end), 0) as available_stock')
            )
            ->groupBy('barcode');

        $stockByItemId = \Illuminate\Support\Facades\DB::table('vendor_stock')
            ->whereNotNull('item_id')
            ->where('item_id', '>', 0)
            ->select(
                'item_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(vendor_id), 0) as vendor_id'),
                \Illuminate\Support\Facades\DB::raw('MAX(purchase_price) as purchase_price'),
                \Illuminate\Support\Facades\DB::raw('MAX(sale_price) as sale_price'),
                \Illuminate\Support\Facades\DB::raw('count(sno) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('coalesce(sum(case when avilable_qty > 0 then avilable_qty else 0 end), 0) as available_stock')
            )
            ->groupBy('item_id');

        $query = \Illuminate\Support\Facades\DB::table('auto_designer_specification_master as spec')
            ->leftJoinSub($stockByBarcode, 'vs_bar', 'vs_bar.barcode', '=', 'spec.barcode')
            ->leftJoinSub($stockByItemId, 'vs_item', 'vs_item.item_id', '=', 'spec.id')
            ->leftJoin('auto_itemname_master as iname', 'iname.id', '=', 'spec.item_name')
            ->leftJoin('auto_itemtype_master as itype', 'itype.id', '=', 'spec.item_type')
            ->leftJoin('auto_gender_master as igender', 'igender.id', '=', 'spec.gender')
            ->leftJoin('auto_colour_master as icolour', 'icolour.id', '=', 'spec.colour')
            ->leftJoin('auto_size_master as isize', 'isize.id', '=', 'spec.sizes')
            ->leftJoin('auto_composition_master_stock as icomp', 'icomp.id', '=', 'spec.composition')
            ->leftJoin('suppliers', 'suppliers.sno', '=', \Illuminate\Support\Facades\DB::raw('COALESCE(vs_bar.vendor_id, vs_item.vendor_id, spec.supplier_id)'))
            ->select(
                'spec.id as spec_id',
                \Illuminate\Support\Facades\DB::raw("COALESCE(NULLIF(spec.sku, ''), NULLIF(spec.sku_supplier, ''), vs_bar.barcode, spec.barcode, CONCAT('ITEM-', spec.id)) as sku"),
                'spec.sku_supplier',
                \Illuminate\Support\Facades\DB::raw("COALESCE(NULLIF(vs_bar.barcode, ''), spec.barcode) as barcode"),
                'spec.item_name',
                'iname.itemname as item_name_text',
                'spec.item_type',
                'itype.itemtype as item_type_text',
                'spec.designer_name as designer',
                'spec.gender',
                'igender.name as gender_text',
                'spec.composition',
                'icomp.composition_details as composition_text',
                'spec.colour',
                'icolour.colourname as colour_text',
                'spec.yarn',
                'spec.sizes as size',
                'isize.size as size_text',
                'spec.embellishment',
                'spec.manufacturing_process',
                'spec.craftsman',
                'spec.manufecture as manufacture',
                \Illuminate\Support\Facades\DB::raw("COALESCE(spec.price, vs_bar.purchase_price, vs_item.purchase_price) as price"),
                \Illuminate\Support\Facades\DB::raw("COALESCE(spec.sale_price, vs_bar.sale_price, vs_item.sale_price) as sale_price"),
                'spec.min_price',
                'spec.img_path',
                'spec.subimg_path',
                'suppliers.name as supplier_name',
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(vs_bar.total_stock, 0), vs_item.total_stock, 0) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(vs_bar.available_stock, 0), vs_item.available_stock, 0) as available_stock')
            );

        if (!empty($itemType)) {
            $query->where('spec.item_type', $itemType);
        }
        if (!empty($itemName)) {
            $query->where('spec.item_name', $itemName);
        }
        if (!empty($composition)) {
            $query->where('spec.composition', $composition);
        }
        if (!empty($gender)) {
            $query->where('spec.gender', $gender);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('spec.sku', 'LIKE', "%{$q}%")
                  ->orWhere('spec.sku_supplier', 'LIKE', "%{$q}%")
                  ->orWhere('spec.barcode', 'LIKE', "%{$q}%")
                  ->orWhere('vs_bar.barcode', 'LIKE', "%{$q}%")
                  ->orWhere('iname.itemname', 'LIKE', "%{$q}%")
                  ->orWhere('itype.itemtype', 'LIKE', "%{$q}%")
                  ->orWhere('igender.name', 'LIKE', "%{$q}%")
                  ->orWhere('icolour.colourname', 'LIKE', "%{$q}%")
                  ->orWhere('isize.size', 'LIKE', "%{$q}%")
                  ->orWhere('icomp.composition_details', 'LIKE', "%{$q}%");
            });
        }

        $limit = min((int) $request->get('limit', 100), 250);
        $results = $query->orderBy('spec.id', 'desc')
            ->limit($limit)
            ->get()
            ->unique('sku')
            ->values()
            ->map(function ($item) {
                $item->image_url = $this->resolveSpecImage($item->img_path);
                return $item;
            });

        return response()->json($results);
    }

    /**
     * Helper to resolve specification images to full accessible URL
     */
    public function resolveSpecImage($imgPath)
    {
        if (empty($imgPath)) return null;

        if (is_string($imgPath) && (str_starts_with($imgPath, '[') || str_starts_with($imgPath, '{'))) {
            $decoded = json_decode($imgPath, true);
            if (is_array($decoded) && !empty($decoded)) {
                $imgPath = is_array($decoded[0]) ? ($decoded[0]['url'] ?? reset($decoded[0])) : $decoded[0];
            }
        }

        if (empty($imgPath)) return null;

        if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
            return $imgPath;
        }

        $marker = 'ItemsDesigner_Masterwithbarcode/';
        $pos = strpos($imgPath, $marker);
        if ($pos !== false) {
            $rel = trim(substr($imgPath, $pos), '/');
            $fullPath = public_path($rel);
            if (is_file($fullPath)) {
                return asset($rel);
            }
            if (is_dir($fullPath)) {
                $files = @scandir($fullPath);
                if ($files) {
                    foreach ($files as $f) {
                        if ($f === '.' || $f === '..') continue;
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                            return asset($rel . '/' . $f);
                        }
                    }
                }
            }
            return asset($rel);
        }

        $cleanPath = ltrim($imgPath, '/');
        if (is_file(public_path($cleanPath))) {
            return asset($cleanPath);
        }
        if (is_file(storage_path('app/public/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        return asset($cleanPath);
    }

    /**
     * Helper to load vendor stock SKUs and their specifications for an integrated supplier across all suppliers
     */
    private function getSupplierVendorStockSkus($user, $supplier)
    {
        $stockByBarcode = \Illuminate\Support\Facades\DB::table('vendor_stock')
            ->whereNotNull('barcode')
            ->where('barcode', '<>', '')
            ->select(
                'barcode',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(vendor_id), 0) as vendor_id'),
                \Illuminate\Support\Facades\DB::raw('MAX(purchase_price) as purchase_price'),
                \Illuminate\Support\Facades\DB::raw('MAX(sale_price) as sale_price'),
                \Illuminate\Support\Facades\DB::raw('count(sno) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('coalesce(sum(case when avilable_qty > 0 then avilable_qty else 0 end), 0) as available_stock')
            )
            ->groupBy('barcode');

        $stockByItemId = \Illuminate\Support\Facades\DB::table('vendor_stock')
            ->whereNotNull('item_id')
            ->where('item_id', '>', 0)
            ->select(
                'item_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(MAX(vendor_id), 0) as vendor_id'),
                \Illuminate\Support\Facades\DB::raw('MAX(purchase_price) as purchase_price'),
                \Illuminate\Support\Facades\DB::raw('MAX(sale_price) as sale_price'),
                \Illuminate\Support\Facades\DB::raw('count(sno) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('coalesce(sum(case when avilable_qty > 0 then avilable_qty else 0 end), 0) as available_stock')
            )
            ->groupBy('item_id');

        return \Illuminate\Support\Facades\DB::table('auto_designer_specification_master as spec')
            ->leftJoinSub($stockByBarcode, 'vs_bar', 'vs_bar.barcode', '=', 'spec.barcode')
            ->leftJoinSub($stockByItemId, 'vs_item', 'vs_item.item_id', '=', 'spec.id')
            ->leftJoin('auto_itemname_master as iname', 'iname.id', '=', 'spec.item_name')
            ->leftJoin('auto_itemtype_master as itype', 'itype.id', '=', 'spec.item_type')
            ->leftJoin('auto_gender_master as igender', 'igender.id', '=', 'spec.gender')
            ->leftJoin('auto_colour_master as icolour', 'icolour.id', '=', 'spec.colour')
            ->leftJoin('auto_size_master as isize', 'isize.id', '=', 'spec.sizes')
            ->leftJoin('auto_composition_master_stock as icomp', 'icomp.id', '=', 'spec.composition')
            ->leftJoin('suppliers', 'suppliers.sno', '=', \Illuminate\Support\Facades\DB::raw('COALESCE(vs_bar.vendor_id, vs_item.vendor_id, spec.supplier_id)'))
            ->select(
                'spec.id as spec_id',
                \Illuminate\Support\Facades\DB::raw("COALESCE(NULLIF(spec.sku, ''), NULLIF(spec.sku_supplier, ''), vs_bar.barcode, spec.barcode, CONCAT('ITEM-', spec.id)) as sku"),
                'spec.sku_supplier',
                \Illuminate\Support\Facades\DB::raw("COALESCE(NULLIF(vs_bar.barcode, ''), spec.barcode) as barcode"),
                'spec.item_name',
                'iname.itemname as item_name_text',
                'spec.item_type',
                'itype.itemtype as item_type_text',
                'spec.designer_name as designer',
                'spec.gender',
                'igender.name as gender_text',
                'spec.composition',
                'icomp.composition_details as composition_text',
                'spec.colour',
                'icolour.colourname as colour_text',
                'spec.yarn',
                'spec.sizes as size',
                'isize.size as size_text',
                'spec.embellishment',
                'spec.manufacturing_process',
                'spec.craftsman',
                'spec.manufecture as manufacture',
                \Illuminate\Support\Facades\DB::raw("COALESCE(spec.price, vs_bar.purchase_price, vs_item.purchase_price) as price"),
                \Illuminate\Support\Facades\DB::raw("COALESCE(spec.sale_price, vs_bar.sale_price, vs_item.sale_price) as sale_price"),
                'spec.min_price',
                'spec.img_path',
                'spec.subimg_path',
                'suppliers.name as supplier_name',
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(vs_bar.total_stock, 0), vs_item.total_stock, 0) as total_stock'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(vs_bar.available_stock, 0), vs_item.available_stock, 0) as available_stock')
            )
            ->orderBy('spec.id', 'desc')
            ->get()
            ->unique('sku')
            ->values()
            ->map(function ($item) {
                $item->image_url = $this->resolveSpecImage($item->img_path);
                return $item;
            });
    }

    /**
     * Helper to load system master data for integrated supplier
     */
    private function getSupplierMasterData($user, $supplier)
    {
        $loadMaster = function($table, $orderCol, $cols) {
            return \Illuminate\Support\Facades\DB::table($table)->orderBy($orderCol)->get($cols);
        };

        return [
            'itemNames' => $loadMaster('auto_itemname_master', 'itemname', ['sno', 'id', 'itemname', 'code']),
            'itemTypes' => $loadMaster('auto_itemtype_master', 'itemtype', ['sno', 'id', 'itemtype', 'code']),
            'designers' => $loadMaster('auto_designer_master', 'designername', ['sno', 'id', 'designername', 'code']),
            'genders' => $loadMaster('auto_gender_master', 'name', ['sno', 'id', 'name', 'code']),
            'compositions' => $loadMaster('auto_composition_master_stock', 'composition_details', ['sno', 'id', 'composition_details', 'code']),
            'colours' => $loadMaster('auto_colour_master', 'colourname', ['sno', 'id', 'colourname', 'code']),
            'yarns' => $loadMaster('auto_yarn_master', 'yarnname', ['sno', 'id', 'yarnname', 'code']),
            'sizes' => $loadMaster('auto_size_master', 'size', ['sno', 'id', 'size', 'code']),
            'embellishments' => $loadMaster('auto_embellishment_master', 'embellishmentname', ['sno', 'id', 'embellishmentname', 'code']),
            'manufacturingProcesses' => $loadMaster('auto_manufacturing_process_master', 'manufacturing_process', ['sno', 'id', 'manufacturing_process', 'code']),
            'craftsmen' => $loadMaster('auto_craftsman_master', 'name', ['sno', 'id', 'name', 'code']),
            'manufactures' => $loadMaster('auto_manufacture_master', 'name', ['sno', 'id', 'name', 'code']),
        ];
    }

    public function update(Request $request, $id)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;
        $product = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600',
            'sub_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600',
        ]);

        $supplier = \App\Models\Supplier::find($supplierId);
        $isSupplierIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        $data = $request->except(['main_image', 'sub_images', '_token', '_method', 'catalog_mode', 'catalog_mode_switch']);
        if ($isSupplierIntegrated && ($request->has('catalog_mode') || $request->has('catalog_mode_switch'))) {
            $mode = $request->input('catalog_mode', $request->input('catalog_mode_switch', 'integrated'));
            $data['is_integrated'] = ($mode === 'integrated');
        }
        $data['stock'] = (isset($data['stock']) && $data['stock'] !== '' && is_numeric($data['stock'])) ? (int) $data['stock'] : 1;
        if ($request->filled('product_sku')) {
            $data['product_sku'] = $request->product_sku;
            $data['intregated_sku'] = $request->product_sku;
        }

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "raw_products/{$supplierId}/main_image";
            $this->saveAndCompressImage($file, $path, $filename);
            $data['main_image'] = $path . '/' . $filename;
        }

        if ($request->hasFile('sub_images')) {
            $subImages = json_decode($product->sub_images, true) ?? [];
            foreach ($request->file('sub_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = "raw_products/{$supplierId}/additional_images";
                $this->saveAndCompressImage($file, $path, $filename);
                $subImages[] = $path . '/' . $filename;
            }
            $data['sub_images'] = json_encode($subImages);
        }

        $product->update($data);

        return redirect()->route('supplier.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Save an uploaded image, automatically resizing and compressing it if it exceeds 2MB.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $relativeDir
     * @param  string  $filename
     * @return void
     */
    private function saveAndCompressImage($file, $relativeDir, $filename)
    {
        $destinationDir = public_path($relativeDir);
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $fullPath = $destinationDir . DIRECTORY_SEPARATOR . $filename;
        $maxSizeBytes = 2 * 1024 * 1024; // 2MB limit
        $fileSize = $file->getSize();

        // If file is already <= 2MB, move it directly
        if ($fileSize <= $maxSizeBytes) {
            $file->move($destinationDir, $filename);
            return;
        }

        // Exceeds 2MB: reduce size via GD
        $realPath = $file->getRealPath();
        $imageInfo = @getimagesize($realPath);

        if (!$imageInfo) {
            $file->move($destinationDir, $filename);
            return;
        }

        $mime = $imageInfo['mime'] ?? '';
        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];

        $sourceImage = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = @imagecreatefromjpeg($realPath);
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($realPath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = @imagecreatefromwebp($realPath);
                }
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($realPath);
                break;
        }

        if (!$sourceImage) {
            $file->move($destinationDir, $filename);
            return;
        }

        // Handle JPEG EXIF orientation
        if (function_exists('exif_read_data') && ($mime === 'image/jpeg' || $mime === 'image/jpg')) {
            $exif = @exif_read_data($realPath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $sourceImage = imagerotate($sourceImage, 180, 0);
                        break;
                    case 6:
                        $sourceImage = imagerotate($sourceImage, -90, 0);
                        $t = $srcWidth;
                        $srcWidth = $srcHeight;
                        $srcHeight = $t;
                        break;
                    case 8:
                        $sourceImage = imagerotate($sourceImage, 90, 0);
                        $t = $srcWidth;
                        $srcWidth = $srcHeight;
                        $srcHeight = $t;
                        break;
                }
            }
        }

        // Proportionally scale down if larger than 1920px
        $maxDimension = 1920;
        $width = $srcWidth;
        $height = $srcHeight;
        if ($width > $maxDimension || $height > $maxDimension) {
            $scale = min($maxDimension / $width, $maxDimension / $height);
            $width = (int) round($width * $scale);
            $height = (int) round($height * $scale);
        }

        $targetImage = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG, WebP, GIF
        if ($mime === 'image/png' || $mime === 'image/webp' || $mime === 'image/gif') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

        // Compress and save under 2MB
        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $quality = 82;
            imagejpeg($targetImage, $fullPath, $quality);
            while (filesize($fullPath) > $maxSizeBytes && $quality > 25) {
                $quality -= 10;
                imagejpeg($targetImage, $fullPath, $quality);
            }
        } elseif ($mime === 'image/png') {
            imagepng($targetImage, $fullPath, 8);
            // If still over 2MB, progressively scale dimensions down
            while (filesize($fullPath) > $maxSizeBytes && $width > 600) {
                $width = (int) round($width * 0.8);
                $height = (int) round($height * 0.8);
                $smaller = imagecreatetruecolor($width, $height);
                imagealphablending($smaller, false);
                imagesavealpha($smaller, true);
                $transparent = imagecolorallocatealpha($smaller, 255, 255, 255, 127);
                imagefilledrectangle($smaller, 0, 0, $width, $height, $transparent);
                imagecopyresampled($smaller, $targetImage, 0, 0, 0, 0, $width, $height, imagesx($targetImage), imagesy($targetImage));
                imagedestroy($targetImage);
                $targetImage = $smaller;
                imagepng($targetImage, $fullPath, 9);
            }
        } elseif ($mime === 'image/webp') {
            $quality = 80;
            imagewebp($targetImage, $fullPath, $quality);
            while (filesize($fullPath) > $maxSizeBytes && $quality > 25) {
                $quality -= 10;
                imagewebp($targetImage, $fullPath, $quality);
            }
        } else {
            imagegif($targetImage, $fullPath);
        }

        imagedestroy($sourceImage);
        imagedestroy($targetImage);
    }

    public function deleteImage(Request $request, $id)
    {
        $user = auth()->guard('supplier')->user();
        $product = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        $type = $request->input('type'); // 'main' or 'sub'
        $imagePath = $request->input('image_path');

        if ($type === 'main') {
            if ($product->main_image && $product->main_image === $imagePath) {
                if (file_exists(public_path($product->main_image))) {
                    unlink(public_path($product->main_image));
                }
                $product->update(['main_image' => null]);
                return response()->json(['success' => true]);
            }
        } elseif ($type === 'sub') {
            $subImages = json_decode($product->sub_images, true) ?? [];
            if (in_array($imagePath, $subImages)) {
                if (file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }
                $subImages = array_values(array_diff($subImages, [$imagePath]));
                $product->update(['sub_images' => empty($subImages) ? null : json_encode($subImages)]);
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    public function destroy($id)
    {
        $user = auth()->guard('supplier')->user();
        $product = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        if ($product->main_image && file_exists(public_path($product->main_image))) {
            @unlink(public_path($product->main_image));
        }

        if ($product->sub_images) {
            $subImages = json_decode($product->sub_images, true) ?? [];
            if (is_array($subImages)) {
                foreach ($subImages as $image) {
                    if ($image && file_exists(public_path($image))) {
                        @unlink(public_path($image));
                    }
                }
            }
        }

        $product->delete();

        return redirect()->route('supplier.products.index')->with('success', 'Product deleted successfully.');
    }
}
