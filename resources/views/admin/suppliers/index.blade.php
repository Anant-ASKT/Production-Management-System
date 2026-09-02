@extends('layouts.app')

@section('title', 'Suppliers')

@section('page-title', 'Suppliers')

@section('content')

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Supplier Management</h4>
        <div>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Supplier
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">S.No</th>
                            <th>Supplier Name</th>
                            <th>Nick Name</th>
                            <th>Users</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->sno }}</td>
                                <td class="fw-bold">{{ $supplier->name }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-bold">
                                        {{ $supplier->nickname ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                        <i class="bi bi-people-fill me-1"></i> {{ $supplier->users_count }} {{ Str::plural('User', $supplier->users_count) }}
                                    </span>
                                </td>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                                <td>{{ Str::limit($supplier->address, 40) ?: '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.suppliers.edit', $supplier->sno) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Edit & Users
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
