@extends('layouts.municipality')

@section('title', 'Assigned Cases - NagarRakshak')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Assigned Cases Directory</h3>
            <p class="text-secondary mb-0">Search, filter, and update civic reports assigned to Kota Municipal divisions.</p>
        </div>
        <span class="text-muted small fw-semibold">{{ $cases->total() }} total cases found</span>
    </div>

    <!-- Filters Card -->
    <div class="card card-custom p-3 mb-4">
        <form action="{{ route('municipality.cases.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary">Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary">Filter by Category</label>
                <select name="category" class="form-select">
                    <option value="">-- All Categories --</option>
                    <option value="Pothole" {{ request('category') === 'Pothole' ? 'selected' : '' }}>Pothole</option>
                    <option value="Open Drain" {{ request('category') === 'Open Drain' ? 'selected' : '' }}>Open Drain</option>
                    <option value="Open Manhole" {{ request('category') === 'Open Manhole' ? 'selected' : '' }}>Open Manhole</option>
                    <option value="Waterlogging" {{ request('category') === 'Waterlogging' ? 'selected' : '' }}>Waterlogging</option>
                    <option value="Broken Streetlight" {{ request('category') === 'Broken Streetlight' ? 'selected' : '' }}>Broken Streetlight</option>
                    <option value="Garbage" {{ request('category') === 'Garbage' ? 'selected' : '' }}>Garbage Dump</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-grow-1" style="background-color: #10B981; border: none;"><i class="fa-solid fa-filter me-1"></i> Apply Filters</button>
                <a href="{{ route('municipality.cases.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            </div>
        </form>
    </div>

    <!-- Cases Table Card -->
    <div class="card card-custom p-4 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Severity</th>
                        <th>Evidences (Merged)</th>
                        <th>Status</th>
                        <th>Date Logged</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cases as $case)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-5">⚠️</span>
                                <span class="fw-bold text-dark">{{ $case->category }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $case->location_name }}</span>
                        </td>
                        <td>
                            @php
                                $sevBadge = str_contains($case->severity, 'High') || str_contains($case->severity, 'Critical') ? 'bg-danger-subtle text-danger border border-danger-subtle' : (str_contains($case->severity, 'Low') ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle');
                            @endphp
                            <span class="badge {{ $sevBadge }} rounded-pill px-3">{{ $case->severity }}</span>
                        </td>
                        <td>
                            @php
                                $urls = $case->image_urls;
                            @endphp
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="fa-regular fa-image me-1"></i> {{ count($urls) }} Photo(s)</span>
                        </td>
                        <td>
                            @php
                                $statusBadge = $case->status === 'Resolved' ? 'bg-success text-white' : ($case->status === 'In Progress' ? 'bg-primary text-white' : 'bg-warning text-dark');
                            @endphp
                            <span class="badge {{ $statusBadge }} rounded-1">{{ $case->status }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $case->created_at->format('M d, Y H:i') }}</span>
                        </td>
                        <td>
                            <a href="{{ route('municipality.cases.show', $case->id) }}" class="btn btn-sm btn-success px-3" style="background-color: #10B981; border: none;">Manage</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No cases matching filter options found in database.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $cases->links() }}
        </div>
    </div>
</div>
@endsection
