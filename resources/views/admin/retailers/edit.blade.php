@extends('layouts.app')

@section('title', 'Edit Retailer')
@section('page-title', 'Edit Retailer')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0"><i class="bi bi-shop me-2 text-primary"></i>Edit Retailer - {{ $retailer->name }}</h4>
            <small class="text-muted">Update retailer details and WooCommerce API credentials</small>
        </div>
        <div class="d-flex gap-2">
            @if(!empty($retailer->store_url))
                <a href="{{ route('admin.suppliers.website.index', $retailer->sno) }}" class="btn btn-outline-success" title="Manage WordPress Store">
                    <i class="bi bi-shop me-1"></i> Store Manager
                </a>
            @endif
            <a href="{{ route('admin.retailers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Retailers
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.retailers.update', $retailer->sno) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Retailer Basic Details -->
                <h5 class="text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-person-badge me-1"></i> Retailer Details
                </h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $retailer->name) }}" placeholder="e.g. Agra Shop" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $retailer->email) }}" placeholder="e.g. info@company.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Mobile / Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $retailer->phone) }}" placeholder="e.g. +91 9876543210">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Address <span class="text-muted fw-normal">(Optional)</span></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Full address (optional)">{{ old('address', $retailer->address) }}</textarea>
                    </div>
                </div>

                <!-- API Credentials -->
                <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">
                    <i class="bi bi-key-fill me-1"></i> WooCommerce API Credentials <span class="text-danger">*</span>
                </h5>
                <p class="text-muted small">Required to connect and manage live WooCommerce store products and categories.</p>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Store URL *</label>
                        <input type="url" name="store_url" class="form-control @error('store_url') is-invalid @enderror" value="{{ old('store_url', $retailer->store_url) }}" placeholder="https://example.com" required>
                        @error('store_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Consumer Key *</label>
                        <input type="text" name="consumer_key" class="form-control @error('consumer_key') is-invalid @enderror" value="{{ old('consumer_key', $retailer->consumer_key) }}" placeholder="ck_..." required>
                        @error('consumer_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Consumer Secret *</label>
                        <input type="text" name="consumer_secret" class="form-control @error('consumer_secret') is-invalid @enderror" value="{{ old('consumer_secret', $retailer->consumer_secret) }}" placeholder="cs_..." required>
                        @error('consumer_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Update Retailer
                    </button>
                    <a href="{{ route('admin.retailers.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
