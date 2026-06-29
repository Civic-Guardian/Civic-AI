@extends('layouts.admin')

@section('title', 'NagarRakshak Admin Hub')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Municipal Safety Command Center</h2>
            <p class="text-secondary" style="font-size: 0.95rem;">Real-time civic surveillance telemetry, risk routing, and Gemini AI intelligence models.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">
        @foreach($stats as $key => $data)
            <div class="col">
                <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between h-100">
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.72rem; font-weight:600; letter-spacing: 0.5px;">{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
                        <h3 class="fw-bold m-0 text-dark" style="font-size: 1.8rem;">{{ number_format($data['count']) }}</h3>
                        <div class="mt-2" style="font-size: 0.75rem; font-weight:600;">
                            @if($data['trend'] === 'up')
                                <span class="text-success"><i class="fa-solid fa-arrow-trend-up"></i> {{ $data['change'] }}</span>
                            @elseif($data['trend'] === 'down')
                                <span class="text-danger"><i class="fa-solid fa-arrow-trend-down"></i> {{ $data['change'] }}</span>
                            @else
                                <span class="text-secondary"><i class="fa-solid fa-minus"></i> {{ $data['change'] }}</span>
                            @endif
                            <span class="text-muted font-weight-normal"> vs last week</span>
                        </div>
                    </div>
                    <div class="rounded-circle bg-light p-3 text-{{ $data['color'] }}">
                        <i class="fa-solid {{ $data['icon'] }} fa-lg"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Live Map & Activity Feed -->
    <div class="row mb-4">
        <!-- Google Map Section -->
        <div class="col-lg-8" id="map-card">
            <div class="card card-custom p-4 mb-4" style="min-height: 500px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-map-location-dot text-green"></i> Live Hazard Map</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="mapSeverityFilter" style="width: 150px;">
                            <option value="All">All Severities</option>
                            <option value="Critical">Critical</option>
                            <option value="High Risk">High Risk</option>
                            <option value="Medium Risk">Medium Risk</option>
                            <option value="Low Risk">Low Risk</option>
                        </select>
                    </div>
                </div>

                <!-- Leaflet Map Container -->
                <div id="liveMap" class="rounded-4 border" style="min-height: 400px; z-index: 10;"></div>
            </div>
        </div>

        <!-- Activity Feed Section -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 mb-4 h-100">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-bolt text-warning"></i> Real-time Activity Feed</h5>
                
                <div class="timeline-feed" style="max-height: 380px; overflow-y: auto;">
                    @if($activities->isEmpty())
                        <p class="text-muted text-center py-4">No recent activity logs.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <li class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-light text-{{ $activity->type === 'Admin' ? 'danger border border-danger' : 'primary border border-primary' }} mb-1" style="font-size: 0.65rem;">
                                            {{ $activity->type }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="fw-semibold m-0 text-dark" style="font-size: 0.85rem;">{{ $activity->action }}</p>
                                    <small class="text-muted d-block mt-1 leading-normal" style="font-size: 0.8rem;">{{ $activity->description }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Hazard Reports Table -->
    <div class="card card-custom p-4">
        <h5 class="fw-bold mb-4"><i class="fa-solid fa-list-check text-green"></i> Recent Hazard Reports</h5>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Reported At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($recentReports->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No hazard reports found.</td>
                        </tr>
                    @else
                        @foreach($recentReports as $report)
                            <tr>
                                <td>
                                    <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fa-solid fa-image text-muted"></i>
                                    </div>
                                </td>
                                <td class="fw-semibold">{{ $report->category }}</td>
                                <td>{{ $report->location_name }}</td>
                                <td>
                                    @if($report->severity === 'High Risk' || $report->severity === 'Critical')
                                        <span class="badge badge-high">{{ $report->severity }}</span>
                                    @elseif($report->severity === 'Medium Risk')
                                        <span class="badge badge-medium">{{ $report->severity }}</span>
                                    @else
                                        <span class="badge badge-low">{{ $report->severity }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status === 'Pending')
                                        <span class="badge bg-warning text-dark">{{ $report->status }}</span>
                                    @elseif($report->status === 'Verified')
                                        <span class="badge bg-primary">{{ $report->status }}</span>
                                    @elseif($report->status === 'Resolved')
                                        <span class="badge bg-success">{{ $report->status }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $report->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $report->creator ? $report->creator->name : 'Anonymous' }}</td>
                                <td>{{ $report->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('admin.cases.show', $report->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">View</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Services\SettingsService::get('google_maps_api_key') }}"></script>
<script>
    var map;
    var markers = [];
    var hazards = {!! json_encode($hazards) !!};

    function initMap() {
        map = new google.maps.Map(document.getElementById('liveMap'), {
            center: { lat: 25.18, lng: 75.83 },
            zoom: 13,
            styles: [
                {
                    "featureType": "poi",
                    "elementType": "labels",
                    "stylers": [{ "visibility": "off" }]
                }
            ]
        });

        drawMarkers();
    }

    function drawMarkers(filterSeverity = 'All') {
        markers.forEach(function(marker) {
            marker.setMap(null);
        });
        markers = [];
        
        hazards.forEach(function(hazard) {
            if (filterSeverity === 'All' || hazard.severity === filterSeverity) {
                var markerColor = '#10B981'; // Low (Green)
                if (hazard.severity === 'Critical') {
                    markerColor = '#EF4444'; // Red
                } else if (hazard.severity === 'High Risk') {
                    markerColor = '#F59E0B'; // Orange
                } else if (hazard.severity === 'Medium Risk') {
                    markerColor = '#FBBF24'; // Yellow
                }

                var marker = new google.maps.Marker({
                    position: { lat: parseFloat(hazard.latitude), lng: parseFloat(hazard.longitude) },
                    map: map,
                    title: hazard.category,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: markerColor,
                        fillOpacity: 0.9,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                        scale: 8
                    }
                });

                var detailUrl = "{{ route('admin.cases.show', ':id') }}".replace(':id', hazard.id);
                var popupContent = '<div style="font-family: sans-serif; min-width: 150px; padding: 4px;">' +
                                   '<strong style="font-size:0.9rem; color:#111827;">' + hazard.category + '</strong><br>' +
                                   '<span class="badge mt-1 mb-2" style="background-color:' + markerColor + '; color:#fff;">' + hazard.severity + '</span><br>' +
                                   '<small style="color:#6B7280;" class="d-block mb-2">' + hazard.location_name + '</small>' +
                                   '<a href="' + detailUrl + '" class="btn btn-sm btn-success text-white w-100 py-1" style="font-size:0.75rem; font-weight:600;">View Details</a>' +
                                   '</div>';
                
                var infowindow = new google.maps.InfoWindow({
                    content: popupContent
                });

                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });

                markers.push(marker);
            }
        });
    }

    $(document).ready(function() {
        if (typeof google !== 'undefined') {
            initMap();
        }
    });

    $('#mapSeverityFilter').change(function() {
        drawMarkers($(this).val());
    });
</script>
@endsection
