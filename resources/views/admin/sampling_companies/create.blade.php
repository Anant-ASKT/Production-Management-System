@extends('layouts.app')

@section('title', 'Add Sampling Company')
@section('page-title', 'Add Sampling Company')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0 fw-bold text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Sampling Company</h4>
            <p class="text-muted small mb-0">Register a new sampling company studio and its initial primary user</p>
        </div>
        <div>
            <a href="{{ route('admin.sampling-companies.index') }}" class="btn btn-light border px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Listing
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.sampling-companies.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            {{-- Company Information --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title m-0 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i>Company / Studio Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Company / Studio Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Artisans Sampling Studio Pvt Ltd" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Company Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. SMP-001" value="{{ old('code') }}" required>
                                <small class="text-muted">Unique identifier</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="e.g. Rajesh Sharma" value="{{ old('contact_person') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Company Email</label>
                                <input type="email" name="email" class="form-control" placeholder="company@sampling.com" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Studio / Workshop Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Plot / Studio address details...">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary User Login Account (Company Admin) --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title m-0 fw-bold text-dark"><i class="bi bi-shield-lock me-2 text-primary"></i>Company Admin User (All Permissions)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i> This user will be the <strong>Company Admin</strong> with all permissions. Once logged in at <strong>/sampling/login</strong>, they can manage projects, samples, and add more users/artisans for their company.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Admin Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="user_name" class="form-control" placeholder="e.g. Ramesh Kumar" value="{{ old('user_name') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Admin Login Email <span class="text-danger">*</span></label>
                                <input type="email" name="user_email" class="form-control" placeholder="admin@samplingcompany.com" value="{{ old('user_email') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Role Permissions</label>
                                <input type="text" class="form-control bg-light text-primary fw-semibold" value="Company Admin (Full Permissions)" readonly>
                                <input type="hidden" name="role" value="company_admin">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.sampling-companies.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">Create Sampling Company</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
