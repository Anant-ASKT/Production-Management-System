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
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        $query = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno);

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where(function($q) use ($name) {
                $q->where('name', 'like', "%{$name}%")
                  ->orWhere('product_sku', 'like', "%{$name}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $matchingTypeIds = \Illuminate\Support\Facades\DB::table('auto_itemtype_master')
                ->where('itemtype', 'like', "%{$search}%")
                ->pluck('id')
                ->toArray();

            $query->where(function($q) use ($search, $matchingTypeIds) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_type', 'like', "%{$search}%")
                  ->orWhere('colour', 'like', "%{$search}%");

                if (!empty($matchingTypeIds)) {
                    $q->orWhereIn('item_type', $matchingTypeIds);
                }
            });
        }

        $tab = $request->query('tab', 'pending');

        if ($tab === 'sent') {
            $query->where(function($q) {
                $q->where('status', 'sent_to_photo_section')
                  ->orWhere('status', 'sent_to_specification')
                  ->orWhere('status_done', 'sent_to_photo_section')
                  ->orWhere('status_done', 'sent_to_specification')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('status_done')
                          ->where('status_done', '!=', '')
                          ->where('status_done', '!=', 'draft');
                  });
            });
        } else {
            // Default pending list: only products not yet sent to Photo Section or Specification
            $query->where(function($q) {
                $q->whereNull('status')
                  ->orWhere(function($sq) {
                      $sq->where('status', '!=', 'sent_to_photo_section')
                         ->where('status', '!=', 'sent_to_specification');
                  });
            })->where(function($q) {
                $q->whereNull('status_done')
                  ->orWhere('status_done', '')
                  ->orWhere('status_done', 'draft');
            });
        }

        $pendingCount = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->where(function($q) {
                $q->whereNull('status')
                  ->orWhere(function($sq) {
                      $sq->where('status', '!=', 'sent_to_photo_section')
                         ->where('status', '!=', 'sent_to_specification');
                  });
            })->where(function($q) {
                $q->whereNull('status_done')
                  ->orWhere('status_done', '')
                  ->orWhere('status_done', 'draft');
            })->count();

        $sentCount = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->where(function($q) {
                $q->where('status', 'sent_to_photo_section')
                  ->orWhere('status', 'sent_to_specification')
                  ->orWhere('status_done', 'sent_to_photo_section')
                  ->orWhere('status_done', 'sent_to_specification')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('status_done')
                          ->where('status_done', '!=', '')
                          ->where('status_done', '!=', 'draft');
                  });
            })->count();

        $products = $query->latest('sno')->paginate(15)->withQueryString();

        return view('supplier.products.index', compact('products', 'tab', 'pendingCount', 'sentCount', 'isIntegrated'));
    }

    public function create()
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;
        $masters = $this->getSupplierMasterData($user, $supplier);
        $vendorStockSkus = collect();

        $supplierNickname = '';
        if ($supplier) {
            $supplierNickname = !empty($supplier->nickname) 
                ? trim($supplier->nickname) 
                : (!empty($supplier->name) ? substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $supplier->name)), 0, 4) : 'SUP');
        }
        if (empty($supplierNickname)) $supplierNickname = 'SUP';

        $nextSpecId = ((int) \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->max('id')) + 1;

        return view('supplier.products.create', compact('isIntegrated', 'masters', 'vendorStockSkus', 'supplierNickname', 'nextSpecId'));
    }

    public function store(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;
        $supplier = \App\Models\Supplier::find($supplierId);
        $isSupplierIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        $supplierNickname = '';
        if ($supplier) {
            $supplierNickname = !empty($supplier->nickname) 
                ? trim($supplier->nickname) 
                : (!empty($supplier->name) ? substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $supplier->name)), 0, 4) : 'SUP');
        }
        if (empty($supplierNickname)) $supplierNickname = 'SUP';

        $isProductIntegrated = false;
        if ($isSupplierIntegrated) {
            $mode = $request->input('catalog_mode', $request->input('catalog_mode_switch', 'integrated'));
            $isProductIntegrated = ($mode === 'integrated');
        }

        if ($isProductIntegrated) {
            $itemCode = 'ITM';
            if ($request->filled('item_name')) {
                $itemNameRow = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                    ->where('id', $request->item_name)
                    ->orWhere('sno', $request->item_name)
                    ->first();
                if ($itemNameRow) {
                    $request->merge(['name' => $itemNameRow->itemname]);
                    if (!empty($itemNameRow->code)) {
                        $itemCode = strtoupper($itemNameRow->code);
                    }
                } else {
                    $request->merge(['name' => $request->item_name]);
                }
            }

            // Auto-generate SKU if not manually specified
            if (!$request->filled('product_sku')) {
                $nextSpecId = ((int) \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->max('id')) + 1;
                $autoGeneratedSku = strtoupper("{$itemCode}-{$supplierNickname}-{$nextSpecId}");
                $request->merge(['product_sku' => $autoGeneratedSku]);
            }
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
        }

        $data = $request->except([
            'main_image', 'sub_images', '_token', 'catalog_mode', 'catalog_mode_switch',
            'existing_spec_image', 'existing_spec_sub_images', 'action_type',
            'filter_item_type', 'filter_item_name', 'filter_composition', 'filter_gender',
            'filter_stock_qty', 'selected_spec_id'
        ]);
        $data['is_integrated'] = $isProductIntegrated ? 1 : 0;
        $data['stock'] = (isset($data['stock']) && $data['stock'] !== '' && is_numeric($data['stock'])) ? (int) $data['stock'] : 1;

        if ($isProductIntegrated) {
            // Item name will be product name
            if ($request->filled('item_name')) {
                $itemNameRow = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                    ->where('id', $request->item_name)
                    ->orWhere('sno', $request->item_name)
                    ->first();
                if ($itemNameRow) {
                    $data['name'] = $itemNameRow->itemname;
                }
            }

            // Stock cannot be more than actual available stock
            if ($request->filled('product_sku')) {
                $sku = $request->product_sku;
                $stockByBarcode = (int) \Illuminate\Support\Facades\DB::table('vendor_stock')
                    ->where('barcode', $sku)
                    ->sum(\Illuminate\Support\Facades\DB::raw('case when avilable_qty > 0 then avilable_qty else 0 end'));

                $stockByItemId = (int) \Illuminate\Support\Facades\DB::table('vendor_stock as vs')
                    ->join('auto_designer_specification_master as spec', 'spec.id', '=', 'vs.item_id')
                    ->where(function($w) use ($sku) {
                        $w->where('spec.sku', $sku)
                          ->orWhere('spec.sku_supplier', $sku)
                          ->orWhere('spec.barcode', $sku);
                    })
                    ->sum(\Illuminate\Support\Facades\DB::raw('case when vs.avilable_qty > 0 then vs.avilable_qty else 0 end'));

                $maxAvailStock = max($stockByBarcode, $stockByItemId);
                if ($maxAvailStock > 0 && $data['stock'] > $maxAvailStock) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['stock' => "Stock quantity cannot exceed the actual available stock ({$maxAvailStock})."]);
                }
            }
        } else {
            $data['name'] = $request->input('name');
            $data['item_name'] = null;
            $data['product_sku'] = null;
            $data['intregated_sku'] = null;
        }

        $data['supplier_id'] = $supplierId;
        $data['supplier_user_id'] = $user->sno;
        $data['countryid'] = $user->countryid;
        $data['companyid'] = $user->companyid;
        $data['subcompanyid'] = $user->subcompanyid;
        $data['projectid'] = $user->projectid;
        $data['subprojectid'] = $user->subprojectid;

        if ($isProductIntegrated && $request->filled('product_sku')) {
            $data['product_sku'] = $request->product_sku;
            $data['intregated_sku'] = $request->product_sku;
        } else {
            $data['product_sku'] = null;
            $data['intregated_sku'] = null;
        }

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "raw_products/{$supplierId}/main_image";
            $this->saveAndCompressImage($file, $path, $filename);
            $data['main_image'] = $path . '/' . $filename;
        } elseif ($request->filled('existing_spec_image')) {
            $rawImg = $request->existing_spec_image;
            if (is_string($rawImg) && (str_starts_with($rawImg, '[') || str_starts_with($rawImg, '{'))) {
                $decoded = json_decode($rawImg, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $rawImg = is_array($decoded[0]) ? ($decoded[0]['url'] ?? reset($decoded[0])) : $decoded[0];
                }
            }
            $data['main_image'] = $rawImg;
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

        // SAVE WORKFLOW FOR "Choose from Ready List" ($isProductIntegrated is true)
        if ($isProductIntegrated) {
            $actionType = $request->input('action_type', 'save_next');
            $data['status_done'] = 'draft';
            $data['status'] = 'draft';

            $supplierProduct = SupplierProduct::create($data);

            if ($actionType === 'save_next') {
                $redirectParams = array_filter([
                    'item_type' => $request->input('filter_item_type'),
                    'item_name' => $request->input('filter_item_name'),
                    'composition' => $request->input('filter_composition'),
                    'gender' => $request->input('filter_gender'),
                    'collection' => $request->input('filter_collection'),
                    'designer' => $request->input('filter_designer'),
                    'size' => $request->input('filter_size'),
                    'colour' => $request->input('filter_colour'),
                    'manufacture' => $request->input('filter_manufacture'),
                    'manufacturing_process' => $request->input('filter_manufacturing_process'),
                    'craftsman' => $request->input('filter_craftsman'),
                    'stock_qty' => $request->input('filter_stock_qty'),
                ], function($val) {
                    return $val !== null && $val !== '';
                });

                return redirect()->route('supplier.products.create', $redirectParams)
                    ->with('success', 'Product saved successfully.');
            }

            return redirect()->route('supplier.products.index')
                ->with('success', 'Product saved successfully.');
        }

        // FLOW FOR "ADD NEW PRODUCT" (Manual Mode / Non-Integrated):
        $data['status'] = 'draft';
        $data['status_done'] = 'draft';
        $supplierProduct = SupplierProduct::create($data);

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully and added to Review Selection.');
    }

    public function show($id)
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);
        $isIntegrated = $supplier ? (bool) $supplier->is_integrated : false;

        $product = SupplierProduct::where('supplier_id', $user->supplier_id)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        $masterNames = $this->resolveProductMasterNames($product);

        $subImages = [];
        if (!empty($product->sub_images)) {
            $rawSubs = is_string($product->sub_images) ? json_decode($product->sub_images, true) : $product->sub_images;
            if (is_array($rawSubs)) {
                foreach ($rawSubs as $img) {
                    if (empty($img)) continue;
                    if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                        $subImages[] = $img;
                    } else {
                        $subImages[] = asset(ltrim($img, '/'));
                    }
                }
            }
        }

        return view('supplier.products.show', compact('product', 'isIntegrated', 'supplier', 'masterNames', 'subImages'));
    }

    private function resolveProductMasterNames($product)
    {
        $resolve = function($table, $col, $val) {
            if (empty($val) || $val === '-') return '-';
            if (is_numeric($val)) {
                $row = \Illuminate\Support\Facades\DB::table($table)
                    ->where('id', $val)
                    ->orWhere('sno', $val)
                    ->value($col);
                return $row ?: $val;
            }
            return $val;
        };

        return [
            'item_name' => $resolve('auto_itemname_master', 'itemname', $product->item_name),
            'item_type' => $resolve('auto_itemtype_master', 'itemtype', $product->item_type),
            'designer' => $resolve('auto_designer_master', 'designername', $product->designer),
            'gender' => $resolve('auto_gender_master', 'name', $product->gender),
            'composition' => $resolve('auto_composition_master_stock', 'composition_details', $product->composition),
            'colour' => $resolve('auto_colour_master', 'colourname', $product->colour),
            'yarn' => $resolve('auto_yarn_master', 'yarnname', $product->yarn),
            'size' => $resolve('auto_size_master', 'size', $product->size),
            'embellishment' => $resolve('auto_embellishment_master', 'embellishmentname', $product->embellishment),
            'manufacturing_process' => $resolve('auto_manufacturing_process_master', 'manufacturing_process', $product->manufacturing_process),
            'craftsman' => $resolve('auto_craftsman_master', 'name', $product->craftsman),
            'manufacture' => $resolve('auto_manufacture_master', 'name', $product->manufacture),
        ];
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
        $collection = $request->get('collection');
        $designer = $request->get('designer');
        $size = $request->get('size');
        $colour = $request->get('colour', $request->get('color'));
        $manufacture = $request->get('manufacture');
        $manufacturingProcess = $request->get('manufacturing_process');
        $craftsman = $request->get('craftsman');
        $stockFilter = (string) $request->get('stock_filter', $request->get('stock_qty', ''));

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
            ->leftJoin('auto_designer_master as idesigner', 'idesigner.id', '=', 'spec.designer_name')
            ->leftJoin('auto_manufacture_master as imanuf', 'imanuf.id', '=', 'spec.manufecture')
            ->leftJoin('auto_manufacturing_process_master as iprocess', 'iprocess.id', '=', 'spec.manufacturing_process')
            ->leftJoin('auto_craftsman_master as icraftsman', function($join) {
                $join->on('icraftsman.id', '=', 'spec.craftsman')
                     ->orOn('icraftsman.sno', '=', 'spec.craftsman');
            })
            ->leftJoin('auto_client_master as iclient', 'iclient.id', '=', 'spec.client')
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
                'idesigner.designername as designer_text',
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
                'iprocess.manufacturing_process as manufacturing_process_text',
                'spec.craftsman',
                'icraftsman.name as craftsman_text',
                'spec.manufecture as manufacture',
                'imanuf.name as manufacture_text',
                'spec.client as collection',
                'iclient.name as collection_text',
                'iclient.code as collection_code',
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
        if (!empty($collection)) {
            $query->where('spec.client', $collection);
        }
        if (!empty($designer)) {
            $query->where('spec.designer_name', $designer);
        }
        if (!empty($size)) {
            $query->where('spec.sizes', $size);
        }
        if (!empty($colour)) {
            $query->where('spec.colour', $colour);
        }
        if (!empty($manufacture)) {
            $query->where('spec.manufecture', $manufacture);
        }
        if (!empty($manufacturingProcess)) {
            $query->where('spec.manufacturing_process', $manufacturingProcess);
        }
        if (!empty($craftsman)) {
            $query->where(function ($w) use ($craftsman) {
                $w->where('spec.craftsman', $craftsman)
                  ->orWhere('icraftsman.id', $craftsman)
                  ->orWhere('icraftsman.sno', $craftsman);
            });
        }

        // Only show products with stock > 0 (exclude 0 stock items)
        $query->where(function ($w) {
            $w->where('vs_bar.available_stock', '>', 0)
              ->orWhere('vs_item.available_stock', '>', 0);
        });

        if ($stockFilter !== '') {
            $stockExpr = 'COALESCE(NULLIF(vs_bar.available_stock, 0), vs_item.available_stock, 0)';
            if ($stockFilter === '1' || $stockFilter == '1') {
                $query->whereRaw("{$stockExpr} = 1");
            } elseif ($stockFilter === 'less_than_5' || $stockFilter === '<5') {
                $query->whereRaw("{$stockExpr} > 1 AND {$stockExpr} < 5");
            } elseif ($stockFilter === '5_10' || $stockFilter === '5-10') {
                $query->whereRaw("{$stockExpr} >= 5 AND {$stockExpr} <= 10");
            } elseif ($stockFilter === '10_above' || $stockFilter === 'above_10' || $stockFilter === '>10' || $stockFilter === '10+') {
                $query->whereRaw("{$stockExpr} >= 10");
            }
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
                  ->orWhere('icomp.composition_details', 'LIKE', "%{$q}%")
                  ->orWhere('idesigner.designername', 'LIKE', "%{$q}%")
                  ->orWhere('imanuf.name', 'LIKE', "%{$q}%")
                  ->orWhere('iprocess.manufacturing_process', 'LIKE', "%{$q}%")
                  ->orWhere('icraftsman.name', 'LIKE', "%{$q}%")
                  ->orWhere('iclient.name', 'LIKE', "%{$q}%")
                  ->orWhere('iclient.code', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('limit') && (int) $request->get('limit') > 0) {
            $query->limit((int) $request->get('limit'));
        }

        $results = $query->orderBy('spec.id', 'desc')
            ->get()
            ->unique('sku')
            ->values()
            ->map(function ($item) {
                $item->image_url = $this->resolveSpecImage($item->img_path, $item->subimg_path ?? null);
                return $item;
            });

        return response()->json($results);
    }

    /**
     * Helper to resolve specification images to full accessible URL
     */
    public function resolveSpecImage($imgPath, $subImgPath = null)
    {
        $findImage = function($pathInput) {
            if (empty($pathInput)) return null;

            $images = [];
            if (is_string($pathInput)) {
                $trimmed = trim($pathInput);
                if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
                    $decoded = json_decode($trimmed, true);
                    $images = is_array($decoded) ? $decoded : [$trimmed];
                } else {
                    $images = [$trimmed];
                }
            } elseif (is_array($pathInput)) {
                $images = $pathInput;
            }

            $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif', 'avif'];

            foreach ($images as $img) {
                if (is_array($img)) {
                    $img = $img['url'] ?? $img['path'] ?? reset($img);
                }
                if (!is_string($img)) {
                    continue;
                }

                $img = str_replace('\\', '/', trim($img));
                if ($img === '') {
                    continue;
                }

                // If already an absolute HTTP/HTTPS URL
                if (preg_match('/^https?:\\/\\//i', $img)) {
                    return $img;
                }

                // Check ItemsDesigner_Masterwithbarcode
                $marker = 'ItemsDesigner_Masterwithbarcode/';
                $pos = strpos($img, $marker);
                if ($pos !== false) {
                    $rel = trim(substr($img, $pos), '/');
                    $physicalPath = public_path($rel);

                    if (is_file($physicalPath)) {
                        return asset($rel);
                    }

                    if (is_dir($physicalPath)) {
                        $files = @scandir($physicalPath);
                        if (is_array($files)) {
                            foreach ($files as $file) {
                                if ($file === '.' || $file === '..') continue;
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($ext, $validExtensions, true)) {
                                    return asset($rel . '/' . $file);
                                }
                            }
                        }
                    }
                    continue;
                }

                // Check other direct or relative paths
                $clean = ltrim($img, '/');
                $physicalPath = public_path($clean);
                if (is_file($physicalPath)) {
                    return asset($clean);
                }

                if (is_dir($physicalPath)) {
                    $files = @scandir($physicalPath);
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            if ($file === '.' || $file === '..') continue;
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($ext, $validExtensions, true)) {
                                return asset($clean . '/' . $file);
                            }
                        }
                    }
                }

                if (is_file(storage_path('app/public/' . $clean))) {
                    return asset('storage/' . $clean);
                }

                if (str_starts_with($clean, 'storage/')) {
                    return asset($clean);
                }

                if (str_starts_with($clean, 'raw_products/')) {
                    return asset($clean);
                }
            }

            return null;
        };

        $resolved = $findImage($imgPath);
        if ($resolved) {
            return $resolved;
        }

        if (!empty($subImgPath)) {
            $subResolved = $findImage($subImgPath);
            if ($subResolved) {
                return $subResolved;
            }
        }

        return null;
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
                $item->image_url = $this->resolveSpecImage($item->img_path, $item->subimg_path ?? null);
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
            'collections' => $loadMaster('auto_client_master', 'name', ['sno', 'id', 'name', 'code']),
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
        } elseif ($request->filled('existing_spec_image')) {
            $rawImg = $request->existing_spec_image;
            if (is_string($rawImg) && (str_starts_with($rawImg, '[') || str_starts_with($rawImg, '{'))) {
                $decoded = json_decode($rawImg, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $rawImg = is_array($decoded[0]) ? ($decoded[0]['url'] ?? reset($decoded[0])) : $decoded[0];
                }
            }
            $data['main_image'] = $rawImg;
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

    public function bulkDelete(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;

        $productIds = $request->input('product_ids', []);
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_filter(array_map('intval', (array) $productIds));

        if (empty($productIds)) {
            return redirect()->back()->with('error', 'Please select at least one product to delete.');
        }

        $products = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->whereIn('sno', $productIds)
            ->get();

        $count = 0;
        foreach ($products as $product) {
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
            $count++;
        }

        return redirect()->route('supplier.products.index')
            ->with('success', "{$count} product(s) deleted successfully.");
    }

    public function sendToPhotoSection(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;

        $productIds = $request->input('product_ids', []);
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_filter(array_map('intval', (array) $productIds));

        if (empty($productIds)) {
            return redirect()->back()->with('error', 'Please select at least one product.');
        }

        $products = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->whereIn('sno', $productIds)
            ->get();

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'No products found.');
        }

        $photoCount = 0;
        $specCount = 0;
        foreach ($products as $product) {
            if ($product->is_integrated) {
                $this->dispatchProductToPhotoSection($product, $user, $supplierId);
                $photoCount++;
            } else {
                $this->dispatchProductToSpecification($product, $user, $supplierId);
                $specCount++;
            }
        }

        $parts = [];
        if ($photoCount > 0) $parts[] = "{$photoCount} product(s) sent to Photo Section";
        if ($specCount > 0) $parts[] = "{$specCount} product(s) sent to Specification";

        return redirect()->route('supplier.products.index')
            ->with('success', implode(' and ', $parts) . ' successfully.');
    }

    public function sendToSpecification(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;

        $productIds = $request->input('product_ids', []);
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }
        $productIds = array_filter(array_map('intval', (array) $productIds));

        if (empty($productIds)) {
            return redirect()->back()->with('error', 'Please select at least one product to send to Specification.');
        }

        $products = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->whereIn('sno', $productIds)
            ->get();

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'No products found.');
        }

        $count = 0;
        foreach ($products as $product) {
            $this->dispatchProductToSpecification($product, $user, $supplierId);
            $count++;
        }

        return redirect()->route('supplier.products.index')
            ->with('success', "{$count} product(s) sent to Specification successfully.");
    }

    public function sendSingleToSpecification($id)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;

        $product = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        $this->dispatchProductToSpecification($product, $user, $supplierId);

        return redirect()->route('supplier.products.index')
            ->with('success', "Product '{$product->name}' sent to Specification successfully.");
    }

    public function sendSingleToPhotoSection($id)
    {
        $user = auth()->guard('supplier')->user();
        $supplierId = $user->supplier_id;

        $product = SupplierProduct::where('supplier_id', $supplierId)
            ->where('supplier_user_id', $user->sno)
            ->findOrFail($id);

        if (!$product->is_integrated) {
            $this->dispatchProductToSpecification($product, $user, $supplierId);
            return redirect()->route('supplier.products.index')
                ->with('success', "Product '{$product->name}' sent to Specification successfully.");
        }

        $this->dispatchProductToPhotoSection($product, $user, $supplierId);

        return redirect()->route('supplier.products.index')
            ->with('success', "Product '{$product->name}' sent to Photo Section successfully.");
    }

    protected function dispatchProductToSpecification(SupplierProduct $product, $user, $supplierId)
    {
        $product->update([
            'status' => 'sent_to_specification',
            'status_done' => 'sent_to_specification',
        ]);
    }

    protected function dispatchProductToPhotoSection(SupplierProduct $product, $user, $supplierId)
    {
        $sku = trim((string) $product->product_sku);

        // 1. Find matching specification in auto_designer_specification_master (by supplier_product_id or sku)
        $spec = null;
        if (!empty($product->sno)) {
            $spec = \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')
                ->where('supplier_product_id', $product->sno)
                ->orderByDesc('sno')
                ->first();
        }
        if (!$spec && !empty($sku)) {
            $spec = \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')
                ->where(function ($q) use ($sku) {
                    $q->where('sku', $sku)
                      ->orWhere('sku_supplier', $sku)
                      ->orWhere('barcode', $sku);
                })
                ->orderByDesc('sno')
                ->first();

            if (!$spec && preg_match('/ITEM-(\d+)/i', $sku, $matches)) {
                $spec = \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')
                    ->where(function ($q) use ($matches) {
                        $q->where('id', $matches[1])
                          ->orWhere('sno', $matches[1]);
                    })
                    ->first();
            }
        }

        $targetSpecId = null;

        if ($spec) {
            $updateData = [
                'is_sent_to_photo_section' => 0,
                'supplier_id' => $supplierId,
                'supplier_person_id' => $user->sno,
                'supplier_product_id' => $product->sno,
                'edatetime' => now(),
            ];

            // Preserve pending_specification status if not completed by admin
            if ($spec->status === 'pending_specification') {
                $updateData['status'] = 'pending_specification';
            } elseif (!empty($spec->status) && $spec->status !== 'done') {
                $updateData['status'] = '';
            }

            if ($product->price !== null && $product->price !== '') {
                $updateData['price'] = $product->price;
            }
            if ($product->sale_price !== null && $product->sale_price !== '') {
                $updateData['sale_price'] = $product->sale_price;
            }
            if ($product->min_price !== null && $product->min_price !== '') {
                $updateData['min_price'] = $product->min_price;
            }
            if (!empty($product->main_image)) {
                $updateData['img_path'] = $product->main_image;
            }
            if (!empty($product->sub_images)) {
                $updateData['subimg_path'] = $product->sub_images;
            }

            \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')
                ->where('sno', $spec->sno)
                ->update($updateData);

            $targetSpecId = $spec->id ?: $spec->sno;
        } else {
            // If no existing spec found, create a new row in auto_designer_specification_master with status = pending_specification
            $nextId = (\Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->max('id') ?? 0) + 1;
            $nextSno = (\Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->max('sno') ?? 0) + 1;
            $barcode = $sku ?: 'SP' . str_pad($product->sno, 6, '0', STR_PAD_LEFT);
            $targetSpecId = $nextId;

            \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->insert([
                'sno' => $nextSno,
                'id' => $nextId,
                'companyid' => $product->companyid ?? $user->companyid ?? 1,
                'subcompanyid' => $product->subcompanyid ?? $user->subcompanyid ?? 1,
                'projectid' => $product->projectid ?? $user->projectid ?? 1,
                'designer_name' => $product->designer,
                'item_type' => $product->item_type,
                'gender' => $product->gender,
                'item_name' => $product->item_name,
                'composition' => $product->composition,
                'colour' => $product->colour,
                'sizes' => $product->size,
                'yarn' => $product->yarn,
                'embellishment' => $product->embellishment,
                'manufacturing_process' => $product->manufacturing_process,
                'craftsman' => $product->craftsman,
                'manufecture' => $product->manufacture,
                'barcode' => $barcode,
                'sku' => $sku ?: $barcode,
                'sku_supplier' => $sku ?: $barcode,
                'img_path' => $product->main_image,
                'subimg_path' => $product->sub_images,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'min_price' => $product->min_price,
                'supplier_id' => $supplierId,
                'supplier_person_id' => $user->sno,
                'supplier_product_id' => $product->sno,
                'is_sent_to_photo_section' => 0,
                'status' => 'pending_specification',
                'edatetime' => now(),
            ]);
        }

        // Upsert description in AI_product_description only if supplier provided an actual description
        if ($targetSpecId && !empty(trim($product->description ?? '')) && \Illuminate\Support\Facades\Schema::hasTable('AI_product_description')) {
            \Illuminate\Support\Facades\DB::table('AI_product_description')->updateOrInsert(
                ['product_id' => $targetSpecId],
                [
                    'AI_product_name' => $product->name ?: ($sku ?: 'Product'),
                    'AI_product_description' => trim($product->description),
                ]
            );
        }

        // Mark supplier product as sent to photo section
        $product->update([
            'status' => 'sent_to_photo_section',
            'status_done' => 'sent_to_photo_section',
        ]);
    }

    /**
     * AJAX to add a new Item Name master from supplier panel
     */
    public function storeItemName(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $name = trim($request->name);
        $companyId = (int) ($user->companyid ?? 1);
        $subCompanyId = (int) ($user->subcompanyid ?? 1);
        $projectId = (int) ($user->projectid ?? 1);

        // Check if item name already exists (case-insensitive)
        $duplicate = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
            ->whereRaw('LOWER(itemname) = ?', [strtolower($name)])
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => true,
                'already_exists' => true,
                'message' => 'Item name already exists.',
                'id' => $duplicate->id,
                'name' => $duplicate->itemname,
                'code' => $duplicate->code,
            ]);
        }

        $code = null;
        if ($request->filled('code')) {
            $rawCode = strtoupper(preg_replace('/[^A-Z0-9]/', '', trim($request->code)));
            if (!empty($rawCode)) {
                if (strlen($rawCode) < 3) {
                    $rawCode = str_pad($rawCode, 3, 'X');
                } else {
                    $rawCode = substr($rawCode, 0, 4);
                }

                $codeExists = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                    ->where('code', $rawCode)
                    ->exists();

                if ($codeExists) {
                    return response()->json([
                        'success' => false,
                        'message' => "The code '{$rawCode}' is already in use by another item. Please choose a different code or leave blank to auto-generate."
                    ], 422);
                }

                $code = $rawCode;
            }
        }

        // If blank or not provided, auto-generate unique 3-letter code
        if (empty($code)) {
            $code = $this->generateItemCode($name, $companyId, $subCompanyId, $projectId);
        }

        $maxId = (int) \Illuminate\Support\Facades\DB::table('auto_itemname_master')->max('id');
        $nextId = $maxId + 1;

        $newSno = \Illuminate\Support\Facades\DB::table('auto_itemname_master')->insertGetId([
            'id' => $nextId,
            'itemname' => $name,
            'code' => $code,
            'companyid' => $companyId,
            'subcompanyid' => $subCompanyId,
            'projectid' => $projectId,
            'loginid' => $user->email ?? $user->name ?? 'supplier',
            'edatetime' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item name added successfully.',
            'id' => $nextId,
            'sno' => $newSno,
            'name' => $name,
            'code' => $code,
        ]);
    }

    /**
     * Helper to generate unique 3-char code for Item Name
     */
    private function generateItemCode(string $name, int $companyId, int $subCompanyId, int $projectId): string
    {
        $cleanName = strtoupper(preg_replace('/[^A-Z0-9 ]/', '', trim($name)));
        $words = preg_split('/\s+/', $cleanName);
        $code = '';
        foreach ($words as $word) {
            if ($word === '') continue;
            $code .= substr($word, 0, 1);
            if (strlen($code) >= 3) break;
        }

        $lettersOnly = preg_replace('/[^A-Z0-9]/', '', $cleanName);
        for ($i = 0; $i < strlen($lettersOnly) && strlen($code) < 3; $i++) {
            $char = $lettersOnly[$i];
            if (strpos($code, $char) === false) {
                $code .= $char;
            }
        }

        $code = substr(str_pad($code, 3, 'X'), 0, 3);
        $baseCode = $code;
        $counter = 1;

        while (true) {
            $exists = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                ->where('code', $code)
                ->exists();

            if (!$exists) break;

            $suffix = (string) $counter;
            $code = substr($baseCode, 0, 3 - strlen($suffix)) . $suffix;
            $counter++;
        }

        return strtoupper($code);
    }

    /**
     * AJAX endpoint to preview auto-generated SKU for supplier
     */
    public function generateSkuPreview(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        $supplier = \App\Models\Supplier::find($user->supplier_id);

        $itemId = $request->query('item_name_id');
        $itemCode = 'ITM';

        if ($itemId) {
            $itemRow = \Illuminate\Support\Facades\DB::table('auto_itemname_master')
                ->where('id', $itemId)
                ->orWhere('sno', $itemId)
                ->first();
            if ($itemRow && !empty($itemRow->code)) {
                $itemCode = strtoupper($itemRow->code);
            }
        }

        $supplierNickname = '';
        if ($supplier) {
            $supplierNickname = !empty($supplier->nickname) 
                ? trim($supplier->nickname) 
                : (!empty($supplier->name) ? substr(strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $supplier->name)), 0, 4) : 'SUP');
        }
        if (empty($supplierNickname)) $supplierNickname = 'SUP';

        $nextId = ((int) \Illuminate\Support\Facades\DB::table('auto_designer_specification_master')->max('id')) + 1;
        $generatedSku = strtoupper("{$itemCode}-{$supplierNickname}-{$nextId}");

        return response()->json([
            'success' => true,
            'sku' => $generatedSku,
            'next_id' => $nextId,
            'item_code' => $itemCode,
            'supplier_nickname' => $supplierNickname,
        ]);
    }
}

