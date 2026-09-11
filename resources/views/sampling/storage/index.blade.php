@extends('layouts.sampling')

@section('title', 'Sample Physical Storage Archive')
@section('page-title', 'Physical Storage Archive')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-archive me-2 text-primary"></i>Physical Sample Storage Registry</h4>
        <p class="text-muted small mb-0">Track exact physical location and condition of approved master reference samples</p>
    </div>
</div>

<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('sampling.storage.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <select name="location_id" class="form-select">
                <option value="">All Physical Archive Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->formatted_location }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">Filter</button>
            <a href="{{ route('sampling.storage.index') }}" class="btn btn-light border px-3">Reset</a>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Sample Code</th>
                        <th>Style Name</th>
                        <th>Project & Batch</th>
                        <th>Archive Location</th>
                        <th>Condition</th>
                        <th>Quantity</th>
                        <th>Date Stored</th>
                        <th>Archived By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($storages as $s)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold">
                                <a href="{{ route('sampling.samples.show', $s->sample->id) }}?tab=storage" class="text-decoration-none text-primary">
                                    {{ $s->sample->sample_code }}
                                </a>
                            </td>
                            <td class="fw-semibold text-dark">{{ $s->sample->style_name }}</td>
                            <td>
                                <div class="small fw-semibold">{{ $s->sample->project->project_name ?? '-' }}</div>
                                <small class="text-muted font-monospace">{{ $s->sample->batch->batch_number ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                                    {{ $s->location->formatted_location ?? '-' }}
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $s->sample_condition }}</span></td>
                            <td class="font-monospace fw-bold">{{ $s->quantity }}</td>
                            <td class="small text-muted">{{ $s->date_stored->format('d M Y') }}</td>
                            <td class="small">{{ $s->storedByUser->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-archive fs-1 opacity-50 mb-2 d-block"></i>
                                No physical samples registered in storage yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($storages->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $storages->links() }}
        </div>
    @endif
</div>
@endsection
