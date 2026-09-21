@extends('layouts.app')

@section('title', 'AI Photo Enhancing - Receiving')

@section('content')

@php
    $currentStatus = $status ?? 'pending';
@endphp

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Receiving Products</h4>
                <p class="text-muted small mb-0">Check photos sent by AI photo editors.</p>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                {{ $products->total() }} Product{{ $products->total() !== 1 ? 's' : '' }}
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

@if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm mb-4" role="alert">
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Status Filter Tabs: Approved, Need More, Reject, All --}}
<ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm gap-2">
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'pending' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'pending', 'search' => request('search')]) }}">
            <i class="bi bi-hourglass-split me-1"></i> Needs Check
            @if(!empty($counts['pending']))
                <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $counts['pending'] }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'approved' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'approved', 'search' => request('search')]) }}">
            <i class="bi bi-check-circle-fill me-1"></i> Approved
            @if(!empty($counts['approved']))
                <span class="badge bg-success ms-1 rounded-pill">{{ $counts['approved'] }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'need_version' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'need_version', 'search' => request('search')]) }}">
            <i class="bi bi-arrow-repeat me-1"></i> Need More
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'rejected' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'rejected', 'search' => request('search')]) }}">
            <i class="bi bi-x-circle-fill me-1"></i> Reject
            @if(!empty($counts['rejected']))
                <span class="badge bg-danger ms-1 rounded-pill">{{ $counts['rejected'] }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'all' ? 'active' : 'text-secondary' }}" 
           href="{{ route('admin.ai-photo-enhancing.receiving', ['status' => 'all', 'search' => request('search')]) }}">
            <i class="bi bi-list-ul me-1"></i> All
            @if(!empty($counts['total']))
                <span class="badge bg-secondary ms-1 rounded-pill">{{ $counts['total'] }}</span>
            @endif
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <form action="{{ route('admin.ai-photo-enhancing.receiving') }}" method="GET">
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search SKU, Barcode, or Product..." value="{{ $search }}">
                        <button type="submit" class="btn btn-primary px-3">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="fw-semibold">#</th>
                            <th scope="col" class="fw-semibold">Product</th>
                            <th scope="col" class="fw-semibold">AI Enhancer</th>
                            <th scope="col" class="fw-semibold text-center">Photos Sent</th>
                            <th scope="col" class="fw-semibold text-center">Status</th>
                            <th scope="col" class="fw-semibold">Sent Date</th>
                            <th scope="col" class="fw-semibold text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $prod)
                            <tr>
                                <td class="text-muted small">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $prod->product_name ?: 'Unnamed Product' }}</div>
                                    <div class="text-muted small">
                                        <span class="badge bg-light text-dark border">SKU: {{ $prod->sku ?: '—' }}</span>
                                        @if($prod->barcode)
                                            <span class="badge bg-light text-dark border ms-1">Barcode: {{ $prod->barcode }}</span>
                                        @endif
                                        @if($prod->color)
                                            <span class="text-muted ms-1">&bull; Color: {{ $prod->color }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $prod->enhancer_first_name }} {{ $prod->enhancer_last_name }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark rounded-pill px-3 py-1.5 fw-bold fs-7">
                                        <i class="bi bi-images me-1"></i> {{ $prod->total_images }} Photo{{ $prod->total_images !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        @if($prod->pending_count > 0)
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold">
                                                {{ $prod->pending_count }} Needs Check
                                            </span>
                                        @endif
                                        @if($prod->approved_count > 0)
                                            <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold">
                                                {{ $prod->approved_count }} Approved
                                            </span>
                                        @endif
                                        @if($prod->need_version_count > 0)
                                            <span class="badge bg-warning-subtle text-dark border border-warning rounded-pill px-2.5 py-1 fw-bold">
                                                {{ $prod->need_version_count }} Need More
                                            </span>
                                        @endif
                                        @if($prod->rejected_count > 0)
                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold">
                                                {{ $prod->rejected_count }} Reject
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $prod->latest_submission_date ? \Carbon\Carbon::parse($prod->latest_submission_date)->format('d M, Y - h:i A') : '—' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.ai-photo-enhancing.receiving.show', $prod->specification_id) }}" class="btn btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm">
                                        <i class="bi bi-eye-fill me-1"></i> Check Photos
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->appends(['status' => $currentStatus, 'search' => $search])->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                <h5>No products found</h5>
            </div>
        @endif
    </div>
</div>

@endsection
