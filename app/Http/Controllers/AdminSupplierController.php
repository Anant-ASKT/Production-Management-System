<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('users')->orderBy('sno', 'desc')->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|size:3|alpha|unique:suppliers,nickname',
            'user_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:supplier_users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:Employee,Owner',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'nullable|url',
            'consumer_key' => 'nullable|string',
            'consumer_secret' => 'nullable|string',
        ], [
            'nickname.size' => 'Nick Name must be exactly 3 letters (e.g. ABC).',
            'nickname.alpha' => 'Nick Name must contain only letters.',
            'nickname.unique' => 'This Nick Name has already been taken.',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $supplier = Supplier::create([
                'name' => $request->name,
                'nickname' => strtoupper(trim($request->nickname)),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'store_url' => $request->store_url,
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'countryid' => $user->country_id ?? null,
                'companyid' => $user->company_id ?? null,
                'subcompanyid' => $user->sub_company_id ?? null,
                'projectid' => $user->project_id ?? null,
                'subprojectid' => $user->sub_project_id ?? null,
            ]);

            // Create initial user for the supplier
            SupplierUser::create([
                'supplier_id' => $supplier->sno,
                'name' => $request->filled('user_name') ? $request->user_name : $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role ?? 'Owner',
                'status' => 'active',
                'countryid' => $user->country_id ?? null,
                'companyid' => $user->company_id ?? null,
                'subcompanyid' => $user->sub_company_id ?? null,
                'projectid' => $user->project_id ?? null,
                'subprojectid' => $user->sub_project_id ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.suppliers.index')->with('success', 'Supplier and initial user account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create supplier: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $supplier = Supplier::with('users')->findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|size:3|alpha|unique:suppliers,nickname,' . $id . ',sno',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'nullable|url',
            'consumer_key' => 'nullable|string',
            'consumer_secret' => 'nullable|string',
        ], [
            'nickname.size' => 'Nick Name must be exactly 3 letters (e.g. ABC).',
            'nickname.alpha' => 'Nick Name must contain only letters.',
            'nickname.unique' => 'This Nick Name has already been taken.',
        ]);

        $user = auth()->user();

        $data = $request->only(['name', 'phone', 'address', 'store_url', 'consumer_key', 'consumer_secret']);
        $data['nickname'] = strtoupper(trim($request->nickname));
        $data['countryid'] = $user->country_id ?? null;
        $data['companyid'] = $user->company_id ?? null;
        $data['subcompanyid'] = $user->sub_company_id ?? null;
        $data['projectid'] = $user->project_id ?? null;
        $data['subprojectid'] = $user->sub_project_id ?? null;

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Add a new user under a specific supplier.
     */
    public function addUser(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:supplier_users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Employee,Owner',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();

        SupplierUser::create([
            'supplier_id' => $supplier->sno,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'active',
            'countryid' => $user->country_id ?? null,
            'companyid' => $user->company_id ?? null,
            'subcompanyid' => $user->sub_company_id ?? null,
            'projectid' => $user->project_id ?? null,
            'subprojectid' => $user->sub_project_id ?? null,
        ]);

        return redirect()->route('admin.suppliers.edit', $supplierId)->with('success', 'User added successfully to supplier.');
    }

    /**
     * Update an existing user under a supplier.
     */
    public function updateUser(Request $request, $supplierId, $userId)
    {
        $supplierUser = SupplierUser::where('supplier_id', $supplierId)->findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:supplier_users,email,' . $userId . ',sno',
            'role' => 'required|in:Employee,Owner',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $supplierUser->update($data);

        return redirect()->route('admin.suppliers.edit', $supplierId)->with('success', 'Supplier user updated successfully.');
    }

    /**
     * Delete a user under a supplier.
     */
    public function deleteUser($supplierId, $userId)
    {
        $supplierUser = SupplierUser::where('supplier_id', $supplierId)->findOrFail($userId);

        // Check if this is the only user for the supplier
        $userCount = SupplierUser::where('supplier_id', $supplierId)->count();
        if ($userCount <= 1) {
            return redirect()->route('admin.suppliers.edit', $supplierId)->withErrors(['error' => 'Cannot delete the only user of this supplier. Add another user first or deactivate this user.']);
        }

        $supplierUser->delete();

        return redirect()->route('admin.suppliers.edit', $supplierId)->with('success', 'Supplier user deleted successfully.');
    }
}
