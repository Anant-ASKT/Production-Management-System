@extends('layouts.sampling')

@section('title', 'Operations Master')
@section('page-title', 'Operations Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-gear-wide text-warning me-2"></i>Standard Operations & Making Steps
            </h4>
            <p class="text-muted small mb-0">Production steps (Knitting, Linking, Stitching, Hand Needlework, Finishing) with standard timings and rates</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addOperationModal">
            <i class="bi bi-plus-lg me-1"></i> Add Operation
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Please review form errors:
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Operation Name</th>
                            <th>Department</th>
                            <th>Skill Level</th>
                            <th>Standard Time</th>
                            <th>Hourly Rate</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operations as $index => $op)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $op->operation_name }}</td>
                                <td>
                                    @if($op->division)
                                        <span class="badge bg-light text-dark border">{{ $op->division->name }}</span>
                                    @else
                                        <span class="text-muted small">General</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-info-subtle text-info">{{ $op->skill_level }}</span></td>
                                <td>{{ number_format($op->default_time_minutes, 0) }} mins</td>
                                <td class="fw-semibold text-dark">₹{{ number_format($op->default_rate_per_hour, 2) }}/hr</td>
                                <td>
                                    @if($op->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editOperationModal{{ $op->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.operations.destroy', $op->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove operation \'{{ $op->operation_name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editOperationModal{{ $op->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Operation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.operations.update', $op->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Operation Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="operation_name" class="form-control" value="{{ $op->operation_name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Department</label>
                                                        <select name="division_id" class="form-select">
                                                            <option value="">-- General / Any --</option>
                                                            @foreach($departments as $dept)
                                                                <option value="{{ $dept->id }}" {{ $op->division_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Skill Level <span class="text-danger">*</span></label>
                                                        <select name="skill_level" class="form-select" required>
                                                            @forelse($skillLevels as $sk)
                                                                <option value="{{ $sk->name }}" {{ $op->skill_level === $sk->name ? 'selected' : '' }}>{{ $sk->name }}</option>
                                                            @empty
                                                                <option value="Master Artisan" {{ $op->skill_level === 'Master Artisan' ? 'selected' : '' }}>Master Artisan</option>
                                                                <option value="Skilled" {{ $op->skill_level === 'Skilled' ? 'selected' : '' }}>Skilled</option>
                                                                <option value="Semi-Skilled" {{ $op->skill_level === 'Semi-Skilled' ? 'selected' : '' }}>Semi-Skilled</option>
                                                                <option value="Trainee" {{ $op->skill_level === 'Trainee' ? 'selected' : '' }}>Trainee</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Default Time (Minutes)</label>
                                                        <input type="number" step="0.5" name="default_time_minutes" class="form-control" value="{{ $op->default_time_minutes }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                                                        <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" value="{{ $op->default_rate_per_hour }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $op->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $op->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No operations configured. Click "Add Operation" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addOperationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-warning me-2"></i>Add Standard Operation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.operations') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Operation / Process Name <span class="text-danger">*</span></label>
                            <input type="text" name="operation_name" class="form-control" placeholder="e.g. Hand Embroidery Needlework" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="division_id" class="form-select">
                                <option value="">-- General / Any --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Skill Level <span class="text-danger">*</span></label>
                            <select name="skill_level" class="form-select" required>
                                @forelse($skillLevels as $sk)
                                    <option value="{{ $sk->name }}">{{ $sk->name }}</option>
                                @empty
                                    <option value="Master Artisan">Master Artisan</option>
                                    <option value="Skilled" selected>Skilled</option>
                                    <option value="Semi-Skilled">Semi-Skilled</option>
                                    <option value="Trainee">Trainee</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Time (Minutes)</label>
                            <input type="number" step="0.5" name="default_time_minutes" class="form-control" placeholder="e.g. 45">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                            <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" placeholder="e.g. 120">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Operation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
