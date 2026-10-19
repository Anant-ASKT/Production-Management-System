<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminRetailerController extends Controller
{
    /**
     * Display a listing of retailers.
     */
    public function index()
    {
        $retailers = Supplier::where('type', 'retailer')
            ->with(['users', 'defaultOwner'])
            ->withCount('users')
            ->orderBy('sno', 'desc')
            ->paginate(10);

        return view('admin.retailers.index', compact('retailers'));
    }

    /**
     * Show the form for creating a new retailer.
     */
    public function create()
    {
        return view('admin.retailers.create');
    }

    /**
     * Store a newly created retailer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|size:3|alpha|unique:suppliers,nickname',
            'email' => 'required|email|max:255|unique:suppliers,email|unique:supplier_users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
            'password' => 'nullable|string|min:6',
        ], [
            'nickname.size' => 'Nick Name must be exactly 3 letters (e.g. ABC).',
            'nickname.alpha' => 'Nick Name must contain only letters.',
            'nickname.unique' => 'This Nick Name has already been taken.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid Email address.',
            'email.unique' => 'This email is already registered.',
            'store_url.required' => 'Store URL is required for retailers.',
            'store_url.url' => 'Please enter a valid URL (e.g. https://example.com).',
            'consumer_key.required' => 'Consumer Key is required for retailers.',
            'consumer_secret.required' => 'Consumer Secret is required for retailers.',
        ]);

        $user = auth()->user();

        // Auto-generate 3-letter nickname if left blank
        $nickname = $request->filled('nickname') ? strtoupper(trim($request->nickname)) : null;
        if (!$nickname) {
            $cleanName = strtoupper(preg_replace('/[^A-Za-z]/', '', $request->name));
            $baseCode = strlen($cleanName) >= 3 ? substr($cleanName, 0, 3) : str_pad($cleanName, 3, 'X');
            $code = $baseCode;
            $counter = 1;
            while (Supplier::where('nickname', $code)->exists()) {
                $code = substr($baseCode, 0, 2) . chr(64 + ($counter % 26 == 0 ? 26 : $counter % 26));
                $counter++;
                if ($counter > 50) {
                    $code = strtoupper(Str::random(3));
                }
            }
            $nickname = $code;
        }

        $password = $request->filled('password') ? $request->password : '123456';

        DB::beginTransaction();
        try {
            // Retailers are always type='retailer' and is_integrated=1
            $retailer = Supplier::create([
                'name' => $request->name,
                'nickname' => $nickname,
                'type' => 'retailer',
                'is_integrated' => 1,
                'email' => $request->email,
                'password' => Hash::make($password),
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

            // Retailer login user is ALWAYS created with role='Owner'
            SupplierUser::create([
                'supplier_id' => $retailer->sno,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'phone' => $request->phone,
                'role' => 'Owner',
                'status' => 'active',
                'countryid' => $user->country_id ?? null,
                'companyid' => $user->company_id ?? null,
                'subcompanyid' => $user->sub_company_id ?? null,
                'projectid' => $user->project_id ?? null,
                'subprojectid' => $user->sub_project_id ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.retailers.index')->with('success', 'Retailer created successfully with default Owner account and credentials.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create retailer: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified retailer.
     */
    public function edit($id)
    {
        $retailer = Supplier::where('type', 'retailer')->with('users')->findOrFail($id);
        return view('admin.retailers.edit', compact('retailer'));
    }

    /**
     * Update the specified retailer in storage.
     */
    public function update(Request $request, $id)
    {
        $retailer = Supplier::where('type', 'retailer')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|size:3|alpha|unique:suppliers,nickname,' . $id . ',sno',
            'email' => 'required|email|max:255|unique:suppliers,email,' . $id . ',sno',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_url' => 'required|url',
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
        ], [
            'nickname.size' => 'Nick Name must be exactly 3 letters (e.g. ABC).',
            'nickname.alpha' => 'Nick Name must contain only letters.',
            'nickname.unique' => 'This Nick Name has already been taken.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid Email address.',
            'email.unique' => 'This email is already registered.',
            'store_url.required' => 'Store URL is required for retailers.',
            'store_url.url' => 'Please enter a valid URL.',
            'consumer_key.required' => 'Consumer Key is required for retailers.',
            'consumer_secret.required' => 'Consumer Secret is required for retailers.',
        ]);

        $user = auth()->user();

        $data = $request->only(['name', 'email', 'phone', 'address', 'store_url', 'consumer_key', 'consumer_secret']);
        $data['type'] = 'retailer';
        $data['is_integrated'] = 1;
        if ($request->filled('nickname')) {
            $data['nickname'] = strtoupper(trim($request->nickname));
        }
        $data['countryid'] = $user->country_id ?? null;
        $data['companyid'] = $user->company_id ?? null;
        $data['subcompanyid'] = $user->sub_company_id ?? null;
        $data['projectid'] = $user->project_id ?? null;
        $data['subprojectid'] = $user->sub_project_id ?? null;

        $retailer->update($data);

        // Keep default Owner user account synchronized
        $owner = SupplierUser::where('supplier_id', $retailer->sno)->where('role', 'Owner')->first();
        if ($owner) {
            $owner->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
        }

        return redirect()->route('admin.retailers.index')->with('success', 'Retailer updated successfully.');
    }

    /**
     * Add a new user under a specific retailer.
     */
    public function addUser(Request $request, $retailerId)
    {
        $retailer = Supplier::where('type', 'retailer')->findOrFail($retailerId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:supplier_users,email',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:Employee,Owner',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $password = $request->filled('password') ? $request->password : '123456';

        SupplierUser::create([
            'supplier_id' => $retailer->sno,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'active',
            'countryid' => $user->country_id ?? null,
            'companyid' => $user->company_id ?? null,
            'subcompanyid' => $user->sub_company_id ?? null,
            'projectid' => $user->project_id ?? null,
            'subprojectid' => $user->sub_project_id ?? null,
        ]);

        return redirect()->route('admin.retailers.edit', $retailerId)->with('success', 'User added successfully to retailer.');
    }

    /**
     * Update an existing user under a retailer.
     */
    public function updateUser(Request $request, $retailerId, $userId)
    {
        $supplierUser = SupplierUser::where('supplier_id', $retailerId)->findOrFail($userId);

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

        return redirect()->route('admin.retailers.edit', $retailerId)->with('success', 'Retailer user updated successfully.');
    }

    /**
     * Delete a user under a retailer.
     */
    public function deleteUser($retailerId, $userId)
    {
        $supplierUser = SupplierUser::where('supplier_id', $retailerId)->findOrFail($userId);

        $userCount = SupplierUser::where('supplier_id', $retailerId)->count();
        if ($userCount <= 1) {
            return redirect()->route('admin.retailers.edit', $retailerId)->withErrors(['error' => 'Cannot delete the only user of this retailer. Add another user first or deactivate this user.']);
        }

        $supplierUser->delete();

        return redirect()->route('admin.retailers.edit', $retailerId)->with('success', 'Retailer user deleted successfully.');
    }
}
