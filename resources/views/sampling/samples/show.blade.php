@extends('layouts.sampling')

@section('title', $sample->sample_code . ' | ' . $sample->style_name)
@section('page-title', 'Sample Details')

@section('content')

{{-- Simple Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('sampling.samples.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Samples
        </a>
        <h4 class="fw-bold mb-1 text-dark">
            {{ $sample->style_name }}
            <span class="badge bg-dark font-monospace fs-6 ms-2">{{ $sample->sample_code }}</span>
        </h4>
        <div class="text-muted small">
            <strong>Project:</strong> {{ $sample->project->project_name ?? '-' }} &bull;
            <strong>Batch:</strong> {{ $sample->batch->batch_number ?? '-' }} &bull;
            <strong>Division:</strong> {{ $sample->overallDivision->name ?? '-' }}
            @if($sample->assignedPerson)
                &bull; <strong>Artisan:</strong> {{ $sample->assignedPerson->name }}
            @endif
        </div>
    </div>

    <div>
        @if($sample->is_frozen)
            <span class="badge bg-primary fs-6 px-3 py-2">
                <i class="bi bi-lock-fill me-1"></i> FROZEN {{ $sample->currentRevision->revision_code ?? 'REV 0' }}
            </span>
        @elseif($sample->approval_status === 'approved')
            <span class="badge bg-success fs-6 px-3 py-2">
                <i class="bi bi-check-circle-fill me-1"></i> Approved
            </span>
        @elseif($sample->approval_status === 'submitted')
            <span class="badge bg-info fs-6 px-3 py-2">
                <i class="bi bi-hourglass-split me-1"></i> Under Review
            </span>
        @else
            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                <i class="bi bi-pencil-fill me-1"></i> In Making
            </span>
        @endif
    </div>
</div>

{{-- Standard Tabs Across Top --}}
<ul class="nav nav-tabs mt-4" id="sampleTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-dark" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
            <i class="bi bi-info-circle me-1"></i> 1. Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-materials-btn" data-bs-toggle="tab" data-bs-target="#tab-materials" type="button" role="tab">
            <i class="bi bi-box-seam me-1 text-primary"></i> 2. Materials (BOM)
            <span class="badge bg-light text-dark border ms-1">{{ $sample->activeBoms->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-measurements-btn" data-bs-toggle="tab" data-bs-target="#tab-measurements" type="button" role="tab">
            <i class="bi bi-rulers me-1 text-info"></i> 3. Measurements & Specs
            <span class="badge bg-light text-dark border ms-1">{{ $sample->activeMeasurements->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-operations-btn" data-bs-toggle="tab" data-bs-target="#tab-operations" type="button" role="tab">
            <i class="bi bi-scissors me-1 text-danger"></i> 4. Making Steps
            <span class="badge bg-light text-dark border ms-1">{{ $sample->activeOperations->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-notes-btn" data-bs-toggle="tab" data-bs-target="#tab-notes" type="button" role="tab">
            <i class="bi bi-mic me-1 text-warning"></i> 5. Voice & Notes
            <span class="badge bg-warning text-dark ms-1">{{ $sample->activeProductionNotes->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-photos-btn" data-bs-toggle="tab" data-bs-target="#tab-photos" type="button" role="tab">
            <i class="bi bi-camera me-1 text-secondary"></i> 6. Photos & Storage
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-dark" id="tab-approval-btn" data-bs-toggle="tab" data-bs-target="#tab-approval" type="button" role="tab">
            <i class="bi bi-shield-check me-1 text-success"></i> 7. Approval & Freeze
        </button>
    </li>
</ul>

{{-- Tab Panes: ONLY THE SELECTED TAB OPENS PROPERLY --}}
<div class="tab-content border border-top-0 p-4 bg-white rounded-bottom shadow-sm mb-5" id="sampleTabsContent">

    {{-- ======================================================== --}}
    {{-- TAB 1: OVERVIEW                                          --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
        <form action="{{ route('sampling.samples.update-overview', $sample->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Style Name / Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="style_name" class="form-control" value="{{ old('style_name', $sample->style_name) }}" {{ $sample->is_frozen ? 'disabled' : 'required' }}>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Sample Code</label>
                    <input type="text" class="form-control font-monospace bg-light" value="{{ $sample->sample_code }}" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Priority</label>
                    <select name="priority" class="form-select" {{ $sample->is_frozen ? 'disabled' : '' }}>
                        <option value="low" {{ $sample->priority === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $sample->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $sample->priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $sample->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Responsible Department <span class="text-danger">*</span></label>
                    <select name="overall_division_id" class="form-select" {{ $sample->is_frozen ? 'disabled' : 'required' }}>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $sample->overall_division_id == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Assigned Artisan / Master Tailor</label>
                    <select name="assigned_person_id" class="form-select" {{ $sample->is_frozen ? 'disabled' : '' }}>
                        <option value="">Unassigned</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $sample->assigned_person_id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ str_replace('_', ' ', $u->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $sample->start_date?->format('Y-m-d')) }}" {{ $sample->is_frozen ? 'disabled' : '' }}>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Target Completion Date</label>
                    <input type="date" name="target_date" class="form-control" value="{{ old('target_date', $sample->target_date?->format('Y-m-d')) }}" {{ $sample->is_frozen ? 'disabled' : '' }}>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Concept & Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief visual description..." {{ $sample->is_frozen ? 'disabled' : '' }}>{{ old('description', $sample->description) }}</textarea>
                </div>

                @if(!$sample->is_frozen)
                    <div class="col-12 text-end pt-2">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Save Overview</button>
                    </div>
                @endif
            </div>
        </form>

        {{-- Inspiration Sketches Sub-Section --}}
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-paperclip me-1 text-primary"></i> Attached Sketches & Swatches</h6>
                @if(!$sample->is_frozen)
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addReferenceModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Sketch / Swatch
                    </button>
                @endif
            </div>

            <div class="row g-2">
                @forelse($sample->referenceMaterials as $ref)
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div class="text-truncate me-2">
                                <strong class="small d-block text-truncate">{{ $ref->title }}</strong>
                                <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">{{ ucwords(str_replace('_', ' ', $ref->reference_type)) }}</span>
                            </div>
                            @if($ref->file_path)
                                <a href="{{ $ref->download_url }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="View"><i class="bi bi-eye"></i></a>
                            @elseif($ref->url)
                                <a href="{{ $ref->url }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="Open Link"><i class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted small">No reference sketches or swatches attached.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 2: MATERIALS (BOM)                                   --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-materials" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Raw Materials Required (BOM)</h5>
                <small class="text-muted">Enter fabric, yarn, thread, and buttons needed for 1 sample piece</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <span class="small text-muted d-block">Total Material Cost:</span>
                    <span class="fs-5 fw-bold font-monospace text-success">₹{{ number_format($sample->activeBoms->sum('material_cost'), 2) }}</span>
                </div>
                @if(!$sample->is_frozen)
                    <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addBomModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Material
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle border mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Material Name</th>
                        <th>Category</th>
                        <th>Colour / Shade</th>
                        <th>Unit</th>
                        <th class="text-end">Net Qty</th>
                        <th class="text-end">Wastage %</th>
                        <th class="text-end">Gross Qty</th>
                        <th class="text-end">Rate (₹)</th>
                        <th class="text-end">Total Cost (₹)</th>
                        @if(!$sample->is_frozen) <th class="text-end" style="width: 50px;"></th> @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sample->activeBoms as $bom)
                        <tr>
                            <td class="fw-bold">{{ $bom->description }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $bom->material_category }}</span></td>
                            <td>{{ $bom->colour ?: '-' }} {{ $bom->shade ? "({$bom->shade})" : '' }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $bom->unit_of_measure }}</span></td>
                            <td class="text-end font-monospace">{{ number_format($bom->net_quantity, 3) }}</td>
                            <td class="text-end font-monospace text-muted">{{ number_format($bom->wastage_percentage, 1) }}%</td>
                            <td class="text-end font-monospace fw-bold">{{ number_format($bom->gross_quantity, 3) }}</td>
                            <td class="text-end font-monospace">₹{{ number_format($bom->cost_rate, 2) }}</td>
                            <td class="text-end font-monospace fw-bold text-success">₹{{ number_format($bom->material_cost, 2) }}</td>
                            @if(!$sample->is_frozen)
                                <td class="text-end">
                                    <form action="{{ route('sampling.samples.bom.delete', [$sample->id, $bom->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted small">
                                No materials added yet. Click "+ Add Material" above to add cloth, yarn, buttons, or trims.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sample->activeBoms->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="8" class="text-end">Total Material Cost:</td>
                            <td class="text-end text-success font-monospace fs-6">₹{{ number_format($sample->activeBoms->sum('material_cost'), 2) }}</td>
                            @if(!$sample->is_frozen) <td></td> @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 3: MEASUREMENTS & SPECS                              --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-measurements" role="tabpanel">
        <div class="row g-4">
            {{-- Left: Measurements Table --}}
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Finished Garment Measurements (Naap)</h5>
                        <small class="text-muted">Standard points of measure for quality control</small>
                    </div>
                    @if(!$sample->is_frozen)
                        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addMeasurementModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Measurement
                        </button>
                    @endif
                </div>

                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Point of Measure</th>
                                <th>Size / Dimension</th>
                                <th>Tolerance (+/-)</th>
                                <th>How to Measure</th>
                                @if(!$sample->is_frozen) <th class="text-end" style="width: 50px;"></th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sample->activeMeasurements as $m)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $m->measurement_point_name }}</td>
                                    <td class="font-monospace fs-6 text-primary fw-bold">{{ $m->specified_dimension }} {{ $m->unit_of_measure }}</td>
                                    <td class="font-monospace text-muted">&plusmn; {{ $m->tolerance_plus ?? 0.5 }} {{ $m->unit_of_measure }}</td>
                                    <td class="small text-muted">{{ $m->how_to_measure ?: 'Measure flat on table' }}</td>
                                    @if(!$sample->is_frozen)
                                        <td class="text-end">
                                            <form action="{{ route('sampling.samples.measurements.delete', [$sample->id, $m->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete measurement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        No measurements added yet (e.g. Chest Width, Body Length, Sleeve Length).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right: Technical Specs --}}
            <div class="col-lg-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0 text-dark">Technical Specs</h5>
                    @if(!$sample->is_frozen)
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSpecModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Spec
                        </button>
                    @endif
                </div>

                <div class="border rounded p-3 bg-light">
                    @forelse($sample->activeSpecifications as $spec)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.7rem;">{{ $spec->attribute_name }}</small>
                                <span class="fs-6 fw-bold text-dark">{{ $spec->attribute_value }} {{ $spec->unit }}</span>
                            </div>
                            @if(!$sample->is_frozen)
                                <form action="{{ route('sampling.samples.specs.delete', [$sample->id, $spec->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete spec?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            No technical specs added yet (e.g. Machine Gauge, Needle Size, Yarn Count).
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 4: MAKING STEPS (OPERATIONS)                         --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-operations" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Making Steps & Sequence (Karigari)</h5>
                <small class="text-muted">Operations required to craft this sample and labour time taken</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <span class="small text-muted d-block">Total Labour Cost:</span>
                    <span class="fs-5 fw-bold font-monospace text-primary">₹{{ number_format($sample->activeOperations->sum('estimated_labour_cost'), 2) }}</span>
                </div>
                @if(!$sample->is_frozen)
                    <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addOperationModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Making Step
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle border mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 60px;">Step #</th>
                        <th>Operation Name</th>
                        <th>Department</th>
                        <th>Skill Level</th>
                        <th class="text-end">Time (Mins)</th>
                        <th class="text-end">Rate / Hr (₹)</th>
                        <th class="text-end">Labour Cost (₹)</th>
                        @if(!$sample->is_frozen) <th class="text-end" style="width: 50px;"></th> @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sample->activeOperations as $op)
                        <tr>
                            <td class="font-monospace fw-bold text-muted">#{{ $op->sequence_number }}</td>
                            <td class="fw-bold text-dark">{{ $op->operation_name }}</td>
                            <td>{{ $op->division->name ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $op->skill_level ?: 'General' }}</span></td>
                            <td class="text-end font-monospace">{{ number_format($op->estimated_time_minutes, 1) }} min</td>
                            <td class="text-end font-monospace">₹{{ number_format($op->labour_rate_per_hour, 2) }}</td>
                            <td class="text-end font-monospace fw-bold text-primary">₹{{ number_format($op->estimated_labour_cost, 2) }}</td>
                            @if(!$sample->is_frozen)
                                <td class="text-end">
                                    <form action="{{ route('sampling.samples.operations.delete', [$sample->id, $op->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove operation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">
                                No making steps added yet (e.g. Cutting, Hand Knitting, Assembly, Washing, Pressing).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sample->activeOperations->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Total Time & Labour Cost:</td>
                            <td class="text-end font-monospace">{{ number_format($sample->activeOperations->sum('estimated_time_minutes'), 1) }} min</td>
                            <td></td>
                            <td class="text-end text-primary font-monospace fs-6">₹{{ number_format($sample->activeOperations->sum('estimated_labour_cost'), 2) }}</td>
                            @if(!$sample->is_frozen) <td></td> @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 5: VOICE & PRODUCTION NOTES                          --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-notes" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">🎙️ Voice Notes & Artisan Tips</h5>
                <small class="text-muted">Master Ji can speak into the microphone to record critical stitching tips</small>
            </div>
            @if(!$sample->is_frozen)
                <button type="button" class="btn btn-warning fw-bold text-dark px-3" data-bs-toggle="modal" data-bs-target="#addProductionNoteModal">
                    <i class="bi bi-mic me-1"></i> Record Voice / Write Note
                </button>
            @endif
        </div>

        <div class="row g-3">
            @forelse($sample->activeProductionNotes as $note)
                <div class="col-12">
                    <div class="card border p-3 bg-light shadow-sm" style="border-left: 5px solid #f59e0b !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark font-monospace">Note #{{ $note->sequence }}</span>
                                <h6 class="fw-bold text-dark m-0">{{ $note->subject }}</h6>
                            </div>
                            <div class="small text-muted">
                                Recorded on {{ $note->created_at->format('d M Y') }} by <strong>{{ $note->creator->name ?? 'Artisan' }}</strong>
                                @if(!$sample->is_frozen)
                                    <form action="{{ route('sampling.samples.notes.delete', [$sample->id, $note->id]) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Delete this note?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1 border-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($note->written_note)
                            <p class="text-dark small mb-2 p-2 bg-white rounded border">{{ $note->written_note }}</p>
                        @endif

                        {{-- Audio Player --}}
                        @if($note->voice_audio_path)
                            <div class="p-2 bg-white border rounded d-flex align-items-center gap-3 mb-2">
                                <i class="bi bi-volume-up-fill fs-4 text-warning"></i>
                                <div class="flex-grow-1">
                                    <audio controls class="w-100" style="height: 36px;">
                                        <source src="{{ $note->audio_url }}" type="audio/webm">
                                        <source src="{{ $note->audio_url }}" type="audio/mpeg">
                                        Your browser does not support audio playback.
                                    </audio>
                                </div>
                                @if($note->audio_duration_seconds)
                                    <span class="badge bg-light text-dark border font-monospace">{{ gmdate('i:s', $note->audio_duration_seconds) }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Attached Warning Photos --}}
                        @if($note->attachments->count() > 0)
                            <div class="mt-2 pt-2 border-top">
                                <small class="fw-semibold text-muted d-block mb-1">Attached Warning Photos:</small>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach($note->attachments as $att)
                                        <a href="{{ $att->file_url }}" target="_blank">
                                            <img src="{{ $att->file_url }}" class="rounded border" style="width: 75px; height: 75px; object-fit: cover;">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-mic fs-1 text-warning opacity-50 mb-2 d-block"></i>
                    <h6>No Voice Notes Recorded Yet</h6>
                    <p class="small text-muted mb-3">Master Ji can tap the button above to speak tips into the microphone.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 6: PHOTOS & PHYSICAL STORAGE                         --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-photos" role="tabpanel">
        {{-- Technical Photos --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Approved Sample Photos</h5>
                <small class="text-muted">Front, back, and detail pictures of the finished sample</small>
            </div>
            @if(!$sample->is_frozen)
                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addFinalImageModal">
                    <i class="bi bi-camera me-1"></i> Upload Photo
                </button>
            @endif
        </div>

        <div class="row g-3 mb-4">
            @forelse($sample->activeFinalImages as $img)
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border overflow-hidden">
                        <a href="{{ $img->image_url }}" target="_blank">
                            <img src="{{ $img->image_url }}" class="img-fluid" style="height: 180px; width: 100%; object-fit: cover;">
                        </a>
                        <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary text-capitalize" style="font-size: 0.72rem;">{{ str_replace('_', ' ', $img->image_type) }}</span>
                                <small class="text-muted d-block text-truncate mt-1" style="max-width: 140px;">{{ $img->caption ?: 'View' }}</small>
                            </div>
                            @if(!$sample->is_frozen)
                                <form action="{{ route('sampling.samples.photos.delete', [$sample->id, $img->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete photo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted small border rounded bg-light">
                    No technical photos uploaded yet. (Click "+ Upload Photo" to add garment pictures).
                </div>
            @endforelse
        </div>

        {{-- Physical Storage Archive Location --}}
        <div class="pt-4 border-top">
            <h5 class="fw-bold text-dark mb-2">Physical Sample Room Storage</h5>
            <p class="text-muted small mb-3">Where this physical garment is stored in the studio archive</p>

            @php $ps = $sample->activePhysicalStorage; @endphp
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    @if(!$sample->is_frozen)
                        <form action="{{ route('sampling.samples.storage', $sample->id) }}" method="POST">
                            @csrf
                            <div class="p-3 border rounded bg-light">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Select Studio Rack / Shelf / Box <span class="text-danger">*</span></label>
                                        <select name="storage_location_id" class="form-select" required>
                                            <option value="">-- Select Rack / Box --</option>
                                            @foreach($storageLocations as $loc)
                                                <option value="{{ $loc->id }}" {{ ($ps && $ps->storage_location_id == $loc->id) ? 'selected' : '' }}>
                                                    {{ $loc->formatted_location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Date Placed</label>
                                        <input type="date" name="date_stored" class="form-control" value="{{ old('date_stored', $ps?->date_stored?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Quantity</label>
                                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $ps?->quantity ?? 1) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Condition</label>
                                        <input type="text" name="sample_condition" class="form-control" value="{{ old('sample_condition', $ps?->sample_condition ?? 'Approved Master Reference') }}" required>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">Save Storage</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="p-3 border rounded bg-light">
                            <span class="text-muted small d-block">Current Location:</span>
                            <h5 class="fw-bold text-dark my-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $ps->location->formatted_location ?? 'Not specified' }}</h5>
                            <small class="text-muted">Date: {{ $ps?->date_stored?->format('d M Y') ?? '-' }} &bull; Qty: {{ $ps?->quantity ?? 1 }}</small>
                        </div>
                    @endif
                </div>

                <div class="col-lg-5 text-center">
                    <div class="p-3 border rounded bg-light">
                        <i class="bi bi-qr-code fs-1 text-dark mb-1"></i>
                        <div class="fw-bold">Garment Tag Code</div>
                        <div class="fs-5 font-monospace fw-bold text-primary my-1">{{ $sample->sample_code }}</div>
                        <small class="text-muted">Attach this tag code to the garment hanger</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TAB 7: APPROVAL & FREEZE                                 --}}
    {{-- ======================================================== --}}
    <div class="tab-pane fade" id="tab-approval" role="tabpanel">
        <div class="row g-4">
            {{-- Sign-Off Column --}}
            <div class="col-lg-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h5 class="fw-bold mb-2 text-dark">1. Design Approval</h5>

                    @if($sample->approval_status === 'approved')
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3 py-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div>
                                <strong>Sample Approved!</strong> Accepted on {{ $sample->approval_date?->format('d M Y') }} by {{ $sample->approvedByUser->name ?? 'Design Team' }}.
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="bi bi-hourglass-split me-1"></i> Current Status: <strong>{{ ucfirst($sample->approval_status) }}</strong>
                        </div>
                    @endif

                    @if(!$sample->is_frozen)
                        <form action="{{ route('sampling.samples.approval', $sample->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Review Comments</label>
                                <textarea name="review_comments" class="form-control" rows="2" placeholder="Approval notes..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                @if($sample->approval_status !== 'approved')
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm fw-bold">
                                        <i class="bi bi-check-lg me-1"></i> Accept & Approve Sample
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm">
                                        Request Changes
                                    </button>
                                @else
                                    <span class="text-success small fw-bold"><i class="bi bi-check-all me-1"></i> Sample is already approved.</span>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Freeze Column --}}
            <div class="col-lg-6">
                <div class="p-3 border rounded bg-light h-100">
                    <h5 class="fw-bold mb-2 text-dark">2. Factory Production Freeze</h5>

                    @if($sample->is_frozen)
                        <div class="alert alert-primary mb-3">
                            <h6 class="fw-bold"><i class="bi bi-lock-fill me-1"></i> Sample is FROZEN</h6>
                            <p class="small mb-0">Active Revision: <strong>{{ $sample->currentRevision->revision_code }}</strong> (Frozen {{ $sample->currentRevision->frozen_date?->format('d M Y') }}). This is now the locked master for bulk production.</p>
                        </div>
                        <button type="button" class="btn btn-outline-primary fw-bold w-100" data-bs-toggle="modal" data-bs-target="#createRevisionModal">
                            <i class="bi bi-arrow-repeat me-1"></i> Create New Revision (e.g. REV 1)
                        </button>
                    @else
                        <p class="small text-muted mb-2">Checklist before sealing sample for factory:</p>
                        <div class="list-group mb-3 shadow-sm">
                            @foreach($checklistEvaluation['checklist'] as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 small">
                                    <span class="fw-semibold text-dark">{{ $item['title'] }}</span>
                                    @if($item['passed'])
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i> Ready</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i> Missing</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($checklistEvaluation['can_freeze'])
                            <button type="button" class="btn btn-success fw-bold w-100 py-2" data-bs-toggle="modal" data-bs-target="#freezeChecklistModal">
                                <i class="bi bi-snow me-1"></i> Freeze & Lock Master Sample Now
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary w-100 py-2" disabled>
                                Complete Missing Items to Enable Freeze
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Revision History Table --}}
            @if($sample->revisions->count() > 0)
                <div class="col-12">
                    <h6 class="fw-bold mb-2">Revision History Log</h6>
                    <div class="table-responsive border rounded">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Revision</th>
                                    <th>Frozen Date</th>
                                    <th>Frozen By</th>
                                    <th>Direct Unit Cost</th>
                                    <th>Reason / Notes</th>
                                    <th>State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sample->revisions as $rev)
                                    <tr>
                                        <td class="font-monospace fw-bold text-primary">{{ $rev->revision_code }}</td>
                                        <td>{{ $rev->frozen_date->format('d M Y, h:i A') }}</td>
                                        <td>{{ $rev->freezer->name ?? '-' }}</td>
                                        <td class="font-monospace fw-bold text-success">₹{{ number_format($rev->direct_production_cost, 2) }}</td>
                                        <td class="text-muted">{{ $rev->change_summary }}</td>
                                        <td>
                                            @if($rev->is_active)
                                                <span class="badge bg-success">Active Master</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Historical</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ======================================================== --}}
{{-- MODALS SECTION                                           --}}
{{-- ======================================================== --}}

{{-- Modal 1: Add BOM Material --}}
<div class="modal fade" id="addBomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Material (BOM)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.bom', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Material Category <span class="text-danger">*</span></label>
                            <select name="material_category" class="form-select" required>
                                <option value="Fabric">Fabric</option>
                                <option value="Yarn">Yarn</option>
                                <option value="Thread">Sewing / Embroidery Thread</option>
                                <option value="Buttons">Buttons</option>
                                <option value="Zippers">Zippers</option>
                                <option value="Labels">Labels / Tags</option>
                                <option value="Lining">Lining</option>
                                <option value="Trims">Trims</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Material Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" placeholder="e.g. 2-Ply Pure Cashmere Wool" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Colour / Shade</label>
                            <input type="text" name="colour" class="form-control" placeholder="e.g. Melange Grey">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Net Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" name="net_quantity" class="form-control" placeholder="e.g. 450" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unit of Measure <span class="text-danger">*</span></label>
                            <select name="unit_of_measure" class="form-select" required>
                                <option value="Grams">Grams (g)</option>
                                <option value="Kg">Kilograms (kg)</option>
                                <option value="Metres">Metres (m)</option>
                                <option value="Yards">Yards</option>
                                <option value="Pieces">Pieces (pcs)</option>
                                <option value="Cones">Cones</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Wastage %</label>
                            <input type="number" step="0.1" name="wastage_percentage" class="form-control" value="0.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cost Rate per Unit (₹)</label>
                            <input type="number" step="0.01" name="cost_rate" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Add Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Add Measurement Point --}}
<div class="modal fade" id="addMeasurementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Measurement Point</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.measurements', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Point of Measure <span class="text-danger">*</span></label>
                            <input type="text" name="measurement_point_name" class="form-control" placeholder="e.g. Chest Width, Body Length, Sleeve Length" list="commonPoints" required>
                            <datalist id="commonPoints">
                                <option value="Chest Width">
                                <option value="Across Shoulder">
                                <option value="Total Body Length">
                                <option value="Sleeve Length">
                                <option value="Armhole Depth">
                                <option value="Neck Width">
                                <option value="Collar Height">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Specified Dimension <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="specified_dimension" class="form-control" placeholder="e.g. 42.0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Unit <span class="text-danger">*</span></label>
                            <select name="unit_of_measure" class="form-select" required>
                                <option value="inches">Inches (")</option>
                                <option value="cm">Centimetres (cm)</option>
                                <option value="mm">Millimetres (mm)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tolerance (+/-)</label>
                            <input type="number" step="0.01" name="tolerance_plus" class="form-control" value="0.5">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">How to Measure</label>
                            <input type="text" name="how_to_measure" class="form-control" placeholder="e.g. Measure flat 1 inch below armhole">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Save Measurement</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Add Technical Spec --}}
<div class="modal fade" id="addSpecModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Technical Specification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.specs', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Attribute Name <span class="text-danger">*</span></label>
                            <input type="text" name="attribute_name" class="form-control" placeholder="e.g. Machine Gauge, Yarn Count" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Value <span class="text-danger">*</span></label>
                            <input type="text" name="attribute_value" class="form-control" placeholder="e.g. 7 GG / 2-Ply" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="e.g. GG, ply">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Add Spec</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 4: Add Making Step (Operation) --}}
<div class="modal fade" id="addOperationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Making Step (Operation)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.operations', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Operation / Process Name <span class="text-danger">*</span></label>
                            <input type="text" name="operation_name" class="form-control" placeholder="e.g. Hand Flat Knitting / Panel Linking" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                            <select name="sampling_division_id" class="form-select" required>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Skill Level</label>
                            <select name="skill_level" class="form-select">
                                @forelse($skillLevels as $sk)
                                    <option value="{{ $sk->name }}">{{ $sk->name }}</option>
                                @empty
                                    <option value="Master Artisan">Master Artisan</option>
                                    <option value="Skilled">Skilled</option>
                                    <option value="Semi-Skilled">Semi-Skilled</option>
                                    <option value="Trainee">Trainee</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estimated Time (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="estimated_time_minutes" class="form-control" placeholder="e.g. 45" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Labour Rate / Hr (₹)</label>
                            <input type="number" step="0.01" name="labour_rate_per_hour" class="form-control" value="200.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Add Step</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 5: Upload Final Photo --}}
<div class="modal fade" id="addFinalImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Upload Garment Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.photos', $sample->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Photo Angle <span class="text-danger">*</span></label>
                            <select name="image_type" class="form-select" required>
                                <option value="front">Front View (Main)</option>
                                <option value="back">Back View</option>
                                <option value="stitch_detail">Stitch / Cable Detail</option>
                                <option value="flat_lay">Flat Lay</option>
                                <option value="construction_detail">Construction Detail</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Select Image File <span class="text-danger">*</span></label>
                            <input type="file" name="image_file" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Caption</label>
                            <input type="text" name="caption" class="form-control" placeholder="e.g. Front view with collar">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Upload Photo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 6: Record Voice & Production Note --}}
<div class="modal fade" id="addProductionNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-mic-fill me-2"></i>Record Voice / Write Production Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.notes', $sample->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    {{-- Interactive Audio Recorder --}}
                    <div class="p-3 rounded border text-center mb-3 bg-light">
                        <h6 class="fw-bold text-dark mb-1">🎤 Speak into your microphone</h6>
                        <p class="text-muted small mb-2">Click Record, speak your tips, and click Stop.</p>

                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <button type="button" id="recordBtn" class="btn btn-danger btn-sm px-3 fw-bold" onclick="toggleAudioRecording()">
                                <i class="bi bi-record-circle me-1"></i> Start Recording
                            </button>
                            <span id="recordingTimer" class="font-monospace fw-bold text-danger" style="display: none;">00:00</span>
                        </div>

                        <audio id="audioPreview" controls class="mt-2 mx-auto w-100" style="display: none; height: 36px;"></audio>

                        <input type="hidden" name="audio_base64" id="audioBase64Input">
                        <input type="hidden" name="audio_duration" id="audioDurationInput">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Subject / Title <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Collar Tension & Steam Press Warning" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Department</label>
                            <select name="division_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Written Instruction</label>
                            <textarea name="written_note" class="form-control" rows="2" placeholder="Write any specific points for production..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Or Upload Audio File (.mp3, .wav, .m4a)</label>
                            <input type="file" name="audio_file" class="form-control" accept="audio/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Attach Photos of Mistakes / Warnings (Optional)</label>
                            <input type="file" name="attachments[]" class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 7: Add Reference / Sketch --}}
<div class="modal fade" id="addReferenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Sketch / Swatch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.references', $sample->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                            <select name="reference_type" class="form-select" required>
                                @if(isset($referenceTypes) && $referenceTypes->count() > 0)
                                    @foreach($referenceTypes as $rt)
                                        <option value="{{ $rt->code }}">{{ $rt->name }}</option>
                                    @endforeach
                                @else
                                    <option value="sketch">Sketch / Drawing</option>
                                    <option value="fabric_ref">Fabric Swatch</option>
                                    <option value="photograph">Photo Reference</option>
                                    <option value="web_url">External Link</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Collar cable swatch" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Attach File</label>
                            <input type="file" name="reference_file" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Or External URL</label>
                            <input type="url" name="url" class="form-control" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 8: Freeze Confirmation Modal --}}
<div class="modal fade" id="freezeChecklistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-snow me-2"></i>Freeze & Lock Master Sample</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.freeze', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-dark mb-3">
                        Freezing seals this approved sample into an <strong>immutable Revision 0 Master</strong>. All future bulk production orders will strictly follow this sheet.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Release Summary / Comments</label>
                        <input type="text" name="change_summary" class="form-control" value="Initial Approved Production Master Revision 0">
                    </div>
                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="bi bi-lock-fill me-1"></i> Once frozen, technical records cannot be altered except by creating Revision 1.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-snow me-1"></i> Confirm & Freeze Sample
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 9: Create Next Revision Modal --}}
<div class="modal fade" id="createRevisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Create New Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.samples.create-revision', $sample->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        This clones the current frozen master into a new working revision so changes can be made without altering past production records.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Revision <span class="text-danger">*</span></label>
                        <textarea name="change_justification" class="form-control" rows="3" placeholder="e.g. Customer requested collar ribbing change from 2-ply to 3-ply" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Create Revision</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab switching based on URL parameter or hash
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || window.location.hash.replace('#', '');

        const tabMap = {
            'overview': '#tab-overview-btn',
            'references': '#tab-overview-btn',
            'bom': '#tab-materials-btn',
            'materials': '#tab-materials-btn',
            'measurements': '#tab-measurements-btn',
            'specifications': '#tab-measurements-btn',
            'operations': '#tab-operations-btn',
            'notes': '#tab-notes-btn',
            'production_notes': '#tab-notes-btn',
            'voice': '#tab-notes-btn',
            'photos': '#tab-photos-btn',
            'storage': '#tab-photos-btn',
            'approval': '#tab-approval-btn',
            'freeze': '#tab-approval-btn'
        };

        if (tabParam && tabMap[tabParam]) {
            const btnEl = document.querySelector(tabMap[tabParam]);
            if (btnEl) {
                const triggerTab = new bootstrap.Tab(btnEl);
                triggerTab.show();
            }
        }

        // When user clicks a tab, update URL query parameter cleanly without reloading
        const tabButtons = document.querySelectorAll('#sampleTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function (e) {
                const targetId = e.target.getAttribute('data-bs-target');
                let tabName = 'overview';
                if (targetId === '#tab-materials') tabName = 'bom';
                else if (targetId === '#tab-measurements') tabName = 'measurements';
                else if (targetId === '#tab-operations') tabName = 'operations';
                else if (targetId === '#tab-notes') tabName = 'notes';
                else if (targetId === '#tab-photos') tabName = 'photos';
                else if (targetId === '#tab-approval') tabName = 'approval';

                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', newUrl);
            });
        });
    });

    // Audio Recording Logic
    let mediaRecorder;
    let audioChunks = [];
    let recordStartTime;
    let timerInterval;

    async function toggleAudioRecording() {
        const recordBtn = document.getElementById('recordBtn');
        const timer = document.getElementById('recordingTimer');
        const audioPreview = document.getElementById('audioPreview');

        if (!mediaRecorder || mediaRecorder.state === 'inactive') {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;
                    audioPreview.style.display = 'block';

                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = () => {
                        document.getElementById('audioBase64Input').value = reader.result;
                    };

                    const durationSec = Math.round((Date.now() - recordStartTime) / 1000);
                    document.getElementById('audioDurationInput').value = durationSec;

                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();
                recordStartTime = Date.now();
                recordBtn.classList.replace('btn-danger', 'btn-dark');
                recordBtn.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Stop Recording';
                timer.style.display = 'inline';

                timerInterval = setInterval(() => {
                    const sec = Math.floor((Date.now() - recordStartTime) / 1000);
                    const mins = Math.floor(sec / 60);
                    const remSec = sec % 60;
                    timer.textContent = `${String(mins).padStart(2, '0')}:${String(remSec).padStart(2, '0')}`;
                }, 1000);
            } catch (err) {
                alert('Microphone access was denied or unavailable: ' + err.message);
            }
        } else if (mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            clearInterval(timerInterval);
            recordBtn.classList.replace('btn-dark', 'btn-outline-success');
            recordBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Re-record Voice';
        }
    }
</script>
@endpush
@endsection
