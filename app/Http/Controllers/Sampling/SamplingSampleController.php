<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingSample;
use App\Models\Sampling\SamplingProject;
use App\Models\Sampling\SamplingBatch;
use App\Models\Sampling\SamplingDivision;
use App\Models\Sampling\SamplingUser;
use App\Models\Sampling\SamplingAssignmentHistory;
use App\Models\Sampling\SamplingReferenceMaterial;
use App\Models\Sampling\SamplingDevelopmentAttempt;
use App\Models\Sampling\SamplingApproval;
use App\Models\Sampling\SamplingBom;
use App\Models\Sampling\SamplingOperation;
use App\Models\Sampling\SamplingSpecification;
use App\Models\Sampling\SamplingMeasurement;
use App\Models\Sampling\SamplingMeasurementPoint;
use App\Models\Sampling\SamplingPattern;
use App\Models\Sampling\SamplingFinalImage;
use App\Models\Sampling\SamplingProductionNote;
use App\Models\Sampling\SamplingProductionNoteAttachment;
use App\Models\Sampling\SamplingCosting;
use App\Models\Sampling\SamplingStorageLocation;
use App\Models\Sampling\SamplingPhysicalStorage;
use App\Models\Sampling\SamplingProductionLearningNote;
use App\Services\Sampling\FreezeChecklistService;
use App\Services\Sampling\SampleFreezeService;
use App\Models\Sampling\SamplingSkillLevel;
use App\Models\Sampling\SamplingReferenceType;
use App\Models\ItemNameMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SamplingSampleController extends Controller
{
    protected FreezeChecklistService $checklistService;
    protected SampleFreezeService $freezeService;

    public function __construct(FreezeChecklistService $checklistService, SampleFreezeService $freezeService)
    {
        $this->checklistService = $checklistService;
        $this->freezeService = $freezeService;
    }

    public function index(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $query = SamplingSample::where('sampling_company_id', $companyId)
            ->with(['project', 'batch', 'overallDivision', 'assignedPerson', 'currentRevision'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sample_code', 'like', "%{$search}%")
                  ->orWhere('style_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->filled('division_id')) {
            $query->where('overall_division_id', $request->division_id);
        }

        if ($request->filled('project_id')) {
            $query->where('sampling_project_id', $request->project_id);
        }

        if ($request->get('filter') === 'frozen') {
            $query->where('is_frozen', true);
        } elseif ($request->get('filter') === 'ready_to_freeze') {
            $query->where('approval_status', 'approved')->where('is_frozen', false);
        }

        $samples = $query->paginate(15)->withQueryString();
        $divisions = SamplingDivision::where('sampling_company_id', $companyId)->get();
        $projects = SamplingProject::where('sampling_company_id', $companyId)->get();

        return view('sampling.samples.index', compact('samples', 'divisions', 'projects'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $projects = SamplingProject::where('sampling_company_id', $companyId)->with('batches')->get();
        $divisions = SamplingDivision::where('sampling_company_id', $companyId)->get();
        $users = SamplingUser::where('sampling_company_id', $companyId)->get();

        $selectedProjectId = $request->project_id;
        $selectedBatchId = $request->batch_id;

        return view('sampling.samples.create', compact('projects', 'divisions', 'users', 'selectedProjectId', 'selectedBatchId'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $userId = Auth::guard('sampling')->id();

        $request->validate([
            'sampling_project_id' => 'required|exists:sampling_projects,id',
            'sampling_batch_id' => 'required|exists:sampling_batches,id',
            'style_name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'overall_division_id' => 'required|exists:sampling_divisions,id',
            'assigned_person_id' => 'nullable|exists:sampling_users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'supporting_division_ids' => 'nullable|array',
            'supporting_division_ids.*' => 'exists:sampling_divisions,id',
        ]);

        DB::beginTransaction();
        try {
            $batch = SamplingBatch::findOrFail($request->sampling_batch_id);

            // Auto-generate unique sample code: SAM-{YEAR}-{PROJECT_ID}-{BATCH_NUM}-S{SEQ}
            $year = date('Y');
            $sampleSeq = SamplingSample::where('sampling_batch_id', $batch->id)->count() + 1;
            $sampleCode = sprintf("SAM-%s-%03d-%s-S%02d", $year, $request->sampling_project_id, $batch->batch_number, $sampleSeq);

            $sample = SamplingSample::create([
                'sampling_company_id' => $companyId,
                'sampling_project_id' => $request->sampling_project_id,
                'sampling_batch_id' => $request->sampling_batch_id,
                'sample_code' => $sampleCode,
                'style_name' => $request->style_name,
                'description' => $request->description,
                'overall_division_id' => $request->overall_division_id,
                'assigned_person_id' => $request->assigned_person_id,
                'date_assigned' => $request->assigned_person_id ? now()->toDateString() : null,
                'start_date' => $request->start_date ?? now()->toDateString(),
                'target_date' => $request->target_date,
                'priority' => $request->priority,
                'approval_status' => 'pending',
                'is_frozen' => false,
                'created_by' => $userId,
            ]);

            // Attach supporting divisions if selected
            if (!empty($request->supporting_division_ids)) {
                $sample->supportingDivisions()->sync($request->supporting_division_ids);
            }

            // Record assignment history
            if ($request->assigned_person_id) {
                SamplingAssignmentHistory::create([
                    'sample_id' => $sample->id,
                    'assigned_to_user_id' => $request->assigned_person_id,
                    'assigned_by_user_id' => $userId,
                    'assigned_date' => now()->toDateString(),
                    'notes' => 'Initial assignment during sample creation',
                ]);
            }

            DB::commit();

            return redirect()->route('sampling.samples.show', $sample->id)->with('success', "Sample {$sample->sample_code} created. Welcome to the 360° Workspace.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create sample: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;

        $sample = SamplingSample::where('sampling_company_id', $companyId)
            ->with([
                'project',
                'batch',
                'overallDivision',
                'supportingDivisions',
                'assignedPerson',
                'approvedByUser',
                'revisions.freezer',
                'currentRevision',
                'referenceMaterials.uploader',
                'developmentAttempts.creator',
                'approvals.submitter',
                'approvals.approver',
                'assignmentHistories.assignedTo',
                'assignmentHistories.assignedBy',
                'activeBoms',
                'activeOperations.division',
                'activeSpecifications',
                'activeMeasurements',
                'activePatterns.approver',
                'activeFinalImages.uploader',
                'activeProductionNotes.attachments',
                'activeProductionNotes.division',
                'activeProductionNotes.creator',
                'activeCosting',
                'activePhysicalStorage.location',
            ])
            ->findOrFail($id);

        $checklistEvaluation = $this->checklistService->evaluate($sample);
        $divisions = SamplingDivision::where('sampling_company_id', $companyId)->get();
        $users = SamplingUser::where('sampling_company_id', $companyId)->get();
        $measurementPoints = SamplingMeasurementPoint::where('sampling_company_id', $companyId)->get();
        $storageLocations = SamplingStorageLocation::where('sampling_company_id', $companyId)->where('status', 'active')->get();
        $skillLevels = SamplingSkillLevel::where('sampling_company_id', $companyId)->where('status', 'active')->get();
        $referenceTypes = SamplingReferenceType::where('sampling_company_id', $companyId)->where('status', 'active')->get();

        return view('sampling.samples.show', compact(
            'sample',
            'checklistEvaluation',
            'divisions',
            'users',
            'measurementPoints',
            'storageLocations',
            'skillLevels',
            'referenceTypes'
        ));
    }

    public function edit($id)
    {
        return redirect()->route('sampling.samples.show', ['sample' => $id, 'tab' => 'overview']);
    }

    // 1. Update Overview & Assignments
    public function updateOverview(Request $request, $id)
    {
        $companyId = Auth::guard('sampling')->user()->sampling_company_id;
        $sample = SamplingSample::where('sampling_company_id', $companyId)->findOrFail($id);

        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify an immutable frozen sample. Create a new revision to make changes.']);
        }

        $request->validate([
            'style_name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'overall_division_id' => 'required|exists:sampling_divisions,id',
            'assigned_person_id' => 'nullable|exists:sampling_users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'target_date' => 'nullable|date',
            'general_notes' => 'nullable|string',
            'supporting_division_ids' => 'nullable|array',
        ]);

        $oldAssigned = $sample->assigned_person_id;

        $sample->update([
            'style_name' => $request->style_name,
            'description' => $request->description,
            'overall_division_id' => $request->overall_division_id,
            'assigned_person_id' => $request->assigned_person_id,
            'date_assigned' => ($request->assigned_person_id != $oldAssigned) ? now()->toDateString() : $sample->date_assigned,
            'start_date' => $request->start_date,
            'target_date' => $request->target_date,
            'priority' => $request->priority,
            'general_notes' => $request->general_notes,
            'updated_by' => Auth::guard('sampling')->id(),
        ]);

        $sample->supportingDivisions()->sync($request->supporting_division_ids ?? []);

        // Log history if assigned person changed
        if ($request->assigned_person_id && $request->assigned_person_id != $oldAssigned) {
            SamplingAssignmentHistory::create([
                'sample_id' => $sample->id,
                'assigned_to_user_id' => $request->assigned_person_id,
                'assigned_by_user_id' => Auth::guard('sampling')->id(),
                'assigned_date' => now()->toDateString(),
                'notes' => 'Responsible artisan updated',
            ]);
        }

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'overview'])->with('success', 'Sample overview updated successfully.');
    }

    // 2. Reference Materials (Sketches, moodboard URLs, PDFs)
    public function storeReference(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $request->validate([
            'reference_type' => 'required|string|max:50',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'reference_file' => 'nullable|file|max:20480',
            'url' => 'nullable|url',
        ]);

        $filePath = null;
        if ($request->hasFile('reference_file')) {
            $file = $request->file('reference_file');
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $folder = "uploads/sampling/references/{$sample->id}";
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
            $file->move(public_path($folder), $filename);
            $filePath = "{$folder}/{$filename}";
        }

        SamplingReferenceMaterial::create([
            'sample_id' => $sample->id,
            'reference_type' => $request->reference_type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'url' => $request->url,
            'uploaded_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'references'])->with('success', 'Reference material added.');
    }

    // 3. Optional Physical Development Attempts
    public function storeAttempt(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $request->validate([
            'notes' => 'required|string',
            'attempt_date' => 'required|date',
            'result_status' => 'required|in:inconclusive,reworked,passed,failed',
        ]);

        $attemptNum = SamplingDevelopmentAttempt::where('sample_id', $sample->id)->count() + 1;

        SamplingDevelopmentAttempt::create([
            'sample_id' => $sample->id,
            'attempt_number' => $attemptNum,
            'attempt_date' => $request->attempt_date,
            'notes' => $request->notes,
            'result_status' => $request->result_status,
            'created_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'development'])->with('success', "Attempt #{$attemptNum} logged successfully.");
    }

    // 4. Formal Approval Workflow
    public function submitApproval(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $action = $request->action; // 'submit', 'approve', 'reject'

        $userId = Auth::guard('sampling')->id();

        if ($action === 'submit') {
            $sample->update(['approval_status' => 'submitted']);
            SamplingApproval::create([
                'sample_id' => $sample->id,
                'submitted_by' => $userId,
                'submitted_date' => now(),
                'status' => 'submitted',
            ]);
            return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'approval'])->with('success', 'Sample submitted for approval.');
        }

        if ($action === 'approve') {
            $sample->update([
                'approval_status' => 'approved',
                'approval_date' => now()->toDateString(),
                'approved_by' => $userId,
            ]);
            SamplingApproval::create([
                'sample_id' => $sample->id,
                'submitted_by' => $sample->created_by ?? $userId,
                'submitted_date' => now(),
                'reviewed_by' => $userId,
                'review_date' => now(),
                'review_comments' => $request->review_comments ?? 'Formally approved',
                'approved_by' => $userId,
                'approval_date' => now(),
                'status' => 'approved',
            ]);
            return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'approval'])->with('success', 'Sample accepted and formally APPROVED! Now proceed with technical documentation before freezing.');
        }

        if ($action === 'reject') {
            $sample->update(['approval_status' => 'rejected']);
            SamplingApproval::create([
                'sample_id' => $sample->id,
                'submitted_by' => $sample->created_by ?? $userId,
                'submitted_date' => now(),
                'reviewed_by' => $userId,
                'review_date' => now(),
                'review_comments' => $request->review_comments,
                'status' => 'rejected',
            ]);
            return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'approval'])->with('error', 'Sample marked as needs revision/rejected.');
        }

        return back();
    }

    // 5. Bill of Materials (BOM)
    public function storeBom(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify BOM on frozen sample. Create a revision.']);
        }

        $request->validate([
            'material_category' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'colour' => 'nullable|string|max:100',
            'shade' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:30',
            'net_quantity' => 'required|numeric|min:0.0001',
            'wastage_percentage' => 'nullable|numeric|min:0|max:100',
            'cost_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        SamplingBom::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null, // active draft
            'material_category' => $request->material_category,
            'description' => $request->description,
            'colour' => $request->colour,
            'shade' => $request->shade,
            'unit_of_measure' => $request->unit_of_measure,
            'net_quantity' => $request->net_quantity,
            'wastage_percentage' => $request->wastage_percentage ?? 0.00,
            'cost_rate' => $request->cost_rate ?? 0.00,
            'notes' => $request->notes,
        ]);

        // Recalculate direct costing automatically
        $this->recalculateCosting($sample);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'bom'])->with('success', 'BOM material line added.');
    }

    public function deleteBom($sampleId, $bomId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete BOM from frozen sample.']);
        }
        SamplingBom::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($bomId)->delete();
        $this->recalculateCosting($sample);
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'bom'])->with('success', 'BOM item removed.');
    }

    // 6. Labour and Operations
    public function storeOperation(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify operations on frozen sample. Create a revision.']);
        }

        $request->validate([
            'operation_name' => 'required|string|max:191',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'skill_level' => 'nullable|string|max:50',
            'estimated_time_minutes' => 'required|numeric|min:0.1',
            'labour_rate_per_hour' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $seq = SamplingOperation::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->count() + 1;

        SamplingOperation::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'sequence_number' => $seq,
            'operation_name' => $request->operation_name,
            'division_id' => $request->division_id,
            'skill_level' => $request->skill_level,
            'estimated_time_minutes' => $request->estimated_time_minutes,
            'labour_rate_per_hour' => $request->labour_rate_per_hour ?? 0.00,
            'notes' => $request->notes,
        ]);

        $this->recalculateCosting($sample);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'operations'])->with('success', 'Operation step added.');
    }

    public function deleteOperation($sampleId, $opId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete operation from frozen sample.']);
        }
        SamplingOperation::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($opId)->delete();
        $this->recalculateCosting($sample);
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'operations'])->with('success', 'Operation step removed.');
    }

    // 7. Technical Specifications
    public function storeSpec(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify specs on frozen sample.']);
        }

        $request->validate([
            'attribute_name' => 'required|string|max:100',
            'attribute_value' => 'required|string|max:255',
            'unit' => 'nullable|string|max:30',
        ]);

        SamplingSpecification::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
            'unit' => $request->unit,
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'specifications'])->with('success', 'Specification attribute saved.');
    }

    public function deleteSpec($sampleId, $specId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete specs from frozen sample.']);
        }
        SamplingSpecification::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($specId)->delete();
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'specifications'])->with('success', 'Specification attribute deleted.');
    }

    // 8. Measurements
    public function storeMeasurement(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify measurements on frozen sample.']);
        }

        $request->validate([
            'point_name' => 'required|string|max:100',
            'spec_value' => 'required|numeric',
            'unit' => 'required|string|max:20',
            'tolerance_plus' => 'nullable|numeric',
            'tolerance_minus' => 'nullable|numeric',
            'notes' => 'nullable|string|max:255',
        ]);

        SamplingMeasurement::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'point_name' => $request->point_name,
            'spec_value' => $request->spec_value,
            'unit' => $request->unit,
            'tolerance_plus' => $request->tolerance_plus ?? 0.00,
            'tolerance_minus' => $request->tolerance_minus ?? 0.00,
            'notes' => $request->notes,
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'measurements'])->with('success', 'Measurement point added.');
    }

    public function deleteMeasurement($sampleId, $measurementId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete measurements from frozen sample.']);
        }
        SamplingMeasurement::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($measurementId)->delete();
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'measurements'])->with('success', 'Measurement point deleted.');
    }

    // 9. Pattern Library
    public function storePattern(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot modify patterns on frozen sample.']);
        }

        $request->validate([
            'pattern_name' => 'required|string|max:191',
            'pattern_type' => 'required|in:sewing,knitting,crochet,embroidery,weaving,cutting_template,leather,other',
            'version' => 'nullable|string|max:20',
            'size_label' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
            'pattern_file' => 'nullable|file|max:25600',
        ]);

        $filePath = null;
        if ($request->hasFile('pattern_file')) {
            $file = $request->file('pattern_file');
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $folder = "uploads/sampling/patterns/{$sample->id}";
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
            $file->move(public_path($folder), $filename);
            $filePath = "{$folder}/{$filename}";
        }

        SamplingPattern::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'pattern_code' => 'PAT-' . time(),
            'pattern_name' => $request->pattern_name,
            'pattern_type' => $request->pattern_type,
            'version' => $request->version ?? '1.0',
            'size_label' => $request->size_label,
            'file_path' => $filePath,
            'instructions' => $request->instructions,
            'approved_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'patterns'])->with('success', 'Pattern added to central library.');
    }

    public function deletePattern($sampleId, $patternId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete patterns from frozen sample.']);
        }
        SamplingPattern::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($patternId)->delete();
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'patterns'])->with('success', 'Pattern deleted.');
    }

    // 10. Final Technical Photos
    public function storeFinalImage(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot upload photos to frozen sample.']);
        }

        $request->validate([
            'image_type' => 'required|in:front,back,side,detail,inside,construction_detail,stitch_detail,flat_lay,model,line_drawing,label_position,other',
            'caption' => 'nullable|string|max:255',
            'image_file' => 'required|image|max:15360',
        ]);

        $file = $request->file('image_file');
        $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $folder = "uploads/sampling/photos/{$sample->id}";
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }
        $file->move(public_path($folder), $filename);

        SamplingFinalImage::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'image_type' => $request->image_type,
            'file_path' => "{$folder}/{$filename}",
            'caption' => $request->caption,
            'uploaded_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'photos'])->with('success', 'Final technical photo uploaded.');
    }

    public function deleteFinalImage($sampleId, $photoId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete photos from frozen sample.']);
        }
        SamplingFinalImage::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($photoId)->delete();
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'photos'])->with('success', 'Photo deleted.');
    }

    // 11. CRITICAL REQUIREMENT: Production Notes (Written + Voice Recording + Attachments)
    public function storeProductionNote(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot add notes to frozen sample. Create a revision.']);
        }

        $request->validate([
            'subject' => 'required|string|max:191',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'written_note' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:webm,mp3,wav,ogg,m4a,mp4|max:25600',
            'audio_base64' => 'nullable|string',
            'audio_duration' => 'nullable|integer',
            'attachments.*' => 'nullable|file|max:15360',
        ]);

        // Verify that either written note or voice note is supplied
        if (empty($request->written_note) && !$request->hasFile('audio_file') && empty($request->audio_base64)) {
            return back()->withErrors(['error' => 'Please provide either written notes or an audio recording for the Production Note.']);
        }

        $audioPath = null;
        $audioDuration = $request->audio_duration;

        $folder = "uploads/sampling/notes/{$sample->id}";
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        // Handle direct audio file upload
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            $filename = 'voice_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($folder), $filename);
            $audioPath = "{$folder}/{$filename}";
        }
        // Handle in-browser recorded base64 blob
        elseif (!empty($request->audio_base64)) {
            $audioData = base64_decode(preg_replace('#^data:audio/\w+;base64,#i', '', $request->audio_base64));
            $filename = 'voice_' . time() . '_' . uniqid() . '.webm';
            file_put_contents(public_path("{$folder}/{$filename}"), $audioData);
            $audioPath = "{$folder}/{$filename}";
        }

        $seq = SamplingProductionNote::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->count() + 1;

        $note = SamplingProductionNote::create([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
            'sequence' => $seq,
            'subject' => $request->subject,
            'division_id' => $request->division_id,
            'written_note' => $request->written_note,
            'voice_audio_path' => $audioPath,
            'audio_duration_seconds' => $audioDuration,
            'created_by' => Auth::guard('sampling')->id(),
        ]);

        // Process attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $att) {
                $attFilename = time() . '_' . uniqid() . '_' . $att->getClientOriginalName();
                $att->move(public_path($folder), $attFilename);

                SamplingProductionNoteAttachment::create([
                    'production_note_id' => $note->id,
                    'file_type' => 'image',
                    'file_path' => "{$folder}/{$attFilename}",
                    'file_name' => $att->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'production_notes'])->with('success', 'Production Note saved successfully.');
    }

    public function deleteProductionNote($sampleId, $noteId)
    {
        $sample = SamplingSample::findOrFail($sampleId);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot delete notes from frozen sample.']);
        }
        SamplingProductionNote::where('sample_id', $sampleId)->whereNull('sampling_revision_id')->findOrFail($noteId)->delete();
        return redirect()->route('sampling.samples.show', ['id' => $sampleId, 'tab' => 'production_notes'])->with('success', 'Production Note deleted.');
    }

    // 12. Direct Costing
    public function updateCosting(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot alter costing of frozen sample.']);
        }

        $costing = SamplingCosting::firstOrNew([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
        ]);

        $costing->dyeing_cost = $request->dyeing_cost ?? 0;
        $costing->washing_processing_cost = $request->washing_processing_cost ?? 0;
        $costing->trims_accessories_cost = $request->trims_accessories_cost ?? 0;
        $costing->outside_services_cost = $request->outside_services_cost ?? 0;
        $costing->other_direct_cost = $request->other_direct_cost ?? 0;
        $costing->save();

        $this->recalculateCosting($sample);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'costing'])->with('success', 'Costing breakdown updated.');
    }

    // 13. Physical Sample Storage Archive
    public function updateStorage(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        if ($sample->is_frozen) {
            return back()->withErrors(['error' => 'Cannot alter storage of frozen sample.']);
        }

        $request->validate([
            'storage_location_id' => 'required|exists:sampling_storage_locations,id',
            'date_stored' => 'required|date',
            'sample_condition' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        SamplingPhysicalStorage::updateOrCreate(
            ['sample_id' => $sample->id, 'sampling_revision_id' => null],
            [
                'storage_location_id' => $request->storage_location_id,
                'date_stored' => $request->date_stored,
                'stored_by' => Auth::guard('sampling')->id(),
                'sample_condition' => $request->sample_condition,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
            ]
        );

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'storage'])->with('success', 'Physical storage location recorded.');
    }

    // 14. Formal Freezing Action
    public function freeze(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $userId = Auth::guard('sampling')->id();

        try {
            $revision = $this->freezeService->freeze($sample, $userId, $request->change_summary);
            return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'overview'])
                ->with('success', "SUCCESS: Sample has been FROZEN as {$revision->revision_code}! It is now the authoritative master for Production Orders.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 15. Create Next Revision (e.g. Rev 1 from Rev 0)
    public function createRevision(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        try {
            $this->freezeService->createNextDraftRevision($sample, $request->reason);
            return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'overview'])
                ->with('success', "New working draft revision created. The previous revision remains completely preserved in history.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 16. Production Learning Notes
    public function storeLearningNote(Request $request, $id)
    {
        $sample = SamplingSample::findOrFail($id);
        $request->validate([
            'written_note' => 'required|string',
        ]);

        $revId = $sample->current_revision_id ?? $sample->revisions->first()?->id;
        if (!$revId) {
            return back()->withErrors(['error' => 'Sample does not have a frozen revision.']);
        }

        SamplingProductionLearningNote::create([
            'sampling_revision_id' => $revId,
            'written_note' => $request->written_note,
            'status' => 'pending',
            'created_by' => Auth::guard('sampling')->id(),
        ]);

        return redirect()->route('sampling.samples.show', ['id' => $sample->id, 'tab' => 'history'])->with('success', 'Production Learning Note recorded for review.');
    }

    protected function recalculateCosting(SamplingSample $sample): void
    {
        $materialTotal = SamplingBom::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->sum('material_cost');
        $labourTotal = SamplingOperation::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->sum('estimated_labour_cost');

        $costing = SamplingCosting::firstOrNew([
            'sample_id' => $sample->id,
            'sampling_revision_id' => null,
        ]);

        $costing->material_cost_total = $materialTotal;
        $costing->labour_cost_total = $labourTotal;
        $costing->save();
    }
}
