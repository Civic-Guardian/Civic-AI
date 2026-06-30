@extends('layouts.admin')

@section('title', 'Developer Portal & API Docs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-code text-primary me-2"></i>Developer Portal</h2>
            <p class="text-muted mb-0">Integrate real-time Kota civic intelligence, EV hazards telemetry, road notices, and billboard advertisements.</p>
        </div>
        <div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                <i class="fa-solid fa-circle-check me-1"></i> API Gateway Online
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom border-light p-0">
            <ul class="nav nav-tabs nav-fill border-0 px-3 pt-2" id="apiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 fw-semibold border-0" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab"><i class="fa-solid fa-circle-info me-2"></i>Overview & Auth</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-semibold border-0" id="ev-tab" data-bs-toggle="tab" data-bs-target="#ev" type="button" role="tab"><i class="fa-solid fa-car-rear me-2 text-success"></i>OLA / Ather EV</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-semibold border-0" id="billboards-tab" data-bs-toggle="tab" data-bs-target="#billboards" type="button" role="tab"><i class="fa-solid fa-rectangle-ad me-2 text-primary"></i>Govt. Billboards</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-semibold border-0" id="ads-tab" data-bs-toggle="tab" data-bs-target="#ads" type="button" role="tab"><i class="fa-solid fa-chart-simple me-2 text-warning"></i>Ad Zones</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-semibold border-0" id="notices-tab" data-bs-toggle="tab" data-bs-target="#notices" type="button" role="tab"><i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Road Notices</button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-4">
            <div class="tab-content" id="apiTabsContent">
                
                <!-- Overview & Auth -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <h5 class="fw-bold mb-3">Getting Started</h5>
                    <p class="text-secondary leading-relaxed">The NagarRakshak Developer Portal exposes live civic utility and safety endpoints for public integrations. These REST APIs allow transportation networks, navigation apps, and municipal advertisers to synchronize seamlessly with City Administration data.</p>
                    
                    <div class="alert alert-info border-0 bg-info-subtle rounded-3 p-3 mb-4">
                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-key me-2"></i>Rate Limiting & Authentication</h6>
                        <p class="mb-0 text-secondary" style="font-size: 0.9rem;">By default, developer APIs are public and do not require authentication tokens for read-only access. However, production consumers must request a client ID key to bypass the standard limit of <strong>60 requests per minute</strong>.</p>
                    </div>

                    <h6 class="fw-bold text-uppercase text-primary mb-3">Base API Endpoint Url</h6>
                    <div class="d-flex align-items-center bg-dark text-light p-3 rounded-3 font-monospace mb-4 justify-content-between">
                        <span>{{ url('/api/developer') }}</span>
                        <button class="btn btn-sm btn-outline-secondary text-light border-secondary" onclick="navigator.clipboard.writeText('{{ url('/api/developer') }}')"><i class="fa-regular fa-copy"></i> Copy</button>
                    </div>

                    <h6 class="fw-bold mb-3">Developer Resources</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold"><i class="fa-brands fa-github me-2 text-dark"></i>Kota Smart Map SDK</h6>
                                <p class="text-muted mb-0 small">Import GeoJSON and spatial coordinates directly into Mapbox or Leaflet client web/mobile views.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold"><i class="fa-solid fa-file-invoice me-2 text-secondary"></i>Municipal Ad Portal</h6>
                                <p class="text-muted mb-0 small">Submit advertising requests and book available billboard spaces programmatically via API.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OLA / Ather EV Integration -->
                <div class="tab-pane fade" id="ev" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">OLA / Ather EV Map Telemetry Integration</h5>
                            <p class="text-muted small">Standard GeoJSON endpoint yielding real-time road hazard alerts (potholes, open manholes) to display on vehicle dashboard navigation screens.</p>
                        </div>
                        <span class="badge bg-success py-2 px-3 fw-bold">ACTIVE</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Endpoint Details</h6>
                            <div class="d-flex align-items-center bg-light border p-2 rounded-3 mb-3">
                                <span class="badge bg-success me-2 px-3 py-2 text-uppercase">GET</span>
                                <span class="font-monospace text-dark">{{ url('/api/developer/ev-hazards') }}</span>
                            </div>

                            <h6 class="fw-bold mb-2">Query Parameters</h6>
                            <table class="table table-bordered table-sm small mb-4">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-monospace">min_severity</td>
                                        <td>String</td>
                                        <td>No</td>
                                        <td>Filter hazards by severity (e.g. `High`, `Critical`). Default is all.</td>
                                    </tr>
                                    <tr>
                                        <td class="font-monospace">bbox</td>
                                        <td>String</td>
                                        <td>No</td>
                                        <td>Bounding box filter format: `minLon,minLat,maxLon,maxLat`.</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="fw-bold mb-2">Sample cURL Command</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small"><code>curl -X GET "{{ url('/api/developer/ev-hazards') }}" \
  -H "Accept: application/json"</code></pre>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Sample GeoJSON Response Payload</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small overflow-auto" style="max-height: 380px;"><code>{
  "type": "FeatureCollection",
  "crs": {
    "type": "name",
    "properties": {
      "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
    }
  },
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [
          75.82736,
          25.18254
        ]
      },
      "properties": {
        "id": "12",
        "category": "Pothole",
        "severity": "High Risk",
        "description": "Deep open pothole on left lane near roundabout.",
        "status": "Verified",
        "reported_at": "2026-06-30T14:36:37Z",
        "verification_count": 8,
        "api_integration_source": "NagarRakshak Live Civic API"
      }
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Govt Billboards -->
                <div class="tab-pane fade" id="billboards" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Government Billboards Directory API</h5>
                            <p class="text-muted small">Fetch details, dimensions, locations, and booking status of municipal government digital display boards.</p>
                        </div>
                        <span class="badge bg-success py-2 px-3 fw-bold">ACTIVE</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Endpoint Details</h6>
                            <div class="d-flex align-items-center bg-light border p-2 rounded-3 mb-3">
                                <span class="badge bg-success me-2 px-3 py-2 text-uppercase">GET</span>
                                <span class="font-monospace text-dark">{{ url('/api/developer/billboards') }}</span>
                            </div>

                            <h6 class="fw-bold mb-2">Sample cURL Command</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small"><code>curl -X GET "{{ url('/api/developer/billboards') }}" \
  -H "Accept: application/json"</code></pre>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Sample Response Payload</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small overflow-auto" style="max-height: 380px;"><code>{
  "success": true,
  "count": 2,
  "data": [
    {
      "id": 1,
      "location_name": "Kalyan Circle Junction",
      "latitude": 25.18254,
      "longitude": 75.82736,
      "size": "20x10 ft",
      "type": "Digital LED Screen",
      "hourly_rate_inr": 250,
      "status": "Active",
      "current_advertisement": "Smart City Kota Digital Billboard Ad",
      "booking_contact": "municipal-ads@kota.gov.in"
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Ad Zones -->
                <div class="tab-pane fade" id="ads" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Commercial Advertisement Zones API</h5>
                            <p class="text-muted small">Access average daily footfall, pricing, and availability parameters for municipal approved advertising locations.</p>
                        </div>
                        <span class="badge bg-success py-2 px-3 fw-bold">ACTIVE</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Endpoint Details</h6>
                            <div class="d-flex align-items-center bg-light border p-2 rounded-3 mb-3">
                                <span class="badge bg-success me-2 px-3 py-2 text-uppercase">GET</span>
                                <span class="font-monospace text-dark">{{ url('/api/developer/advertisements') }}</span>
                            </div>

                            <h6 class="fw-bold mb-2">Sample cURL Command</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small"><code>curl -X GET "{{ url('/api/developer/advertisements') }}" \
  -H "Accept: application/json"</code></pre>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Sample Response Payload</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small overflow-auto" style="max-height: 380px;"><code>{
  "success": true,
  "count": 1,
  "data": [
    {
      "id": 101,
      "ward": "Ward No. 12 (Vigyan Nagar)",
      "zone_type": "Bus Shelter Pole",
      "latitude": 25.1554,
      "longitude": 75.8291,
      "daily_average_footfall": 45000,
      "monthly_booking_rate_inr": 15000,
      "availability": "Available"
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Road Notices -->
                <div class="tab-pane fade" id="notices" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Road Construction & Traffic Notices API</h5>
                            <p class="text-muted small">Fetch active municipal road repairs, detour layouts, and official department road notices.</p>
                        </div>
                        <span class="badge bg-success py-2 px-3 fw-bold">ACTIVE</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Endpoint Details</h6>
                            <div class="d-flex align-items-center bg-light border p-2 rounded-3 mb-3">
                                <span class="badge bg-success me-2 px-3 py-2 text-uppercase">GET</span>
                                <span class="font-monospace text-dark">{{ url('/api/developer/road-notices') }}</span>
                            </div>

                            <h6 class="fw-bold mb-2">Sample cURL Command</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small"><code>curl -X GET "{{ url('/api/developer/road-notices') }}" \
  -H "Accept: application/json"</code></pre>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem;">Sample Response Payload</h6>
                            <pre class="bg-dark text-light p-3 rounded-3 small overflow-auto" style="max-height: 380px;"><code>{
  "success": true,
  "count": 1,
  "data": [
    {
      "id": 501,
      "title": "Sewerage Line Construction Closure",
      "description": "Complete road closure at Kalyan Circle towards Aerodrome road due to urgent sewer line laying.",
      "severity": "High",
      "status": "Active",
      "affected_street": "Aerodrome Main Road",
      "started_at": "2026-06-28",
      "estimated_duration": "5 days"
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
