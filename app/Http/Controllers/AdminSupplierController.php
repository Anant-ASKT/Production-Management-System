<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class AdminSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('sno', 'desc')->paginate(10);
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
            'email' => 'required|email|unique:suppliers,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'nullable|url',
            'consumer_key' => 'nullable|string',
            'consumer_secret' => 'nullable|string',
        ]);

        $user = auth()->user();

        Supplier::create([
            'name' => $request->name,
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

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,'.$id.',sno',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'nullable|url',
            'consumer_key' => 'nullable|string',
            'consumer_secret' => 'nullable|string',
        ]);

        $user = auth()->user();

        $data = $request->only(['name', 'email', 'phone', 'address', 'store_url', 'consumer_key', 'consumer_secret']);
        $data['countryid'] = $user->country_id ?? null;
        $data['companyid'] = $user->company_id ?? null;
        $data['subcompanyid'] = $user->sub_company_id ?? null;
        $data['projectid'] = $user->project_id ?? null;
        $data['subprojectid'] = $user->sub_project_id ?? null;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }
}
