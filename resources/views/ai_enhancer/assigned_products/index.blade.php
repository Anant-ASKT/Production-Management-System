@extends('layouts.ai_enhancer')

@section('title', 'Assigned Products')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Assigned Products</h4>
            <p class="text-muted mb-0 small">Products that have been sent to you for photo enhancement.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fs-6">
            {{ $assignedProducts->count() }} Product{{ $assignedProducts->count() !== 1 ? 's' : '' }}
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if($assignedProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="fw-semibold">#</th>
                                <th scope="col" class="fw-semibold">Item Name</th>
                                <th scope="col" class="fw-semibold">Supplier</th>
                                <th scope="col" class="fw-semibold">Colour</th>
                                <th scope="col" class="fw-semibold">Gender</th>
                                <th scope="col" class="fw-semibold">Assigned Date</th>
                                <th scope="col" class="fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedProducts as $index => $product)
                                <tr>
                                    <td class="text-muted small">{{ $index + 1 }}</td>
                                    <td><span class="fw-medium text-dark">{{ $product->product_name ?: 'N/A' }}</span></td>
                                    <td>{{ $product->supplier_name ?: '--' }}</td>
                                    <td>{{ $product->color ?: '--' }}</td>
                                    <td>{{ $product->age_group ?: '--' }}</td>
                                    <td class="text-muted small">{{ \Carbon\Carbon::parse($product->assigned_date)->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('ai-enhancer.assigned-products.show', $product->assignment_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                    <h5 class="fw-semibold">No assigned products yet</h5>
                    <p class="mb-0">When products are sent to the photo section, they will appear here.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
