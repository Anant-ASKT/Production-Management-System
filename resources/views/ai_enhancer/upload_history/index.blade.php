@extends('layouts.ai_enhancer')

@section('title', 'Upload History')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Upload History</h4>
            <p class="text-muted mb-0 small">History of all photo enhancements you have submitted.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fs-6">
            {{ $history->total() }} Submission{{ $history->total() !== 1 ? 's' : '' }}
        </span>
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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if($history->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th scope="col" class="fw-semibold">#</th>
                                <th scope="col" class="fw-semibold">Product Details</th>
                                <th scope="col" class="fw-semibold text-center">Type</th>
                                <th scope="col" class="fw-semibold text-center">Original Image</th>
                                <th scope="col" class="fw-semibold text-center">Enhanced Image</th>
                                <th scope="col" class="fw-semibold">Date Submitted</th>
                                <th scope="col" class="fw-semibold text-center">Status</th>
                                <th scope="col" class="fw-semibold">Feedback / Reason</th>
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
                                        <div class="fw-semibold text-dark">{{ $item->product_name ?: 'N/A' }}</div>
                                        <div class="text-muted small">SKU: {{ $item->sku ?: '—' }} | Color: {{ $item->color ?: '—' }}</div>
                                        <div class="text-muted small">Supplier: {{ $item->supplier_name ?: '—' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 text-uppercase fw-medium" style="font-size: 0.65rem;">
                                            {{ $item->image_type }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ asset($item->original_image_path) }}" target="_blank" rel="noopener noreferrer" class="d-inline-block">
                                            <img src="{{ asset($item->original_image_path) }}" class="rounded border shadow-sm" style="max-height: 48px; width: 48px; object-fit: contain;" alt="Original" onerror="this.parentElement.innerHTML='<span class=\'text-muted small\'>Not found</span>'">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ asset($item->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer" class="d-inline-block">
                                            <img src="{{ asset($item->enhanced_image_path) }}" class="rounded border shadow-sm" style="max-height: 48px; width: 48px; object-fit: contain;" alt="Enhanced" onerror="this.parentElement.innerHTML='<span class=\'text-muted small\'>Not found</span>'">
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 fw-semibold">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-semibold">Approved</span>
                                        @elseif($item->status == 'approved_need_version')
                                            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1 fw-semibold">Need Version</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-semibold">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->admin_feedback)
                                            <span class="text-danger small fw-medium">{{ $item->admin_feedback }}</span>
                                        @elseif($item->status == 'approved_need_version')
                                            <span class="text-info small fw-medium">Approved, but new version requested</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('ai-enhancer.upload-history.show', $item->sno) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Links --}}
                <div class="d-flex justify-content-end mt-4">
                    {{ $history->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                    <h5 class="fw-semibold">No uploads history found</h5>
                    <p class="mb-0">Enhancements you submit will be logged here with their verification statuses.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
