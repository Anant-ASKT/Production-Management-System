@extends('layouts.ai_enhancer')

@section('title', 'Product Photos & Upload')

@section('content')

{{-- ===================================================
     HEADER: Back + Simple Title
==================================================== --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('ai-enhancer.assigned-products.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">
                Product: <span class="text-primary">{{ $product->sku ?: ('#' . $product->spec_id) }}</span>
            </h3>
            <p class="text-muted small mb-0">{{ $product->item_name_text ?: 'Product Details' }} &bull; Color: {{ $product->colour_text ?: '—' }}</p>
        </div>
    </div>
    <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold">
        <i class="bi bi-tag-fill me-1"></i> {{ ucfirst($product->assignment_status ?? 'Assigned') }}
    </span>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 p-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
            <div>
                <h6 class="fw-bold mb-0">Success!</h6>
                <div>{{ session('success') }}</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm mb-4 p-3" role="alert">
        <ul class="mb-0 fw-semibold">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Overall / Main Note from Admin --}}
@if(!empty($product->admin_comment))
    <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-3.5 mb-4">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 40px; height: 40px;">
                <i class="bi bi-chat-square-quote-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fw-bold fs-6">
                        <i class="bi bi-shield-check text-primary me-1"></i> Main Note / Overall Feedback from Admin
                    </strong>
                    <span class="badge bg-warning text-dark border border-warning-subtle px-2 py-1">Applies to All Photos</span>
                </div>
                <div class="text-dark fw-medium" style="white-space: pre-line; font-size: 0.95rem;">{{ $product->admin_comment }}</div>
            </div>
        </div>
    </div>
@endif

{{-- ===================================================
     PRODUCT SUMMARY (SIMPLE CARDS)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                    <span class="text-muted small d-block mb-1">Product Name</span>
                    <strong class="text-dark">{{ $product->item_name_text ?: '—' }}</strong>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                    <span class="text-muted small d-block mb-1">SKU</span>
                    <strong class="text-dark">{{ $product->sku ?: '—' }}</strong>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                    <span class="text-muted small d-block mb-1">Color</span>
                    <strong class="text-dark">{{ $product->colour_text ?: '—' }}</strong>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                    <span class="text-muted small d-block mb-1">Barcode</span>
                    <strong class="text-dark">{{ $product->barcode ?: '—' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    function resolveImageUrl($path) {
        if (!$path) return null;
        $path = trim($path, " \t\n\r\0\x0B\"'\\");
        $path = str_replace('\\', '/', $path);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) return $path;
        if (str_contains($path, 'ItemsDesigner_Masterwithbarcode/')) {
            $pos = strpos($path, 'ItemsDesigner_Masterwithbarcode/');
            return '/' . substr($path, $pos);
        }
        if (str_starts_with($path, 'raw_products/')) return '/' . $path;
        if (str_starts_with($path, 'enhanced_images/')) return '/' . $path;
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
    $allOriginalImages = array_values(array_unique(array_merge($mainImages, $subImages)));
@endphp

{{-- ===================================================
     STEP 1: ORIGINAL PHOTOS (DOWNLOAD THEM)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                1
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Original Photos (Download to Edit)</h5>
                <small class="text-muted">Click any photo or download button to save to your phone or computer.</small>
            </div>
        </div>
        @if(count($allOriginalImages) > 1)
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-bold" onclick="downloadAllOriginals()">
                <i class="bi bi-download me-1"></i> Download All Photos
            </button>
        @endif
    </div>
    <div class="card-body p-4 bg-light">
        @if(count($allOriginalImages) > 0)
            <div class="row g-3">
                @foreach($allOriginalImages as $idx => $imgUrl)
                    @php $isMain = in_array($imgUrl, $mainImages); @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card border rounded-3 p-2 bg-white text-center shadow-xs h-100">
                            <span class="badge {{ $isMain ? 'bg-primary' : 'bg-secondary' }} align-self-start mb-1 px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $isMain ? 'Main Photo' : 'Photo #' . $idx }}
                            </span>
                            <div class="d-flex align-items-center justify-content-center p-2 mb-2 bg-light rounded" style="height: 140px;">
                                <a href="{{ $imgUrl }}" target="_blank" title="Click to view">
                                    <img src="{{ $imgUrl }}" class="img-fluid rounded" style="max-height: 125px; object-fit: contain;" alt="Original">
                                </a>
                            </div>
                            <a href="{{ $imgUrl }}" download="original_{{ $product->sku }}_{{ $idx + 1 }}" class="btn btn-sm btn-primary rounded-pill w-100 py-1.5 fw-bold download-orig-link" data-url="{{ $imgUrl }}">
                                <i class="bi bi-download me-1"></i> Download Photo
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">No original photos found.</p>
        @endif
    </div>
</div>

{{-- ===================================================
     STEP 2: UPLOAD FINISHED PHOTOS
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 border-2 border-primary">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                2
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Send Finished Photos (Upload Multiple)</h5>
                <small class="text-muted">Select one or more edited photos. You can select large photos; they will be automatically compressed.</small>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('ai-enhancer.submissions.upload') }}" method="POST" enctype="multipart/form-data" id="batchUploadForm">
            @csrf
            <input type="hidden" name="specification_id" value="{{ $product->spec_id }}">

            {{-- Big Friendly Dropzone --}}
            <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light mb-3" id="dropZone" style="border: 3px dashed #0d6efd; background-color: #f8faff; cursor: pointer;">
                <i class="bi bi-cloud-arrow-up-fill fs-1 text-primary mb-2 d-block"></i>
                <h5 class="fw-bold text-dark mb-1">Click Here to Choose Photos</h5>
                <p class="text-muted small mb-3">Or drag and drop your edited photo files here</p>
                <input type="file" name="enhanced_images[]" id="fileInput" class="d-none" multiple accept="image/*">
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm" onclick="document.getElementById('fileInput').click()">
                    <i class="bi bi-plus-circle-fill me-2"></i> Choose Photos
                </button>
            </div>

            {{-- Preview Area --}}
            <div id="previewArea" class="mb-3 d-none">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-dark" id="selectedCountText">0 photos selected</span>
                    <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none fw-bold p-0" onclick="clearSelectedFiles()">Remove All</button>
                </div>
                <div class="row g-2" id="previewGrid"></div>
            </div>

            <div class="text-end">
                <button type="submit" id="submitBtn" class="btn btn-success btn-lg rounded-pill px-5 py-2.5 fw-bold shadow" disabled>
                    <i class="bi bi-send-fill me-2"></i> Send Photos to Admin
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     STEP 3: STATUS OF SENT PHOTOS
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                3
            </span>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Photos You Have Sent ({{ count($submissions) }})</h5>
                <small class="text-muted">Check below to see which photos are accepted or need changes.</small>
            </div>
        </div>
        <span class="badge bg-dark rounded-pill px-3 py-1.5 fw-bold">{{ count($submissions) }} Sent</span>
    </div>
    <div class="card-body p-4 bg-light">
        @if(count($submissions) > 0)
            <div class="row g-4">
                @foreach($submissions as $sub)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border rounded-4 shadow-sm h-100 overflow-hidden bg-white">
                            
                            {{-- Visual Status Banner --}}
                            @if($sub->status == 'approved')
                                <div class="p-2 text-center bg-success text-white fw-bold small">
                                    <i class="bi bi-check-circle-fill me-1"></i> APPROVED
                                </div>
                            @elseif($sub->status == 'approved_need_version')
                                <div class="p-2 text-center bg-warning text-dark fw-bold small">
                                    <i class="bi bi-arrow-repeat me-1"></i> NEED MORE
                                </div>
                            @elseif($sub->status == 'rejected')
                                <div class="p-2 text-center bg-danger text-white fw-bold small">
                                    <i class="bi bi-x-circle-fill me-1"></i> REJECT
                                </div>
                            @else
                                <div class="p-2 text-center bg-secondary text-white fw-bold small">
                                    <i class="bi bi-hourglass-split me-1"></i> Waiting for Admin to Check
                                </div>
                            @endif

                            {{-- Image Display --}}
                            <div class="p-3 text-center">
                                <div class="d-flex align-items-center justify-content-center bg-light rounded-3 p-2 mb-2" style="height: 180px;">
                                    <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" title="Click to view large">
                                        <img src="{{ asset($sub->enhanced_image_path) }}" class="img-fluid rounded" style="max-height: 165px; object-fit: contain;" alt="Enhanced">
                                    </a>
                                </div>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-zoom-in me-1"></i> View Full
                                    </a>
                                    <a href="{{ asset($sub->enhanced_image_path) }}" download class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>

                            {{-- Note / Reason from Admin --}}
                            @if($sub->admin_feedback || in_array($sub->status, ['rejected', 'approved_need_version']))
                                <div class="card-footer p-3 small {{ $sub->status == 'rejected' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    <div class="fw-bold mb-1">
                                        <i class="bi bi-chat-left-text-fill me-1"></i> Note from Admin:
                                    </div>
                                    <div class="fw-semibold">
                                        {{ $sub->admin_feedback ?: ($sub->status == 'approved_need_version' ? 'Please fix issues and send a new photo.' : 'This photo was rejected.') }}
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-cloud-upload fs-1 opacity-25 d-block mb-2"></i>
                <h5>No photos sent yet</h5>
                <p class="text-muted small">Choose your edited photos in Step 2 above and click Send.</p>
            </div>
        @endif
    </div>
</div>

<script>
    const fileInput = document.getElementById('fileInput');
    const previewArea = document.getElementById('previewArea');
    const previewGrid = document.getElementById('previewGrid');
    const selectedCountText = document.getElementById('selectedCountText');
    const submitBtn = document.getElementById('submitBtn');
    const dropZone = document.getElementById('dropZone');

    let dt = new DataTransfer();

    fileInput.addEventListener('change', function() {
        for (let i = 0; i < this.files.length; i++) {
            dt.items.add(this.files[i]);
        }
        fileInput.files = dt.files;
        renderPreviews();
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#e8f0fe';
    });
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8faff';
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8faff';
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            for (let i = 0; i < e.dataTransfer.files.length; i++) {
                if (e.dataTransfer.files[i].type.startsWith('image/')) {
                    dt.items.add(e.dataTransfer.files[i]);
                }
            }
            fileInput.files = dt.files;
            renderPreviews();
        }
    });

    function renderPreviews() {
        previewGrid.innerHTML = '';
        const files = dt.files;

        if (files.length === 0) {
            previewArea.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        previewArea.classList.remove('d-none');
        submitBtn.disabled = false;
        selectedCountText.innerText = `${files.length} photo(s) selected to send`;

        Array.from(files).forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3 col-lg-2';

            const sizeMb = (file.size / (1024 * 1024)).toFixed(1);

            const card = document.createElement('div');
            card.className = 'card p-2 border rounded-3 position-relative bg-white text-center shadow-xs';
            card.innerHTML = `
                <div style="height: 80px;" class="d-flex align-items-center justify-content-center overflow-hidden mb-1">
                    <img id="prev-img-${index}" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                </div>
                <div class="text-truncate small fw-bold text-dark" title="${file.name}" style="font-size:0.75rem;">${file.name}</div>
                <div class="text-muted" style="font-size:0.68rem;">${sizeMb} MB</div>
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;" onclick="removeFile(${index})" title="Remove photo">
                    &times;
                </button>
            `;
            col.appendChild(card);
            previewGrid.appendChild(col);

            const reader = new FileReader();
            reader.onload = (e) => {
                const imgElem = document.getElementById(`prev-img-${index}`);
                if (imgElem) imgElem.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeFile = function(index) {
        const newDt = new DataTransfer();
        const files = dt.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                newDt.items.add(files[i]);
            }
        }
        dt = newDt;
        fileInput.files = dt.files;
        renderPreviews();
    };

    window.clearSelectedFiles = function() {
        dt = new DataTransfer();
        fileInput.files = dt.files;
        renderPreviews();
    };

    window.downloadAllOriginals = function() {
        const links = document.querySelectorAll('.download-orig-link');
        links.forEach((a, idx) => {
            setTimeout(() => {
                const trigger = document.createElement('a');
                trigger.href = a.getAttribute('href');
                trigger.download = a.getAttribute('download') || 'photo_' + idx;
                document.body.appendChild(trigger);
                trigger.click();
                document.body.removeChild(trigger);
            }, idx * 350);
        });
    };

    document.getElementById('batchUploadForm').addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending photos... please wait`;
    });
</script>

@endsection
