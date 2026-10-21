@extends('layouts.app')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gray-800">Add New Category</h4>
        </div>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Categories
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2 mb-3">Category Details</h5>
                    </div>

                    {{-- Category Name --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter category name" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Retailer --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Retailer <span class="text-danger">*</span></label>
                        <select name="retailer_id" id="retailer_id" class="form-select @error('retailer_id') is-invalid @enderror @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Select Retailer --</option>
                            @foreach($retailers as $retailer)
                                <option value="{{ $retailer->sno }}" {{ old('retailer_id', old('supplier_id')) == $retailer->sno ? 'selected' : '' }}>
                                    {{ $retailer->name }} ({{ $retailer->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('retailer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Parent Category --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">-- None (This is a Main / Parent Category) --</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->sno }}" {{ old('parent_id') == $parent->sno ? 'selected' : '' }} data-retailer="{{ $parent->supplier_id }}">
                                    {{ $parent->name }} @if($parent->supplier) (Retailer: {{ $parent->supplier->name }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>Leave as "None" for a top-level category, or select a parent category if this is a subcategory.
                        </div>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Save Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const retailerSelect = document.getElementById('retailer_id');
    const parentSelect = document.getElementById('parent_id');
    if (!retailerSelect || !parentSelect) return;

    const allParentOptions = Array.from(parentSelect.querySelectorAll('option'));

    function filterParentCategories() {
        const selectedRetailer = retailerSelect.value;
        const currentParentVal = parentSelect.value;
        let isCurrentValVisible = false;

        allParentOptions.forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            const optRetailer = option.getAttribute('data-retailer');
            if (!selectedRetailer || !optRetailer || optRetailer === selectedRetailer) {
                option.hidden = false;
                if (option.value === currentParentVal) {
                    isCurrentValVisible = true;
                }
            } else {
                option.hidden = true;
            }
        });

        if (currentParentVal && !isCurrentValVisible) {
            parentSelect.value = '';
        }
    }

    retailerSelect.addEventListener('change', filterParentCategories);
    filterParentCategories();
});
</script>
@endsection
