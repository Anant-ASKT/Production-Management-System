<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sampling\SamplingCompany;
use App\Models\Sampling\SamplingUser;
use App\Models\Sampling\SamplingDivision;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSamplingCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = SamplingCompany::withCount(['users', 'projects', 'samples'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->paginate(15)->withQueryString();

        return view('admin.sampling_companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.sampling_companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:sampling_companies,code',
            'contact_person' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'user_name' => 'required|string|max:191',
            'user_email' => 'required|email|unique:sampling_users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $company = SamplingCompany::create([
                'name' => $request->name,
                'code' => strtoupper(trim($request->code)),
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // Seed standard divisions for this company so they can start immediately
            $standardDivisions = [
                'Knit', 'Crochet', 'Handloom Development', 'Pattern Making & Stitching',
                'Leather', 'Embroidery', 'Jewellery', 'Yarn Dyeing', 'Yarn Spinning', 'Washing / Finishing'
            ];
            foreach ($standardDivisions as $divName) {
                SamplingDivision::create([
                    'sampling_company_id' => $company->id,
                    'name' => $divName,
                    'status' => 'active',
                ]);
            }

            // Create primary user
            SamplingUser::create([
                'sampling_company_id' => $company->id,
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?: 'company_admin',
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.sampling-companies.index')->with('success', 'Sampling company and primary user created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create sampling company: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $company = SamplingCompany::with(['users.division', 'divisions'])->findOrFail($id);
        return view('admin.sampling_companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = SamplingCompany::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:sampling_companies,code,' . $id,
            'contact_person' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $company->update([
            'name' => $request->name,
            'code' => strtoupper(trim($request->code)),
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.sampling-companies.index')->with('success', 'Sampling company updated successfully.');
    }

    public function addUser(Request $request, $companyId)
    {
        $company = SamplingCompany::findOrFail($companyId);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:sampling_users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:company_admin,sampling_manager,designer,sampling_staff,division_head,approver,viewer',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        SamplingUser::create([
            'sampling_company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division_id' => $request->division_id,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.sampling-companies.edit', $companyId)->with('success', 'User added to sampling company successfully.');
    }

    public function updateUser(Request $request, $companyId, $userId)
    {
        $user = SamplingUser::where('sampling_company_id', $companyId)->findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:sampling_users,email,' . $userId,
            'role' => 'required|in:company_admin,sampling_manager,designer,sampling_staff,division_head,approver,viewer',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'division_id' => $request->division_id,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.sampling-companies.edit', $companyId)->with('success', 'Company user updated successfully.');
    }

    public function deleteUser($companyId, $userId)
    {
        $user = SamplingUser::where('sampling_company_id', $companyId)->findOrFail($userId);

        $count = SamplingUser::where('sampling_company_id', $companyId)->count();
        if ($count <= 1) {
            return back()->withErrors(['error' => 'Cannot delete the only user of this sampling company.']);
        }

        $user->delete();

        return redirect()->route('admin.sampling-companies.edit', $companyId)->with('success', 'User removed successfully.');
    }
}
