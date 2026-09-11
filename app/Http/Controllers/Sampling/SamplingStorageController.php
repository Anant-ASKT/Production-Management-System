<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingPhysicalStorage;
use App\Models\Sampling\SamplingStorageLocation;
use Illuminate\Support\Facades\Auth;

class SamplingStorageController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $locations = SamplingStorageLocation::where('sampling_company_id', $companyId)->get();

        $query = SamplingPhysicalStorage::whereHas('sample', function ($q) use ($companyId) {
            $q->where('sampling_company_id', $companyId);
        })->with(['sample.project', 'sample.batch', 'location', 'storedByUser'])->latest();

        if ($request->filled('location_id')) {
            $query->where('storage_location_id', $request->location_id);
        }

        $storages = $query->paginate(20)->withQueryString();

        return view('sampling.storage.index', compact('storages', 'locations'));
    }
}
