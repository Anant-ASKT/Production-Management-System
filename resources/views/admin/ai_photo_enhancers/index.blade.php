@extends('layouts.app')

@section('title', 'AI Photo Enhancers')

@section('page-title', 'AI Photo Enhancers')

@section('content')

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">AI Photo Enhancers</h4>
        <div>
            <a href="{{ route('admin.ai-photo-enhancers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add New
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enhancers as $enhancer)
                            <tr>
                                <td>{{ $enhancer->first_name }}</td>
                                <td>{{ $enhancer->last_name }}</td>
                                <td>{{ $enhancer->email }}</td>
                                <td>{{ $enhancer->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($enhancer->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.ai-photo-enhancers.edit', $enhancer->sno) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No AI Photo Enhancers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $enhancers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
