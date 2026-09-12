@extends('layouts.sampling')

@section('title', 'Collections & Seasons Master')
@section('page-title', 'Collections & Seasons Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-collection text-primary me-2"></i>Collections & Seasons Master
            </h4>
            <p class="text-muted small mb-0">Brand collections, seasonal releases, and design drop campaigns</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
            <i class="bi bi-plus-lg me-1"></i> Add Collection
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
                            <th>Collection Name</th>
                            <th>Code</th>
                            <th>Season</th>
                            <th>Year</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $index => $coll)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $coll->name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $coll->code ?: '—' }}</span></td>
                                <td><span class="badge bg-info-subtle text-info">{{ $coll->season ?: '—' }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $coll->year ?: '—' }}</span></td>
                                <td class="text-muted small">{{ Str::limit($coll->description, 50) ?: '—' }}</td>
                                <td>
                                    @if($coll->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editCollectionModal{{ $coll->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.collections.destroy', $coll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove collection \'{{ $coll->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCollectionModal{{ $coll->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Collection</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.collections.update', $coll->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label fw-semibold">Collection Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $coll->name }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Code</label>
                                                        <input type="text" name="code" class="form-control font-monospace" value="{{ $coll->code }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Season</label>
                                                        <select name="season" class="form-select">
                                                            <option value="">-- Select Season --</option>
                                                            @foreach(['Autumn / Winter', 'Spring / Summer', 'Resort / Cruise', 'Festive / Wedding', 'Pre-Fall', 'Core Basics'] as $s)
                                                                <option value="{{ $s }}" {{ $coll->season === $s ? 'selected' : '' }}>{{ $s }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Year</label>
                                                        <input type="text" name="year" class="form-control" value="{{ $coll->year }}" placeholder="e.g. 2026">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $coll->description }}</textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $coll->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $coll->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <td colspan="8" class="text-center py-4 text-muted">No collections created. Click "Add Collection" to define one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Add Collection / Season</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.collections') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Collection Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Autumn / Winter 2026 Cashmere Drop" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. AW26">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Season</label>
                            <select name="season" class="form-select">
                                <option value="Autumn / Winter">Autumn / Winter</option>
                                <option value="Spring / Summer">Spring / Summer</option>
                                <option value="Resort / Cruise">Resort / Cruise</option>
                                <option value="Festive / Wedding">Festive / Wedding</option>
                                <option value="Core Basics">Core Basics</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="text" name="year" class="form-control" placeholder="e.g. 2026" value="2026">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Theme, colour palette notes, inspiration..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Collection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
