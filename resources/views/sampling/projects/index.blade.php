@extends('layouts.sampling')

@section('title', 'Sampling Projects')
@section('page-title', 'Sampling Projects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open me-2 text-primary"></i>Sampling Projects</h4>
        <p class="text-muted small mb-0">Overview of design and collection development projects</p>
    </div>
    <div>
        <a href="{{ route('sampling.projects.create') }}" class="btn btn-primary px-3 fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
            <i class="bi bi-plus-lg me-1"></i> New Project
        </a>
    </div>
</div>

<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('sampling.projects.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search projects by name or code..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_development" {{ request('status') === 'in_development' ? 'selected' : '' }}>In Development</option>
                <option value="partially_approved" {{ request('status') === 'partially_approved' ? 'selected' : '' }}>Partially Approved</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Filter</button>
            <a href="{{ route('sampling.projects.index') }}" class="btn btn-light border px-3">Reset</a>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th style="width: 150px;">Project Code</th>
                        <th>Project Name</th>
                        <th class="text-center" style="width: 120px;">Batches</th>
                        <th class="text-center" style="width: 120px;">Samples</th>
                        <th style="width: 140px;">Start Date</th>
                        <th style="width: 140px;">Target Date</th>
                        <th style="width: 130px;">Status</th>
                        <th class="text-end pe-3" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        <tr>
                            <td class="ps-3 text-muted small">
                                {{ $projects->firstItem() ? $projects->firstItem() + $index : $index + 1 }}
                            </td>
                            <td class="font-monospace fw-bold">
                                <a href="{{ route('sampling.projects.show', $project->id) }}" class="badge bg-light text-dark border text-decoration-none py-2 px-2">
                                    <i class="bi bi-folder me-1 text-primary"></i>{{ $project->project_code }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('sampling.projects.show', $project->id) }}" class="text-decoration-none text-dark">
                                        {{ $project->project_name }}
                                    </a>
                                </div>
                                @if($project->notes)
                                    <small class="text-muted d-block text-truncate" style="max-width: 320px;">{{ $project->notes }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-monospace">
                                    <i class="bi bi-diagram-3 text-secondary me-1"></i>{{ $project->batches_count }} {{ Str::plural('Batch', $project->batches_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                                    <i class="bi bi-layers-half me-1"></i>{{ $project->samples_count }} {{ Str::plural('Sample', $project->samples_count) }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                <i class="bi bi-calendar-check me-1 text-secondary"></i>
                                {{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}
                            </td>
                            <td class="small text-muted">
                                @if($project->target_completion_date)
                                    <i class="bi bi-calendar-event me-1 text-secondary"></i>
                                    {{ $project->target_completion_date->format('d M Y') }}
                                @else
                                    <span class="text-muted">Flexible</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'open' => 'bg-success-subtle text-success border border-success-subtle',
                                        'in_development' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                        'partially_approved' => 'bg-info-subtle text-info border border-info-subtle',
                                        'completed' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                        'closed' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                        'draft' => 'bg-light text-dark border',
                                    ];
                                    $badgeClass = $statusClasses[$project->status] ?? 'bg-light text-dark border';
                                @endphp
                                <span class="badge {{ $badgeClass }} text-capitalize px-2 py-1">
                                    {{ str_replace('_', ' ', $project->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('sampling.projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary fw-semibold px-2 py-1" title="Open Project Batches">
                                    Batches <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="{{ route('sampling.projects.edit', $project->id) }}" class="btn btn-sm btn-light border text-secondary ms-1 py-1 px-2" title="Edit Project">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 opacity-50 mb-2 d-block"></i>
                                <h6>No Sampling Projects Found</h6>
                                <p class="small mb-3">Create your first design project to start organizing batches and sample development.</p>
                                <a href="{{ route('sampling.projects.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Create Project
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($projects->hasPages())
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
@endif
@endsection
