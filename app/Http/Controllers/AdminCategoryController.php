<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Supplier;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('supplier')->orderBy('sno', 'desc');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('admin.categories.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,sno',
        ]);

        $user = auth()->user();

        Category::create([
            'name' => $request->name,
            'supplier_id' => $request->supplier_id ?: null,
            'status' => 'active',
            'countryid' => $user->country_id ?? null,
            'companyid' => $user->company_id ?? null,
            'subcompanyid' => $user->sub_company_id ?? null,
            'projectid' => $user->project_id ?? null,
            'subprojectid' => $user->sub_project_id ?? null,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $suppliers = Supplier::orderBy('name', 'asc')->get();

        return view('admin.categories.edit', compact('category', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,sno',
        ]);

        $user = auth()->user();

        $data = [
            'name' => $request->name,
            'supplier_id' => $request->supplier_id ?: null,
            'countryid' => $user->country_id ?? null,
            'companyid' => $user->company_id ?? null,
            'subcompanyid' => $user->sub_company_id ?? null,
            'projectid' => $user->project_id ?? null,
            'subprojectid' => $user->sub_project_id ?? null,
        ];

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
