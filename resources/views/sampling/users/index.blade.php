@extends('layouts.sampling')

@section('title', 'Team & Users')
@section('page-title', 'Company Users & Artisans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-people me-2 text-primary"></i>Company Users & Artisans</h4>
        <p class="text-muted small mb-0">Manage authorized staff, fashion designers, pattern makers, and master artisans for {{ auth()->guard('sampling')->user()->company->name }}</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Team Member
        </button>
    </div>
</div>

{{-- Filter Box --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('sampling.users.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="company_admin" {{ request('role') === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                <option value="sampling_manager" {{ request('role') === 'sampling_manager' ? 'selected' : '' }}>Sampling Manager</option>
                <option value="designer" {{ request('role') === 'designer' ? 'selected' : '' }}>Designer</option>
                <option value="pattern_maker" {{ request('role') === 'pattern_maker' ? 'selected' : '' }}>Pattern Maker</option>
                <option value="division_head" {{ request('role') === 'division_head' ? 'selected' : '' }}>Division Head</option>
                <option value="sampling_staff" {{ request('role') === 'sampling_staff' ? 'selected' : '' }}>Sampling Artisan / Staff</option>
                <option value="costing_officer" {{ request('role') === 'costing_officer' ? 'selected' : '' }}>Costing Officer</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3 w-100 fw-semibold">Filter</button>
            <a href="{{ route('sampling.users.index') }}" class="btn btn-light border">Reset</a>
        </div>
    </form>
</div>

{{-- Users Table --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3" style="width: 50px;">#</th>
                        <th>Name</th>
                        <th>Email (Login)</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Department / Division</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        @if($u->id == auth()->guard('sampling')->id())
                                            <span class="badge bg-light text-success border" style="font-size: 0.68rem;">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="font-monospace small text-dark">{{ $u->email }}</span></td>
                            <td><span class="small text-muted">{{ $u->phone ?: '-' }}</span></td>
                            <td>
                                @if($u->role === 'company_admin')
                                    <span class="badge bg-primary px-2 py-1">Company Admin</span>
                                @elseif($u->role === 'sampling_manager')
                                    <span class="badge bg-info text-dark px-2 py-1">Sampling Manager</span>
                                @elseif($u->role === 'designer')
                                    <span class="badge bg-purple text-white px-2 py-1" style="background-color: #7c3aed;">Fashion Designer</span>
                                @elseif($u->role === 'pattern_maker')
                                    <span class="badge bg-warning text-dark px-2 py-1">Pattern Maker</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 text-capitalize">
                                        {{ str_replace('_', ' ', $u->role) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($u->division)
                                    <span class="badge bg-light text-dark border">{{ $u->division->name }}</span>
                                @else
                                    <span class="text-muted small">All Departments</span>
                                @endif
                            </td>
                            <td>
                                @if($u->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal_{{ $u->id }}" title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    @if($u->id != auth()->guard('sampling')->id())
                                        <form action="{{ route('sampling.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete user {{ $u->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Edit User Modal --}}
                        <div class="modal fade" id="editUserModal_{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">Edit Team Member: {{ $u->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('sampling.users.update', $u->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $u->name) }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Email (Login ID) <span class="text-danger">*</span></label>
                                                    <input type="email" name="email" class="form-control" value="{{ old('email', $u->email) }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Reset Password (leave blank to keep existing)</label>
                                                    <input type="password" name="password" class="form-control" placeholder="New password (optional)">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="company_admin" {{ $u->role === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                                                        <option value="sampling_manager" {{ $u->role === 'sampling_manager' ? 'selected' : '' }}>Sampling Manager</option>
                                                        <option value="designer" {{ $u->role === 'designer' ? 'selected' : '' }}>Fashion Designer</option>
                                                        <option value="pattern_maker" {{ $u->role === 'pattern_maker' ? 'selected' : '' }}>Pattern Maker / Master Ji</option>
                                                        <option value="division_head" {{ $u->role === 'division_head' ? 'selected' : '' }}>Department Head</option>
                                                        <option value="sampling_staff" {{ $u->role === 'sampling_staff' ? 'selected' : '' }}>Artisan / Staff</option>
                                                        <option value="costing_officer" {{ $u->role === 'costing_officer' ? 'selected' : '' }}>Costing Officer</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Department / Division</label>
                                                    <select name="division_id" class="form-select">
                                                        <option value="">All Departments</option>
                                                        @foreach($divisions as $div)
                                                            <option value="{{ $div->id }}" {{ $u->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Phone Number</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $u->phone) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                                <h6>No Users Found</h6>
                                <p class="small mb-3">Add designers, pattern makers, and master artisans for your studio.</p>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    <i class="bi bi-person-plus-fill me-1"></i> Add First User
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-3 border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>Add New Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Tariq Ahmad" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Login Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="tariq@studio.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="sampling_staff" {{ old('role') === 'sampling_staff' ? 'selected' : '' }}>Artisan / Staff</option>
                                <option value="pattern_maker" {{ old('role') === 'pattern_maker' ? 'selected' : '' }}>Pattern Maker / Master Ji</option>
                                <option value="designer" {{ old('role') === 'designer' ? 'selected' : '' }}>Fashion Designer</option>
                                <option value="division_head" {{ old('role') === 'division_head' ? 'selected' : '' }}>Department Head</option>
                                <option value="sampling_manager" {{ old('role') === 'sampling_manager' ? 'selected' : '' }}>Sampling Manager</option>
                                <option value="costing_officer" {{ old('role') === 'costing_officer' ? 'selected' : '' }}>Costing Officer</option>
                                <option value="company_admin" {{ old('role') === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Department / Division</label>
                            <select name="division_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 99060 00000" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Add Team Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
