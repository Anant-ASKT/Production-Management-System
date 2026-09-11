<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingProject;
use App\Models\Sampling\SamplingBatch;
use App\Models\Sampling\SamplingSample;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        // Metric Counts
        $activeProjectsCount = SamplingProject::where('sampling_company_id', $companyId)
            ->whereIn('status', ['open', 'in_development', 'partially_approved'])
            ->count();

        $samplesUnderDevCount = SamplingSample::where('sampling_company_id', $companyId)
            ->where('approval_status', 'pending')
            ->where('is_frozen', false)
            ->count();

        $awaitingApprovalCount = SamplingSample::where('sampling_company_id', $companyId)
            ->where('approval_status', 'submitted')
            ->count();

        $readyToFreezeCount = SamplingSample::where('sampling_company_id', $companyId)
            ->where('approval_status', 'approved')
            ->where('is_frozen', false)
            ->count();

        $frozenCount = SamplingSample::where('sampling_company_id', $companyId)
            ->where('is_frozen', true)
            ->count();

        // Recent Samples
        $recentSamples = SamplingSample::where('sampling_company_id', $companyId)
            ->with(['project', 'batch', 'overallDivision', 'assignedPerson'])
            ->latest()
            ->take(8)
            ->get();

        return view('sampling.dashboard', compact(
            'activeProjectsCount',
            'samplesUnderDevCount',
            'awaitingApprovalCount',
            'readyToFreezeCount',
            'frozenCount',
            'recentSamples'
        ));
    }
}
