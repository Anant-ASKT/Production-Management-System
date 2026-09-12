@extends('layouts.sampling')

@section('title', 'Sketch & Swatch Types Master')
@section('page-title', 'Sketch & Swatch Types Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-images text-primary me-2"></i>Sketch & Swatch Types Master
            </h4>
            <p class="text-muted small mb-0">Define reference categories for design sketches, fabric swatches, inspiration photos, and external digital links</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addRefTypeModal">
            <i class="bi bi-plus-lg me-1"></i> Add Sketch / Swatch Type
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
                            <th>Type Name</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referenceTypes as $index => $rt)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">
                                    <span class="badge bg-primary-subtle text-primary border me-1">
                                        <i class="bi bi-tag me-1"></i>{{ $rt->name }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $rt->code ?: '—' }}</span></td>
                                <td class="text-muted small">{{ $rt->description ?: '—' }}</td>
                                <td>
                                    @if($rt->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editRefTypeModal{{ $rt->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.reference-types.destroy', $rt->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove type \'{{ $rt->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editRefTypeModal{{ $rt->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Sketch / Swatch Type</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.reference-types.update', $rt->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Type Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $rt->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Code / Key <small class="text-muted">(Used internally)</small></label>
                                                    <input type="text" name="code" class="form-control font-monospace" value="{{ $rt->code }}" placeholder="e.g. sketch, fabric_ref">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $rt->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $rt->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $rt->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm px-3">Update Type</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-images fs-2 d-block mb-2 text-secondary"></i>
                                    No sketch / swatch types found. Click "Add Sketch / Swatch Type" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addRefTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add Sketch / Swatch Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.reference-types') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 3D Render / CAD" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code / Key <small class="text-muted">(Optional identifier)</small></label>
                        <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. render_3d">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief explanation of this reference type"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Save Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
