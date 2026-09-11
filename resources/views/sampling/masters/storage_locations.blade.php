@extends('layouts.sampling')

@section('title', 'Storage Locations Master')
@section('page-title', 'Storage Locations Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-archive text-info me-2"></i>Physical Archive & Sample Storage Locations
            </h4>
            <p class="text-muted small mb-0">Studio rooms, racks, shelves, and boxes where physical master samples are archived</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStorageModal">
            <i class="bi bi-plus-lg me-1"></i> Add Storage Location
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
                            <th>Studio / Building</th>
                            <th>Room</th>
                            <th>Rack</th>
                            <th>Shelf</th>
                            <th>Box</th>
                            <th>Formatted Location</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($storageLocations as $index => $loc)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $loc->studio_name }}</td>
                                <td>{{ $loc->room }}</td>
                                <td>{{ $loc->rack ?: '—' }}</td>
                                <td>{{ $loc->shelf ?: '—' }}</td>
                                <td>{{ $loc->box ?: '—' }}</td>
                                <td class="text-primary fw-semibold font-monospace">{{ $loc->formatted_location }}</td>
                                <td>
                                    @if($loc->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editStorageModal{{ $loc->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.locations.destroy', $loc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this storage location?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editStorageModal{{ $loc->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Storage Location</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.locations.update', $loc->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Studio / Building <span class="text-danger">*</span></label>
                                                        <input type="text" name="studio_name" class="form-control" value="{{ $loc->studio_name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                                                        <input type="text" name="room" class="form-control" value="{{ $loc->room }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Rack</label>
                                                        <input type="text" name="rack" class="form-control" value="{{ $loc->rack }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Shelf</label>
                                                        <input type="text" name="shelf" class="form-control" value="{{ $loc->shelf }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Box</label>
                                                        <input type="text" name="box" class="form-control" value="{{ $loc->box }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $loc->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $loc->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <td colspan="9" class="text-center py-4 text-muted">No storage locations configured. Click "Add Storage Location" to register one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addStorageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-info me-2"></i>Add Physical Storage Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.locations') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Studio / Building <span class="text-danger">*</span></label>
                            <input type="text" name="studio_name" class="form-control" placeholder="e.g. Main Craft Studio" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                            <input type="text" name="room" class="form-control" placeholder="e.g. Sample Archive Room" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rack</label>
                            <input type="text" name="rack" class="form-control" placeholder="Rack A">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Shelf</label>
                            <input type="text" name="shelf" class="form-control" placeholder="Shelf 2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Box</label>
                            <input type="text" name="box" class="form-control" placeholder="Box 08">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Storage Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
