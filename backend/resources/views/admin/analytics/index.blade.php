@extends('layouts.admin')

@section('title', 'NagarRakshak Analytics Hub')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Analytics & Intelligence Insights</h2>
            <p class="text-secondary" style="font-size: 0.95rem;">Analyze city-wide hazard frequencies, resolution speeds, and community reporting indices.</p>
        </div>
    </div>

    <!-- Overview Counters -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card card-custom p-4 h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase d-block mb-1" style="font-size:0.75rem; font-weight: 700; letter-spacing: 0.06em;">Total Cases Tracked</span>
                    <h2 class="fw-bold m-0 text-dark" style="font-size: 2rem; letter-spacing: -0.02em;">{{ number_format($totalCount) }}</h2>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                    <i class="fa-solid fa-shield-halved fa-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-custom p-4 h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase d-block mb-1" style="font-size:0.75rem; font-weight: 700; letter-spacing: 0.06em;">Average Resolution Rate</span>
                    <h2 class="fw-bold text-success m-0" style="font-size: 2rem; letter-spacing: -0.02em;">{{ $resolutionRate }}%</h2>
                </div>
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                    <i class="fa-solid fa-circle-check fa-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-custom p-4 h-100 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase d-block mb-1" style="font-size:0.75rem; font-weight: 700; letter-spacing: 0.06em;">Audit Consensus Score</span>
                    <h2 class="fw-bold text-primary m-0" style="font-size: 2rem; letter-spacing: -0.02em;">94.8%</h2>
                </div>
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                    <i class="fa-solid fa-user-check fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="row mb-4">
        <!-- Reports by Category -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Reports by Category</h5>
                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1">Multi-Color Spectrum</span>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Reports by Severity -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-chart-bar text-primary me-2"></i> Reports by Severity</h5>
                    <span class="badge bg-light text-secondary border px-2.5 py-1">Risk Levels</span>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Reports Trend -->
        <div class="col-lg-12">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-chart-area text-primary me-2"></i> Monthly Incident Trends</h5>
                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1">Telemetry Growth</span>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Vibrant Multi-Color Palette for Category Breakdown
    var vibrantPalette = [
        '#2563EB', // Electric Cobalt Blue
        '#E11D48', // Crimson Rose
        '#D97706', // Amber Gold
        '#059669', // Emerald Green
        '#7C3AED', // Vivid Violet
        '#0891B2', // Cyan Blue
        '#EA580C', // Deep Orange
        '#4F46E5', // Indigo Blue
        '#DB2777', // Magenta Pink
        '#0284C7', // Sky Blue
        '#65A30D', // Lime Green
        '#9333EA', // Deep Purple
        '#475569', // Slate Gray
        '#CA8A04', // Dark Yellow
        '#14B8A6'  // Teal
    ];

    // 1. Category Chart (Multi-color Doughnut)
    var catCtx = document.getElementById('categoryChart').getContext('2d');
    var categoryLabels = {!! json_encode(array_keys($byCategory)) !!};
    
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: {!! json_encode(array_values($byCategory)) !!},
                backgroundColor: vibrantPalette.slice(0, categoryLabels.length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                            weight: '500'
                        }
                    }
                }
            }
        }
    });

    // 2. Severity Chart (Vibrant Bar Chart)
    var sevCtx = document.getElementById('severityChart').getContext('2d');
    new Chart(sevCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($bySeverity)) !!},
            datasets: [{
                data: {!! json_encode(array_values($bySeverity)) !!},
                backgroundColor: ['#E11D48', '#F59E0B', '#2563EB', '#059669'],
                borderRadius: 10,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 3. Monthly Trends Chart (Fintech Gradient Line)
    var trendCtx = document.getElementById('trendChart').getContext('2d');
    var gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyTrend['Labels']) !!},
            datasets: [{
                label: 'Reported Incidents',
                data: {!! json_encode($monthlyTrend['Values']) !!},
                borderColor: '#2563EB',
                backgroundColor: gradient,
                fill: true,
                borderWidth: 3,
                tension: 0.38,
                pointBackgroundColor: '#2563EB',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
