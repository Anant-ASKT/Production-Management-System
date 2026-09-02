@extends('layouts.ai_enhancer')

@section('title', 'Product Detail')

@section('content')

{{-- ===================================================
     HEADER: Back + Title + Status
==================================================== --}}
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('ai-enhancer.assigned-products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <div>
        <h4 class="fw-bold mb-0">Product Detail</h4>
        <p class="text-muted small mb-0">Assignment #{{ $product->assignment_id }}</p>
    </div>
    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 ms-auto fs-6">
        {{ ucfirst($product->assignment_status ?? 'Assigned') }}
    </span>
</div>

{{-- ===================================================
     SECTION 1: PRODUCT SPECIFICATIONS
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clipboard-data text-primary me-2"></i> Product Specifications
        </h5>
    </div>
    <div class="card-body px-4 py-4">
        <div class="row g-4">

            @php
                $specs = [
                    'Item Name'             => $product->item_name_text,
                    'Colour'                => $product->colour_text,
                    'Gender'                => $product->gender_text,
                    'Item Type'             => $product->item_type_text,
                    'Designer'              => $product->designer_name_text,
                    'Composition'           => $product->composition_text,
                    'Size'                  => $product->size_text,
                    'Embellishment'         => $product->embellishment_text,
                    'Manufacturing Process' => $product->manufacturing_process_text,
                    'Craftsman'             => $product->craftsman_text,
                    'Manufacturer'          => $product->manufacture_text,
                    'Client'                => $product->client_text,
                    'Supplier'              => $product->supplier_name,
                    'Client Reference'      => $product->clientreference,
                    'Barcode'               => $product->barcode,
                    'SKU'                   => $product->sku,
                    'Assigned Date'         => $product->assigned_date ? \Carbon\Carbon::parse($product->assigned_date)->format('M d, Y  h:i A') : null,
                ];
            @endphp

            @foreach($specs as $label => $value)
                @if($value)
                <div class="col-md-3 col-sm-6">
                    <div class="bg-light rounded-3 p-3 h-100">
                        <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:0.68rem; letter-spacing:0.06em;">{{ $label }}</div>
                        <div class="fw-semibold text-dark">{{ $value }}</div>
                    </div>
                </div>
                @endif
            @endforeach

        </div>
    </div>
</div>

{{-- ===================================================
     SECTION 2: PRODUCT IMAGES
==================================================== --}}
@php
    // Helper: convert a stored path to a usable URL
    function resolveImageUrl($path) {
        if (!$path) return null;
        $path = trim($path, " \t\n\r\0\x0B\"'\\");
        $path = str_replace('\\', '/', $path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) return $path;
        if (str_contains($path, 'ItemsDesigner_Masterwithbarcode/')) {
            $pos = strpos($path, 'ItemsDesigner_Masterwithbarcode/');
            return '/' . substr($path, $pos);
        }
        if (str_starts_with($path, 'raw_products/')) {
            return '/' . $path;
        }
        if (str_starts_with($path, 'enhanced_images/')) {
            return '/' . $path;
        }
        if (str_starts_with($path, '/')) return $path;
        if (str_starts_with($path, 'storage/')) return '/' . $path;
        return '/' . $path;
    }

    function parseImages($raw) {
        if (!$raw) return [];
        $raw = trim($raw);
        if (str_starts_with($raw, '[')) {
            try {
                $arr = json_decode($raw, true);
                if (is_array($arr)) {
                    return array_values(array_filter(array_map(fn($p) => resolveImageUrl($p), $arr)));
                }
            } catch (\Exception $e) {}
        }
        $url = resolveImageUrl($raw);
        return $url ? [$url] : [];
    }

    $mainImages = parseImages($product->img_path);
    $subImages  = parseImages($product->subimg_path);
    $allImages  = array_values(array_unique(array_merge($mainImages, $subImages)));
@endphp

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-images text-primary me-2"></i> Product Images
        </h5>
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ count($allImages) }} Image{{ count($allImages) !== 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body px-4 py-4">

        @if(count($allImages) > 0)
            <div class="row g-4">
                @foreach($allImages as $index => $img)
                    @php
                        $isMain = in_array($img, $mainImages);
                        $latestSubmission = $submissions->firstWhere('original_image_path', $img);
                    @endphp
                    <div class="col-12 col-md-6">
                        <div class="card border border-light-subtle rounded-4 shadow-sm h-100 overflow-hidden">
                            <div class="card-header bg-light border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                                <span class="badge bg-secondary rounded-pill px-3 py-1.5 fw-semibold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                    {{ $isMain ? 'Main Image' : 'Sub Image' }}
                                </span>
                                @if($latestSubmission)
                                    @if($latestSubmission->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-hourglass-split me-1"></i> Pending Review
                                        </span>
                                    @elseif($latestSubmission->status == 'approved')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-check-circle me-1"></i> Approved
                                        </span>
                                    @elseif($latestSubmission->status == 'approved_need_version')
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-arrow-repeat me-1"></i> Approved (Need Version)
                                        </span>
                                    @elseif($latestSubmission->status == 'rejected')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1.5 fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i> Rejected
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-secondary rounded-pill px-3 py-1.5 border fw-semibold">
                                        Not Uploaded
                                    </span>
                                @endif
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    {{-- Left side: Original Image Preview & Download --}}
                                    <div class="col-sm-5 d-flex flex-column align-items-center justify-content-between bg-light rounded-3 p-3 text-center" style="min-height: 180px;">
                                        <div class="w-100 d-flex align-items-center justify-content-center flex-grow-1" style="height: 120px;">
                                            <a href="{{ $img }}" target="_blank" rel="noopener noreferrer" title="View Original in new tab">
                                                <img src="{{ $img }}" class="img-fluid rounded border shadow-sm" style="max-height: 120px; object-fit: contain; cursor: pointer;" alt="Original Image" onerror="this.parentElement.parentElement.innerHTML='<span class=\'text-muted small\'><i class=\'bi bi-exclamation-circle\'></i> Not found</span>'">
                                            </a>
                                        </div>
                                        <a href="{{ $img }}" download class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-2">
                                            <i class="bi bi-download me-1"></i> Download Original
                                        </a>
                                    </div>

                                    {{-- Right side: Upload Form or Current Enhancement status --}}
                                    <div class="col-sm-7 d-flex flex-column justify-content-center">
                                        @if(!$latestSubmission)
                                            <form action="{{ route('ai-enhancer.submissions.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                                @csrf
                                                <input type="hidden" name="specification_id" value="{{ $product->spec_id }}">
                                                <input type="hidden" name="original_image_path" value="{{ $img }}">
                                                <input type="hidden" name="image_type" value="{{ $isMain ? 'main' : 'sub' }}">

                                                <label class="form-label small fw-semibold text-secondary mb-2">Upload Enhanced Version</label>
                                                <div class="input-group input-group-sm mb-2">
                                                    <input type="file" name="enhanced_image" class="form-control rounded-start-pill" accept="image/*" required>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm">
                                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Submit Enhancement
                                                </button>
                                            </form>
                                        @else
                                            {{-- Pending, Approved, Approved Need Version or Rejected state --}}
                                            @if($latestSubmission->status == 'pending')
                                                <div class="text-center p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column align-items-center justify-content-center">
                                                    <i class="bi bi-clock-history fs-3 text-warning opacity-75 mb-2"></i>
                                                    <p class="small text-muted mb-0">Enhancement is pending review by Admin.</p>
                                                </div>
                                            @elseif($latestSubmission->status == 'approved')
                                                <div class="text-center p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column align-items-center justify-content-center">
                                                    <div class="w-100 mb-2 d-flex align-items-center justify-content-center" style="height: 100px;">
                                                        <a href="{{ asset($latestSubmission->enhanced_image_path) }}" target="_blank" rel="noopener noreferrer" title="View Enhanced in new tab">
                                                            <img src="{{ asset($latestSubmission->enhanced_image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height: 100px; object-fit: contain; cursor: pointer;" alt="Enhanced Image">
                                                        </a>
                                                    </div>
                                                    <a href="{{ asset($latestSubmission->enhanced_image_path) }}" download class="btn btn-xs btn-outline-success rounded-pill mt-2">
                                                        <i class="bi bi-download me-1"></i> Download Enhanced
                                                    </a>
                                                </div>
                                            @elseif($latestSubmission->status == 'rejected')
                                                <div class="alert alert-danger p-3 rounded-3 mb-0 small border-0 shadow-sm text-center">
                                                    <i class="bi bi-exclamation-triangle-fill fs-4 text-danger mb-2 d-block"></i>
                                                    <div class="fw-bold text-dark mb-1">Enhancement Rejected</div>
                                                    <p class="text-muted mb-3 fs-7">Feedback: "{{ $latestSubmission->admin_feedback }}"</p>
                                                    <a href="{{ route('ai-enhancer.upload-history.show', $latestSubmission->sno) }}" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm w-100">
                                                        <i class="bi bi-arrow-right-circle me-1"></i> Go to History to Re-upload
                                                    </a>
                                                </div>
                                            @elseif($latestSubmission->status == 'approved_need_version')
                                                <div class="alert alert-info p-3 rounded-3 mb-0 small border-0 shadow-sm text-center">
                                                    <i class="bi bi-info-circle-fill fs-4 text-info mb-2 d-block"></i>
                                                    <div class="fw-bold text-dark mb-1">Approved (New Version Requested)</div>
                                                    <p class="text-muted mb-3 fs-7">Admin has approved the image but requested a new version.</p>
                                                    <a href="{{ route('ai-enhancer.upload-history.show', $latestSubmission->sno) }}" class="btn btn-sm btn-info rounded-pill px-3 text-white shadow-sm w-100">
                                                        <i class="bi bi-arrow-right-circle me-1"></i> Go to History to Upload New
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-image fs-1 opacity-25 d-block mb-2"></i>
                <p class="mb-0">No images available for this product.</p>
            </div>
        @endif

    </div>
</div>

@endsection
