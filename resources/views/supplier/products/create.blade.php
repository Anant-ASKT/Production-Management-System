@extends('layouts.supplier')

@section('title', 'Create Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0">New Product</h4>
    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">Back to Products</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Media section -->
                <div class="col-md-12 mb-3">
                    <h5 class="text-primary border-bottom pb-2">Media</h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Main Image</label>
                    <input type="file" name="main_image" class="form-control" accept="image/*" id="mainImageInput">
                    <div class="mt-2 d-none" id="mainImagePreviewContainer">
                        <img src="" id="mainImagePreview" class="rounded" style="height: 150px; object-fit: cover;">
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Additional Images</label>
                    <input type="file" name="sub_images[]" class="form-control" accept="image/*" multiple id="subImagesInput">
                    <div class="mt-2 d-flex gap-2 flex-wrap d-none" id="subImagesPreviewContainer">
                        <!-- Preview images will be appended here -->
                    </div>
                </div>

                <!-- Basic Details section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">Basic Details</h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <!-- Attributes section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">Attributes (Simple Text)</h5>
                    <small class="text-muted mb-3 d-block">Please type simple text values for these attributes (e.g., Color: "Navy Blue", Size: "Small", Type: "Shirt").</small>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Colour</label>
                    <input type="text" name="colour" class="form-control" value="{{ old('colour') }}" placeholder="e.g. Red, Blue">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Size</label>
                    <input type="text" name="size" class="form-control" value="{{ old('size') }}" placeholder="e.g. S, M, L">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Item Type</label>
                    <input type="text" name="item_type" class="form-control" value="{{ old('item_type') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Gender</label>
                    <input type="text" name="gender" class="form-control" value="{{ old('gender') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Composition</label>
                    <input type="text" name="composition" class="form-control" value="{{ old('composition') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Compositions (Misc)</label>
                    <input type="text" name="compositions" class="form-control" value="{{ old('compositions') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Designer</label>
                    <input type="text" name="designer" class="form-control" value="{{ old('designer') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Designers (Misc)</label>
                    <input type="text" name="designers" class="form-control" value="{{ old('designers') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Design Names</label>
                    <input type="text" name="design_names" class="form-control" value="{{ old('design_names') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Collection</label>
                    <input type="text" name="collection" class="form-control" value="{{ old('collection') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Embellishment</label>
                    <input type="text" name="embellishment" class="form-control" value="{{ old('embellishment') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Manufacture</label>
                    <input type="text" name="manufacture" class="form-control" value="{{ old('manufacture') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Manufacturing Process</label>
                    <input type="text" name="manufacturing_process" class="form-control" value="{{ old('manufacturing_process') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Mfg Processes (Misc)</label>
                    <input type="text" name="mfg_processes" class="form-control" value="{{ old('mfg_processes') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Craftsman</label>
                    <input type="text" name="craftsman" class="form-control" value="{{ old('craftsman') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Craftsmen (Misc)</label>
                    <input type="text" name="craftsmen" class="form-control" value="{{ old('craftsmen') }}">
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">Save Product</button>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-light border ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainInput = document.getElementById('mainImageInput');
        const mainPreviewContainer = document.getElementById('mainImagePreviewContainer');
        const mainPreview = document.getElementById('mainImagePreview');

        mainInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainPreview.src = e.target.result;
                    mainPreviewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                mainPreviewContainer.classList.add('d-none');
            }
        });

        const subInput = document.getElementById('subImagesInput');
        const subPreviewContainer = document.getElementById('subImagesPreviewContainer');

        subInput.addEventListener('change', function(e) {
            subPreviewContainer.innerHTML = '';
            
            if (e.target.files.length > 0) {
                subPreviewContainer.classList.remove('d-none');
                
                Array.from(e.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'rounded';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        subPreviewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                subPreviewContainer.classList.add('d-none');
            }
        });
    });
</script>
@endpush
@endsection
