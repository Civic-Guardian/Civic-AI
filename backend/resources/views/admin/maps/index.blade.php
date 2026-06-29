@extends('layouts.admin')

@section('title', 'Geo Map Center - Municipal Command')

@section('styles')
<style>
    .map-tools-panel {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 1000;
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid var(--fintech-border);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        padding: 1rem;
        width: 340px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .map-inspector-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid var(--fintech-border);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        padding: 1.25rem;
        width: 360px;
        display: none;
    }

    .custom-leaflet-marker {
        background: transparent;
        border: none;
    }

    .marker-pin-badge {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 3px solid #FFFFFF;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 15px;
        transition: transform 0.2s ease;
    }

    .marker-pin-badge:hover {
        transform: scale(1.18);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0 m-0 position-relative" id="mapContainer" style="height: calc(100vh - 85px); overflow: hidden;">
    
    <!-- Floating Toggle Button to Show Tools Panel when Collapsed -->
    <button type="button" class="btn btn-white bg-white shadow-sm border rounded-pill position-absolute px-3.5 py-2 fw-bold text-dark" id="toggleToolsBtn" style="top: 75px; left: 20px; z-index: 1005; display: none;">
        <i class="fa-solid fa-sliders text-primary me-2"></i> Show Map Tools
    </button>

    <!-- Left Floating Map Tools Bar -->
    <div class="map-tools-panel">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h6 class="fw-bold m-0 text-dark">Geo Map Tools</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white px-2.5 py-1" id="markerCount">0 hazards</span>
                <button type="button" class="btn-close btn-sm ms-1" id="hideToolsBtn" title="Hide Tools Panel"></button>
            </div>
        </div>

        <!-- Search location -->
        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-magnifying-glass me-1"></i> Jump to City / Street</label>
            <div class="input-group input-group-sm">
                <input type="text" id="mapSearchInput" class="form-control" placeholder="e.g. Talwandi, Kota">
                <button class="btn btn-primary" type="button" id="btnMapSearch"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>

        <!-- Marker Display Style Mode Switcher -->
        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-icons me-1 text-primary"></i> Marker Display Mode</label>
            <select class="form-select form-select-sm fw-semibold" id="filterMarkerStyle" style="border-color: var(--fintech-primary);">
                <option value="icons" selected>🏷️ Category Icons with Severity Badges</option>
                <option value="dots">🔴 Severity Color Dots</option>
                <option value="pins">📍 Classic Red Map Pins</option>
            </select>
        </div>

        <!-- Live Filters -->
        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-filter me-1"></i> Severity Filter</label>
            <select class="form-select form-select-sm" id="filterSeverity">
                <option value="All">All Severities</option>
                <option value="Critical">Critical</option>
                <option value="High Risk">High Risk</option>
                <option value="Medium Risk">Medium Risk</option>
                <option value="Low Risk">Low Risk</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-list-check me-1"></i> Status Filter</label>
            <select class="form-select form-select-sm" id="filterStatus">
                <option value="All">All Statuses</option>
                <option value="Pending">Pending Review</option>
                <option value="Verified">Verified</option>
                <option value="Resolved">Resolved</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-tags me-1"></i> Hazard Taxonomy</label>
            <select class="form-select form-select-sm" id="filterCategory">
                <option value="All">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Map View Mode & Layer Overlays -->
        <div class="mb-3 pt-2 border-top">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="fa-solid fa-earth-americas me-1 text-primary"></i> Map View Type</label>
            <select class="form-select form-select-sm mb-3" id="filterMapType">
                <option value="roadmap" selected>🗺️ Standard Roadmap</option>
                <option value="satellite">🛰️ Satellite / Hybrid View</option>
            </select>

            <label class="form-label text-muted small fw-semibold mb-2"><i class="fa-solid fa-sliders me-1"></i> Admin Overlays & Clean View</label>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="toggleCleanMap">
                <label class="form-check-label small text-dark fw-medium" for="toggleCleanMap">
                    <i class="fa-solid fa-eye-slash text-danger me-1"></i> Clean Map (Hide Shops & POIs)
                </label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="toggleRadiusOverlay">
                <label class="form-check-label small text-dark fw-medium" for="toggleRadiusOverlay">
                    <i class="fa-solid fa-satellite-dish text-primary me-1"></i> Broadcast Radius Circles (500m)
                </label>
            </div>
        </div>

        <div class="d-grid gap-2 pt-2 border-top">
            <button class="btn btn-sm btn-outline-secondary rounded-3" id="resetFilters"><i class="fa-solid fa-rotate-right me-1"></i> Reset All Tools</button>
            <button class="btn btn-sm btn-outline-primary rounded-3" id="fullscreenBtn"><i class="fa-solid fa-expand me-1"></i> Toggle Fullscreen</button>
        </div>
    </div>

    <!-- Right Floating Hazard Inspector Drawer -->
    <div class="map-inspector-panel" id="hazardInspector">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-cat text-primary"></i> Case Inspector
            </h6>
            <button type="button" class="btn-close btn-sm" id="closeInspector"></button>
        </div>

        <div id="inspectorContent">
            <!-- Dynamic hazard details inserted via JavaScript -->
        </div>
    </div>

    <!-- The Fullscreen Map Canvas -->
    <div id="fullScreenMap" style="width: 100%; height: 100%; z-index: 1;"></div>
</div>
@endsection

@section('scripts')
<script>
    var hazards = {!! json_encode($hazards) !!};
    var activeMarkers = [];
    var radiusCircles = [];
    var showRadiusCircles = false;

    var currentMarkerStyle = 'icons';
    var currentSeverity = 'All';
    var currentStatus = 'All';
    var currentCategory = 'All';
    var currentMapType = 'roadmap';
    var isCleanMap = false;

    var countLabel = document.getElementById('markerCount');
    var inspector = document.getElementById('hazardInspector');
    var inspectorContent = document.getElementById('inspectorContent');

    var toolsPanel = document.querySelector('.map-tools-panel');
    var toggleToolsBtn = document.getElementById('toggleToolsBtn');
    var hideToolsBtn = document.getElementById('hideToolsBtn');

    if (hideToolsBtn) {
        hideToolsBtn.addEventListener('click', function() {
            toolsPanel.style.display = 'none';
            toggleToolsBtn.style.display = 'inline-flex';
        });
    }

    if (toggleToolsBtn) {
        toggleToolsBtn.addEventListener('click', function() {
            toolsPanel.style.display = 'block';
            toggleToolsBtn.style.display = 'none';
        });
    }

    // Clean map styles for Google Maps (Hides Shops, Businesses & Transit POIs)
    var cleanMapStyles = [
        {
            featureType: "poi",
            elementType: "labels",
            stylers: [{ visibility: "off" }]
        },
        {
            featureType: "poi.business",
            stylers: [{ visibility: "off" }]
        },
        {
            featureType: "transit",
            elementType: "labels.icon",
            stylers: [{ visibility: "off" }]
        }
    ];

    function applyMapStylesAndType() {
        if (typeof gMap !== 'undefined' && gMap) {
            if (currentMapType === 'satellite') {
                gMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
            } else {
                gMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
            }
            gMap.setOptions({ styles: isCleanMap ? cleanMapStyles : [] });
        } else if (typeof lMap !== 'undefined' && lMap) {
            if (currentMapType === 'satellite') {
                if (!window.satLayer) {
                    window.satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri'
                    });
                }
                lMap.addLayer(window.satLayer);
            } else if (window.satLayer) {
                lMap.removeLayer(window.satLayer);
            }
        }
    }

    function getMarkerColor(severity, status) {
        if (status === 'Resolved') return '#64748B';
        if (severity === 'Critical') return '#E11D48';
        if (severity === 'High Risk') return '#F59E0B';
        if (severity === 'Medium Risk') return '#FBBF24';
        return '#059669';
    }

    function getCategoryIconClass(category) {
        var cat = (category || '').toLowerCase();
        if (cat.includes('pothole') || cat.includes('road')) return 'fa-road';
        if (cat.includes('water') || cat.includes('leak') || cat.includes('drain')) return 'fa-droplet';
        if (cat.includes('light') || cat.includes('lamp')) return 'fa-lightbulb';
        if (cat.includes('garbage') || cat.includes('waste') || cat.includes('trash')) return 'fa-trash-can';
        if (cat.includes('electric') || cat.includes('wire') || cat.includes('power')) return 'fa-bolt';
        if (cat.includes('tree') || cat.includes('branch')) return 'fa-tree';
        return 'fa-triangle-exclamation';
    }

    function createCustomGoogleIcon(color, category) {
        var iconClass = getCategoryIconClass(category);
        var iconChar = '⚠️';
        if (iconClass === 'fa-road') iconChar = '🛣️';
        else if (iconClass === 'fa-droplet') iconChar = '💧';
        else if (iconClass === 'fa-lightbulb') iconChar = '💡';
        else if (iconClass === 'fa-trash-can') iconChar = '🗑️';
        else if (iconClass === 'fa-bolt') iconChar = '⚡';
        else if (iconClass === 'fa-tree') iconChar = '🌳';

        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">' +
            '<circle cx="20" cy="20" r="18" fill="' + color + '" stroke="#FFFFFF" stroke-width="3"/>' +
            '<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-size="16" fill="#FFFFFF">' + iconChar + '</text>' +
            '</svg>';
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
    }

    function openInspector(hazard, color) {
        var detailUrl = "{{ route('admin.cases.show', ':id') }}".replace(':id', hazard.id);
        var imageUrl = hazard.thumbnail_url || (hazard.image_path ? (hazard.image_path.startsWith('http') ? hazard.image_path : "{{ asset('storage') }}/" + hazard.image_path.split(',')[0]) : null);
        var imgHtml = imageUrl ? 
            '<img src="' + imageUrl + '" class="rounded-3 w-100 mb-3" style="height: 160px; object-fit: cover;" alt="Hazard Evidence">' : 
            '<div class="rounded-3 bg-light d-flex align-items-center justify-content-center mb-3 text-muted" style="height: 120px;"><i class="fa-regular fa-image fa-2x"></i></div>';

        inspectorContent.innerHTML = 
            imgHtml +
            '<div class="d-flex align-items-center justify-content-between mb-2">' +
                '<h5 class="fw-bold text-dark m-0"><i class="fa-solid ' + getCategoryIconClass(hazard.category) + ' me-1 text-primary"></i> ' + hazard.category + '</h5>' +
                '<span class="badge" style="background-color:' + color + '; color:#fff;">' + hazard.severity + '</span>' +
            '</div>' +
            '<p class="text-muted small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> ' + hazard.location_name + '</p>' +
            '<p class="text-secondary small leading-normal mb-3" style="max-height: 80px; overflow-y: auto;">' + (hazard.description || 'No detailed description logged.') + '</p>' +
            '<div class="p-2.5 bg-light rounded-3 mb-3 d-flex justify-content-around text-center small fw-semibold">' +
                '<div><span class="text-muted d-block" style="font-size:0.7rem;">Status</span><span class="text-dark">' + hazard.status + '</span></div>' +
                '<div class="border-start"></div>' +
                '<div><span class="text-muted d-block" style="font-size:0.7rem;">Votes</span><span class="text-success">' + (hazard.verification_count || 0) + '</span></div>' +
            '</div>' +
            '<a href="' + detailUrl + '" class="btn btn-primary w-100 py-2 rounded-3 fw-bold"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Open Full Case File</a>';

        inspector.style.display = 'block';
    }

    document.getElementById('closeInspector').addEventListener('click', function() {
        inspector.style.display = 'none';
    });

    // Jump to City / Street Search Functionality
    function performMapSearch() {
        var query = document.getElementById('mapSearchInput').value.trim();
        if (!query) return;
        
        var searchBtn = document.getElementById('btnMapSearch');
        searchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                searchBtn.innerHTML = '<i class="fa-solid fa-arrow-right"></i>';
                if (data && data.length > 0) {
                    var lat = parseFloat(data[0].lat);
                    var lon = parseFloat(data[0].lon);
                    if (typeof gMap !== 'undefined' && gMap) {
                        gMap.setCenter({lat: lat, lng: lon});
                        gMap.setZoom(15);
                    } else if (typeof lMap !== 'undefined' && lMap) {
                        lMap.setView([lat, lon], 15);
                    }
                } else {
                    alert('Location not found. Please try another street or city name.');
                }
            })
            .catch(err => {
                searchBtn.innerHTML = '<i class="fa-solid fa-arrow-right"></i>';
                console.error('Geocoding error:', err);
                alert('Search request failed. Please check your network connection.');
            });
    }

    document.getElementById('btnMapSearch').addEventListener('click', performMapSearch);
    document.getElementById('mapSearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') performMapSearch();
    });

    @php
        $gmapKey = \App\Services\SettingsService::get('google_maps_api_key');
    @endphp

    @if($gmapKey)
        // Google Maps implementation
        var gMap;

        window.initGoogleMap = function() {
            gMap = new google.maps.Map(document.getElementById('fullScreenMap'), {
                center: {lat: 25.18, lng: 75.83},
                zoom: 13,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: false
            });

            setupFilterListeners(drawGoogleMarkers);
            drawGoogleMarkers();
        };

        function drawGoogleMarkers() {
            activeMarkers.forEach(m => m.setMap(null));
            radiusCircles.forEach(c => c.setMap(null));
            activeMarkers = [];
            radiusCircles = [];
            let count = 0;

            applyMapStylesAndType();

            hazards.forEach(hazard => {
                if (
                    (currentSeverity === 'All' || hazard.severity === currentSeverity) &&
                    (currentStatus === 'All' || hazard.status === currentStatus) &&
                    (currentCategory === 'All' || hazard.category === currentCategory)
                ) {
                    let color = getMarkerColor(hazard.severity, hazard.status);
                    let lat = parseFloat(hazard.latitude);
                    let lng = parseFloat(hazard.longitude);

                    let markerOptions = {
                        position: {lat: lat, lng: lng},
                        map: gMap,
                        title: hazard.category
                    };

                    if (currentMarkerStyle === 'icons') {
                        markerOptions.icon = {
                            url: createCustomGoogleIcon(color, hazard.category),
                            scaledSize: new google.maps.Size(38, 38),
                            anchor: new google.maps.Point(19, 19)
                        };
                    } else if (currentMarkerStyle === 'dots') {
                        markerOptions.icon = {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: color,
                            fillOpacity: 1,
                            strokeColor: '#FFFFFF',
                            strokeWeight: 3
                        };
                    } else {
                        markerOptions.icon = null;
                    }

                    let marker = new google.maps.Marker(markerOptions);

                    if (showRadiusCircles) {
                        let circle = new google.maps.Circle({
                            strokeColor: color,
                            strokeOpacity: 0.8,
                            strokeWeight: 1.5,
                            fillColor: color,
                            fillOpacity: 0.15,
                            map: gMap,
                            center: {lat: lat, lng: lng},
                            radius: 500
                        });
                        radiusCircles.push(circle);
                    }

                    marker.addListener('click', function() {
                        openInspector(hazard, color);
                    });

                    activeMarkers.push(marker);
                    count++;
                }
            });
            countLabel.innerText = count + " hazards";
        }

        var script = document.createElement('script');
        script.src = "https://maps.googleapis.com/maps/api/js?key={{ $gmapKey }}&callback=initGoogleMap";
        script.async = true;
        document.head.appendChild(script);
    @else
        // Leaflet implementation
        var lMap;
        var markersLayer;

        function initLeafletMap() {
            lMap = L.map('fullScreenMap', {zoomControl: false}).setView([25.18, 75.83], 13);
            L.control.zoom({ position: 'bottomright' }).addTo(lMap);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(lMap);

            markersLayer = L.layerGroup().addTo(lMap);
            setupFilterListeners(drawLeafletMarkers);
            drawLeafletMarkers();
        }

        function drawLeafletMarkers() {
            markersLayer.clearLayers();
            let count = 0;

            applyMapStylesAndType();

            hazards.forEach(hazard => {
                if (
                    (currentSeverity === 'All' || hazard.severity === currentSeverity) &&
                    (currentStatus === 'All' || hazard.status === currentStatus) &&
                    (currentCategory === 'All' || hazard.category === currentCategory)
                ) {
                    let color = getMarkerColor(hazard.severity, hazard.status);
                    let lat = parseFloat(hazard.latitude);
                    let lng = parseFloat(hazard.longitude);

                    let marker;
                    if (currentMarkerStyle === 'icons') {
                        let iconClass = getCategoryIconClass(hazard.category);
                        let customIcon = L.divIcon({
                            className: 'custom-leaflet-marker',
                            html: '<div class="marker-pin-badge" style="background-color:' + color + ';"><i class="fa-solid ' + iconClass + '"></i></div>',
                            iconSize: [38, 38],
                            iconAnchor: [19, 19]
                        });
                        marker = L.marker([lat, lng], {icon: customIcon});
                    } else if (currentMarkerStyle === 'dots') {
                        marker = L.circleMarker([lat, lng], {
                            radius: 10,
                            fillColor: color,
                            color: '#FFFFFF',
                            weight: 2.5,
                            opacity: 1,
                            fillOpacity: 0.95
                        });
                    } else {
                        let redIcon = L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        });
                        marker = L.marker([lat, lng], {icon: redIcon});
                    }

                    if (showRadiusCircles) {
                        let circle = L.circle([lat, lng], {
                            radius: 500,
                            color: color,
                            weight: 1.5,
                            fillColor: color,
                            fillOpacity: 0.12
                        });
                        markersLayer.addLayer(circle);
                    }

                    marker.on('click', function() {
                        openInspector(hazard, color);
                    });

                    markersLayer.addLayer(marker);
                    count++;
                }
            });
            countLabel.innerText = count + " hazards";
        }

        document.addEventListener('DOMContentLoaded', initLeafletMap);
    @endif

    function setupFilterListeners(drawFunction) {
        document.getElementById('filterMarkerStyle').addEventListener('change', function(e) {
            currentMarkerStyle = e.target.value;
            drawFunction();
        });
        document.getElementById('filterMapType').addEventListener('change', function(e) {
            currentMapType = e.target.value;
            drawFunction();
        });
        document.getElementById('toggleCleanMap').addEventListener('change', function(e) {
            isCleanMap = e.target.checked;
            drawFunction();
        });
        document.getElementById('filterSeverity').addEventListener('change', function(e) {
            currentSeverity = e.target.value;
            drawFunction();
        });
        document.getElementById('filterStatus').addEventListener('change', function(e) {
            currentStatus = e.target.value;
            drawFunction();
        });
        document.getElementById('filterCategory').addEventListener('change', function(e) {
            currentCategory = e.target.value;
            drawFunction();
        });
        document.getElementById('toggleRadiusOverlay').addEventListener('change', function(e) {
            showRadiusCircles = e.target.checked;
            drawFunction();
        });
        document.getElementById('resetFilters').addEventListener('click', function() {
            document.getElementById('filterMarkerStyle').value = 'icons';
            document.getElementById('filterMapType').value = 'roadmap';
            document.getElementById('toggleCleanMap').checked = false;
            document.getElementById('filterSeverity').value = 'All';
            document.getElementById('filterStatus').value = 'All';
            document.getElementById('filterCategory').value = 'All';
            document.getElementById('toggleRadiusOverlay').checked = false;
            currentMarkerStyle = 'icons'; currentMapType = 'roadmap'; isCleanMap = false; currentSeverity = 'All'; currentStatus = 'All'; currentCategory = 'All'; showRadiusCircles = false;
            drawFunction();
        });
    }

    // Fullscreen Toggle
    var mapContainer = document.getElementById('mapContainer');
    document.getElementById('fullscreenBtn').addEventListener('click', function() {
        if (!document.fullscreenElement) {
            mapContainer.requestFullscreen().catch(err => console.error(err));
        } else {
            document.exitFullscreen();
        }
    });
</script>
@endsection
