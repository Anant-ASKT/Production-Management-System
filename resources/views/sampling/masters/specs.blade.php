@extends('layouts.sampling')

@section('title', 'Technical Spec Attributes')
@section('page-title', 'Technical Spec Attributes')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-card-checklist text-teal me-2" style="color: #20c997;"></i>Technical Specification Attributes
            </h4>
            <p class="text-muted small mb-0">Standard parameters recorded on styles (Machine Gauge, Yarn Ply, SPI, Target GSM, Shrinkage %)</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSpecAttrModal">
            <i class="bi bi-plus-lg me-1"></i> Add Spec Attribute
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
                            <th>Attribute Name</th>
                            <th>Field Type</th>
                            <th>Default Unit</th>
                            <th>Mandatory in Tech Pack?</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specAttributes as $index => $spec)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $spec->attribute_name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ ucfirst($spec->field_type) }}</span></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $spec->unit ?: '—' }}</span></td>
                                <td>
                                    @if($spec->is_required)
                                        <span class="badge bg-danger-subtle text-danger">Required</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Optional</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editSpecAttrModal{{ $spec->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.specs.destroy', $spec->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove spec attribute \'{{ $spec->attribute_name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editSpecAttrModal{{ $spec->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Spec Attribute</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.specs.update', $spec->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="attribute_name" class="form-control" value="{{ $spec->attribute_name }}" required>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                                                        <select name="field_type" class="form-select" required>
                                                            <option value="text" {{ $spec->field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                            <option value="number" {{ $spec->field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                            <option value="select" {{ $spec->field_type === 'select' ? 'selected' : '' }}>Dropdown List</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Unit (e.g. GG, Ply, GSM, %)</label>
                                                        <input type="text" name="unit" class="form-control" value="{{ $spec->unit }}">
                                                    </div>
                                                </div>
                                                <div class="mt-3 form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_required" value="1" id="reqCheck{{ $spec->id }}" {{ $spec->is_required ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold" for="reqCheck{{ $spec->id }}">
                                                        Mandatory for Freeze Approval
                                                    </label>
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
                                <td colspan="6" class="text-center py-4 text-muted">No spec attributes defined yet. Click "Add Spec Attribute" to register one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addSpecAttrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-teal me-2" style="color: #20c997;"></i>Add Tech Spec Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.specs') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                        <input type="text" name="attribute_name" class="form-control" placeholder="e.g. Yarn Gauge, Ply Count, Needle Size" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" class="form-select" required>
                                <option value="text" selected>Text</option>
                                <option value="number">Number</option>
                                <option value="select">Dropdown List</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit (e.g. GG, Ply, GSM, %)</label>
                            <input type="text" name="unit" class="form-control" placeholder="e.g. GG or Ply">
                        </div>
                    </div>
                    <div class="mt-3 form-check">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1" id="addReqCheck">
                        <label class="form-check-label fw-semibold" for="addReqCheck">
                            Mandatory for Freeze Approval
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Attribute</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
