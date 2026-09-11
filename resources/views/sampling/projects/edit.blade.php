@extends('layouts.sampling')

@section('title', 'Edit ' . $project->project_name)
@section('page-title', 'Edit Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder-check me-2 text-primary"></i>Edit Sampling Project</h4>
        <span class="badge bg-dark-subtle text-dark border font-monospace">{{ $project->project_code }}</span>
    </div>
    <div>
        <a href="{{ route('sampling.projects.show', $project->id) }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i> Back to Project
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm w-100">
    <div class="card-body p-4">
        <form action="{{ route('sampling.projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="project_name" class="form-control" value="{{ old('project_name', $project->project_name) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ $project->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ $project->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_development" {{ $project->status === 'in_development' ? 'selected' : '' }}>In Development</option>
                        <option value="partially_approved" {{ $project->status === 'partially_approved' ? 'selected' : '' }}>Partially Approved</option>
                        <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="closed" {{ $project->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Target Completion Date</label>
                    <input type="date" name="target_completion_date" class="form-control" value="{{ old('target_completion_date', $project->target_completion_date?->format('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Project Notes / Direction</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $project->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <a href="{{ route('sampling.projects.show', $project->id) }}" class="btn btn-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
                    Update Project Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
