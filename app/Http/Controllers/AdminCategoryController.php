<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\Supplier;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['supplier', 'parent'])->withCount('children')->orderBy('sno', 'desc');

        $retailerId = $request->input('retailer_id', $request->input('supplier_id'));
        if (!empty($retailerId)) {
            $query->where('supplier_id', $retailerId);
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
        $retailers = Supplier::where('type', 'retailer')->orderBy('name', 'asc')->get();
        $suppliers = $retailers; // For backward compatibility
        $parentCategories = Category::with('supplier')->orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories', 'retailers', 'suppliers', 'parentCategories'));
    }

    public function create()
    {
        $retailers = Supplier::where('type', 'retailer')->orderBy('name', 'asc')->get();
        $suppliers = $retailers;
        $parentCategories = Category::with('supplier')->orderBy('name', 'asc')->get();

        return view('admin.categories.create', compact('retailers', 'suppliers', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $retailerId = $request->input('retailer_id', $request->input('supplier_id'));
        $request->merge([
            'retailer_id' => $retailerId,
            'supplier_id' => $retailerId,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'retailer_id' => [
                'required',
                Rule::exists('suppliers', 'sno')->where(function ($query) {
                    $query->where('type', 'retailer');
                }),
            ],
            'parent_id' => 'nullable|exists:categories,sno',
        ], [
            'retailer_id.required' => 'The retailer field is required.',
            'retailer_id.exists' => 'The selected retailer is invalid.',
        ]);

        $user = auth()->user();

        Category::create([
            'name' => $request->name,
            'supplier_id' => $retailerId ?: null,
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
        $retailers = Supplier::where('type', 'retailer')->orderBy('name', 'asc')->get();
        $suppliers = $retailers;

        // Exclude current category and its descendants to avoid cyclic hierarchy
        $descendantIds = $this->getDescendantIds($category);
        $descendantIds[] = $category->sno;

        $parentCategories = Category::with('supplier')
            ->whereNotIn('sno', $descendantIds)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.edit', compact('category', 'retailers', 'suppliers', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $descendantIds = $this->getDescendantIds($category);
        $descendantIds[] = $category->sno;

        $retailerId = $request->input('retailer_id', $request->input('supplier_id'));
        $request->merge([
            'retailer_id' => $retailerId,
            'supplier_id' => $retailerId,
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'retailer_id' => [
                'required',
                Rule::exists('suppliers', 'sno')->where(function ($query) {
                    $query->where('type', 'retailer');
                }),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,sno',
                function ($attribute, $value, $fail) use ($descendantIds) {
                    if ($value && in_array($value, $descendantIds)) {
                        $fail('A category cannot have itself or any of its subcategories as a parent.');
                    }
                },
            ],
        ], [
            'retailer_id.required' => 'The retailer field is required.',
            'retailer_id.exists' => 'The selected retailer is invalid.',
        ]);

        $user = auth()->user();

        $data = [
            'name' => $request->name,
            'supplier_id' => $retailerId ?: null,
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
