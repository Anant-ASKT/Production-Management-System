@extends('layouts.sampling')

@section('title', 'Designers Master')
@section('page-title', 'Designers Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-palette text-purple me-2" style="color: #6f42c1;"></i>Designers Master
            </h4>
            <p class="text-muted small mb-0">Fashion designers, stylists, and artisan leads associated with sampling projects</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDesignerModal">
            <i class="bi bi-plus-lg me-1"></i> Add Designer
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
                            <th>Designer Name</th>
                            <th>Code</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designers as $index => $des)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $des->name }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $des->code ?: '—' }}</span></td>
                                <td class="text-muted small">{{ $des->email ?: '—' }}</td>
                                <td class="text-muted small">{{ $des->phone ?: '—' }}</td>
                                <td><span class="badge bg-light text-primary border">{{ $des->specialization ?: 'General' }}</span></td>
                                <td>
                                    @if($des->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editDesignerModal{{ $des->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.designers.destroy', $des->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove designer \'{{ $des->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editDesignerModal{{ $des->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Designer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.designers.update', $des->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label fw-semibold">Designer Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $des->name }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Code</label>
                                                        <input type="text" name="code" class="form-control font-monospace" value="{{ $des->code }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $des->email }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Phone</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $des->phone }}">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label fw-semibold">Specialization</label>
                                                        <input type="text" name="specialization" class="form-control" value="{{ $des->specialization }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $des->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $des->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <td colspan="8" class="text-center py-4 text-muted">No designers registered yet. Click "Add Designer" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addDesignerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-purple me-2" style="color: #6f42c1;"></i>Add Designer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.designers') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Designer Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Tariq Ahmad" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. DES-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="designer@studio.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Knitwear, Hand Embroidery, Leather Crafts">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Designer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
