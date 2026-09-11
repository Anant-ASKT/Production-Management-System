@extends('layouts.sampling')

@section('title', 'Samples Catalog')
@section('page-title', 'Samples Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-layers-half me-2 text-primary"></i>Samples Development Catalog</h4>
        <p class="text-muted small mb-0">Browse and track individual product samples through development and freezing</p>
    </div>
    <div>
        <a href="{{ route('sampling.samples.create') }}" class="btn btn-primary px-3 fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
            <i class="bi bi-plus-lg me-1"></i> New Sample
        </a>
    </div>
</div>

<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('sampling.samples.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by sample code, style name..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Approvals</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Dev</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="division_id" class="form-select">
                <option value="">All Departments</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="filter" class="form-select">
                <option value="">All Master States</option>
                <option value="frozen" {{ request('filter') === 'frozen' ? 'selected' : '' }}>Frozen Only</option>
                <option value="ready_to_freeze" {{ request('filter') === 'ready_to_freeze' ? 'selected' : '' }}>Ready to Freeze</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3 w-100">Filter</button>
            <a href="{{ route('sampling.samples.index') }}" class="btn btn-light border">Reset</a>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 170px;">Sample Code</th>
                        <th>Style / Product</th>
                        <th>Project & Batch</th>
                        <th>Department</th>
                        <th>Assigned Person</th>
                        <th>Priority</th>
                        <th>Approval</th>
                        <th>Master State</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($samples as $sample)
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
                                <div class="small fw-semibold text-dark">{{ $sample->project->project_name ?? '-' }}</div>
                                <span class="badge bg-light text-dark border font-monospace">{{ $sample->batch->batch_number ?? '-' }}</span>
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
                                @if($sample->priority === 'urgent')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Urgent</span>
                                @elseif($sample->priority === 'high')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">High</span>
                                @else
                                    <span class="badge bg-light text-muted border text-capitalize">{{ $sample->priority }}</span>
                                @endif
                            </td>
                            <td>
                                @if($sample->approval_status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                @elseif($sample->approval_status === 'submitted')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <i class="bi bi-hourglass-split me-1"></i> Under Review
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        Development
                                    </span>
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
                                <a href="{{ route('sampling.samples.show', $sample->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    Open 360° <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-layers fs-1 opacity-50 mb-2 d-block"></i>
                                <h5>No Samples Found</h5>
                                <p class="small mb-3">Adjust your search filters or create a new sample to begin.</p>
                                <a href="{{ route('sampling.samples.create') }}" class="btn btn-sm btn-primary">Create Sample</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($samples->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $samples->links() }}
        </div>
    @endif
</div>
@endsection
