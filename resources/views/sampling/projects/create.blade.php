@extends('layouts.sampling')

@section('title', 'Create Sampling Project')
@section('page-title', 'New Sampling Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder-plus me-2 text-primary"></i>Create Sampling Project</h4>
        <p class="text-muted small mb-0">Define an overarching design, collection, or development exercise</p>
    </div>
    <div>
        <a href="{{ route('sampling.projects.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm w-100">
    <div class="card-body p-4">
        <form action="{{ route('sampling.projects.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control" placeholder="e.g. Autumn / Winter Knitwear & Accessories Collection" value="{{ old('project_name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Designer / Lead</label>
                            <input type="text" name="designer_name" class="form-control" placeholder="e.g. Elena Rostova" value="{{ old('designer_name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Collection / Season</label>
                            <input type="text" name="collection_name" class="form-control" placeholder="e.g. AW 2026-27" value="{{ old('collection_name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Completion Date</label>
                            <input type="date" name="target_completion_date" class="form-control" value="{{ old('target_completion_date') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Project Notes / Creative Direction</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Creative brief notes, target market, yarns or techniques...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-dark"><i class="bi bi-diagram-3 me-2 text-primary"></i>Initial Development Batch (B01)</h6>
                            <p class="text-muted small">Each project is created with its first sequential batch for immediate sample assignment.</p>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Initial Batch Name <span class="text-danger">*</span></label>
                                    <input type="text" name="initial_batch_name" class="form-control" placeholder="e.g. Batch 1 — Hand-knitted Outerwear" value="{{ old('initial_batch_name', 'Batch 1 — Initial Development') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="{{ route('sampling.projects.index') }}" class="btn btn-light border me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
                            Save Project & Open Batches
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endsection
