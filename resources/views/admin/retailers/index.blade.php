@extends('layouts.app')

@section('title', 'Retailers')

@section('page-title', 'Retailers')

@section('content')

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0"><i class="bi bi-shop me-2 text-primary"></i>Retailer Management</h4>
            <small class="text-muted">Manage retailer stores and their WooCommerce API connections</small>
        </div>
        <div>
            <a href="{{ route('admin.retailers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Retailer
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>By Default Owner</th>
                            <th>Store URL</th>
                            <th>Consumer Key</th>
                            <th>Consumer Secret</th>
                            <th style="width: 180px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($retailers as $retailer)
                            @php
                                $owner = $retailer->defaultOwner ?? $retailer->users->firstWhere('role', 'Owner') ?? $retailer->users->first();
                            @endphp
                            <tr>
                                <td class="text-muted fw-semibold">{{ $loop->iteration + ($retailers->currentPage() - 1) * $retailers->perPage() }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $retailer->name }}</div>
                                    @if(!empty($retailer->nickname))
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0" style="font-size: 0.75rem;">
                                            {{ $retailer->nickname }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($retailer->email))
                                        <a href="mailto:{{ $retailer->email }}" class="text-decoration-none text-dark">
                                            <i class="bi bi-envelope me-1 text-muted"></i>{{ $retailer->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($owner)
                                        <div class="fw-semibold text-dark">
                                            <i class="bi bi-person-fill text-primary me-1"></i>{{ $owner->name }}
                                        </div>
                                        <small class="text-muted">{{ $owner->email }}</small>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            No Owner
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($retailer->store_url))
                                        <a href="{{ $retailer->store_url }}" target="_blank" class="text-decoration-none fw-medium text-primary text-break" title="Open Store URL">
                                            <i class="bi bi-box-arrow-up-right me-1 small"></i>{{ Str::limit($retailer->store_url, 35) }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($retailer->consumer_key))
                                        <div class="d-flex align-items-center gap-1">
                                            <code class="text-dark bg-light px-2 py-1 rounded small border text-break font-monospace" style="font-size: 0.8rem;">
                                                {{ Str::limit($retailer->consumer_key, 18) }}
                                            </code>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-secondary copy-btn" data-clipboard-text="{{ $retailer->consumer_key }}" title="Copy Consumer Key">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($retailer->consumer_secret))
                                        <div class="d-flex align-items-center gap-1">
                                            <code class="text-dark bg-light px-2 py-1 rounded small border text-break font-monospace" style="font-size: 0.8rem;">
                                                {{ Str::mask($retailer->consumer_secret, '*', 6, -4) }}
                                            </code>
                                            <button type="button" class="btn btn-sm btn-link p-0 text-secondary copy-btn" data-clipboard-text="{{ $retailer->consumer_secret }}" title="Copy Consumer Secret">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Not set</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <a href="{{ route('admin.retailers.edit', $retailer->sno) }}" class="btn btn-sm btn-outline-primary" title="Edit Retailer & Users">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </a>
                                        @if(!empty($retailer->store_url))
                                            <a href="{{ route('admin.suppliers.website.index', $retailer->sno) }}" class="btn btn-sm btn-outline-success" title="Manage WordPress Website Products & Categories">
                                                <i class="bi bi-shop me-1"></i> Store Manager
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-shop fs-1 d-block mb-2 text-secondary"></i>
                                    <h5>No Retailers Found</h5>
                                    <p class="text-muted small mb-3">Add your first retailer to start managing stores and WooCommerce integration.</p>
                                    <a href="{{ route('admin.retailers.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-lg me-1"></i> Add Retailer
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $retailers->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.getAttribute('data-clipboard-text');
            if (navigator.clipboard && text) {
                navigator.clipboard.writeText(text).then(() => {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = 'bi bi-check text-success';
                        setTimeout(() => {
                            icon.className = 'bi bi-clipboard';
                        }, 2000);
                    }
                });
            }
        });
    });
});
</script>

@endsection
