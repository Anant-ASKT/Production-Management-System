@extends('layouts.sampling')

@section('title', 'Units of Measure (UOM)')
@section('page-title', 'Units of Measure (UOM)')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-calculator text-secondary me-2"></i>Units of Measure (UOM)
            </h4>
            <p class="text-muted small mb-0">Standard measuring units for raw materials, trims, and dimensions</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUomModal">
            <i class="bi bi-plus-lg me-1"></i> Add Unit
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
                            <th>Unit Name</th>
                            <th>Symbol / Short Code</th>
                            <th>Measurement Type</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($uoms as $index => $u)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $u->name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $u->symbol }}</span></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $u->type ?: 'General' }}</span></td>
                                <td>
                                    @if($u->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editUomModal{{ $u->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.uoms.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove unit \'{{ $u->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editUomModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Unit of Measure</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.uoms.update', $u->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Symbol / Code <span class="text-danger">*</span></label>
                                                    <input type="text" name="symbol" class="form-control font-monospace" value="{{ $u->symbol }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Type</label>
                                                    <select name="type" class="form-select">
                                                        <option value="Quantity" {{ $u->type === 'Quantity' ? 'selected' : '' }}>Quantity (e.g. pcs, sets)</option>
                                                        <option value="Weight" {{ $u->type === 'Weight' ? 'selected' : '' }}>Weight (e.g. kg, g)</option>
                                                        <option value="Length" {{ $u->type === 'Length' ? 'selected' : '' }}>Length (e.g. m, yd, in, cm)</option>
                                                        <option value="Area" {{ $u->type === 'Area' ? 'selected' : '' }}>Area (e.g. sq ft, sq m)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <td colspan="6" class="text-center py-4 text-muted">No units of measure configured. Click "Add Unit" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addUomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-secondary me-2"></i>Add Unit of Measure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.uoms') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Kilograms, Metres, Pieces" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Symbol / Code <span class="text-danger">*</span></label>
                        <input type="text" name="symbol" class="form-control font-monospace" placeholder="e.g. kg, m, pcs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Measurement Type</label>
                        <select name="type" class="form-select">
                            <option value="Quantity">Quantity (e.g. pcs, sets, cones)</option>
                            <option value="Weight">Weight (e.g. kg, g)</option>
                            <option value="Length">Length (e.g. m, yd, in, cm)</option>
                            <option value="Area">Area (e.g. sq ft, sq m)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
