@extends('layouts.app')

@section('title', 'AI Photo Enhancing - Review Submission')

@section('content')

{{-- ===================================================
     HEADER: Back + Title
==================================================== --}}
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('admin.ai-photo-enhancing.receiving') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
    <div>
        <h4 class="fw-bold mb-0">Review Submission #{{ $submission->sno }}</h4>
        <p class="text-muted small mb-0">Submitted by: {{ $submission->enhancer_first_name }} {{ $submission->enhancer_last_name }} (ID: #{{ $submission->ai_photo_enhancer_id }})</p>
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

@if($submission->status !== 'pending')
    <div class="alert alert-{{ $submission->status === 'rejected' ? 'danger' : ($submission->status === 'approved_need_version' ? 'info' : 'success') }} d-flex align-items-center rounded-4 shadow-sm mb-4">
        <i class="bi bi-info-circle-fill fs-5 me-2"></i>
        <div>
            This submission has already been marked as <strong>{{ strtoupper(str_replace('_', ' ', $submission->status)) }}</strong>.
            @if($submission->admin_feedback)
                <br><span class="small opacity-75">Feedback: "{{ $submission->admin_feedback }}"</span>
            @endif
        </div>
    </div>
@endif

{{-- ===================================================
     SECTION 1: PRODUCT SPECIFICATIONS (TOP)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-info-circle text-primary me-2"></i> Product Specifications
        </h5>
    </div>
    <div class="card-body px-4 py-4">
        <div class="row g-4">
            @php
                $specs = [
                    'Item Name'     => $submission->product_name,
                    'SKU'           => $submission->sku,
                    'Colour'        => $submission->color,
                    'Gender'        => $submission->gender_text,
                    'Supplier'      => $submission->supplier_name,
                    'Barcode'       => $submission->barcode,
                    'Client Ref'    => $submission->clientreference,
                    'Submitted At'  => \Carbon\Carbon::parse($submission->created_at)->format('M d, Y h:i A'),
                ];
            @endphp

            @foreach($specs as $label => $value)
                <div class="col-md-3 col-sm-6">
                    <div class="bg-light rounded-3 p-3 h-100">
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem; letter-spacing:0.06em;">{{ $label }}</div>
                        <div class="fw-semibold text-dark">{{ $value ?: '—' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===================================================
     SECTION 2: IMAGES SIDE-BY-SIDE (MIDDLE)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images text-primary me-2"></i> Image Comparison ({{ strtoupper($submission->image_type) }} Image)
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4 mb-4">
            {{-- Left: Original Image --}}
            <div class="col-md-6">
                <div class="d-flex flex-column align-items-center justify-content-between p-3 rounded-3 bg-light border h-100" style="min-height: 550px;">
                    <span class="badge bg-secondary rounded-pill px-3 py-1.5 mb-3 fw-semibold">Original Image</span>
                    <div class="w-100 d-flex align-items-center justify-content-center flex-grow-1" style="height: 450px;">
                        <a href="{{ asset($submission->original_image_path) }}" target="_blank" rel="noopener noreferrer" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset($submission->original_image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height: 450px; object-fit: contain; cursor: pointer;" alt="Original">
                        </a>
                    </div>
                    <a href="{{ asset($submission->original_image_path) }}" download class="btn btn-sm btn-outline-secondary w-100 rounded-pill mt-3">
                        <i class="bi bi-download me-1"></i> Download Original
                    </a>
                </div>
            </div>

            {{-- Right: Enhanced Image --}}
            <div class="col-md-6">
                <div class="d-flex flex-column align-items-center justify-content-between p-3 rounded-3 bg-primary-subtle border border-primary-subtle h-100" style="min-height: 550px;">
                    <span class="badge bg-primary rounded-pill px-3 py-1.5 mb-3 fw-semibold">Enhanced Image</span>
                    <div class="w-100 d-flex align-items-center justify-content-center flex-grow-1" style="height: 450px;">
                        <a href="{{ asset($submission->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset($submission->enhanced_image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height: 450px; object-fit: contain; cursor: pointer;" alt="Enhanced">
                        </a>
                    </div>
                    <a href="{{ asset($submission->enhanced_image_path) }}" download class="btn btn-sm btn-primary w-100 rounded-pill mt-3 shadow-xs">
                        <i class="bi bi-download me-1"></i> Download Enhanced
                    </a>
                </div>
            </div>
        </div>

        {{-- Take Action Row --}}
        <hr class="my-4">
        <div class="p-3 bg-light rounded-4 border">
            <form method="POST" id="reviewActionForm">
                @csrf
                <div class="mb-4">
                    <label for="admin_feedback" class="form-label fw-semibold text-secondary mb-2">Review Notes / Feedback (Required for Rejection, Optional for Approval)</label>
                    <textarea name="admin_feedback" id="admin_feedback" class="form-control rounded-3" rows="3" placeholder="Add rejection reasons or approval notes here..." required></textarea>
                </div>

                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    {{-- Reject button (requires feedback) --}}
                    <button type="submit" formaction="{{ route('admin.ai-photo-enhancing.receiving.reject', $submission->sno) }}" class="btn btn-danger rounded-pill px-4 py-2.5 fw-semibold shadow-sm">
                        <i class="bi bi-x-circle me-1"></i> Reject & Send Feedback
                    </button>

                    <div class="d-flex gap-3">
                        {{-- Approve with new version (clears required flag) --}}
                        <button type="submit" onclick="document.getElementById('admin_feedback').required = false;" formaction="{{ route('admin.ai-photo-enhancing.receiving.approve-need-version', $submission->sno) }}" class="btn btn-outline-warning rounded-pill px-4 py-2.5 fw-semibold border-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Approved (Need New Version)
                        </button>

                        {{-- Approve completely (clears required flag) --}}
                        <button type="submit" onclick="document.getElementById('admin_feedback').required = false;" formaction="{{ route('admin.ai-photo-enhancing.receiving.approve', $submission->sno) }}" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Approve Completely
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================================================
     SECTION 3: PREVIOUS IMAGES (BOTTOM)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i> Previous Submission History for this Image
        </h5>
    </div>
    <div class="card-body p-4">
        @if($history->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="fw-semibold">#</th>
                            <th scope="col" class="fw-semibold text-center">Enhanced Preview</th>
                            <th scope="col" class="fw-semibold">Submitted At</th>
                            <th scope="col" class="fw-semibold">Status</th>
                            <th scope="col" class="fw-semibold">Admin Feedback</th>
                            <th scope="col" class="fw-semibold text-end">Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $index => $hist)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <a href="{{ asset($hist->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset($hist->enhanced_image_path) }}" class="rounded border shadow-sm" style="max-height: 48px; width: 48px; object-fit: contain;" alt="Enhanced Version">
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($hist->created_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($hist->status == 'approved')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Approved</span>
                                    @elseif($hist->status == 'approved_need_version')
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">Approved (Need New Version)</span>
                                    @elseif($hist->status == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hist->admin_feedback)
                                        <span class="text-danger fw-medium">{{ $hist->admin_feedback }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ asset($hist->enhanced_image_path) }}" download class="btn btn-xs btn-outline-secondary rounded-pill">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-info-circle opacity-50 d-block mb-1.5 fs-5"></i>
                <p class="mb-0 small">No previous attempts found for this image.</p>
            </div>
        @endif
    </div>
</div>

@endsection
