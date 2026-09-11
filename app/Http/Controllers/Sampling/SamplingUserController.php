<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingUser;
use App\Models\Sampling\SamplingDivision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SamplingUserController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $query = SamplingUser::where('sampling_company_id', $companyId)->with('division')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();
        $divisions = SamplingDivision::where('sampling_company_id', $companyId)->get();

        return view('sampling.users.index', compact('users', 'divisions'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:sampling_users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|max:50',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        SamplingUser::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division_id' => $request->division_id,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.users.index')->with('success', "Team member '{$request->name}' added successfully.");
    }

    public function update(Request $request, $id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $user = SamplingUser::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:sampling_users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|max:50',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'division_id' => $request->division_id,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('sampling.users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy($id)
    {
        $currentUserId = Auth::guard('sampling')->id();
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        if ($id == $currentUserId) {
            return back()->withErrors(['error' => 'You cannot delete your own logged-in account.']);
        }

        $user = SamplingUser::where('sampling_company_id', $companyId)->findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('sampling.users.index')->with('success', "User '{$name}' deleted successfully.");
    }
}
