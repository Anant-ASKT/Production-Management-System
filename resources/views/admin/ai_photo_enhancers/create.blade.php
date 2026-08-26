@extends('layouts.app')

@section('title', 'Create AI Photo Enhancer')
@section('page-title', 'Create AI Photo Enhancer')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ai-photo-enhancers.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>AI Photo Enhancer Details</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control">
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save AI Photo Enhancer</button>
                    <a href="{{ route('admin.ai-photo-enhancers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
