<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingProject;
use App\Models\Sampling\SamplingBatch;
use App\Models\Sampling\SamplingSample;
use App\Models\Sampling\SamplingDivision;
use App\Models\DesignerMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SamplingProjectController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $query = SamplingProject::where('sampling_company_id', $companyId)
            ->withCount(['batches', 'samples'])
            ->with('creator')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('sampling.projects.index', compact('projects'));
    }

    public function create()
    {
        // Fetch existing designers if table exists, otherwise empty collection
        $designers = class_exists(DesignerMaster::class) ? DesignerMaster::all() : collect([]);
        return view('sampling.projects.create', compact('designers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $userId = Auth::guard('sampling')->id();

        $request->validate([
            'project_name' => 'required|string|max:191',
            'designer_name' => 'nullable|string|max:191',
            'collection_name' => 'nullable|string|max:191',
            'start_date' => 'required|date',
            'target_completion_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'initial_batch_name' => 'required|string|max:191',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate project code: PRJ-YYYY-{3-digit-seq}
            $year = date('Y');
            $countThisYear = SamplingProject::where('sampling_company_id', $companyId)
                ->whereYear('created_at', $year)
                ->count() + 1;
            $projectCode = sprintf("PRJ-%s-%03d", $year, $countThisYear);

            $project = SamplingProject::create([
                'sampling_company_id' => $companyId,
                'project_code' => $projectCode,
                'project_name' => $request->project_name,
                'start_date' => $request->start_date,
                'target_completion_date' => $request->target_completion_date,
                'notes' => $request->notes,
                'status' => 'open',
                'created_by' => $userId,
            ]);

            // Create initial Batch B01
            SamplingBatch::create([
                'sampling_project_id' => $project->id,
                'batch_number' => 'B01',
                'batch_name' => $request->initial_batch_name,
                'start_date' => $request->start_date,
                'target_date' => $request->target_completion_date,
                'status' => 'open',
                'created_by' => $userId,
            ]);

            DB::commit();

            return redirect()->route('sampling.projects.show', $project->id)->with('success', "Sampling Project {$project->project_code} created with initial Batch B01.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create project: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $project = SamplingProject::where('sampling_company_id', $companyId)
            ->with(['batches.samples.overallDivision', 'batches.samples.assignedPerson', 'batches.samples.currentRevision'])
            ->findOrFail($id);

        $divisions = SamplingDivision::where('sampling_company_id', $companyId)->get();

        return view('sampling.projects.show', compact('project', 'divisions'));
    }

    public function storeBatch(Request $request, $projectId)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $userId = Auth::guard('sampling')->id();

        $project = SamplingProject::where('sampling_company_id', $companyId)->findOrFail($projectId);

        $request->validate([
            'batch_name' => 'required|string|max:191',
            'start_date' => 'required|date',
            'target_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate next batch number: B01, B02, B03...
        $batchCount = SamplingBatch::where('sampling_project_id', $project->id)->count() + 1;
        $batchNumber = sprintf("B%02d", $batchCount);

        SamplingBatch::create([
            'sampling_project_id' => $project->id,
            'batch_number' => $batchNumber,
            'batch_name' => $request->batch_name,
            'start_date' => $request->start_date,
            'target_date' => $request->target_date,
            'notes' => $request->notes,
            'status' => 'open',
            'created_by' => $userId,
        ]);

        return redirect()->route('sampling.projects.show', $project->id)->with('success', "Batch {$batchNumber} added successfully to project.");
    }

    public function edit($id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $project = SamplingProject::where('sampling_company_id', $companyId)->findOrFail($id);
        return view('sampling.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $project = SamplingProject::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'project_name' => 'required|string|max:191',
            'start_date' => 'required|date',
            'target_completion_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,open,in_development,partially_approved,completed,closed',
        ]);

        $project->update([
            'project_name' => $request->project_name,
            'start_date' => $request->start_date,
            'target_completion_date' => $request->target_completion_date,
            'notes' => $request->notes,
            'status' => $request->status,
            'updated_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.projects.show', $project->id)->with('success', 'Project details updated successfully.');
    }
}
