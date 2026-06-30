@extends('layouts.municipality')

@section('title', 'Municipal Department Dashboard - NagarRakshak')

@section('content')
<div class="container">
    <!-- Welcome section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Department Dashboard</h3>
            <p class="text-secondary mb-0">Welcome back, Municipal Authority Officer. Manage road notices, resolve potholes, and post official reports.</p>
        </div>
        <a href="{{ route('municipality.cases.index') }}" class="btn btn-primary bg-emerald border-0 rounded-pill px-4" style="background-color: #10B981;"><i class="fa-solid fa-clipboard-list me-1"></i> View Assigned Cases</a>
    </div>

    <!-- Alert toast for successes -->
    @if(session('success'))
    <div class="alert alert-success border-0 bg-success-subtle text-success rounded-3 mb-4">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Quick Statistics -->
    <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Assignments</h6>
                    <h3 class="fw-bold text-dark m-0">{{ $assignedIssues }}</h3>
                </div>
                <div class="rounded-circle bg-light p-3 text-primary">
                    <i class="fa-solid fa-file-signature fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Resolved Cases</h6>
                    <h3 class="fw-bold text-dark m-0 text-success">{{ $resolvedIssues }}</h3>
                </div>
                <div class="rounded-circle bg-success-subtle p-3 text-success">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Review</h6>
                    <h3 class="fw-bold text-dark m-0 text-warning">{{ $pendingIssues }}</h3>
                </div>
                <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                    <i class="fa-solid fa-clock fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Avg Turnaround</h6>
                    <h3 class="fw-bold text-dark m-0">1.8 Days</h3>
                </div>
                <div class="rounded-circle bg-info-subtle p-3 text-info">
                    <i class="fa-solid fa-stopwatch fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Assignments -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-triangle-exclamation me-2 text-warning"></i>Recently Assigned Cases</h5>
                    <a href="{{ route('municipality.cases.index') }}" class="text-decoration-none text-success fw-semibold small">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCases as $case)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-5">⚠️</span>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $case->category }}</div>
                                            <div class="text-secondary small" style="font-size: 0.75rem;">Reported {{ $case->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium text-truncate" style="max-width: 180px;">{{ $case->location_name }}</div>
                                </td>
                                <td>
                                    @php
                                        $sevBadge = str_contains($case->severity, 'High') || str_contains($case->severity, 'Critical') ? 'bg-danger-subtle text-danger border border-danger-subtle' : (str_contains($case->severity, 'Low') ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle');
                                    @endphp
                                    <span class="badge {{ $sevBadge }} rounded-pill px-3">{{ $case->severity }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusBadge = $case->status === 'Resolved' ? 'bg-success text-white' : ($case->status === 'In Progress' ? 'bg-primary text-white' : 'bg-warning text-dark');
                                    @endphp
                                    <span class="badge {{ $statusBadge }} rounded-1">{{ $case->status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('municipality.cases.show', $case->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Manage</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No cases currently logged in database.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ward Resolution Rates -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Ward Resolution Rates</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($wardPerformance as $ward => $metrics)
                    @php
                        $rate = ($metrics['total'] > 0) ? round(($metrics['resolved'] / $metrics['total']) * 100) : 0;
                        $progressBarColor = $rate >= 75 ? 'bg-success' : ($rate >= 40 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="fw-semibold text-secondary">{{ $ward }}</span>
                            <span class="fw-bold text-dark">{{ $rate }}% ({{ $metrics['resolved'] }}/{{ $metrics['total'] }})</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $progressBarColor }}" role="progressbar" style="width: {{ $rate }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small">No performance telemetry available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
