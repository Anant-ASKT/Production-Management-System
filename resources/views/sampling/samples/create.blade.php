@extends('layouts.sampling')

@section('title', 'New Sample Entry')
@section('page-title', 'New Sample')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-plus-circle me-2 text-primary"></i>New Sample Entry</h4>
        <p class="text-muted small mb-0">Create an individual product development record inside a project batch</p>
    </div>
    <div>
        <a href="{{ route('sampling.samples.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i> Back to Catalog
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm w-100">
    <div class="card-body p-4">
        <form action="{{ route('sampling.samples.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        {{-- Project & Batch Assignment --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sampling Project <span class="text-danger">*</span></label>
                            <select name="sampling_project_id" id="projectSelect" class="form-select" required onchange="updateBatches()">
                                <option value="">Select Project</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ (old('sampling_project_id', $selectedProjectId) == $proj->id) ? 'selected' : '' }} data-batches="{{ json_encode($proj->batches) }}">
                                        {{ $proj->project_code }} — {{ $proj->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Development Batch <span class="text-danger">*</span></label>
                            <select name="sampling_batch_id" id="batchSelect" class="form-select" required>
                                <option value="">Select Batch</option>
                            </select>
                        </div>

                        {{-- Product Style Details --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Style Name / Working Name <span class="text-danger">*</span></label>
                            <input type="text" name="style_name" class="form-control" placeholder="e.g. Chunky Fisherman Cable Sweater" value="{{ old('style_name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Product Description / Concept</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief visual description, intended yarn or weave characteristics...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Responsibility Assignment --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Overall Responsible Division <span class="text-danger">*</span></label>
                            <select name="overall_division_id" class="form-select" required>
                                <option value="">Select Primary Accountable Division</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ old('overall_division_id') == $div->id ? 'selected' : '' }}>
                                        {{ $div->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Single division with primary accountability for finishing this sample.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Responsible Person / Artisan</label>
                            <select name="assigned_person_id" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_person_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ str_replace('_', ' ', $user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Artisan or technician in charge of physical execution.</small>
                        </div>

                        {{-- Supporting Divisions (Checkboxes) --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Supporting Divisions (Optional)</label>
                            <p class="text-muted small mb-2">Divisions participating in embellishment, washing, dyeing, or accessories:</p>
                            <div class="row g-2">
                                @foreach($divisions as $div)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check p-2 border rounded bg-light">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="supporting_division_ids[]" value="{{ $div->id }}" id="div_{{ $div->id }}">
                                            <label class="form-check-label small fw-medium" for="div_{{ $div->id }}">
                                                {{ $div->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Completion Date</label>
                            <input type="date" name="target_date" class="form-control" value="{{ old('target_date') }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="{{ route('sampling.samples.index') }}" class="btn btn-light border me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background: linear-gradient(135deg, #1e3a5f 0%, #2b5288 100%);">
                            Create Sample & Open 360° Workspace
                        </button>
                    </div>
                </form>
            </div>
        </div>

<script>
    function updateBatches() {
        const projectSelect = document.getElementById('projectSelect');
        const batchSelect = document.getElementById('batchSelect');
        const selectedOption = projectSelect.options[projectSelect.selectedIndex];

        batchSelect.innerHTML = '<option value="">Select Batch</option>';

        if (!selectedOption || !selectedOption.dataset.batches) return;

        const batches = JSON.parse(selectedOption.dataset.batches);
        const preselectedBatchId = "{{ old('sampling_batch_id', $selectedBatchId) }}";

        batches.forEach(batch => {
            const opt = document.createElement('option');
            opt.value = batch.id;
            opt.textContent = `${batch.batch_number} — ${batch.batch_name}`;
            if (preselectedBatchId && preselectedBatchId == batch.id) {
                opt.selected = true;
            }
            batchSelect.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateBatches();
    });
</script>
@endsection
