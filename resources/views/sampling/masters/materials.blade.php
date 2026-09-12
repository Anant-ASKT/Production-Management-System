@extends('layouts.sampling')

@section('title', 'Materials & Items Catalog')
@section('page-title', 'Materials & Items Catalog')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-box-seam text-danger me-2"></i>Materials & Raw Item Catalog
            </h4>
            <p class="text-muted small mb-0">Common materials (Fabrics, Yarns, Threads, Buttons, Zippers, Trims, Packaging) with standard rates</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
            <i class="bi bi-plus-lg me-1"></i> Add Material
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
                            <th>Category</th>
                            <th>Material / Item Name</th>
                            <th>Item Code</th>
                            <th>Default UOM</th>
                            <th>Standard Cost Rate</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $index => $mat)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $mat->material_category }}</span></td>
                                <td class="fw-bold text-dark">{{ $mat->material_name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $mat->item_code ?: '—' }}</span></td>
                                <td>{{ $mat->unit_of_measure }}</td>
                                <td class="fw-semibold text-dark">₹{{ number_format($mat->standard_cost, 2) }}</td>
                                <td>
                                    @if($mat->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $mat->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.materials.destroy', $mat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove material \'{{ $mat->material_name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editMaterialModal{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Material</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.materials.update', $mat->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                                        <select name="material_category" class="form-select" required>
                                                            @foreach(['Fabric', 'Yarn', 'Thread', 'Buttons', 'Zippers', 'Labels', 'Lining', 'Trims', 'Packaging', 'Other'] as $cat)
                                                                <option value="{{ $cat }}" {{ $mat->material_category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Material / Item Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="material_name" class="form-control" value="{{ $mat->material_name }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Item Code</label>
                                                        <input type="text" name="item_code" class="form-control font-monospace" value="{{ $mat->item_code }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Default UOM <span class="text-danger">*</span></label>
                                                        <select name="unit_of_measure" class="form-select" required>
                                                            @foreach($uoms as $u)
                                                                <option value="{{ $u->symbol }}" {{ $mat->unit_of_measure === $u->symbol || $mat->unit_of_measure === $u->name ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Cost Rate (₹)</label>
                                                        <input type="number" step="0.01" name="standard_cost" class="form-control" value="{{ $mat->standard_cost }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $mat->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $mat->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <td colspan="8" class="text-center py-4 text-muted">No materials registered. Click "Add Material" to define standard items.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-danger me-2"></i>Add Material / Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.materials') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Material Category <span class="text-danger">*</span></label>
                            <select name="material_category" class="form-select" required>
                                <option value="Fabric">Fabric</option>
                                <option value="Yarn">Yarn</option>
                                <option value="Thread">Thread / Needlework</option>
                                <option value="Buttons">Buttons</option>
                                <option value="Zippers">Zippers</option>
                                <option value="Labels">Labels & Tags</option>
                                <option value="Lining">Lining</option>
                                <option value="Trims">Trims & Laces</option>
                                <option value="Packaging">Packaging</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="material_name" class="form-control" placeholder="e.g. 2/28 Cashmere Melange Yarn" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Item Code</label>
                            <input type="text" name="item_code" class="form-control font-monospace" placeholder="e.g. YRN-CSH-01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Default UOM <span class="text-danger">*</span></label>
                            <select name="unit_of_measure" class="form-select" required>
                                @foreach($uoms as $u)
                                    <option value="{{ $u->symbol }}">{{ $u->name }} ({{ $u->symbol }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Standard Cost Rate (₹)</label>
                            <input type="number" step="0.01" name="standard_cost" class="form-control" placeholder="e.g. 850.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
