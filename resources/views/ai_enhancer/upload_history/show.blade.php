@extends('layouts.ai_enhancer')

@section('title', 'Product Photos History')

@section('content')

{{-- ===================================================
     HEADER: Back + Simple Title
==================================================== --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('ai-enhancer.upload-history.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">
                Photos for: <span class="text-primary">{{ $spec->sku ?: ('#' . $spec->sno) }}</span>
            </h3>
            <p class="text-muted small mb-0">{{ $spec->product_name ?: 'Product Photos' }} &bull; Color: {{ $spec->color ?: '—' }}</p>
        </div>
    </div>
    @if($spec->assignment_id)
        <a href="{{ route('ai-enhancer.assigned-products.show', $spec->assignment_id) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Open Task Page
        </a>
    @endif
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
@if(!empty($spec->assignment_admin_comment))
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
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">Applies to All Photos</span>
                </div>
                <div class="text-dark fw-medium" style="white-space: pre-line; font-size: 0.95rem;">{{ $spec->assignment_admin_comment }}</div>
            </div>
        </div>
    </div>
@endif

{{-- ===================================================
     RE-UPLOAD SECTION (SEND NEW / REVISED PHOTOS)
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 border-2 border-primary">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Send New / Fixed Photos
        </h5>
        <small class="text-muted">If any photo was rejected or needs changes, upload your corrected photo here.</small>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('ai-enhancer.submissions.upload') }}" method="POST" enctype="multipart/form-data" id="historyUploadForm">
            @csrf
            <input type="hidden" name="specification_id" value="{{ $spec->sno }}">

            <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light mb-3" id="historyDropZone" style="border: 3px dashed #0d6efd; background-color: #f8faff; cursor: pointer;">
                <i class="bi bi-images fs-1 text-primary mb-2 d-block"></i>
                <h5 class="fw-bold text-dark mb-1">Click Here to Choose New Photos</h5>
                <p class="text-muted small mb-3">Or drag and drop your photo files here</p>
                <input type="file" name="enhanced_images[]" id="historyFileInput" class="d-none" multiple accept="image/*">
                <button type="button" class="btn btn-primary rounded-pill px-5 py-2 fw-bold" onclick="document.getElementById('historyFileInput').click()">
                    <i class="bi bi-folder-plus me-1"></i> Choose Photos
                </button>
            </div>

            <div id="historyPreviewArea" class="mb-3 d-none">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-dark" id="historySelectedCount">0 photos selected</span>
                    <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none fw-bold p-0" onclick="clearHistoryFiles()">Remove All</button>
                </div>
                <div class="row g-2" id="historyPreviewGrid"></div>
            </div>

            <div class="text-end">
                <button type="submit" id="historySubmitBtn" class="btn btn-success btn-lg rounded-pill px-5 py-2.5 fw-bold shadow" disabled>
                    <i class="bi bi-send-fill me-2"></i> Send Photos to Admin
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================================================
     ALL PHOTOS SENT FOR THIS PRODUCT
==================================================== --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom px-4 pt-4 pb-3 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Photos Sent for this Product ({{ $submissions->count() }})</h5>
            <small class="text-muted">Review the status and comments from admin for each photo.</small>
        </div>
        <span class="badge bg-dark rounded-pill px-3 py-1.5 fw-bold">{{ $submissions->count() }} Total</span>
    </div>
    <div class="card-body p-4 bg-light">
        @if($submissions->count() > 0)
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
                                    <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" title="Click to view full photo">
                                        <img src="{{ asset($sub->enhanced_image_path) }}" class="img-fluid rounded" style="max-height: 165px; object-fit: contain;" alt="Photo">
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

                            {{-- Admin Feedback / Note --}}
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
                <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                <p class="mb-0">No photos found.</p>
            </div>
        @endif
    </div>
</div>

<script>
    const hFileInput = document.getElementById('historyFileInput');
    const hPreviewArea = document.getElementById('historyPreviewArea');
    const hPreviewGrid = document.getElementById('historyPreviewGrid');
    const hSelectedCount = document.getElementById('historySelectedCount');
    const hSubmitBtn = document.getElementById('historySubmitBtn');
    const hDropZone = document.getElementById('historyDropZone');

    let hDt = new DataTransfer();

    hFileInput.addEventListener('change', function() {
        for (let i = 0; i < this.files.length; i++) {
            hDt.items.add(this.files[i]);
        }
        hFileInput.files = hDt.files;
        renderHistoryPreviews();
    });

    hDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        hDropZone.style.backgroundColor = '#e8f0fe';
    });
    hDropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        hDropZone.style.backgroundColor = '#f8faff';
    });
    hDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        hDropZone.style.backgroundColor = '#f8faff';
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            for (let i = 0; i < e.dataTransfer.files.length; i++) {
                if (e.dataTransfer.files[i].type.startsWith('image/')) {
                    hDt.items.add(e.dataTransfer.files[i]);
                }
            }
            hFileInput.files = hDt.files;
            renderHistoryPreviews();
        }
    });

    function renderHistoryPreviews() {
        hPreviewGrid.innerHTML = '';
        const files = hDt.files;

        if (files.length === 0) {
            hPreviewArea.classList.add('d-none');
            hSubmitBtn.disabled = true;
            return;
        }

        hPreviewArea.classList.remove('d-none');
        hSubmitBtn.disabled = false;
        hSelectedCount.innerText = `${files.length} photo(s) selected to send`;

        Array.from(files).forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3 col-lg-2';

            const sizeMb = (file.size / (1024 * 1024)).toFixed(1);

            const card = document.createElement('div');
            card.className = 'card p-2 border rounded-3 position-relative bg-white text-center shadow-xs';
            card.innerHTML = `
                <div style="height: 80px;" class="d-flex align-items-center justify-content-center overflow-hidden mb-1">
                    <img id="h-prev-img-${index}" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                </div>
                <div class="text-truncate small fw-bold text-dark" title="${file.name}" style="font-size:0.75rem;">${file.name}</div>
                <div class="text-muted" style="font-size:0.68rem;">${sizeMb} MB</div>
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;" onclick="removeHistoryFile(${index})" title="Remove">
                    &times;
                </button>
            `;
            col.appendChild(card);
            hPreviewGrid.appendChild(col);

            const reader = new FileReader();
            reader.onload = (e) => {
                const imgElem = document.getElementById(`h-prev-img-${index}`);
                if (imgElem) imgElem.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeHistoryFile = function(index) {
        const newDt = new DataTransfer();
        const files = hDt.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                newDt.items.add(files[i]);
            }
        }
        hDt = newDt;
        hFileInput.files = hDt.files;
        renderHistoryPreviews();
    };

    window.clearHistoryFiles = function() {
        hDt = new DataTransfer();
        hFileInput.files = hDt.files;
        renderHistoryPreviews();
    };

    document.getElementById('historyUploadForm').addEventListener('submit', function() {
        hSubmitBtn.disabled = true;
        hSubmitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending photos... please wait`;
    });
</script>

@endsection
