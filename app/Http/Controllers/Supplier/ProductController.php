<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierProduct;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierProduct::where('supplier_id', auth()->guard('supplier')->id());

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
        return view('supplier.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sub_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $supplier = auth()->guard('supplier')->user();

        $data = $request->except(['main_image', 'sub_images', '_token']);
        $data['stock'] = (isset($data['stock']) && $data['stock'] !== '' && is_numeric($data['stock'])) ? (int) $data['stock'] : 1;
        $data['supplier_id'] = $supplier->sno;
        $data['countryid'] = $supplier->countryid;
        $data['companyid'] = $supplier->companyid;
        $data['subcompanyid'] = $supplier->subcompanyid;
        $data['projectid'] = $supplier->projectid;
        $data['subprojectid'] = $supplier->subprojectid;

        $supplierId = $supplier->sno;

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "raw_products/{$supplierId}/main_image";
            $file->move(public_path($path), $filename);
            $data['main_image'] = $path . '/' . $filename;
        }

        if ($request->hasFile('sub_images')) {
            $subImages = [];
            foreach ($request->file('sub_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = "raw_products/{$supplierId}/additional_images";
                $file->move(public_path($path), $filename);
                $subImages[] = $path . '/' . $filename;
            }
            $data['sub_images'] = json_encode($subImages);
        }

        SupplierProduct::create($data);

        return redirect()->route('supplier.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = SupplierProduct::where('supplier_id', auth()->guard('supplier')->id())->findOrFail($id);
        return view('supplier.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $supplierId = auth()->guard('supplier')->id();
        $product = SupplierProduct::where('supplier_id', $supplierId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sub_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['main_image', 'sub_images', '_token', '_method']);
        $data['stock'] = (isset($data['stock']) && $data['stock'] !== '' && is_numeric($data['stock'])) ? (int) $data['stock'] : 1;

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "raw_products/{$supplierId}/main_image";
            $file->move(public_path($path), $filename);
            $data['main_image'] = $path . '/' . $filename;
            
            // Note: you could optionally delete the old image here if needed
        }

        if ($request->hasFile('sub_images')) {
            $subImages = json_decode($product->sub_images, true) ?? [];
            foreach ($request->file('sub_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = "raw_products/{$supplierId}/additional_images";
                $file->move(public_path($path), $filename);
                $subImages[] = $path . '/' . $filename;
            }
            $data['sub_images'] = json_encode($subImages);
        }

        $product->update($data);

        return redirect()->route('supplier.products.index')->with('success', 'Product updated successfully.');
    }

    public function deleteImage(Request $request, $id)
    {
        $supplierId = auth()->guard('supplier')->id();
        $product = SupplierProduct::where('supplier_id', $supplierId)->findOrFail($id);

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
        $supplierId = auth()->guard('supplier')->id();
        $product = SupplierProduct::where('supplier_id', $supplierId)->findOrFail($id);

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
