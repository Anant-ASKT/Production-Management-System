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

<div class="card shadow-sm border-0 mb-4 filter-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <strong class="text-dark">
            <i class="bi bi-funnel me-1 text-primary"></i>
            Filters
        </strong>
    </div>
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('supplier.products.index') }}" method="GET" id="filterForm">
            <div class="row g-3">
                {{-- NAME --}}
                <div class="col-md-3">
                    <label for="filterName" class="form-label fw-semibold small">
                        Name
                    </label>
                    <input type="text"
                           name="name"
                           id="filterName"
                           class="form-control"
                           placeholder="Search by name or SKU..."
                           value="{{ request('name') }}">
                </div>

                {{-- ITEM TYPE --}}
                <div class="col-md-3">
                    <label for="filterItemType" class="form-label fw-semibold small">
                        Item Type
                    </label>
                    <select name="item_type" id="filterItemType" class="form-select select2-filter">
                        <option value="">All Item Types</option>
                        @foreach($itemTypes as $itemType)
                            <option value="{{ $itemType->id }}" {{ (string)request('item_type') === (string)$itemType->id ? 'selected' : '' }}>
                                {{ $itemType->itemtype }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- COMPOSITION --}}
                <div class="col-md-3">
                    <label for="filterComposition" class="form-label fw-semibold small">
                        Composition
                    </label>
                    <select name="composition" id="filterComposition" class="form-select select2-filter">
                        <option value="">All Compositions</option>
                        @foreach($compositions as $composition)
                            <option value="{{ $composition->id }}" {{ (string)request('composition') === (string)$composition->id ? 'selected' : '' }}>
                                {{ $composition->composition_details }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- GENDER TYPE --}}
                <div class="col-md-3">
                    <label for="filterGender" class="form-label fw-semibold small">
                        Gender Type
                    </label>
                    <select name="gender" id="filterGender" class="form-select select2-filter">
                        <option value="">All Gender Types</option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender->id }}" {{ (string)request('gender') === (string)$gender->id ? 'selected' : '' }}>
                                {{ $gender->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                </button>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i> Clear
                </a>
            </div>
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
                        <th>Source</th>
                        {{-- <th>Price</th> --}}
                        <th>Stock</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->main_image_url)
                                    <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-muted small\'>No Image</span>';">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $product->name }}</td>
                            <td>{{ $product->item_type_name ?? '-' }}</td>
                            <td>
                                @if($product->is_integrated)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-link-45deg me-1"></i> Integrated
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        Manual
                                    </span>
                                @endif
                            </td>
                            {{-- <td>{{ $product->price ? '₹' . number_format($product->price, 2) : '-' }}</td> --}}
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $.fn.select2) {
            $('.select2-filter').select2({
                width: '100%'
            });
        }
    });
</script>
@endpush

