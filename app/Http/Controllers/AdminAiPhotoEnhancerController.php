<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiPhotoEnhancer;
use Illuminate\Support\Facades\Hash;

class AdminAiPhotoEnhancerController extends Controller
{
    public function index()
    {
        $enhancers = AiPhotoEnhancer::orderBy('sno', 'desc')->paginate(10);
        return view('admin.ai_photo_enhancers.index', compact('enhancers'));
    }

    public function create()
    {
        return view('admin.ai_photo_enhancers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:ai_photo_enhancers,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $user = auth()->user();

        AiPhotoEnhancer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
            'countryid' => $user->country_id ?? null,
            'companyid' => $user->company_id ?? null,
            'subcompanyid' => $user->sub_company_id ?? null,
            'projectid' => $user->project_id ?? null,
            'subprojectid' => $user->sub_project_id ?? null,
        ]);

        return redirect()->route('admin.ai-photo-enhancers.index')->with('success', 'AI Photo Enhancer created successfully.');
    }

    public function edit($id)
    {
        $enhancer = AiPhotoEnhancer::findOrFail($id);
        return view('admin.ai_photo_enhancers.edit', compact('enhancer'));
    }

    public function update(Request $request, $id)
    {
        $enhancer = AiPhotoEnhancer::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:ai_photo_enhancers,email,'.$id.',sno',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $user = auth()->user();

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'address', 'status']);
        $data['countryid'] = $user->country_id ?? null;
        $data['companyid'] = $user->company_id ?? null;
        $data['subcompanyid'] = $user->sub_company_id ?? null;
        $data['projectid'] = $user->project_id ?? null;
        $data['subprojectid'] = $user->sub_project_id ?? null;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $enhancer->update($data);

        return redirect()->route('admin.ai-photo-enhancers.index')->with('success', 'AI Photo Enhancer updated successfully.');
    }
}
