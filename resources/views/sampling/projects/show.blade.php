@extends('layouts.sampling')

@section('title', $project->project_name)
@section('page-title', 'Project Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark-subtle text-dark border font-monospace">{{ $project->project_code }}</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
        </div>
        <h4 class="fw-bold mb-0 text-dark">{{ $project->project_name }}</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sampling.projects.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i> All Projects
        </a>
        <button type="button" class="btn btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#addBatchModal">
            <i class="bi bi-plus-lg me-1"></i> Add Batch
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Project Overview Card --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="row g-3 text-muted small">
                    <div class="col-md-3">
                        <strong class="text-dark d-block">Start Date</strong>
                        {{ $project->start_date?->format('d M Y') ?? '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong class="text-dark d-block">Target Completion</strong>
                        {{ $project->target_completion_date?->format('d M Y') ?? 'Flexible' }}
                    </div>
                    <div class="col-md-3">
                        <strong class="text-dark d-block">Batches Count</strong>
                        {{ $project->batches->count() }} Batches
                    </div>
                    <div class="col-md-3">
                        <strong class="text-dark d-block">Total Samples</strong>
                        {{ $project->batches->sum(fn($b) => $b->samples->count()) }} Samples
                    </div>
                    @if($project->notes)
                        <div class="col-12 mt-2 pt-2 border-top">
                            <strong class="text-dark">Direction Notes: </strong> {{ $project->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3 me-2 text-primary"></i>Development Batches</h5>

<div class="row g-4">
    @forelse($project->batches as $batch)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary text-white font-monospace fs-6 px-3 py-1">{{ $batch->batch_number }}</span>
                        <div>
                            <h6 class="m-0 fw-bold text-dark">{{ $batch->batch_name }}</h6>
                            <small class="text-muted">Started {{ $batch->start_date?->format('d M Y') }} &bull; {{ $batch->samples->count() }} {{ Str::plural('Sample', $batch->samples->count()) }}</small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('sampling.samples.create', ['project_id' => $project->id, 'batch_id' => $batch->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Add Sample to {{ $batch->batch_number }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 180px;">Sample Code</th>
                                    <th>Style Name / Category</th>
                                    <th>Department</th>
                                    <th>Responsible Person</th>
                                    <th>Approval Status</th>
                                    <th>Master State</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batch->samples as $sample)
                                    <tr>
                                        <td class="ps-3 font-monospace fw-bold">
                                            <a href="{{ route('sampling.samples.show', $sample->id) }}" class="text-decoration-none text-primary">
                                                {{ $sample->sample_code }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $sample->style_name }}</div>
                                            <small class="text-muted">{{ Str::limit($sample->description, 35) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                {{ $sample->overallDivision->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $sample->assignedPerson->name ?? 'Unassigned' }}</small>
                                        </td>
                                        <td>
                                            @if($sample->approval_status === 'approved')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Approved
                                                </span>
                                            @elseif($sample->approval_status === 'submitted')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">Under Review</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">In Development</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sample->is_frozen)
                                                <span class="badge bg-primary text-white">
                                                    <i class="bi bi-snow me-1"></i> FROZEN {{ $sample->currentRevision->revision_number ?? '0' }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border">Working Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('sampling.samples.show', $sample->id) }}" class="btn btn-sm btn-outline-primary">
                                                Workspace <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <small>No samples added to this batch yet.</small>
                                            <a href="{{ route('sampling.samples.create', ['project_id' => $project->id, 'batch_id' => $batch->id]) }}" class="btn btn-link btn-sm text-primary">
                                                Add First Sample
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-4 text-muted">
            No batches created. Click "Add Batch" to begin.
        </div>
    @endforelse
</div>

{{-- Add Batch Modal --}}
<div class="modal fade" id="addBatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2 text-primary"></i>Add New Batch to Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.projects.batches.store', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Batch Name / Scope <span class="text-danger">*</span></label>
                            <input type="text" name="batch_name" class="form-control" placeholder="e.g. Batch 2 — Crochet & Accessories" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Completion</label>
                            <input type="date" name="target_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Batch Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
