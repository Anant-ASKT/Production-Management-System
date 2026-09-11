@extends('layouts.sampling')

@section('title', 'Studio Masters')
@section('page-title', 'Studio Masters')

@section('content')
<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-sliders text-primary me-2"></i>Studio Masters
            </h4>
            <p class="text-muted small mb-0">Centralized masters hub for divisions, measurement points, operations, materials, units, and studio specifications.</p>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Please review the form errors:
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Master Hub Card with Top Tabs --}}
    <div class="card border-0 shadow-sm">
        {{-- Card Header with Nav Tabs --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 flex-nowrap overflow-auto" id="masterTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-3 fw-semibold text-nowrap" id="tab-divisions" data-bs-toggle="tab" data-bs-target="#divisions" type="button" role="tab">
                        <i class="bi bi-diagram-2 me-1 text-primary"></i> Divisions ({{ $divisions->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-measurements" data-bs-toggle="tab" data-bs-target="#measurements" type="button" role="tab">
                        <i class="bi bi-rulers me-1 text-success"></i> Measurements ({{ $measurementPoints->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-storage" data-bs-toggle="tab" data-bs-target="#storage" type="button" role="tab">
                        <i class="bi bi-archive me-1 text-info"></i> Storage ({{ $storageLocations->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-operations" data-bs-toggle="tab" data-bs-target="#operations" type="button" role="tab">
                        <i class="bi bi-gear-wide me-1 text-warning"></i> Operations ({{ $operations->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-materials" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab">
                        <i class="bi bi-box-seam me-1 text-danger"></i> Materials ({{ $materials->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-uoms" data-bs-toggle="tab" data-bs-target="#uoms" type="button" role="tab">
                        <i class="bi bi-calculator me-1 text-secondary"></i> Units (UOM) ({{ $uoms->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-designers" data-bs-toggle="tab" data-bs-target="#designers" type="button" role="tab">
                        <i class="bi bi-palette me-1 text-purple" style="color: #6f42c1;"></i> Designers ({{ $designers->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-collections" data-bs-toggle="tab" data-bs-target="#collections" type="button" role="tab">
                        <i class="bi bi-collection me-1 text-primary"></i> Collections ({{ $collections->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-semibold text-nowrap" id="tab-specifications" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                        <i class="bi bi-card-checklist me-1 text-teal" style="color: #20c997;"></i> Tech Specs ({{ $specAttributes->count() }})
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab Content Panels --}}
        <div class="tab-content p-0" id="masterTabsContent">

            {{-- ==================================================== --}}
            {{-- TAB 1: DIVISIONS / DEPARTMENTS                       --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade show active" id="divisions" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-2 text-primary me-2"></i>Sampling Divisions & Craft Departments</h6>
                        <small class="text-muted">Master list of production departments (Knit, Stitch, Hand Embroidery, Leather, etc.)</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addDivisionModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Division
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Division Name</th>
                                <th>Code</th>
                                <th>Description / Scope</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($divisions as $div)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $div->name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $div->code ?: '-' }}</span></td>
                                    <td class="text-muted small">{{ $div->description ?: '—' }}</td>
                                    <td>
                                        @if($div->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editDivisionModal{{ $div->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.divisions.destroy', $div->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove division \'{{ $div->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editDivisionModal{{ $div->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Division</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.divisions.update', $div->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Division Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $div->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Division Code</label>
                                                        <input type="text" name="code" class="form-control font-monospace" value="{{ $div->code }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $div->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $div->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $div->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                    <td colspan="5" class="text-center py-4 text-muted">No divisions configured. Click "Add Division" to create one.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 2: MEASUREMENT POINTS (NAAP MASTER)              --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="measurements" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-rulers text-success me-2"></i>Measurement Points (Naap Master)</h6>
                        <small class="text-muted">Standard points of measurement (Chest Width, Body Length, Across Shoulder, Sleeve, etc.)</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addMeasurementModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Measurement Point
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Point Name</th>
                                <th>Code / Short Key</th>
                                <th>Default Unit</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($measurementPoints as $pt)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $pt->point_name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $pt->code ?: '-' }}</span></td>
                                    <td><span class="badge bg-primary-subtle text-primary">{{ $pt->default_unit }}</span></td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editMeasurementModal{{ $pt->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.measurement-points.destroy', $pt->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove measurement point \'{{ $pt->point_name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editMeasurementModal{{ $pt->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Measurement Point</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.measurement-points.update', $pt->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Point Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="point_name" class="form-control" value="{{ $pt->point_name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Code / Short Key</label>
                                                        <input type="text" name="code" class="form-control font-monospace" value="{{ $pt->code }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Default Unit <span class="text-danger">*</span></label>
                                                        <select name="default_unit" class="form-select" required>
                                                            <option value="cm" {{ $pt->default_unit === 'cm' ? 'selected' : '' }}>Centimetres (cm)</option>
                                                            <option value="inch" {{ $pt->default_unit === 'inch' ? 'selected' : '' }}>Inches (inch)</option>
                                                            <option value="mm" {{ $pt->default_unit === 'mm' ? 'selected' : '' }}>Millimetres (mm)</option>
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
                                    <td colspan="4" class="text-center py-4 text-muted">No measurement points defined.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 3: PHYSICAL STORAGE LOCATIONS                    --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="storage" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-archive text-info me-2"></i>Physical Archive & Sample Storage Locations</h6>
                        <small class="text-muted">Studio rooms, racks, shelves, and boxes where physical master samples are archived</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addStorageModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Storage Location
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Studio / Building</th>
                                <th>Room</th>
                                <th>Rack</th>
                                <th>Shelf</th>
                                <th>Box</th>
                                <th>Formatted Location</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($storageLocations as $loc)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $loc->studio_name }}</td>
                                    <td>{{ $loc->room }}</td>
                                    <td>{{ $loc->rack ?: '—' }}</td>
                                    <td>{{ $loc->shelf ?: '—' }}</td>
                                    <td>{{ $loc->box ?: '—' }}</td>
                                    <td class="text-primary fw-semibold font-monospace">{{ $loc->formatted_location }}</td>
                                    <td>
                                        @if($loc->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editStorageModal{{ $loc->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.locations.destroy', $loc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this storage location?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editStorageModal{{ $loc->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Storage Location</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.locations.update', $loc->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Studio / Building <span class="text-danger">*</span></label>
                                                            <input type="text" name="studio_name" class="form-control" value="{{ $loc->studio_name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                                                            <input type="text" name="room" class="form-control" value="{{ $loc->room }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Rack</label>
                                                            <input type="text" name="rack" class="form-control" value="{{ $loc->rack }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Shelf</label>
                                                            <input type="text" name="shelf" class="form-control" value="{{ $loc->shelf }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Box</label>
                                                            <input type="text" name="box" class="form-control" value="{{ $loc->box }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $loc->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $loc->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
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
                                    <td colspan="8" class="text-center py-4 text-muted">No storage locations configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 4: OPERATIONS & MAKING STEPS                     --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="operations" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-gear-wide text-warning me-2"></i>Standard Operations & Making Steps</h6>
                        <small class="text-muted">Processes like Knitting, Linking, Stitching, Hand Needlework with standard timings and rates</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addOperationModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Operation
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Operation / Process Name</th>
                                <th>Department</th>
                                <th>Skill Level</th>
                                <th>Default Time</th>
                                <th>Hourly Rate</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($operations as $op)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $op->operation_name }}</td>
                                    <td>
                                        @if($op->division)
                                            <span class="badge bg-light text-dark border">{{ $op->division->name }}</span>
                                        @else
                                            <span class="text-muted small">General</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $op->skill_level }}</span></td>
                                    <td>{{ number_format($op->default_time_minutes, 0) }} mins</td>
                                    <td class="fw-semibold">₹{{ number_format($op->default_rate_per_hour, 2) }}/hr</td>
                                    <td>
                                        @if($op->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editOperationModal{{ $op->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.operations.destroy', $op->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove operation \'{{ $op->operation_name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editOperationModal{{ $op->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Operation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.operations.update', $op->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Operation Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="operation_name" class="form-control" value="{{ $op->operation_name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Department</label>
                                                            <select name="division_id" class="form-select">
                                                                <option value="">-- General / Any --</option>
                                                                @foreach($divisions as $div)
                                                                    <option value="{{ $div->id }}" {{ $op->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Skill Level <span class="text-danger">*</span></label>
                                                            <select name="skill_level" class="form-select" required>
                                                                <option value="Master Artisan" {{ $op->skill_level === 'Master Artisan' ? 'selected' : '' }}>Master Artisan</option>
                                                                <option value="Skilled" {{ $op->skill_level === 'Skilled' ? 'selected' : '' }}>Skilled</option>
                                                                <option value="Semi-Skilled" {{ $op->skill_level === 'Semi-Skilled' ? 'selected' : '' }}>Semi-Skilled</option>
                                                                <option value="Trainee" {{ $op->skill_level === 'Trainee' ? 'selected' : '' }}>Trainee</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Default Time (Minutes)</label>
                                                            <input type="number" step="0.5" name="default_time_minutes" class="form-control" value="{{ $op->default_time_minutes }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                                                            <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" value="{{ $op->default_rate_per_hour }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $op->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $op->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
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
                                    <td colspan="7" class="text-center py-4 text-muted">No operations configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 5: MATERIALS & ITEMS CATALOG                     --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="materials" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam text-danger me-2"></i>Materials & Raw Item Catalog</h6>
                        <small class="text-muted">Common materials (Fabrics, Yarns, Threads, Buttons, Zippers, Trims, Packaging)</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Material
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Category</th>
                                <th>Material / Item Name</th>
                                <th>Item Code</th>
                                <th>Default UOM</th>
                                <th>Standard Cost Rate</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $mat)
                                <tr>
                                    <td class="ps-3"><span class="badge bg-light text-dark border">{{ $mat->material_category }}</span></td>
                                    <td class="fw-bold text-dark">{{ $mat->material_name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $mat->item_code ?: '—' }}</span></td>
                                    <td>{{ $mat->unit_of_measure }}</td>
                                    <td class="fw-semibold">₹{{ number_format($mat->standard_cost, 2) }}</td>
                                    <td>
                                        @if($mat->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $mat->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.materials.destroy', $mat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove material \'{{ $mat->material_name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editMaterialModal{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Material</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.materials.update', $mat->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                                            <select name="material_category" class="form-select" required>
                                                                @foreach(['Fabric', 'Yarn', 'Thread', 'Buttons', 'Zippers', 'Labels', 'Lining', 'Trims', 'Packaging', 'Other'] as $cat)
                                                                    <option value="{{ $cat }}" {{ $mat->material_category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Material / Item Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="material_name" class="form-control" value="{{ $mat->material_name }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Item Code</label>
                                                            <input type="text" name="item_code" class="form-control font-monospace" value="{{ $mat->item_code }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Default UOM <span class="text-danger">*</span></label>
                                                            <select name="unit_of_measure" class="form-select" required>
                                                                @foreach($uoms as $u)
                                                                    <option value="{{ $u->symbol }}" {{ $mat->unit_of_measure === $u->symbol || $mat->unit_of_measure === $u->name ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Cost Rate (₹)</label>
                                                            <input type="number" step="0.01" name="standard_cost" class="form-control" value="{{ $mat->standard_cost }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $mat->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $mat->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
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
                                    <td colspan="7" class="text-center py-4 text-muted">No materials registered. Click "Add Material" to define standard items.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 6: UNITS OF MEASURE (UOM)                        --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="uoms" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calculator text-secondary me-2"></i>Units of Measure (UOM)</h6>
                        <small class="text-muted">Standard measuring units for raw materials, trims, and dimensions</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addUomModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Unit
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Unit Name</th>
                                <th>Symbol / Short Code</th>
                                <th>Measurement Type</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($uoms as $u)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $u->name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $u->symbol }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $u->type ?: 'General' }}</span></td>
                                    <td>
                                        @if($u->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editUomModal{{ $u->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.uoms.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove unit \'{{ $u->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editUomModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Unit of Measure</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.uoms.update', $u->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Symbol / Code <span class="text-danger">*</span></label>
                                                        <input type="text" name="symbol" class="form-control font-monospace" value="{{ $u->symbol }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Type</label>
                                                        <select name="type" class="form-select">
                                                            <option value="Quantity" {{ $u->type === 'Quantity' ? 'selected' : '' }}>Quantity (e.g. pcs, sets)</option>
                                                            <option value="Weight" {{ $u->type === 'Weight' ? 'selected' : '' }}>Weight (e.g. kg, g)</option>
                                                            <option value="Length" {{ $u->type === 'Length' ? 'selected' : '' }}>Length (e.g. m, yd, in, cm)</option>
                                                            <option value="Area" {{ $u->type === 'Area' ? 'selected' : '' }}>Area (e.g. sq ft, sq m)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="inactive" {{ $u->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                    <td colspan="5" class="text-center py-4 text-muted">No units of measure configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 7: DESIGNERS MASTER                              --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="designers" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-palette text-purple me-2" style="color: #6f42c1;"></i>Designers Master</h6>
                        <small class="text-muted">Fashion designers, stylists, and artisan leads associated with sampling projects</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addDesignerModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Designer
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Designer Name</th>
                                <th>Code</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($designers as $des)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $des->name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $des->code ?: '—' }}</span></td>
                                    <td class="text-muted small">{{ $des->email ?: '—' }}</td>
                                    <td class="text-muted small">{{ $des->phone ?: '—' }}</td>
                                    <td><span class="badge bg-light text-primary border">{{ $des->specialization ?: 'General' }}</span></td>
                                    <td>
                                        @if($des->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editDesignerModal{{ $des->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.designers.destroy', $des->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove designer \'{{ $des->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editDesignerModal{{ $des->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Designer</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.designers.update', $des->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label class="form-label fw-semibold">Designer Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $des->name }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Code</label>
                                                            <input type="text" name="code" class="form-control font-monospace" value="{{ $des->code }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $des->email }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Phone</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ $des->phone }}">
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label class="form-label fw-semibold">Specialization</label>
                                                            <input type="text" name="specialization" class="form-control" value="{{ $des->specialization }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $des->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $des->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
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
                                    <td colspan="7" class="text-center py-4 text-muted">No designers registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 8: COLLECTIONS & SEASONS MASTER                  --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="collections" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-collection text-primary me-2"></i>Collections & Seasons Master</h6>
                        <small class="text-muted">Brand collections, seasonal releases, and design drop campaigns</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Collection
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Collection Name</th>
                                <th>Code</th>
                                <th>Season</th>
                                <th>Year</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $coll)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $coll->name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $coll->code ?: '—' }}</span></td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $coll->season ?: '—' }}</span></td>
                                    <td><span class="badge bg-light text-dark border">{{ $coll->year ?: '—' }}</span></td>
                                    <td class="text-muted small">{{ Str::limit($coll->description, 50) ?: '—' }}</td>
                                    <td>
                                        @if($coll->status === 'active')
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editCollectionModal{{ $coll->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.collections.destroy', $coll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove collection \'{{ $coll->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editCollectionModal{{ $coll->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Collection</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.collections.update', $coll->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label class="form-label fw-semibold">Collection Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $coll->name }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Code</label>
                                                            <input type="text" name="code" class="form-control font-monospace" value="{{ $coll->code }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Season</label>
                                                            <select name="season" class="form-select">
                                                                <option value="">-- Select Season --</option>
                                                                @foreach(['Autumn / Winter', 'Spring / Summer', 'Resort / Cruise', 'Festive / Wedding', 'Pre-Fall', 'Core Basics'] as $s)
                                                                    <option value="{{ $s }}" {{ $coll->season === $s ? 'selected' : '' }}>{{ $s }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Year</label>
                                                            <input type="text" name="year" class="form-control" value="{{ $coll->year }}" placeholder="e.g. 2026">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Description</label>
                                                            <textarea name="description" class="form-control" rows="2">{{ $coll->description }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-semibold">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" {{ $coll->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $coll->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
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
                                    <td colspan="7" class="text-center py-4 text-muted">No collections created. Click "Add Collection" to define one.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ==================================================== --}}
            {{-- TAB 9: TECHNICAL SPEC ATTRIBUTES                    --}}
            {{-- ==================================================== --}}
            <div class="tab-pane fade" id="specifications" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-card-checklist text-teal me-2" style="color: #20c997;"></i>Technical Specification Attributes</h6>
                        <small class="text-muted">Standard technical parameters recorded on styles (Machine Gauge, Yarn Ply, SPI, Target GSM, Shrinkage %)</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addSpecAttrModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Spec Attribute
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-3">Attribute Name</th>
                                <th>Input Field Type</th>
                                <th>Default Unit</th>
                                <th>Mandatory in Tech Pack?</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($specAttributes as $spec)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $spec->attribute_name }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ ucfirst($spec->field_type) }}</span></td>
                                    <td><span class="badge bg-primary-subtle text-primary">{{ $spec->unit ?: '—' }}</span></td>
                                    <td>
                                        @if($spec->is_required)
                                            <span class="badge bg-danger-subtle text-danger">Required</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Optional</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-light border text-primary me-1" data-bs-toggle="modal" data-bs-target="#editSpecAttrModal{{ $spec->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('sampling.masters.specs.destroy', $spec->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove spec attribute \'{{ $spec->attribute_name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editSpecAttrModal{{ $spec->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Edit Spec Attribute</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sampling.masters.specs.update', $spec->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="attribute_name" class="form-control" value="{{ $spec->attribute_name }}" required>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                                                            <select name="field_type" class="form-select" required>
                                                                <option value="text" {{ $spec->field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                                <option value="number" {{ $spec->field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                                <option value="select" {{ $spec->field_type === 'select' ? 'selected' : '' }}>Dropdown List</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-semibold">Unit (e.g. GG, Ply, GSM, %)</label>
                                                            <input type="text" name="unit" class="form-control" value="{{ $spec->unit }}">
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_required" value="1" id="reqCheck{{ $spec->id }}" {{ $spec->is_required ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-semibold" for="reqCheck{{ $spec->id }}">
                                                            Mandatory for Freeze Approval
                                                        </label>
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
                                    <td colspan="5" class="text-center py-4 text-muted">No spec attributes defined yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div> {{-- End Tab Content --}}
    </div> {{-- End Master Hub Card --}}
</div>

{{-- ======================================================== --}}
{{-- GLOBAL ADD MODALS FOR EACH MASTER                        --}}
{{-- ======================================================== --}}

{{-- 1. Add Division Modal --}}
<div class="modal fade" id="addDivisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Add Division / Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.divisions') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Division / Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Handloom Weaving, Leather Craft" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Division Code</label>
                        <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. WVE">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Scope</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief explanation of work done in this department..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Add Measurement Point Modal --}}
<div class="modal fade" id="addMeasurementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>Add Measurement Point</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.measurement-points') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Point Name <span class="text-danger">*</span></label>
                        <input type="text" name="point_name" class="form-control" placeholder="e.g. Chest Width, Total Length, Waist Width" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code / Short Key</label>
                        <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. CHEST, LENGTH">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Default Unit <span class="text-danger">*</span></label>
                        <select name="default_unit" class="form-select" required>
                            <option value="cm" selected>Centimetres (cm)</option>
                            <option value="inch">Inches (inch)</option>
                            <option value="mm">Millimetres (mm)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Measurement Point</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Add Storage Location Modal --}}
<div class="modal fade" id="addStorageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-info me-2"></i>Add Storage Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.locations') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Studio / Building <span class="text-danger">*</span></label>
                            <input type="text" name="studio_name" class="form-control" placeholder="e.g. Main Craft Studio" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                            <input type="text" name="room" class="form-control" placeholder="e.g. Sample Archive Room" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rack</label>
                            <input type="text" name="rack" class="form-control" placeholder="Rack A">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Shelf</label>
                            <input type="text" name="shelf" class="form-control" placeholder="Shelf 2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Box</label>
                            <input type="text" name="box" class="form-control" placeholder="Box 08">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Storage Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 4. Add Operation Modal --}}
<div class="modal fade" id="addOperationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-warning me-2"></i>Add Standard Operation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.operations') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Operation / Process Name <span class="text-danger">*</span></label>
                            <input type="text" name="operation_name" class="form-control" placeholder="e.g. Hand Embroidery Needlework" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="division_id" class="form-select">
                                <option value="">-- General / Any --</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Skill Level <span class="text-danger">*</span></label>
                            <select name="skill_level" class="form-select" required>
                                <option value="Master Artisan">Master Artisan</option>
                                <option value="Skilled" selected>Skilled</option>
                                <option value="Semi-Skilled">Semi-Skilled</option>
                                <option value="Trainee">Trainee</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Time (Minutes)</label>
                            <input type="number" step="0.5" name="default_time_minutes" class="form-control" placeholder="e.g. 45">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (₹/hr)</label>
                            <input type="number" step="0.5" name="default_rate_per_hour" class="form-control" placeholder="e.g. 120">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Operation</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 5. Add Material Modal --}}
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-danger me-2"></i>Add Material / Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.materials') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Material Category <span class="text-danger">*</span></label>
                            <select name="material_category" class="form-select" required>
                                <option value="Fabric">Fabric</option>
                                <option value="Yarn">Yarn</option>
                                <option value="Thread">Thread / Needlework</option>
                                <option value="Buttons">Buttons</option>
                                <option value="Zippers">Zippers</option>
                                <option value="Labels">Labels & Tags</option>
                                <option value="Lining">Lining</option>
                                <option value="Trims">Trims & Laces</option>
                                <option value="Packaging">Packaging</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="material_name" class="form-control" placeholder="e.g. 2/28 Cashmere Melange Yarn" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Item Code</label>
                            <input type="text" name="item_code" class="form-control font-monospace" placeholder="e.g. YRN-CSH-01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Default UOM <span class="text-danger">*</span></label>
                            <select name="unit_of_measure" class="form-select" required>
                                @foreach($uoms as $u)
                                    <option value="{{ $u->symbol }}">{{ $u->name }} ({{ $u->symbol }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Standard Cost Rate (₹)</label>
                            <input type="number" step="0.01" name="standard_cost" class="form-control" placeholder="e.g. 850.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 6. Add UOM Modal --}}
<div class="modal fade" id="addUomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-secondary me-2"></i>Add Unit of Measure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.uoms') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Kilograms, Metres, Pieces" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Symbol / Code <span class="text-danger">*</span></label>
                        <input type="text" name="symbol" class="form-control font-monospace" placeholder="e.g. kg, m, pcs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Measurement Type</label>
                        <select name="type" class="form-select">
                            <option value="Quantity">Quantity (e.g. pcs, sets, cones)</option>
                            <option value="Weight">Weight (e.g. kg, g)</option>
                            <option value="Length">Length (e.g. m, yd, in, cm)</option>
                            <option value="Area">Area (e.g. sq ft, sq m)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 7. Add Designer Modal --}}
<div class="modal fade" id="addDesignerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-purple me-2" style="color: #6f42c1;"></i>Add Designer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.designers') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Designer Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Tariq Ahmad" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. DES-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="designer@studio.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Knitwear, Hand Embroidery, Leather Crafts">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Designer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 8. Add Collection Modal --}}
<div class="modal fade" id="addCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Add Collection / Season</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.collections') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Collection Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Autumn / Winter 2026 Cashmere Drop" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. AW26">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Season</label>
                            <select name="season" class="form-select">
                                <option value="Autumn / Winter">Autumn / Winter</option>
                                <option value="Spring / Summer">Spring / Summer</option>
                                <option value="Resort / Cruise">Resort / Cruise</option>
                                <option value="Festive / Wedding">Festive / Wedding</option>
                                <option value="Core Basics">Core Basics</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="text" name="year" class="form-control" placeholder="e.g. 2026" value="2026">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Theme, colour palette notes, inspiration..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Collection</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 9. Add Tech Spec Attribute Modal --}}
<div class="modal fade" id="addSpecAttrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-teal me-2" style="color: #20c997;"></i>Add Tech Spec Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('sampling.masters.specs') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                        <input type="text" name="attribute_name" class="form-control" placeholder="e.g. Yarn Gauge, Ply Count, Needle Size" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" class="form-select" required>
                                <option value="text" selected>Text</option>
                                <option value="number">Number</option>
                                <option value="select">Dropdown List</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit (e.g. GG, Ply, GSM, %)</label>
                            <input type="text" name="unit" class="form-control" placeholder="e.g. GG or Ply">
                        </div>
                    </div>
                    <div class="mt-3 form-check">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1" id="addReqCheck">
                        <label class="form-check-label fw-semibold" for="addReqCheck">
                            Mandatory for Freeze Approval
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Attribute</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript to maintain active tab on page load and URL hash changes --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check URL hash (e.g. #operations)
    var hash = window.location.hash;
    if (hash) {
        var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (triggerEl) {
            var tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
            tab.show();
        }
    }

    // Update URL hash whenever a tab is clicked
    var tabButtons = document.querySelectorAll('#masterTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(function (button) {
        button.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.getAttribute('data-bs-target');
            if (target) {
                history.replaceState(null, null, target);
            }
        });
    });
});
</script>
@endsection
