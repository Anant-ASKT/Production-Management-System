@extends('layouts.app')

@section('title', 'Edit Supplier & Users')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="m-0">{{ $supplier->name }}</h4>
            <small class="text-muted">Manage supplier organization details and login user accounts</small>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Suppliers
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <div class="row">
        <!-- Supplier Company Profile -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title m-0 text-primary">
                        <i class="bi bi-building me-1"></i> Supplier Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.suppliers.update', $supplier->sno) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Supplier / Company Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nick Name (3 Letters) *</label>
                            <input type="text" name="nickname" class="form-control text-uppercase" maxlength="3" minlength="3" pattern="[A-Za-z]{3}" value="{{ old('nickname', $supplier->nickname) }}" placeholder="e.g. ABC" title="Exactly 3 letters" style="text-transform:uppercase;" required>
                            <small class="text-muted">Unique 3-letter code (e.g. ABC)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Company Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <h6 class="text-secondary fw-bold mb-3">API Credentials (Optional)</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Store URL</label>
                                <input type="url" name="store_url" class="form-control" value="{{ old('store_url', $supplier->store_url) }}" placeholder="https://example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Consumer Key</label>
                                <input type="text" name="consumer_key" class="form-control" value="{{ old('consumer_key', $supplier->consumer_key) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Consumer Secret</label>
                                <input type="text" name="consumer_secret" class="form-control" value="{{ old('consumer_secret', $supplier->consumer_secret) }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-lg me-1"></i> Update Supplier Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Supplier Users Section -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title m-0 text-primary">
                            <i class="bi bi-people me-1"></i> Supplier Users ({{ $supplier->users->count() }})
                        </h5>
                        <small class="text-muted">Accounts that can log in and manage products for this supplier</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add User
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email (Login ID)</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->users as $user)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $user->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $user->email }}</span>
                                        </td>
                                        <td>{{ $user->phone ?: '-' }}</td>
                                        <td>
                                            @if($user->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal_{{ $user->sno }}"
                                                    title="Edit User">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                @if($supplier->users->count() > 1)
                                                    <form action="{{ route('admin.suppliers.users.destroy', [$supplier->sno, $user->sno]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit User Modal -->
                                    <div class="modal fade" id="editUserModal_{{ $user->sno }}" tabindex="-1" aria-labelledby="editUserModalLabel_{{ $user->sno }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('admin.suppliers.users.update', [$supplier->sno, $user->sno]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editUserModalLabel_{{ $user->sno }}">
                                                            <i class="bi bi-pencil-square me-1"></i> Edit User: {{ $user->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Full Name *</label>
                                                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Email (Login ID) *</label>
                                                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Phone</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">New Password</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Min. 6 characters">
                                                            <small class="text-muted">Leave blank if you don't want to change the password.</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No users found for this supplier. Click "Add User" to create one.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.suppliers.users.store', $supplier->sno) }}" method="POST">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">
                        <i class="bi bi-person-plus-fill me-1"></i> Add New User to {{ $supplier->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email (Login ID) *</label>
                        <input type="email" name="email" class="form-control" placeholder="user@company.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password *</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Optional contact number">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
