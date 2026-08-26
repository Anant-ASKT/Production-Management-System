@extends('layouts.ai_enhancer')

@section('title', 'Upload History - Submission Detail')

@section('content')

{{-- ===================================================
     HEADER: Back + Title
==================================================== --}}
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('ai-enhancer.upload-history.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to History
    </a>
    <div>
        <h4 class="fw-bold mb-0">Submission Details</h4>
        <p class="text-muted small mb-0">Record #{{ $submission->sno }} | Submitted on {{ \Carbon\Carbon::parse($submission->created_at)->format('M d, Y h:i A') }}</p>
    </div>
    
    <div class="ms-auto">
        @if($submission->status == 'pending')
            <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fs-6">Pending Review</span>
        @elseif($submission->status == 'approved')
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fs-6">Approved</span>
        @elseif($submission->status == 'approved_need_version')
            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2 fs-6">Approved (Need Version)</span>
        @elseif($submission->status == 'rejected')
            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fs-6">Rejected</span>
        @endif
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

{{-- ===================================================
     SECTION 1: PRODUCT SPECIFICATIONS
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
     SECTION 2: IMAGES COMPARISON & ACTION
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images text-primary me-2"></i> Image Comparison ({{ strtoupper($submission->image_type) }} Image)
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4 mb-4">
            {{-- Original Image --}}
            <div class="col-md-6">
                <div class="d-flex flex-column align-items-center justify-content-between p-3 rounded-3 bg-light border h-100" style="min-height: 480px;">
                    <span class="badge bg-secondary rounded-pill px-3 py-1.5 mb-3 fw-semibold">Original Image</span>
                    <div class="w-100 d-flex align-items-center justify-content-center flex-grow-1" style="height: 380px;">
                        <a href="{{ asset($submission->original_image_path) }}" target="_blank" rel="noopener noreferrer" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset($submission->original_image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height: 380px; object-fit: contain; cursor: pointer;" alt="Original">
                        </a>
                    </div>
                    <a href="{{ asset($submission->original_image_path) }}" download class="btn btn-sm btn-outline-secondary w-100 rounded-pill mt-3">
                        <i class="bi bi-download me-1"></i> Download Original
                    </a>
                </div>
            </div>

            {{-- Enhanced Image --}}
            <div class="col-md-6">
                <div class="d-flex flex-column align-items-center justify-content-between p-3 rounded-3 bg-primary-subtle border border-primary-subtle h-100" style="min-height: 480px;">
                    <span class="badge bg-primary rounded-pill px-3 py-1.5 mb-3 fw-semibold">Enhanced Image</span>
                    <div class="w-100 d-flex align-items-center justify-content-center flex-grow-1" style="height: 380px;">
                        <a href="{{ asset($submission->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset($submission->enhanced_image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height: 380px; object-fit: contain; cursor: pointer;" alt="Enhanced">
                        </a>
                    </div>
                    <a href="{{ asset($submission->enhanced_image_path) }}" download class="btn btn-sm btn-primary w-100 rounded-pill mt-3 shadow-xs">
                        <i class="bi bi-download me-1"></i> Download Enhanced
                    </a>
                </div>
            </div>
        </div>

        {{-- Upload New Version Panel (Enabled only if Rejected or Approved-Need-Version) --}}
        @if($submission->status == 'rejected' || $submission->status == 'approved_need_version')
            <hr class="my-4">
            <div class="p-4 rounded-4 border bg-light">
                <div class="row g-4">
                    {{-- Left side: Feedback explanation --}}
                    <div class="col-md-6 d-flex flex-column justify-content-center">
                        <div class="alert alert-{{ $submission->status == 'rejected' ? 'danger' : 'info' }} rounded-3 border-0 shadow-sm p-3 mb-0 h-100 d-flex flex-column justify-content-center">
                            <h6 class="fw-bold mb-1">
                                <i class="bi {{ $submission->status == 'rejected' ? 'bi-exclamation-triangle-fill text-danger' : 'bi-info-circle-fill text-info' }} me-2"></i>
                                {{ $submission->status == 'rejected' ? 'Rejection Feedback' : 'Admin Request Notes' }}
                            </h6>
                            <p class="mb-0 text-secondary mt-1">
                                @if($submission->status == 'rejected')
                                    This enhancement attempt was rejected. Reason: <strong>"{{ $submission->admin_feedback }}"</strong>. Please correct and upload a fresh enhancement version using the form.
                                @else
                                    This version was approved, but the admin requested a new version with further improvements. Please upload it here.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Right side: Fresh upload form --}}
                    <div class="col-md-6 border-start ps-md-4">
                        <form action="{{ route('ai-enhancer.submissions.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="specification_id" value="{{ $submission->specification_id }}">
                            <input type="hidden" name="original_image_path" value="{{ $submission->original_image_path }}">
                            <input type="hidden" name="image_type" value="{{ $submission->image_type }}">

                            <div class="mb-3">
                                <label for="enhanced_image" class="form-label small fw-semibold text-secondary mb-2">Upload New Improved Version</label>
                                <input type="file" name="enhanced_image" id="enhanced_image" class="form-control rounded-pill" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm w-100 py-2">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Submit New Attempt
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ===================================================
     SECTION 3: OTHER ATTEMPTS HISTORY (BOTTOM)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history text-primary me-2"></i> Other Submissions History for this Image
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
                            <th scope="col" class="fw-semibold">Feedback Comments</th>
                            <th scope="col" class="fw-semibold text-end">Action</th>
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
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1">Need Version</span>
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
                                    <a href="{{ route('ai-enhancer.upload-history.show', $hist->sno) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5">
                                        View Detail
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
                <p class="mb-0 small">No other historical submissions found for this image.</p>
            </div>
        @endif
    </div>
</div>

@endsection
