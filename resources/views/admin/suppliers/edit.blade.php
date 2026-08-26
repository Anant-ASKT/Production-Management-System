@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.suppliers.update', $supplier->sno) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>Supplier Details</h5>
                    </div>

                    <!-- Supplier Fields -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Login ID) *</label>
                        <input type="email" name="email" class="form-control" value="{{ $supplier->email }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Leave blank if you don't want to change the password.</small>
                    </div>


                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ $supplier->address }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>API Credentials</h5>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Store URL</label>
                        <input type="url" name="store_url" class="form-control" value="{{ $supplier->store_url }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Key</label>
                        <input type="text" name="consumer_key" class="form-control" value="{{ $supplier->consumer_key }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consumer Secret</label>
                        <input type="text" name="consumer_secret" class="form-control" value="{{ $supplier->consumer_secret }}">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
