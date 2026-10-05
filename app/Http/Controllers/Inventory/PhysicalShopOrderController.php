<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PhysicalShopOrderController extends Controller
{
    private function tenant(Request $request): array
    {
        $user = $request->user();

        return [
            'companyid' => $user->company_id ?? $user->companyid ?? session('companyid'),
            'subcompanyid' => $user->sub_company_id ?? $user->subcompanyid ?? session('subcompanyid'),
            'projectid' => $user->project_id ?? $user->projectid ?? session('projectid'),
            'user_id' => $user->id ?? session('user_id'),
            'loginid' => $user->mobile ?? $user->phone ?? $user->username ?? $user->email ?? (string) ($user->id ?? ''),
        ];
    }

    private function assertTenant(array $tenant): void
    {
        foreach (['companyid', 'subcompanyid', 'projectid'] as $field) {
            if ($tenant[$field] === null || $tenant[$field] === '') {
                abort(403, "Unable to determine {$field} for the logged-in user. Align tenant() with your existing login/session fields.");
            }
        }
    }

    private function basketsTable(): string { return 'physical_shop_order_baskets'; }
    private function itemsTable(): string { return 'physical_shop_order_items'; }

    public function index(Request $request)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);

        $baskets = $this->basketQuery($tenant)->orderByDesc('id')->get();
        $basketId = $request->integer('basket');
        $activeBasket = $basketId
            ? $this->basketQuery($tenant)->where('id', $basketId)->first()
            : $baskets->first();

        $items = collect();
        if ($activeBasket) {
            $items = $this->basketItemsQuery($tenant, $activeBasket->id)->get();
        }

        $shops = DB::table('tbl_shopmaster')
            ->where('companyid', $tenant['companyid'])
            ->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])
            ->orderBy('shop_name')
            ->get();

        $itemTypes = DB::table('auto_itemtype_master')
            ->where('companyid', $tenant['companyid'])->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])->orderBy('itemtype')->get(['id', 'itemtype']);
        $itemNames = DB::table('auto_itemname_master')
            ->where('companyid', $tenant['companyid'])->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])->orderBy('itemname')->get(['id', 'itemname']);
        $genders = DB::table('auto_gender_master')
            ->where('companyid', $tenant['companyid'])->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])->orderBy('name')->get(['id', 'name']);
        $compositions = DB::table('auto_composition_master_stock')
            ->where('companyid', $tenant['companyid'])->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])->orderBy('composition_details')->get(['id', 'composition_details']);
        $sizes = DB::table('auto_size_master')
            ->where('companyid', $tenant['companyid'])->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])->orderBy('size')->get(['id', 'size']);

        return view('inventory.physical-shop-orders.index', compact(
            'baskets', 'activeBasket', 'items', 'shops',
            'itemTypes', 'itemNames', 'genders', 'compositions', 'sizes'
        ));
    }

    public function products(Request $request)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $query = $this->stockQuery($tenant);

        foreach ([
            'item_type' => 'sm.item_type',
            'item_name' => 'sm.item_name',
            'gender' => 'sm.gender',
            'composition' => 'sm.composition',
            'size' => 'sm.sizes',
        ] as $key => $column) {
            $value = trim((string) $request->input($key, ''));
            if ($value !== '' && ctype_digit($value)) {
                $query->where($column, (int) $value);
            }
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('vs.barcode', 'like', "%{$search}%")
                    ->orWhere('sm.sku', 'like', "%{$search}%")
                    ->orWhere('inm.itemname', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->selectRaw('
                vs.barcode,
                sm.sku,
                sm.img_path as image_path,
                inm.itemname as product_name,
                itm.itemtype as item_type_name,
                gm.name as gender_name,
                cm.composition_details as composition_name,
                szm.size as size_name,
                SUM(COALESCE(vs.quantity_received,0) - COALESCE(vs.send_qty,0)) as available_qty
            ')
            ->groupBy(
                'vs.barcode', 'sm.sku', 'sm.img_path', 'inm.itemname',
                'itm.itemtype', 'gm.name', 'cm.composition_details', 'szm.size'
            )
            ->havingRaw('SUM(COALESCE(vs.quantity_received,0) - COALESCE(vs.send_qty,0)) > 0')
            ->orderBy('inm.itemname')
            ->paginate(24)
            ->withQueryString();

        return response()->json($products);
    }

    public function storeBasket(Request $request)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $data = $request->validate(['basket_name' => ['required', 'string', 'max:150']]);

        if (empty($tenant['user_id'])) {
            throw ValidationException::withMessages([
                'basket_name' => 'Unable to identify the logged-in user. Check your authentication user ID/session mapping.',
            ]);
        }

        $id = DB::table($this->basketsTable())->insertGetId([
            'company_id' => $tenant['companyid'],
            'sub_company_id' => $tenant['subcompanyid'],
            'project_id' => $tenant['projectid'],
            'user_id' => $tenant['user_id'],
            'basket_name' => $data['basket_name'],
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('inventory.physical-shop-orders.index', ['basket' => $id])
            ->with('success', 'Basket created. Stock has not been deducted.');
    }

    public function showBasket(Request $request, $basket)
{
    abort_unless(ctype_digit((string) $basket), 404);

    $basket = (int) $basket;

    $tenant = $this->tenant($request);
    $this->assertTenant($tenant);

    $row = $this->basketQuery($tenant)
        ->where('id', $basket)
        ->firstOrFail();

    return view('inventory.physical-shop-orders.show', [
        'basket' => $row,
        'items' => $this->basketItemsQuery($tenant, $basket)->get(),
    ]);
}

    public function addItem(Request $request, int $basket)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($tenant, $basket, $data) {
            $basketRow = $this->basketQuery($tenant)->where('id', $basket)->lockForUpdate()->firstOrFail();
            if ($basketRow->status !== 'draft') {
                throw ValidationException::withMessages(['basket' => 'Only draft baskets can be edited.']);
            }

            $existing = DB::table($this->itemsTable())->where('basket_id', $basket)
                ->where('barcode', $data['barcode'])->lockForUpdate()->first();
            $newQty = (int) $data['quantity'] + (int) ($existing->quantity ?? 0);

            $available = $this->availableForBarcode($tenant, $data['barcode'], true);
            if ($newQty > $available) {
                throw ValidationException::withMessages([
                    'quantity' => "Requested quantity ({$newQty}) exceeds available stock ({$available}).",
                ]);
            }

            $product = $this->stockQuery($tenant)->where('vs.barcode', $data['barcode'])
                ->selectRaw('vs.barcode, sm.sku, sm.img_path as image_path, inm.itemname as product_name, itm.itemtype as item_type_name, gm.name as gender_name, cm.composition_details as composition_name, szm.size as size_name')
                ->groupBy('vs.barcode', 'sm.sku', 'sm.img_path', 'inm.itemname', 'itm.itemtype', 'gm.name', 'cm.composition_details', 'szm.size')
                ->first();

            if (!$product) {
                throw ValidationException::withMessages(['barcode' => 'This barcode is not available in your company/project stock.']);
            }

            if ($existing) {
                DB::table($this->itemsTable())->where('id', $existing->id)->update([
                    'quantity' => $newQty,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table($this->itemsTable())->insert([
                    'basket_id' => $basket,
                    'company_id' => $tenant['companyid'],
                    'sub_company_id' => $tenant['subcompanyid'],
                    'project_id' => $tenant['projectid'],
                    'barcode' => $product->barcode,
                    'sku' => $product->sku,
                    'product_name' => $product->product_name,
                    'item_type_name' => $product->item_type_name,
                    'gender_name' => $product->gender_name,
                    'composition_name' => $product->composition_name,
                    'size_name' => $product->size_name,
                    'image_path' => $product->image_path,
                    'quantity' => (int) $data['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Product added to basket. Stock has not been deducted.');
    }

    public function updateItem(Request $request, int $basket, int $item)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        DB::transaction(function () use ($tenant, $basket, $item, $data) {
            $basketRow = $this->basketQuery($tenant)->where('id', $basket)->lockForUpdate()->firstOrFail();
            if ($basketRow->status !== 'draft') {
                throw ValidationException::withMessages(['basket' => 'Only draft baskets can be edited.']);
            }

            $row = $this->basketItemsQuery($tenant, $basket)->where('bi.id', $item)->lockForUpdate()->first();
            abort_unless($row, 404);

            $available = $this->availableForBarcode($tenant, $row->barcode, true);
            if ((int) $data['quantity'] > $available) {
                throw ValidationException::withMessages([
                    'quantity' => "Requested quantity exceeds available stock ({$available}).",
                ]);
            }

            DB::table($this->itemsTable())->where('id', $item)->where('basket_id', $basket)->update([
                'quantity' => (int) $data['quantity'],
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Basket quantity updated.');
    }

    public function removeItem(Request $request, int $basket, int $item)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $row = $this->basketQuery($tenant)->where('id', $basket)->firstOrFail();
        abort_if($row->status !== 'draft', 422, 'Only draft baskets can be edited.');

        DB::table($this->itemsTable())->where('basket_id', $basket)->where('id', $item)->delete();
        return back()->with('success', 'Product removed from basket.');
    }

    public function saveDraft(Request $request, int $basket)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $row = $this->basketQuery($tenant)->where('id', $basket)->firstOrFail();
        abort_if($row->status !== 'draft', 422, 'Only draft baskets can be saved.');

        DB::table($this->basketsTable())->where('id', $basket)->update(['updated_at' => now()]);
        return back()->with('success', 'Draft saved. Stock has not been deducted.');
    }

    /**
     * Finalize several draft baskets as one physical shop order.
     * Duplicate barcodes across baskets are combined before stock validation/deduction.
     */
    public function confirmMultipleBaskets(Request $request)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);

        $data = $request->validate([
            'basket_ids' => ['required', 'array', 'min:1'],
            'basket_ids.*' => ['required', 'integer', 'distinct'],
            'shopid' => ['required', 'integer'],
            'box_no' => ['required', 'string', 'max:200'],
        ]);

        $orderNo = DB::transaction(function () use ($tenant, $data) {
            $basketIds = array_values(array_unique(array_map('intval', $data['basket_ids'])));

            $baskets = $this->basketQuery($tenant)
                ->whereIn('id', $basketIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($baskets->count() !== count($basketIds)) {
                throw ValidationException::withMessages([
                    'basket_ids' => 'One or more selected baskets were not found in your company/project.',
                ]);
            }

            $notDraft = $baskets->first(fn ($basket) => $basket->status !== 'draft');
            if ($notDraft) {
                throw ValidationException::withMessages([
                    'basket_ids' => "Basket \"{$notDraft->basket_name}\" is not a draft. Select draft baskets only.",
                ]);
            }

            $shop = DB::table('tbl_shopmaster')
                ->where('id', $data['shopid'])
                ->where('companyid', $tenant['companyid'])
                ->where('subcompanyid', $tenant['subcompanyid'])
                ->where('projectid', $tenant['projectid'])
                ->first();

            if (!$shop) {
                throw ValidationException::withMessages([
                    'shopid' => 'Please select a valid shop for your company/project.',
                ]);
            }

            $items = collect();
            foreach ($basketIds as $basketId) {
                $items = $items->concat(
                    $this->basketItemsQuery($tenant, $basketId)->lockForUpdate()->get()
                );
            }

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'basket_ids' => 'The selected baskets contain no garments.',
                ]);
            }

            // Combine quantities by barcode across every selected basket.
            $combinedItems = $items->groupBy('barcode')->map(function ($rows, $barcode) {
                $first = $rows->first();
                $first->quantity = (int) $rows->sum(fn ($row) => (int) $row->quantity);
                $first->barcode = (string) $barcode;
                return $first;
            })->values();

            // Validate all aggregated quantities before creating the order or changing stock.
            foreach ($combinedItems as $item) {
                $available = $this->availableForBarcode($tenant, $item->barcode, true);
                if ((int) $item->quantity > $available) {
                    throw ValidationException::withMessages([
                        'stock' => "Barcode {$item->barcode}: combined quantity {$item->quantity} exceeds available stock ({$available}).",
                    ]);
                }
            }

            $lastId = (int) DB::table('tbl_orderfromshop')
                ->where('companyid', $tenant['companyid'])
                ->where('subcompanyid', $tenant['subcompanyid'])
                ->where('projectid', $tenant['projectid'])
                ->lockForUpdate()
                ->max('id');

            $nextId = $lastId + 1;
            $orderNo = 'Order-' . $nextId . '-' . $shop->shop_name . '-' . $shop->id;
            $today = Carbon::today()->toDateString();

            DB::table('tbl_orderfromshop')->insert([
                'companyid' => $tenant['companyid'],
                'subcompanyid' => $tenant['subcompanyid'],
                'projectid' => $tenant['projectid'],
                'id' => $nextId,
                'shopid' => $shop->id,
                'orderno' => $orderNo,
                'orderdate' => $today,
                'status' => 'completed',
                'approve_status' => 'Send For Put In The Box',
                'tedit' => now(),
                'loginid' => $tenant['loginid'],
                'orderupdated_date' => $today,
            ]);

            foreach ($combinedItems as $item) {
                DB::table('tbl_orderfromshopdetails')->insert([
                    'companyid' => $tenant['companyid'],
                    'subcompanyid' => $tenant['subcompanyid'],
                    'projectid' => $tenant['projectid'],
                    'id' => $nextId,
                    'shopid' => $shop->id,
                    'orderno' => $orderNo,
                    'barcode' => $item->barcode,
                    'qty' => (int) $item->quantity,
                    'status' => 'completed',
                    'tedit' => now(),
                    'remarks' => 'Physical Shop Order; Box: ' . $data['box_no']
                        . '; Baskets: ' . $baskets->pluck('basket_name')->implode(', '),
                ]);
            }

            // Deduct the aggregated quantity once per barcode.
            foreach ($combinedItems as $item) {
                $remaining = (int) $item->quantity;
                $stockRows = DB::table('vendor_stock')
                    ->where('companyid', $tenant['companyid'])
                    ->where('subcompanyid', $tenant['subcompanyid'])
                    ->where('projectid', $tenant['projectid'])
                    ->where('barcode', $item->barcode)
                    ->whereRaw('COALESCE(quantity_received,0) > COALESCE(send_qty,0)')
                    ->orderBy('sno')
                    ->lockForUpdate()
                    ->get();

                foreach ($stockRows as $stock) {
                    if ($remaining <= 0) break;
                    $availableRow = max(0, (int) $stock->quantity_received - (int) $stock->send_qty);
                    $take = min($remaining, $availableRow);
                    if ($take > 0) {
                        $updates = ['send_qty' => (int) $stock->send_qty + $take];
                        if (Schema::hasColumn('vendor_stock', 'updated_at')) {
                            $updates['updated_at'] = now();
                        }
                        DB::table('vendor_stock')->where('sno', $stock->sno)->update($updates);
                        $remaining -= $take;
                    }
                }

                if ($remaining > 0) {
                    throw ValidationException::withMessages([
                        'stock' => "Stock changed while finalizing barcode {$item->barcode}. Please retry.",
                    ]);
                }
            }

            // Mark every selected basket as confirmed.
            // order_number is UNIQUE in this table, so do NOT store the same
            // combined order number on multiple basket rows. The authoritative
            // order number remains in tbl_orderfromshop. Keep basket-level
            // order_number unique by assigning the shared order number only
            // when one basket is selected; for combined orders, leave it null.
            $basketUpdate = [
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'updated_at' => now(),
            ];

            if (count($basketIds) === 1) {
                $basketUpdate['order_number'] = $orderNo;
            }

            DB::table($this->basketsTable())
                ->whereIn('id', $basketIds)
                ->where('company_id', $tenant['companyid'])
                ->where('sub_company_id', $tenant['subcompanyid'])
                ->where('project_id', $tenant['projectid'])
                ->update($basketUpdate);

            return $orderNo;
        });

        return redirect()->route('inventory.physical-shop-orders.index')
            ->with('success', "Combined order {$orderNo} created successfully from " . count($data['basket_ids']) . ' baskets.');
    }

    public function confirmOrder(Request $request, int $basket)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);
        $data = $request->validate([
            'shopid' => ['required', 'integer'],
            'box_no' => ['required', 'string', 'max:200'],
        ]);

        $orderNo = DB::transaction(function () use ($tenant, $basket, $data) {
            $basketRow = $this->basketQuery($tenant)->where('id', $basket)->lockForUpdate()->firstOrFail();
            if ($basketRow->status !== 'draft') {
                throw ValidationException::withMessages(['basket' => 'This basket has already been finalized.']);
            }

            $shop = DB::table('tbl_shopmaster')
                ->where('id', $data['shopid'])
                ->where('companyid', $tenant['companyid'])
                ->where('subcompanyid', $tenant['subcompanyid'])
                ->where('projectid', $tenant['projectid'])
                ->first();

            if (!$shop) {
                throw ValidationException::withMessages(['shopid' => 'Please select a valid shop for your company/project.']);
            }

            $items = $this->basketItemsQuery($tenant, $basket)->lockForUpdate()->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['basket' => 'Add at least one garment before finalizing.']);
            }

            // Check all lines before writing any order or changing stock.
            foreach ($items as $item) {
                $available = $this->availableForBarcode($tenant, $item->barcode, true);
                if ((int) $item->quantity > $available) {
                    throw ValidationException::withMessages([
                        'stock' => "Barcode {$item->barcode}: requested {$item->quantity}, but only {$available} are currently available.",
                    ]);
                }
            }

            $lastId = (int) DB::table('tbl_orderfromshop')
                ->where('companyid', $tenant['companyid'])
                ->where('subcompanyid', $tenant['subcompanyid'])
                ->where('projectid', $tenant['projectid'])
                ->lockForUpdate()
                ->max('id');

            $nextId = $lastId + 1;
            $orderNo = 'Order-' . $nextId . '-' . $shop->shop_name . '-' . $shop->id;
            $today = Carbon::today()->toDateString();

            DB::table('tbl_orderfromshop')->insert([
                'companyid' => $tenant['companyid'],
                'subcompanyid' => $tenant['subcompanyid'],
                'projectid' => $tenant['projectid'],
                'id' => $nextId,
                'shopid' => $shop->id,
                'orderno' => $orderNo,
                'orderdate' => $today,
                'status' => 'completed',
                'approve_status' => 'Send For Put In The Box',
                'tedit' => now(),
                'loginid' => $tenant['loginid'],
                'orderupdated_date' => $today,
            ]);

            foreach ($items as $item) {
                DB::table('tbl_orderfromshopdetails')->insert([
                    'companyid' => $tenant['companyid'],
                    'subcompanyid' => $tenant['subcompanyid'],
                    'projectid' => $tenant['projectid'],
                    'id' => $nextId,
                    'shopid' => $shop->id,
                    'orderno' => $orderNo,
                    'barcode' => $item->barcode,
                    'qty' => (int) $item->quantity,
                    'status' => 'completed',
                    'tedit' => now(),
                    'remarks' => 'Physical Shop Order; Box: ' . $data['box_no'],
                ]);
            }

            // Current inventory convention: available = quantity_received - send_qty.
            foreach ($items as $item) {
                $remaining = (int) $item->quantity;
                $stockRows = DB::table('vendor_stock')
                    ->where('companyid', $tenant['companyid'])
                    ->where('subcompanyid', $tenant['subcompanyid'])
                    ->where('projectid', $tenant['projectid'])
                    ->where('barcode', $item->barcode)
                    ->whereRaw('COALESCE(quantity_received,0) > COALESCE(send_qty,0)')
                    ->orderBy('sno')
                    ->lockForUpdate()
                    ->get();

                foreach ($stockRows as $stock) {
                    if ($remaining <= 0) break;
                    $availableRow = max(0, (int) $stock->quantity_received - (int) $stock->send_qty);
                    $take = min($remaining, $availableRow);
                    if ($take > 0) {
                        $updates = ['send_qty' => (int) $stock->send_qty + $take];
                        if (Schema::hasColumn('vendor_stock', 'updated_at')) {
                            $updates['updated_at'] = now();
                        }
                        DB::table('vendor_stock')->where('sno', $stock->sno)->update($updates);
                        $remaining -= $take;
                    }
                }

                if ($remaining > 0) {
                    throw ValidationException::withMessages([
                        'stock' => "Stock changed while finalizing barcode {$item->barcode}. Please retry.",
                    ]);
                }
            }

            DB::table($this->basketsTable())->where('id', $basket)->update([
                'status' => 'confirmed',
                'order_number' => $orderNo,
                'confirmed_at' => now(),
                'updated_at' => now(),
            ]);

            return $orderNo;
        });

        return redirect()->route('inventory.physical-shop-orders.index')
            ->with('success', "Order {$orderNo} created successfully.");
    }

    public function orders(Request $request)
    {
        $tenant = $this->tenant($request);
        $this->assertTenant($tenant);

        $orders = DB::table('tbl_orderfromshop as o')
            ->leftJoin('tbl_shopmaster as s', function ($join) {
                $join->on('s.id', '=', 'o.shopid')
                    ->on('s.companyid', '=', 'o.companyid')
                    ->on('s.subcompanyid', '=', 'o.subcompanyid')
                    ->on('s.projectid', '=', 'o.projectid');
            })
            ->where('o.companyid', $tenant['companyid'])
            ->where('o.subcompanyid', $tenant['subcompanyid'])
            ->where('o.projectid', $tenant['projectid'])
            ->select('o.*', 's.shop_name')
            ->orderByDesc('o.sno')
            ->paginate(25);

        return view('inventory.physical-shop-orders.orders', compact('orders'));
    }

    
public function showOrder(Request $request, $order)
{
    abort_unless(ctype_digit((string) $order), 404);
    $order = (int) $order;

    $tenant = $this->tenant($request);
    $this->assertTenant($tenant);

    $header = DB::table('tbl_orderfromshop as o')
        ->leftJoin('tbl_shopmaster as s', function ($join) {
            $join->on('s.id', '=', 'o.shopid')
                ->on('s.companyid', '=', 'o.companyid')
                ->on('s.subcompanyid', '=', 'o.subcompanyid')
                ->on('s.projectid', '=', 'o.projectid');
        })
        ->where('o.sno', $order)
        ->where('o.companyid', $tenant['companyid'])
        ->where('o.subcompanyid', $tenant['subcompanyid'])
        ->where('o.projectid', $tenant['projectid'])
        ->select('o.*', 's.shop_name')
        ->firstOrFail();

    $items = DB::table('tbl_orderfromshopdetails as d')
        ->leftJoin('auto_designer_specification_master as sm', function ($join) use ($tenant) {
            $join->on('sm.barcode', '=', 'd.barcode')
                ->where('sm.companyid', '=', $tenant['companyid'])
                ->where('sm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('sm.projectid', '=', $tenant['projectid']);
        })
        ->leftJoin('auto_itemtype_master as itm', function ($join) use ($tenant) {
            $join->on('itm.id', '=', 'sm.item_type')
                ->where('itm.companyid', '=', $tenant['companyid'])
                ->where('itm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('itm.projectid', '=', $tenant['projectid']);
        })
        ->leftJoin('auto_gender_master as gm', function ($join) use ($tenant) {
            $join->on('gm.id', '=', 'sm.gender')
                ->where('gm.companyid', '=', $tenant['companyid'])
                ->where('gm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('gm.projectid', '=', $tenant['projectid']);
        })
        ->leftJoin('auto_itemname_master as inm', function ($join) use ($tenant) {
            $join->on('inm.id', '=', 'sm.item_name')
                ->where('inm.companyid', '=', $tenant['companyid'])
                ->where('inm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('inm.projectid', '=', $tenant['projectid']);
        })
        ->leftJoin('auto_composition_master_stock as cm', function ($join) use ($tenant) {
            $join->on('cm.id', '=', 'sm.composition')
                ->where('cm.companyid', '=', $tenant['companyid'])
                ->where('cm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('cm.projectid', '=', $tenant['projectid']);
        })
        ->leftJoin('auto_size_master as szm', function ($join) use ($tenant) {
            $join->on('szm.id', '=', 'sm.sizes')
                ->where('szm.companyid', '=', $tenant['companyid'])
                ->where('szm.subcompanyid', '=', $tenant['subcompanyid'])
                ->where('szm.projectid', '=', $tenant['projectid']);
        })
        ->where('d.companyid', $tenant['companyid'])
        ->where('d.subcompanyid', $tenant['subcompanyid'])
        ->where('d.projectid', $tenant['projectid'])
        ->where('d.orderno', $header->orderno)
        ->select([
            'd.barcode',
            'd.qty',
            'd.remarks',
            'd.status',
            'sm.sku',
            'sm.img_path',
            'itm.itemtype as item_type_name',
            'gm.name as gender_name',
            'inm.itemname as item_name',
            'cm.composition_details as composition_name',
            'szm.size as size_name',
        ])
        ->orderBy('d.barcode')
        ->get();

    return view('inventory.physical-shop-orders.show-order', compact(
        'header',
        'items'
    ));
}

    private function basketQuery(array $tenant)
    {
        return DB::table($this->basketsTable())
            ->where('company_id', $tenant['companyid'])
            ->where('sub_company_id', $tenant['subcompanyid'])
            ->where('project_id', $tenant['projectid']);
    }

    private function basketItemsQuery(array $tenant, int $basketId)
    {
        return DB::table($this->itemsTable() . ' as bi')
            ->join($this->basketsTable() . ' as b', 'b.id', '=', 'bi.basket_id')
            ->where('bi.basket_id', $basketId)
            ->where('bi.company_id', $tenant['companyid'])
            ->where('bi.sub_company_id', $tenant['subcompanyid'])
            ->where('bi.project_id', $tenant['projectid'])
            ->where('b.company_id', $tenant['companyid'])
            ->where('b.sub_company_id', $tenant['subcompanyid'])
            ->where('b.project_id', $tenant['projectid'])
            ->select('bi.*');
    }

    private function availableForBarcode(array $tenant, string $barcode, bool $lock = false): int
    {
        $query = DB::table('vendor_stock')
            ->where('companyid', $tenant['companyid'])
            ->where('subcompanyid', $tenant['subcompanyid'])
            ->where('projectid', $tenant['projectid'])
            ->where('barcode', $barcode);

        if ($lock) $query->lockForUpdate();

        return max(0, (int) $query->selectRaw(
            'COALESCE(SUM(COALESCE(quantity_received,0) - COALESCE(send_qty,0)),0) as qty'
        )->value('qty'));
    }

    private function stockQuery(array $tenant)
    {
        return DB::table('vendor_stock as vs')
            ->join('auto_designer_specification_master as sm', function ($join) use ($tenant) {
                $join->on('sm.barcode', '=', 'vs.barcode')
                    ->where('sm.companyid', '=', $tenant['companyid'])
                    ->where('sm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('sm.projectid', '=', $tenant['projectid']);
            })
            ->leftJoin('auto_itemtype_master as itm', function ($join) use ($tenant) {
                $join->on('itm.id', '=', 'sm.item_type')
                    ->where('itm.companyid', '=', $tenant['companyid'])
                    ->where('itm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('itm.projectid', '=', $tenant['projectid']);
            })
            ->leftJoin('auto_gender_master as gm', function ($join) use ($tenant) {
                $join->on('gm.id', '=', 'sm.gender')
                    ->where('gm.companyid', '=', $tenant['companyid'])
                    ->where('gm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('gm.projectid', '=', $tenant['projectid']);
            })
            ->leftJoin('auto_itemname_master as inm', function ($join) use ($tenant) {
                $join->on('inm.id', '=', 'sm.item_name')
                    ->where('inm.companyid', '=', $tenant['companyid'])
                    ->where('inm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('inm.projectid', '=', $tenant['projectid']);
            })
            ->leftJoin('auto_composition_master_stock as cm', function ($join) use ($tenant) {
                $join->on('cm.id', '=', 'sm.composition')
                    ->where('cm.companyid', '=', $tenant['companyid'])
                    ->where('cm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('cm.projectid', '=', $tenant['projectid']);
            })
            ->leftJoin('auto_size_master as szm', function ($join) use ($tenant) {
                $join->on('szm.id', '=', 'sm.sizes')
                    ->where('szm.companyid', '=', $tenant['companyid'])
                    ->where('szm.subcompanyid', '=', $tenant['subcompanyid'])
                    ->where('szm.projectid', '=', $tenant['projectid']);
            })
            ->where('vs.companyid', $tenant['companyid'])
            ->where('vs.subcompanyid', $tenant['subcompanyid'])
            ->where('vs.projectid', $tenant['projectid'])
            ->whereRaw('COALESCE(vs.quantity_received,0) > COALESCE(vs.send_qty,0)');
    }
}
