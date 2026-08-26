@extends('layouts.supplier')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0">Edit Product</h4>
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
        <form action="{{ route('supplier.products.update', $product->sno) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Media section -->
                <div class="col-md-12 mb-3">
                    <h5 class="text-primary border-bottom pb-2">Media</h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Main Image</label>
                    <input type="file" name="main_image" class="form-control mb-2" accept="image/*" id="mainImageInput">
                    <div id="mainImagePreviewContainer" class="{{ $product->main_image ? '' : 'd-none' }} position-relative d-inline-block">
                        <img src="{{ $product->main_image ? asset($product->main_image) : '' }}" id="mainImagePreview" class="rounded" style="height: 150px; object-fit: cover;">
                        @if($product->main_image)
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 delete-image-btn" style="width: 24px; height: 24px; line-height: 1;" data-type="main" data-path="{{ $product->main_image }}">&times;</button>
                        @endif
                    </div>
                    <small class="text-muted">Leave empty to keep existing image.</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Additional Images</label>
                    <input type="file" name="sub_images[]" class="form-control mb-2" accept="image/*" multiple id="subImagesInput">
                    <div id="subImagesPreviewContainer" class="d-flex gap-2 flex-wrap {{ $product->sub_images ? '' : 'd-none' }}">
                        @if($product->sub_images)
                            @php
                                $subImages = json_decode($product->sub_images, true) ?? [];
                            @endphp
                            @foreach($subImages as $img)
                                <div class="position-relative d-inline-block existing-sub-img-container">
                                    <img src="{{ asset($img) }}" alt="Sub Image" class="rounded existing-sub-img" style="height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 delete-image-btn" style="width: 20px; height: 20px; line-height: 1;" data-type="sub" data-path="{{ $img }}">&times;</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <small class="text-muted">New images will be added to existing ones.</small>
                </div>

                <!-- Basic Details section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">Basic Details</h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Product Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Stock</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Attributes section -->
                <div class="col-md-12 mb-3 mt-3">
                    <h5 class="text-primary border-bottom pb-2">Attributes (Simple Text)</h5>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Colour</label>
                    <input type="text" name="colour" class="form-control" value="{{ old('colour', $product->colour) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Size</label>
                    <input type="text" name="size" class="form-control" value="{{ old('size', $product->size) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Item Type</label>
                    <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $product->item_type) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Gender</label>
                    <input type="text" name="gender" class="form-control" value="{{ old('gender', $product->gender) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Composition</label>
                    <input type="text" name="composition" class="form-control" value="{{ old('composition', $product->composition) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Compositions (Misc)</label>
                    <input type="text" name="compositions" class="form-control" value="{{ old('compositions', $product->compositions) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Designer</label>
                    <input type="text" name="designer" class="form-control" value="{{ old('designer', $product->designer) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Designers (Misc)</label>
                    <input type="text" name="designers" class="form-control" value="{{ old('designers', $product->designers) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Design Names</label>
                    <input type="text" name="design_names" class="form-control" value="{{ old('design_names', $product->design_names) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Collection</label>
                    <input type="text" name="collection" class="form-control" value="{{ old('collection', $product->collection) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Embellishment</label>
                    <input type="text" name="embellishment" class="form-control" value="{{ old('embellishment', $product->embellishment) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Manufacture</label>
                    <input type="text" name="manufacture" class="form-control" value="{{ old('manufacture', $product->manufacture) }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Manufacturing Process</label>
                    <input type="text" name="manufacturing_process" class="form-control" value="{{ old('manufacturing_process', $product->manufacturing_process) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Mfg Processes (Misc)</label>
                    <input type="text" name="mfg_processes" class="form-control" value="{{ old('mfg_processes', $product->mfg_processes) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Craftsman</label>
                    <input type="text" name="craftsman" class="form-control" value="{{ old('craftsman', $product->craftsman) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Craftsmen (Misc)</label>
                    <input type="text" name="craftsmen" class="form-control" value="{{ old('craftsmen', $product->craftsmen) }}">
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">Update Product</button>
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
                @if(!$product->main_image)
                mainPreviewContainer.classList.add('d-none');
                @else
                mainPreview.src = "{{ asset($product->main_image) }}";
                @endif
            }
        });

        const subInput = document.getElementById('subImagesInput');
        const subPreviewContainer = document.getElementById('subImagesPreviewContainer');

        subInput.addEventListener('change', function(e) {
            // Remove previously added previews (keep existing ones from server)
            const newPreviews = subPreviewContainer.querySelectorAll('.new-sub-img');
            newPreviews.forEach(img => img.remove());
            
            if (e.target.files.length > 0) {
                subPreviewContainer.classList.remove('d-none');
                
                Array.from(e.target.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'rounded new-sub-img';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        subPreviewContainer.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                @if(!$product->sub_images)
                subPreviewContainer.classList.add('d-none');
                @endif
            }
        });

        // Handle deletion of existing images
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this image?')) return;
                
                const type = this.dataset.type;
                const path = this.dataset.path;
                const container = type === 'main' ? document.getElementById('mainImagePreviewContainer') : this.closest('.existing-sub-img-container');
                const btnElement = this;
                
                fetch("{{ route('supplier.products.delete-image', $product->sno) }}", {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: type, image_path: path })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (type === 'main') {
                            container.classList.add('d-none');
                            document.getElementById('mainImagePreview').src = '';
                            btnElement.remove();
                        } else {
                            container.remove();
                        }
                    } else {
                        alert('Failed to delete image: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the image.');
                });
            });
        });
    });
</script>
@endpush
@endsection
