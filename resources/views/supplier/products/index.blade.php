@extends('layouts.supplier')

@section('title', 'My Products')
@section('page-title', 'My Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0">Products</h4>
    <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Product
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('supplier.products.index') }}" method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, item type or color..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate ?? request('date', now()->toDateString()) }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            @if(request()->has('search') || (request()->has('date') && request('date') != now()->toDateString()))
            <div class="col-md-2">
                <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Item Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->main_image)
                                    <img src="{{ asset($product->main_image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $product->name }}</td>
                            <td>{{ $product->item_type ?? '-' }}</td>
                            <td>{{ $product->price ? '₹' . number_format($product->price, 2) : '-' }}</td>
                            <td>{{ $product->stock ?? 0 }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $product->created_at ? $product->created_at->format('d-m-Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('supplier.products.edit', $product->sno) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('supplier.products.destroy', $product->sno) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

