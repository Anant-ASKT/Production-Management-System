@extends('layouts.app')

@section('title', 'Create Supplier')
@section('page-title', 'Create Supplier')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0">New Supplier</h4>
            <small class="text-muted">Register a supplier company and its primary login user account</small>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
        </a>
    </div>

    @if($errors->any())
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
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                
                <!-- Supplier Organization -->
                <h5 class="text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-building me-1"></i> Supplier Company Information
                </h5>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Supplier / Company Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. ABC Textiles Ltd" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Nick Name (3 Letters) *</label>
                        <input type="text" name="nickname" class="form-control text-uppercase" maxlength="3" minlength="3" pattern="[A-Za-z]{3}" value="{{ old('nickname') }}" placeholder="e.g. ABC" title="Exactly 3 letters" style="text-transform:uppercase;" required>
                        <small class="text-muted">Unique 3-letter code (e.g. ABC)</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Company Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. +91 9876543210">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Full company address">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Primary User Login Details -->
                <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">
                    <i class="bi bi-person-fill-lock me-1"></i> Primary User Login Account
                </h5>
                <p class="text-muted small">This will create the initial login account for this supplier. Additional users can be added later in the edit page.</p>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">User Full Name</label>
                        <input type="text" name="user_name" class="form-control" value="{{ old('user_name') }}" placeholder="Leave blank to use company name">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Login Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="user@company.com" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Login Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="Owner" {{ old('role', 'Owner') === 'Owner' ? 'selected' : '' }}>Owner</option>
                            <option value="Employee" {{ old('role') === 'Employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>
                </div>

                <!-- API Credentials -->
                <h5 class="text-primary border-bottom pb-2 mt-4 mb-3">
                    <i class="bi bi-code-slash me-1"></i> API Credentials (Optional)
                </h5>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Store URL</label>
                        <input type="url" name="store_url" class="form-control" value="{{ old('store_url') }}" placeholder="https://example.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Key</label>
                        <input type="text" name="consumer_key" class="form-control" value="{{ old('consumer_key') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Secret</label>
                        <input type="text" name="consumer_secret" class="form-control" value="{{ old('consumer_secret') }}">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Create Supplier & Account
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
