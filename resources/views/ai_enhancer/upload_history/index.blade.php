@extends('layouts.ai_enhancer')

@section('title', 'My Sent Photos History')

@section('content')

    @php
        $currentStatus = $status ?? 'all';
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Sent Photos History</h4>
            <p class="text-muted mb-0 small">See all products and the status of photos you have sent.</p>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold">
            {{ $history->total() }} Product{{ $history->total() !== 1 ? 's' : '' }}
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Simple Filter Tabs --}}
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm gap-2">
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'all' ? 'active' : 'text-secondary' }}" 
               href="{{ route('ai-enhancer.upload-history.index', ['status' => 'all', 'search' => request('search')]) }}">
                <i class="bi bi-list-ul me-1"></i> All Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'pending' ? 'active' : 'text-secondary' }}" 
               href="{{ route('ai-enhancer.upload-history.index', ['status' => 'pending', 'search' => request('search')]) }}">
                <i class="bi bi-hourglass-split me-1"></i> Waiting for Check
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'approved' ? 'active' : 'text-secondary' }}" 
               href="{{ route('ai-enhancer.upload-history.index', ['status' => 'approved', 'search' => request('search')]) }}">
                <i class="bi bi-check-circle-fill me-1"></i> Approved
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'need_version' ? 'active' : 'text-secondary' }}" 
               href="{{ route('ai-enhancer.upload-history.index', ['status' => 'need_version', 'search' => request('search')]) }}">
                <i class="bi bi-arrow-repeat me-1"></i> Need More
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 {{ $currentStatus == 'rejected' ? 'active' : 'text-secondary' }}" 
               href="{{ route('ai-enhancer.upload-history.index', ['status' => 'rejected', 'search' => request('search')]) }}">
                <i class="bi bi-x-circle-fill me-1"></i> Reject
            </a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <form action="{{ route('ai-enhancer.upload-history.index') }}" method="GET">
                        <input type="hidden" name="status" value="{{ $currentStatus }}">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search SKU, Barcode, or Product Name..." value="{{ $search }}">
                            <button type="submit" class="btn btn-primary px-3">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @if($history->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="fw-semibold">#</th>
                                <th scope="col" class="fw-semibold">Product</th>
                                <th scope="col" class="fw-semibold text-center">Photos Sent</th>
                                <th scope="col" class="fw-semibold text-center">Current Status</th>
                                <th scope="col" class="fw-semibold">Sent Date</th>
                                <th scope="col" class="fw-semibold text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $index => $item)
                                <tr>
                                    <td class="text-muted small">
                                        {{ ($history->currentPage() - 1) * $history->perPage() + $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $item->product_name ?: 'Unnamed Product' }}</div>
                                        <div class="text-muted small">
                                            <span class="badge bg-light text-dark border">SKU: {{ $item->sku ?: '—' }}</span>
                                            @if($item->barcode)
                                                <span class="badge bg-light text-dark border ms-1">Barcode: {{ $item->barcode }}</span>
                                            @endif
                                            @if($item->color)
                                                <span class="text-muted ms-1">&bull; Color: {{ $item->color }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark rounded-pill px-3 py-1.5 fw-bold fs-7">
                                            <i class="bi bi-images me-1"></i> {{ $item->total_images }} Photo{{ $item->total_images !== 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @if($item->pending_count > 0)
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-clock me-1"></i>{{ $item->pending_count }} Waiting
                                                </span>
                                            @endif
                                            @if($item->approved_count > 0)
                                                <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-check me-1"></i>{{ $item->approved_count }} Approved
                                                </span>
                                            @endif
                                            @if($item->need_version_count > 0)
                                                <span class="badge bg-warning-subtle text-dark border border-warning rounded-pill px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-arrow-repeat me-1"></i>{{ $item->need_version_count }} Need More
                                                </span>
                                            @endif
                                            @if($item->rejected_count > 0)
                                                <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-x me-1"></i>{{ $item->rejected_count }} Reject
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $item->latest_submission_date ? \Carbon\Carbon::parse($item->latest_submission_date)->format('d M, Y - h:i A') : '—' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('ai-enhancer.upload-history.show', $item->specification_id) }}" class="btn btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-sm">
                                            <i class="bi bi-eye-fill me-1"></i> Open Photos
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $history->appends(['status' => $currentStatus, 'search' => $search])->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                    <h5>No products found</h5>
                    <p class="text-muted small">Try choosing another tab above.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
