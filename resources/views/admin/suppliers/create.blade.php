@extends('layouts.app')

@section('title', 'Create Supplier')
@section('page-title', 'Create Supplier')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>Supplier Details</h5>
                    </div>

                    <!-- Supplier Fields -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Login ID) *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>


                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>API Credentials</h5>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Store URL</label>
                        <input type="url" name="store_url" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Key</label>
                        <input type="text" name="consumer_key" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Secret</label>
                        <input type="text" name="consumer_secret" class="form-control">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
