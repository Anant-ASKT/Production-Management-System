@extends('layouts.app')

@section('title', 'Sampling Companies')
@section('page-title', 'Sampling Companies')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0 fw-bold text-dark"><i class="bi bi-palette me-2 text-primary"></i>Sampling Companies</h4>
            <p class="text-muted small mb-0">Manage partner sampling companies, studios, and authorized logins</p>
        </div>
        <div>
            <a href="{{ route('admin.sampling-companies.create') }}" class="btn btn-primary px-3 py-2 fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Add Sampling Company
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
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.sampling-companies.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, code, email, contact person..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3">Filter</button>
                    <a href="{{ route('admin.sampling-companies.index') }}" class="btn btn-light border px-3">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">ID</th>
                            <th>Company Code</th>
                            <th>Company Name</th>
                            <th>Contact Info</th>
                            <th>Users</th>
                            <th>Projects / Samples</th>
                            <th>Status</th>
                            <th class="text-end pe-3" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td class="ps-3 text-muted">#{{ $company->id }}</td>
                                <td>
                                    <span class="badge bg-dark-subtle text-dark border px-2 py-1 font-monospace fw-bold">
                                        {{ $company->code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $company->name }}</div>
                                    @if($company->contact_person)
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $company->contact_person }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($company->email)
                                        <div><small><i class="bi bi-envelope me-1 text-muted"></i>{{ $company->email }}</small></div>
                                    @endif
                                    @if($company->phone)
                                        <div><small><i class="bi bi-telephone me-1 text-muted"></i>{{ $company->phone }}</small></div>
                                    @endif
                                    @if(!$company->email && !$company->phone)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <i class="bi bi-people-fill me-1"></i> {{ $company->users_count }} {{ Str::plural('User', $company->users_count) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="badge bg-secondary-subtle text-secondary me-1">{{ $company->projects_count }} Projects</span>
                                        <span class="badge bg-info-subtle text-info">{{ $company->samples_count }} Samples</span>
                                    </div>
                                </td>
                                <td>
                                    @if($company->status === 'active')
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
                                    <a href="{{ route('admin.sampling-companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                        <i class="bi bi-gear-fill me-1"></i> Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-palette fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                    <h5>No Sampling Companies Found</h5>
                                    <p class="small mb-3">Create your first sampling partner company to get started.</p>
                                    <a href="{{ route('admin.sampling-companies.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-lg me-1"></i> Create Company
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($companies->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
