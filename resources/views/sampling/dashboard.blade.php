@extends('layouts.sampling')

@section('title', 'Sampling Dashboard')
@section('page-title', 'Studio Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Welcome back, {{ auth()->guard('sampling')->user()->name }}!</h4>
        <p class="text-muted small mb-0">{{ auth()->guard('sampling')->user()->company->name }} &bull; Development Pipeline</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sampling.projects.create') }}" class="btn btn-outline-primary fw-semibold">
            <i class="bi bi-folder-plus me-1"></i> New Project
        </a>
        <a href="{{ route('sampling.samples.create') }}" class="btn btn-primary fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
            <i class="bi bi-plus-lg me-1"></i> New Sample
        </a>
    </div>
</div>

{{-- KPI Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-xl">
        <div class="card p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Active Projects</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $activeProjectsCount }}</h3>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-folder2-open fs-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('sampling.projects.index') }}" class="small text-primary text-decoration-none fw-semibold">
                    View all projects <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">In Development</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $samplesUnderDevCount }}</h3>
                </div>
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('sampling.samples.index', ['status' => 'pending']) }}" class="small text-warning text-decoration-none fw-semibold">
                    View in development <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Awaiting Approval</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $awaitingApprovalCount }}</h3>
                </div>
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-patch-check fs-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('sampling.samples.index', ['status' => 'submitted']) }}" class="small text-info text-decoration-none fw-semibold">
                    Review submissions <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card p-3 h-100 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Ready to Freeze</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $readyToFreezeCount }}</h3>
                </div>
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('sampling.samples.index', ['filter' => 'ready_to_freeze']) }}" class="small text-success text-decoration-none fw-semibold">
                    Doc pending / freeze <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl">
        <div class="card p-3 h-100 bg-white" style="border-left: 4px solid #0284c7;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Frozen Masters</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $frozenCount }}</h3>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-snow fs-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('sampling.samples.index', ['filter' => 'frozen']) }}" class="small text-primary text-decoration-none fw-semibold">
                    Production ready <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Quick Filter / Search Bar --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('sampling.samples.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Fast search sample code, style name, or description...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Approval Statuses</option>
                <option value="pending">Pending Development</option>
                <option value="submitted">Submitted for Approval</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">Search Samples</button>
        </div>
    </form>
</div>

{{-- Recent Samples Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0 fw-bold text-dark">
            <i class="bi bi-clock-history me-2 text-primary"></i>Recent Samples
        </h5>
        <a href="{{ route('sampling.samples.index') }}" class="btn btn-sm btn-light border">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Sample Code</th>
                        <th>Style / Product</th>
                        <th>Project & Batch</th>
                        <th>Responsible Division</th>
                        <th>Assigned Person</th>
                        <th>Approval</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSamples as $sample)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('sampling.samples.show', $sample->id) }}" class="fw-bold font-monospace text-decoration-none text-primary">
                                    {{ $sample->sample_code }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $sample->style_name }}</div>
                                <small class="text-muted">{{ Str::limit($sample->description, 35) }}</small>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $sample->project->project_name ?? '-' }}</div>
                                <span class="badge bg-light text-dark border">{{ $sample->batch->batch_number ?? '-' }}</span>
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
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <i class="bi bi-hourglass-split me-1"></i> Under Review
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="bi bi-pencil-fill me-1"></i> Development
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($sample->is_frozen)
                                    <span class="badge bg-primary text-white">
                                        <i class="bi bi-snow me-1"></i> FROZEN {{ $sample->currentRevision->revision_number ?? '0' }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">In Progress</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('sampling.samples.show', $sample->id) }}" class="btn btn-sm btn-outline-primary">
                                    Open 360° <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-box2 fs-1 opacity-50 mb-2 d-block"></i>
                                <h6>No Samples Created Yet</h6>
                                <p class="small mb-3">Start by creating a project, batch, and adding your first sample.</p>
                                <a href="{{ route('sampling.samples.create') }}" class="btn btn-sm btn-primary">Create Sample</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
