@extends('layouts.app')

@section('title', 'Edit AI Photo Enhancer')
@section('page-title', 'Edit AI Photo Enhancer')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ai-photo-enhancers.update', $enhancer->sno) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12 mb-3 mt-3 border-top pt-3">
                        <h5>AI Photo Enhancer Details</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $enhancer->first_name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $enhancer->last_name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Login ID) *</label>
                        <input type="email" name="email" class="form-control" value="{{ $enhancer->email }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $enhancer->phone }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Leave blank if you don't want to change the password.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ $enhancer->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $enhancer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ $enhancer->address }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update AI Photo Enhancer</button>
                    <a href="{{ route('admin.ai-photo-enhancers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
