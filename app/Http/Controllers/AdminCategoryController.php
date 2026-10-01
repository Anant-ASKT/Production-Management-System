<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Supplier;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['supplier', 'parent'])->withCount('children')->orderBy('sno', 'desc');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $parentCategories = Category::with('supplier')->orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories', 'suppliers', 'parentCategories'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $parentCategories = Category::with('supplier')->orderBy('name', 'asc')->get();

        return view('admin.categories.create', compact('suppliers', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,sno',
            'parent_id' => 'nullable|exists:categories,sno',
        ]);

        $user = auth()->user();

        Category::create([
            'name' => $request->name,
            'supplier_id' => $request->supplier_id ?: null,
            'parent_id' => $request->parent_id ?: null,
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

        // Exclude current category and its descendants to avoid cyclic hierarchy
        $descendantIds = $this->getDescendantIds($category);
        $descendantIds[] = $category->sno;

        $parentCategories = Category::with('supplier')
            ->whereNotIn('sno', $descendantIds)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.edit', compact('category', 'suppliers', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $descendantIds = $this->getDescendantIds($category);
        $descendantIds[] = $category->sno;

        $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,sno',
            'parent_id' => [
                'nullable',
                'exists:categories,sno',
                function ($attribute, $value, $fail) use ($descendantIds) {
                    if ($value && in_array($value, $descendantIds)) {
                        $fail('A category cannot have itself or any of its subcategories as a parent.');
                    }
                },
            ],
        ]);

        $user = auth()->user();

        $data = [
            'name' => $request->name,
            'supplier_id' => $request->supplier_id ?: null,
            'parent_id' => $request->parent_id ?: null,
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
        $childrenCount = $category->children()->count();
        $category->delete();

        $msg = 'Category deleted successfully.';
        if ($childrenCount > 0) {
            $msg .= " Along with {$childrenCount} subcategory(ies).";
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    /**
     * Recursively retrieve all descendant IDs for a category
     */
    private function getDescendantIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->sno;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}
