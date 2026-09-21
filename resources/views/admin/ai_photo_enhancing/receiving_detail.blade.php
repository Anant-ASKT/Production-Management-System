@extends('layouts.app')

@section('title', 'Review Photos - ' . ($spec->sku ?: 'Product #' . $spec->sno))

@section('content')

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

    $mainImages = parseImages($spec->img_path);
    $subImages = [];
    if (!empty($spec->resolved_sub_images)) {
        foreach ($spec->resolved_sub_images as $s) {
            $subImages[] = resolveImageUrl($s);
        }
    }
    $allOriginals = array_values(array_unique(array_merge($mainImages, $subImages)));
@endphp

<style>
    /* Clean Photo Cards */
    .photo-card {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
        transition: box-shadow 0.15s ease;
    }
    .photo-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Original Photo Viewport */
    .orig-img-box {
        height: 150px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
    }
    .orig-img-box img {
        max-height: 138px;
        max-width: 100%;
        object-fit: contain;
    }

    /* Enhanced Photo Viewport */
    .enh-img-box {
        height: 280px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        position: relative;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
    }
    .enh-img-box img {
        max-height: 260px;
        max-width: 100%;
        object-fit: contain;
    }


    /* 3 Simple Decision Buttons */
    .decision-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 6px;
    }
    .btn-decision {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px 2px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.82rem;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        margin: 0;
        text-align: center;
    }
    .btn-decision:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .radio-status:checked + .btn-decision-approved {
        background: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
    }
    .radio-status:checked + .btn-decision-need {
        background: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #ffffff !important;
    }
    .radio-status:checked + .btn-decision-reject {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    /* Role Pills */
    .role-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 4px;
    }
    .btn-role {
        display: block;
        text-align: center;
        padding: 6px 2px;
        font-size: 0.78rem;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        user-select: none;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        transition: all 0.15s ease;
        margin: 0;
    }
    .btn-role:hover {
        background: #e2e8f0;
    }
    .radio-role:checked + .btn-role-main {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
    }
    .radio-role:checked + .btn-role-sub {
        background: #198754 !important;
        border-color: #198754 !important;
        color: #ffffff !important;
    }
    .radio-role:checked + .btn-role-extra {
        background: #475569 !important;
        border-color: #475569 !important;
        color: #ffffff !important;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3">

    {{-- ===================================================
         NORMAL PAGE HEADER (SCROLLS NATURALLY)
    ==================================================== --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.ai-photo-enhancing.receiving') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">{{ $spec->sku ?: ('Product #' . $spec->sno) }}</h4>
                <div class="text-muted small mt-1">
                    @if($spec->barcode) <span class="me-3"><strong>Barcode:</strong> {{ $spec->barcode }}</span> @endif
                    @if($spec->color) <span class="me-3"><strong>Color:</strong> {{ $spec->color }}</span> @endif
                    @if($spec->supplier_name) <span><strong>Supplier:</strong> {{ $spec->supplier_name }}</span> @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 text-success me-2"></i>
                <div>
                    <strong>Saved Successfully!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-4 p-3" role="alert">
            <strong class="d-block mb-1">Please check the errors below:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===================================================
         1. ALL ORIGINAL IMAGES FIRST
    ==================================================== --}}
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-camera text-primary me-1"></i> Original Images
            </h5>
            <small class="text-muted">{{ count($allOriginals) }} original photo(s)</small>
        </div>

        @if(count($allOriginals) > 0)
            <div class="row g-2 mb-3">
                @foreach($allOriginals as $idx => $imgUrl)
                    @php $isMainOriginal = in_array($imgUrl, $mainImages); @endphp
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="photo-card text-center">
                            <div class="px-2 py-1 bg-light border-bottom small fw-bold text-truncate" style="font-size: 0.72rem;">
                                {{ $isMainOriginal ? 'Main Original' : 'Original #' . ($idx + 1) }}
                            </div>
                            <div class="orig-img-box">
                                <a href="{{ $imgUrl }}" target="_blank" title="Click to view full image">
                                    <img src="{{ $imgUrl }}" alt="Original Photo">
                                </a>
                            </div>
                            <div class="p-1 bg-white border-top">
                                <a href="{{ $imgUrl }}" target="_blank" class="btn btn-xs btn-outline-primary w-100 py-0.5" style="font-size: 0.72rem;">
                                    <i class="bi bi-zoom-in"></i> View Full
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border rounded-3 p-3 text-muted">
                No original photos registered for this product.
            </div>
        @endif
    </div>

    {{-- ===================================================
         2. ENHANCED IMAGES FOR MANAGER REVIEW
    ==================================================== --}}
    <form action="{{ route('admin.ai-photo-enhancing.receiving.review-batch', $spec->sno) }}" method="POST" id="batchReviewForm">
        @csrf

        <div class="d-flex align-items-center justify-content-between mb-3 pt-2 border-top">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-magic text-success me-1"></i> Enhanced Images
                </h5>
                <small class="text-muted">Choose which image to approve, request more edits, or reject.</small>
            </div>
            <span class="badge bg-light text-dark border">{{ $submissions->count() }} photo(s) submitted</span>
        </div>

        @if($submissions->count() > 0)
            <div class="row g-3 mb-4">
                @foreach($submissions as $index => $sub)
                    @php
                        $st = $sub->status;
                        $role = $sub->image_type ?: 'sub';
                    @endphp

                    {{-- Exactly 2 Images Per Row --}}
                    <div class="col-12 col-md-6">
                        <div class="photo-card d-flex flex-column h-100" id="card_{{ $sub->sno }}">

                            {{-- Card Header --}}
                            <div class="px-2.5 py-1.5 bg-light d-flex align-items-center justify-content-between border-bottom">
                                <strong class="small text-dark">Photo #{{ $index + 1 }}</strong>
                                <small class="text-muted">{{ $sub->enhancer_first_name }}</small>
                            </div>

                            {{-- Simple Status Label --}}
                            <div class="px-2 py-1 bg-white border-bottom text-center small fw-bold" id="status_label_{{ $sub->sno }}" style="font-size: 0.76rem;">
                                @if($st == 'approved')
                                    @if($role == 'main')
                                        <span class="text-primary"><i class="bi bi-star-fill"></i> Approved (Main Photo)</span>
                                    @elseif($role == 'extra')
                                        <span class="text-secondary"><i class="bi bi-folder-fill"></i> Approved (Extra Photo)</span>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Approved (Sub Photo)</span>
                                    @endif
                                @elseif($st == 'approved_need_version')
                                    <span class="text-warning"><i class="bi bi-arrow-repeat"></i> Need More Edits</span>
                                @elseif($st == 'rejected')
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                                @else
                                    <span class="text-muted"><i class="bi bi-clock"></i> Not Decided</span>
                                @endif
                            </div>

                            {{-- Clean Photo Preview --}}
                            <div class="enh-img-box">
                                <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" title="Click to view full photo">
                                    <img src="{{ asset($sub->enhanced_image_path) }}" alt="Enhanced Photo">
                                </a>
                                <a href="{{ asset($sub->enhanced_image_path) }}" target="_blank" class="btn btn-sm btn-light border position-absolute top-0 end-0 m-2 p-1" title="View Full Photo" style="line-height: 1; border-radius: 6px;">
                                    <i class="bi bi-arrows-fullscreen small"></i>
                                </a>
                            </div>

                            {{-- Card Controls --}}
                            <div class="p-2.5 bg-white flex-grow-1 d-flex flex-column justify-content-between">
                                <div>
                                    {{-- 3 Decision Buttons --}}
                                    <div class="decision-row mb-2">
                                        {{-- Approved --}}
                                        <div>
                                            <input type="radio" class="d-none radio-status" name="reviews[{{ $sub->sno }}][status]" id="status_{{ $sub->sno }}_approved" value="approved" {{ $st == 'approved' ? 'checked' : '' }} onchange="setDecision({{ $sub->sno }}, 'approved')">
                                            <label for="status_{{ $sub->sno }}_approved" class="btn-decision btn-decision-approved">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </label>
                                        </div>

                                        {{-- Need More --}}
                                        <div>
                                            <input type="radio" class="d-none radio-status" name="reviews[{{ $sub->sno }}][status]" id="status_{{ $sub->sno }}_need" value="approved_need_version" {{ $st == 'approved_need_version' ? 'checked' : '' }} onchange="setDecision({{ $sub->sno }}, 'approved_need_version')">
                                            <label for="status_{{ $sub->sno }}_need" class="btn-decision btn-decision-need">
                                                <i class="bi bi-arrow-repeat"></i> Need More
                                            </label>
                                        </div>

                                        {{-- Reject --}}
                                        <div>
                                            <input type="radio" class="d-none radio-status" name="reviews[{{ $sub->sno }}][status]" id="status_{{ $sub->sno }}_rejected" value="rejected" {{ $st == 'rejected' ? 'checked' : '' }} onchange="setDecision({{ $sub->sno }}, 'rejected')">
                                            <label for="status_{{ $sub->sno }}_rejected" class="btn-decision btn-decision-reject">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Role Options (Shown ONLY when Approved) --}}
                                    <div class="p-2 bg-light rounded-2 border mb-2 {{ $st == 'approved' ? '' : 'd-none' }}" id="role_box_{{ $sub->sno }}">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="small fw-bold text-dark" style="font-size: 0.74rem;">Select Role:</span>
                                            <small class="text-muted" style="font-size: 0.68rem;">1 Main Photo allowed</small>
                                        </div>
                                        <div class="role-row">
                                            {{-- Main --}}
                                            <div>
                                                <input type="radio" class="d-none radio-role radio-role-main" name="reviews[{{ $sub->sno }}][image_type]" id="role_{{ $sub->sno }}_main" value="main" {{ $role == 'main' ? 'checked' : '' }} onchange="setRole({{ $sub->sno }}, 'main')">
                                                <label for="role_{{ $sub->sno }}_main" class="btn-role btn-role-main">
                                                    ⭐ Main
                                                </label>
                                            </div>

                                            {{-- Sub --}}
                                            <div>
                                                <input type="radio" class="d-none radio-role" name="reviews[{{ $sub->sno }}][image_type]" id="role_{{ $sub->sno }}_sub" value="sub" {{ ($role != 'main' && $role != 'extra') ? 'checked' : '' }} onchange="setRole({{ $sub->sno }}, 'sub')">
                                                <label for="role_{{ $sub->sno }}_sub" class="btn-role btn-role-sub">
                                                    🖼️ Sub
                                                </label>
                                            </div>

                                            {{-- Extra --}}
                                            <div>
                                                <input type="radio" class="d-none radio-role" name="reviews[{{ $sub->sno }}][image_type]" id="role_{{ $sub->sno }}_extra" value="extra" {{ $role == 'extra' ? 'checked' : '' }} onchange="setRole({{ $sub->sno }}, 'extra')">
                                                <label for="role_{{ $sub->sno }}_extra" class="btn-role btn-role-extra">
                                                    📁 Extra
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Note Field (Shown when Need More or Reject) --}}
                                    <div class="mb-2 {{ in_array($st, ['rejected', 'approved_need_version']) ? '' : 'd-none' }}" id="note_box_{{ $sub->sno }}">
                                        <input type="text" name="reviews[{{ $sub->sno }}][feedback]" class="form-control form-control-sm" placeholder="Reason for editor (optional)..." value="{{ $sub->admin_feedback }}" style="font-size: 0.78rem;">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===================================================
                 SIMPLE SAVE BUTTON AT THE END
            ==================================================== --}}
            <div class="text-center py-4 my-3 border-top">
                <button type="submit" class="btn btn-primary btn-lg px-5 py-2.5 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-save2-fill me-1"></i> Save Decisions
                </button>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                <h5 class="fw-bold">No enhanced photos submitted yet</h5>
                <p class="small text-muted mb-0">Photos sent by AI editors will appear here.</p>
            </div>
        @endif

    </form>

</div>

<script>
    // Handle decision selection
    function setDecision(id, decision) {
        const roleBox = document.getElementById(`role_box_${id}`);
        const noteBox = document.getElementById(`note_box_${id}`);
        const label = document.getElementById(`status_label_${id}`);

        if (decision === 'approved') {
            roleBox.classList.remove('d-none');
            noteBox.classList.add('d-none');

            // If no Main Image is selected anywhere, make this one Main
            const anyMain = document.querySelector('.radio-role-main:checked');
            if (!anyMain) {
                const myMain = document.getElementById(`role_${id}_main`);
                if (myMain) myMain.checked = true;
            }

            const currentRole = document.querySelector(`input[name="reviews[${id}][image_type]"]:checked`)?.value || 'sub';
            updateLabel(id, 'approved', currentRole);

        } else if (decision === 'approved_need_version') {
            roleBox.classList.add('d-none');
            noteBox.classList.remove('d-none');
            label.innerHTML = `<span class="text-warning"><i class="bi bi-arrow-repeat"></i> Need More Edits</span>`;

        } else if (decision === 'rejected') {
            roleBox.classList.add('d-none');
            noteBox.classList.remove('d-none');
            label.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Rejected</span>`;
        }
    }

    // Role selection change (Main / Sub / Extra)
    function setRole(selectedSubId, newRole) {
        if (newRole === 'main') {
            // Uncheck Main on all other cards and switch them to Sub!
            document.querySelectorAll('.radio-role-main').forEach(radio => {
                const subId = radio.id.split('_')[1];
                if (subId != selectedSubId && radio.checked) {
                    radio.checked = false;
                    const subRadio = document.getElementById(`role_${subId}_sub`);
                    if (subRadio) subRadio.checked = true;
                    updateLabel(subId, 'approved', 'sub');
                }
            });
        }
        updateLabel(selectedSubId, 'approved', newRole);
    }

    function updateLabel(id, status, role) {
        const label = document.getElementById(`status_label_${id}`);
        if (!label) return;

        if (status === 'approved') {
            if (role === 'main') {
                label.innerHTML = `<span class="text-primary"><i class="bi bi-star-fill"></i> Approved (Main Photo)</span>`;
            } else if (role === 'extra') {
                label.innerHTML = `<span class="text-secondary"><i class="bi bi-folder-fill"></i> Approved (Extra Photo)</span>`;
            } else {
                label.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Approved (Sub Photo)</span>`;
            }
        }
    }
</script>
@endsection
