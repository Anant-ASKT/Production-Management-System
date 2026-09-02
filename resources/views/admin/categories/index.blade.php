@extends('layouts.app')

@section('title', 'Categories')

@section('page-title', 'Categories')

@section('content')

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gray-800">Category Management</h4>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Category
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fs-7 fw-semibold">Search Category</label>
                    <input type="text" name="search" class="form-control" placeholder="Category name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-7 fw-semibold">Filter by Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">-- All Suppliers --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->sno }}" {{ request('supplier_id') == $supplier->sno ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'supplier_id']))
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Categories Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">S.No</th>
                            <th>Category Name</th>
                            <th>Supplier</th>
                            <th>Created At</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->sno }}</td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    @if($category->supplier)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-truck me-1"></i>{{ $category->supplier->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Global / None</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $category->created_at ? $category->created_at->format('d M, Y') : '-' }}</small>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category->sno) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->sno) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
