@extends('layouts.app')

@section('title', 'AI Photo Enhancing - Receiving Products')

@section('content')

@php
    $currentStatus = request('status', 'pending');
@endphp

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Receiving Products</h4>
                <p class="text-muted small mb-0">Review and verify photo enhancements submitted by AI Enhancers.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fs-6">
                {{ $submissions->total() }} {{ ucfirst($currentStatus) }} Record{{ $submissions->total() !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Status Filter Tabs --}}
<ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm gap-2">
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'pending' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'pending', 'search' => request('search')]) }}">
            <i class="bi bi-hourglass-split me-1"></i> Pending Reviews
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'approved' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'approved', 'search' => request('search')]) }}">
            <i class="bi bi-check-circle me-1"></i> Approved
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'rejected' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'rejected', 'search' => request('search')]) }}">
            <i class="bi bi-x-circle me-1"></i> Rejected
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'all' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'all', 'search' => request('search')]) }}">
            <i class="bi bi-list-task me-1"></i> All Submissions
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <form action="{{ route('admin.ai-photo-enhancing.receiving') }}" method="GET">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search product, enhancer, SKU..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary px-3">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        @if($submissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="fw-semibold">#</th>
                            <th scope="col" class="fw-semibold">Product Specification</th>
                            <th scope="col" class="fw-semibold">AI Enhancer</th>
                            <th scope="col" class="fw-semibold text-center">Type</th>
                            <th scope="col" class="fw-semibold text-center">Original Image</th>
                            <th scope="col" class="fw-semibold text-center">Enhanced Image</th>
                            <th scope="col" class="fw-semibold">Submitted At</th>
                            <th scope="col" class="fw-semibold text-center">Status</th>
                            <th scope="col" class="fw-semibold text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $index => $sub)
                            <tr>
                                <td class="text-muted small">
                                    {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sub->product_name ?: 'N/A' }}</div>
                                    <div class="text-muted small">SKU: {{ $sub->sku ?: '—' }} | Color: {{ $sub->color ?: '—' }}</div>
                                    <div class="text-muted small">Supplier: {{ $sub->supplier_name ?: '—' }}</div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $sub->enhancer_first_name }} {{ $sub->enhancer_last_name }}</span>
                                    <div class="text-muted small">ID: #{{ $sub->ai_photo_enhancer_id }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-uppercase fw-medium" style="font-size: 0.65rem;">
                                        {{ $sub->image_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ asset($sub->original_image_path) }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset($sub->original_image_path) }}" class="rounded border shadow-sm" style="max-height: 48px; width: 48px; object-fit: contain;" alt="Original" onerror="this.parentElement.innerHTML='<span class=\'text-muted small\'>Not found</span>'">
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset($sub->enhanced_image_path) }}" class="rounded border shadow-sm" style="max-height: 48px; width: 48px; object-fit: contain;" alt="Enhanced">
                                    </a>
                                </td>
                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($sub->created_at)->format('M d, Y h:i A') }}
                                </td>
                                <td class="text-center">
                                    @if($sub->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 fw-semibold">Pending</span>
                                    @elseif($sub->status == 'approved')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-semibold">Approved</span>
                                    @elseif($sub->status == 'approved_need_version')
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 fw-semibold" title="Approved, requested new version">Approved (Need Version)</span>
                                    @elseif($sub->status == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-semibold">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.ai-photo-enhancing.receiving.show', $sub->sno) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                <h5 class="fw-semibold">No {{ $currentStatus }} submissions found</h5>
                <p class="mb-0">There are no records matching the selected status.</p>
            </div>
        @endif
    </div>
</div>

@endsection
