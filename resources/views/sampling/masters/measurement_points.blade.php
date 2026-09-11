@extends('layouts.sampling')

@section('title', 'Measurement Points Master')
@section('page-title', 'Measurement Points Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-rulers text-success me-2"></i>Measurement Points (Naap Master)
            </h4>
            <p class="text-muted small mb-0">Standard points of measurement (Chest Width, Body Length, Across Shoulder, Sleeve Length, etc.)</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMeasurementModal">
            <i class="bi bi-plus-lg me-1"></i> Add Measurement Point
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
                            <th>Point Name</th>
                            <th>Code / Short Key</th>
                            <th>Default Unit</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($measurementPoints as $index => $pt)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $pt->point_name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $pt->code ?: '—' }}</span></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $pt->default_unit }}</span></td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editMeasurementModal{{ $pt->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.measurement-points.destroy', $pt->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove measurement point \'{{ $pt->point_name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editMeasurementModal{{ $pt->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Measurement Point</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.measurement-points.update', $pt->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Point Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="point_name" class="form-control" value="{{ $pt->point_name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Code / Short Key</label>
                                                    <input type="text" name="code" class="form-control font-monospace" value="{{ $pt->code }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Default Unit <span class="text-danger">*</span></label>
                                                    <select name="default_unit" class="form-select" required>
                                                        <option value="cm" {{ $pt->default_unit === 'cm' ? 'selected' : '' }}>Centimetres (cm)</option>
                                                        <option value="inch" {{ $pt->default_unit === 'inch' ? 'selected' : '' }}>Inches (inch)</option>
                                                        <option value="mm" {{ $pt->default_unit === 'mm' ? 'selected' : '' }}>Millimetres (mm)</option>
                                                    </select>
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
                                <td colspan="5" class="text-center py-4 text-muted">No measurement points defined. Click "Add Measurement Point" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addMeasurementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>Add Measurement Point</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.measurement-points') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Point Name <span class="text-danger">*</span></label>
                        <input type="text" name="point_name" class="form-control" placeholder="e.g. Total Length, Chest Width" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code / Short Key</label>
                        <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. LENGTH, CHEST">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Default Unit <span class="text-danger">*</span></label>
                        <select name="default_unit" class="form-select" required>
                            <option value="cm" selected>Centimetres (cm)</option>
                            <option value="inch">Inches (inch)</option>
                            <option value="mm">Millimetres (mm)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Measurement Point</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
