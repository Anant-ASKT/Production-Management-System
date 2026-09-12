@extends('layouts.app')

@section('title', 'Manage ' . $company->name)
@section('page-title', 'Manage Sampling Company')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0 fw-bold text-dark"><i class="bi bi-building-gear me-2 text-primary"></i>{{ $company->name }}</h4>
            <span class="badge bg-dark-subtle text-dark border font-monospace mt-1">{{ $company->code }}</span>
        </div>
        <div>
            <a href="{{ route('admin.sampling-companies.index') }}" class="btn btn-light border px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Listing
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Company Details Edit Form --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title m-0 fw-bold text-dark"><i class="bi bi-info-circle me-2 text-primary"></i>Company Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.sampling-companies.update', $company->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Company Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control font-monospace" value="{{ old('code', $company->code) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $company->contact_person) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Company Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Studio / Workshop Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', $company->address) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">Update Company</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Users Management Section --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 fw-bold text-dark">
                        <i class="bi bi-people me-2 text-primary"></i>Company Authorized Users
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add User
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">User Details</th>
                                    <th>Role</th>
                                    <th>Division</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($company->users as $u)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">{{ $u->name }}</div>
                                            <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</small>
                                            @if($u->phone)
                                                <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i>{{ $u->phone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 text-capitalize">
                                                {{ str_replace('_', ' ', $u->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($u->division)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                    {{ $u->division->name }}
                                                </span>
                                            @else
                                                <span class="text-muted small">All / General</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($u->status === 'active')
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-sm btn-light border me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                                <i class="bi bi-pencil-fill text-secondary"></i>
                                            </button>
                                            @if($company->users->count() > 1)
                                                <form action="{{ route('admin.sampling-companies.users.destroy', [$company->id, $u->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border text-danger">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Edit User Modal --}}
                                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit User: {{ $u->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.sampling-companies.users.update', [$company->id, $u->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4">
                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold">Login Email <span class="text-danger">*</span></label>
                                                                <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                                                <select name="role" class="form-select" required>
                                                                    <option value="company_admin" {{ $u->role === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                                                                    <option value="sampling_manager" {{ $u->role === 'sampling_manager' ? 'selected' : '' }}>Sampling Manager</option>
                                                                    <option value="designer" {{ $u->role === 'designer' ? 'selected' : '' }}>Designer</option>
                                                                    <option value="sampling_staff" {{ $u->role === 'sampling_staff' ? 'selected' : '' }}>Sampling Staff / Artisan</option>
                                                                    <option value="division_head" {{ $u->role === 'division_head' ? 'selected' : '' }}>Division Head</option>
                                                                    <option value="approver" {{ $u->role === 'approver' ? 'selected' : '' }}>Approver</option>
                                                                    <option value="viewer" {{ $u->role === 'viewer' ? 'selected' : '' }}>Viewer (Read Only)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Division</label>
                                                                <select name="division_id" class="form-select">
                                                                    <option value="">General / All Divisions</option>
                                                                    @foreach($company->divisions as $div)
                                                                        <option value="{{ $div->id }}" {{ $u->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Phone</label>
                                                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $u->phone) }}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold">New Password (leave blank to keep current)</label>
                                                                <input type="password" name="password" class="form-control" placeholder="Optional new password">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary px-3">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No users found for this company.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Add New Company User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.sampling-companies.users.store', $company->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Priya Sundaram" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Login Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="priya@sampling.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="sampling_staff">Sampling Staff / Artisan</option>
                                <option value="designer">Designer</option>
                                <option value="sampling_manager">Sampling Manager</option>
                                <option value="division_head">Division Head</option>
                                <option value="approver">Approver</option>
                                <option value="company_admin">Company Admin</option>
                                <option value="viewer">Viewer (Read Only)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Division</label>
                            <select name="division_id" class="form-select">
                                <option value="">General / All Divisions</option>
                                @foreach($company->divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
