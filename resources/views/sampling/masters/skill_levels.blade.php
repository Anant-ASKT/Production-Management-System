@extends('layouts.sampling')

@section('title', 'Skill Levels Master')
@section('page-title', 'Skill Levels Master')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-award text-warning me-2"></i>Skill Levels Master
            </h4>
            <p class="text-muted small mb-0">Define artisan craft competencies and operator skill levels with standard hourly labour rates</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSkillLevelModal">
            <i class="bi bi-plus-lg me-1"></i> Add Skill Level
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Please review form errors:
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Skill Level Name</th>
                            <th>Code</th>
                            <th>Default Hourly Rate</th>
                            <th>Description / Experience Criteria</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skillLevels as $index => $sk)
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">
                                    <span class="badge bg-warning-subtle text-dark border me-1"><i class="bi bi-award me-1 text-warning"></i>{{ $sk->name }}</span>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $sk->code ?: '—' }}</span></td>
                                <td class="fw-semibold text-dark">₹{{ number_format($sk->default_rate_per_hour, 2) }}/hr</td>
                                <td class="text-muted small">{{ $sk->description ?: '—' }}</td>
                                <td>
                                    @if($sk->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editSkillLevelModal{{ $sk->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('sampling.masters.skill-levels.destroy', $sk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove skill level \'{{ $sk->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editSkillLevelModal{{ $sk->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold">Edit Skill Level</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sampling.masters.skill-levels.update', $sk->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Skill Level Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ $sk->name }}" required>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Code</label>
                                                        <input type="text" name="code" class="form-control font-monospace" value="{{ $sk->code }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                                                        <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" value="{{ $sk->default_rate_per_hour }}">
                                                    </div>
                                                </div>
                                                <div class="mb-3 mt-3">
                                                    <label class="form-label fw-semibold">Description / Criteria</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $sk->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $sk->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $sk->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No skill levels configured. Click "Add Skill Level" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addSkillLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-warning me-2"></i>Add Skill Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.skill-levels') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Skill Level Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Master Artisan, Senior Artisan, Skilled" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. MST, SKL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                            <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" placeholder="e.g. 150">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Description / Criteria</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Experience level, craft expertise required..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Skill Level</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
