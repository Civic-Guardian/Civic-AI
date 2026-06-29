@extends('layouts.admin')

@section('title', 'System Settings & Portal Configuration')

@section('content')
<div class="container-fluid" x-data="{ activeTab: 'alerts', isLocked: true, editingCategory: null }">
    <!-- Header Title -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">System Configuration</h2>
            <p class="text-secondary" style="font-size: 0.95rem;">Manage alert threshold radius, GCP APK release builds, hazard taxonomy, and API credentials.</p>
        </div>
    </div>

    <!-- Security Lock Banner (Top Guard) -->
    <div class="card card-custom p-3 mb-4 border-0 shadow-sm" :class="isLocked ? 'bg-light-subtle border-start border-4 border-danger' : 'bg-primary-subtle border-start border-4 border-success'">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" :class="isLocked ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'" style="width: 44px; height: 44px;">
                    <i class="fa-solid" :class="isLocked ? 'fa-lock fa-lg' : 'fa-lock-open fa-lg'"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0 text-dark" x-text="isLocked ? 'Configuration Fields Locked' : 'Configuration Fields Unlocked'"></h6>
                    <small class="text-muted" x-text="isLocked ? 'Safety protection active. Editing is disabled to prevent accidental modifications.' : 'Editing is active. Click Lock All Fields once changes are complete.'"></small>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-sm px-4 py-2 rounded-pill fw-bold transition-all" 
                        :class="isLocked ? 'btn-outline-danger' : 'btn-success'" 
                        @click="isLocked = !isLocked">
                    <i class="fa-solid me-2" :class="isLocked ? 'fa-key' : 'fa-lock'"></i>
                    <span x-text="isLocked ? 'Unlock Settings to Edit' : 'Lock All Fields'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card card-custom p-2 mb-4">
        <ul class="nav nav-pills nav-fill gap-2" id="settingsTabs">
            <li class="nav-item">
                <button class="nav-link py-2.5 px-3 rounded-3 fw-semibold transition-all d-flex align-items-center justify-content-center gap-2" 
                        :class="activeTab === 'alerts' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light'" 
                        @click="activeTab = 'alerts'">
                    <i class="fa-solid fa-bell"></i> Alert & Escalation Thresholds
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-2.5 px-3 rounded-3 fw-semibold transition-all d-flex align-items-center justify-content-center gap-2" 
                        :class="activeTab === 'apk' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light'" 
                        @click="activeTab = 'apk'">
                    <i class="fa-solid fa-android"></i> App Release & GCP APK Manager
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-2.5 px-3 rounded-3 fw-semibold transition-all d-flex align-items-center justify-content-center gap-2" 
                        :class="activeTab === 'categories' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light'" 
                        @click="activeTab = 'categories'">
                    <i class="fa-solid fa-tags"></i> Hazard Taxonomy
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-2.5 px-3 rounded-3 fw-semibold transition-all d-flex align-items-center justify-content-center gap-2" 
                        :class="activeTab === 'system' ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light'" 
                        @click="activeTab = 'system'">
                    <i class="fa-solid fa-key"></i> API Credentials & System
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-2.5 px-3 rounded-3 fw-semibold transition-all d-flex align-items-center justify-content-center gap-2" 
                        :class="activeTab === 'showcase' ? 'active bg-warning text-dark shadow-sm' : 'text-secondary hover-bg-light'" 
                        @click="activeTab = 'showcase'">
                    <i class="fa-solid fa-trophy text-warning"></i> Hackathon Showcase Incidents
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB 1: Alert & Escalation Thresholds -->
    <div x-show="activeTab === 'alerts'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card card-custom p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-bell text-primary me-2"></i> Alert & Escalation Thresholds</h5>
                            <small class="text-muted">Configure geo-fencing radius for citizen broadcasts and auto-escalation parameters.</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2"><i class="fa-solid fa-satellite-dish me-1"></i> Live Radius Broadcasts</span>
                    </div>

                    <form action="{{ route('admin.settings.alerts') }}" method="POST">
                        @csrf
                        <fieldset :disabled="isLocked">
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Broadcast Alert Radius (meters)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-compass-drafting"></i></span>
                                    <input type="number" name="alert_radius" class="form-control" value="{{ $settings['alert_radius'] ?? '500' }}" required>
                                    <span class="input-group-text bg-light text-muted">meters</span>
                                </div>
                                <div class="form-text" style="font-size: 0.75rem;">Citizens within this distance will receive instant real-time push broadcasts whenever a new hazard report is logged.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Critical Reports Threshold (Verifications Required)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-users-viewfinder"></i></span>
                                    <input type="number" name="critical_threshold" class="form-control" value="{{ $settings['critical_threshold'] ?? '10' }}" required>
                                    <span class="input-group-text bg-light text-muted">votes</span>
                                </div>
                                <div class="form-text" style="font-size: 0.75rem;">Minimum community corroborations required before escalating to Critical Severity status.</div>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-4 border">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="auto_escalation" id="autoEscalate" {{ ($settings['auto_escalation'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark ms-2" for="autoEscalate">Auto Escalate to Ward Officers</label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size: 0.75rem;">Automatically notify municipal divisional engineers when thresholds are crossed.</small>
                            </div>

                            <h6 class="fw-bold text-dark mb-3 mt-4"><i class="fa-solid fa-toggle-on text-primary me-2"></i> Feature Toggles</h6>

                            <div class="p-3 bg-light rounded-3 mb-3 border">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="gemini_analysis_enabled" id="geminiEnabled" {{ ($settings['gemini_analysis_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark ms-2" for="geminiEnabled">
                                        <i class="fa-solid fa-brain me-1 text-primary"></i> Enable AI Analysis (Gemini Vision)
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size: 0.75rem;">When disabled, image analysis will be skipped and mobile submissions will use manual categorization.</small>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-4 border">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="petition_enabled" id="petitionEnabled" {{ ($settings['petition_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark ms-2" for="petitionEnabled">
                                        <i class="fa-solid fa-file-signature me-1 text-primary"></i> Enable Petition Generation
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size: 0.75rem;">When disabled, automated petition drafting will be hidden from citizen mobile applications.</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Alert Settings
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: App Release & GCP APK Manager -->
    <div x-show="activeTab === 'apk'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card card-custom p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-android text-success me-2"></i> App Release & GCP Storage Upload</h5>
                            <small class="text-muted">Deploy new Android APK build releases directly to Google Cloud Storage for citizen target updates.</small>
                        </div>
                        <span class="badge bg-success-subtle text-success px-3 py-2"><i class="fa-solid fa-cloud-arrow-up me-1"></i> GCP Storage Direct</span>
                    </div>

                    <form action="{{ route('admin.settings.maintenance') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <fieldset :disabled="isLocked">
                            <div class="p-3 bg-light rounded-3 mb-4 border">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark ms-2" for="maintenanceMode">
                                        Enable Emergency Maintenance Mode
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size: 0.75rem;">Blocks citizen apps with an "Under Maintenance" dialog screen during database operations.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Target Release Version</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-code-branch"></i></span>
                                    <input type="text" name="app_version" class="form-control" value="{{ $settings['app_version'] ?? '1.2.0' }}" placeholder="e.g. 1.3.0" required>
                                </div>
                                <div class="form-text" style="font-size: 0.75rem;">Build version string matching android/app/build.gradle.</div>
                            </div>

                            <!-- GCP Upload Option -->
                            <div class="mb-4 p-4 border rounded-4 bg-white shadow-sm">
                                <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-cloud-arrow-up text-primary fa-lg"></i> Upload New APK Build file (Target GCP Storage)
                                </label>
                                <input type="file" name="apk_file" accept=".apk,.zip,.bin" class="form-control mb-2">
                                <div class="form-text text-secondary" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-circle-info me-1"></i> Uploading an APK file will store the binary on <strong>Google Cloud Storage (GCS)</strong> and generate a CDN download target link for user updates.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Or Direct CDN / Storage URL</label>
                                <input type="text" name="app_update_url" class="form-control form-control-sm" value="{{ $settings['app_update_url'] ?? '' }}" placeholder="https://storage.googleapis.com/your-bucket/apks/release.apk">
                                <div class="form-text" style="font-size: 0.75rem;">Active download target URL returned to citizen mobile devices during version checks.</div>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-4 border">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="app_update_mandatory" id="updateMandatory" {{ ($settings['app_update_mandatory'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark ms-2" for="updateMandatory">
                                        Force Mandatory Update
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size: 0.75rem;">If enabled, mobile app will prevent navigation until the citizen updates to this build.</small>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2.5 rounded-3 fw-bold">
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload APK to GCP & Save Target Release
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Hazard Taxonomy -->
    <div x-show="activeTab === 'categories'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card card-custom p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-tags text-primary me-2"></i> Hazard Taxonomy & Categories</h5>
                            <small class="text-muted">Manage active civic issue classifications synchronized dynamically with mobile report forms.</small>
                        </div>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addCategoryModal" :disabled="isLocked">
                            <i class="fa-solid fa-plus me-1"></i> Add Category
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Icon</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($categories->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No hazard categories registered in taxonomy.</td>
                                    </tr>
                                @else
                                    @foreach($categories as $category)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $category->name }}</td>
                                        <td>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px;">
                                                <i class="fa-solid {{ $category->icon }}"></i>
                                            </div>
                                        </td>
                                        <td style="font-size:0.85rem;" class="text-secondary">{{ $category->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}-subtle text-{{ $category->is_active ? 'success' : 'secondary' }} border border-{{ $category->is_active ? 'success' : 'secondary' }}-subtle">
                                                {{ $category->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:0.75rem;" 
                                                        @click="editingCategory = {{ json_encode($category) }}" 
                                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal" :disabled="isLocked">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.settings.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.75rem;" :disabled="isLocked">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: API Credentials & System -->
    <div x-show="activeTab === 'system'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card card-custom p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-key text-primary me-2"></i> API Credentials & Service Accounts</h5>
                            <small class="text-muted">Configure cloud service credentials for Google Gemini AI Vision, GCP Storage, and Firebase Cloud Messaging.</small>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.system') }}" method="POST">
                        @csrf
                        <fieldset :disabled="isLocked">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Application Name</label>
                                <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'NagarRakshak' }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Municipality Support Email</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@nagarrakshak.org' }}" required>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-brain text-primary me-1"></i> Google Gemini API Key</label>
                                <input type="password" name="gemini_api_key" class="form-control" value="{{ $settings['gemini_api_key'] ?? '' }}" placeholder="AIzaSy...">
                                <div class="form-text" style="font-size: 0.72rem;">Used application-wide for vision classification and petition drafting.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-map-location-dot text-primary me-1"></i> Google Maps API Key</label>
                                <input type="password" name="google_maps_api_key" class="form-control" value="{{ $settings['google_maps_api_key'] ?? '' }}" placeholder="AIzaSy...">
                                <div class="form-text" style="font-size: 0.72rem;">Used application-wide for map rendering, geocoding, and mobile location services.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-box-archive text-primary me-1"></i> GCP Storage Bucket Name</label>
                                <input type="text" name="gcs_bucket_name" class="form-control" value="{{ $settings['gcs_bucket_name'] ?? '' }}" placeholder="my-gcs-bucket">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-shield-cat text-primary me-1"></i> GCP Service Account JSON Credentials</label>
                                <textarea name="gcs_key_file" class="form-control font-monospace" rows="4" style="font-size: 0.8rem;" placeholder='{"type": "service_account", ...}'>{{ $settings['gcs_key_file'] ?? '' }}</textarea>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-fire text-warning me-1"></i> Firebase Cloud Messaging (FCM) Credentials</h6>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-bell text-primary me-1"></i> Firebase Project ID</label>
                                <input type="text" name="fcm_project_id" class="form-control" value="{{ $settings['fcm_project_id'] ?? '' }}" placeholder="my-firebase-project">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark"><i class="fa-solid fa-key text-primary me-1"></i> Firebase Service Account JSON Credentials</label>
                                <textarea name="fcm_service_account" class="form-control font-monospace" rows="4" style="font-size: 0.8rem;" placeholder='{"type": "service_account", ...}'>{{ $settings['fcm_service_account'] ?? '' }}</textarea>
                                <div class="form-text" style="font-size: 0.72rem;">Paste the contents of your Google Firebase Service Account credentials JSON file for real-time push notifications.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Cloud Credentials
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: Hackathon Showcase Incidents -->
    <div x-show="activeTab === 'showcase'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card card-custom p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-trophy text-warning me-2"></i> Hackathon Showcase Incident Media</h5>
                            <p class="text-muted small m-0">Upload real incident images &amp; customize titles for the "Accidents We Could Have Prevented With a Timely Alert" section on the public homepage (welcome.blade.php).</p>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 fw-bold"><i class="fa-solid fa-bolt me-1"></i> Live Hackathon Demo Mode</span>
                    </div>

                    <form action="{{ route('admin.settings.showcase') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <fieldset :disabled="isLocked">
                            <div class="row g-4 mb-4">
                                @php
                                    $defaultTitles = [
                                        1 => 'Biker Killed Hitting Unmarked Pothole on Aerodrome Road',
                                        2 => 'Three Killed as Car Plunges into Uncovered Municipal Drain',
                                        3 => '11 Accidents on Single Andheri Stretch in 30 Days',
                                        4 => 'BMTC Bus Partially Swallowed by Road Sinkhole on ORR',
                                        5 => 'Man Electrocuted Walking Through Flooded Underpass',
                                        6 => '9-Year-Old Girl Falls into Drain Outside School'
                                    ];
                                    $defaultLocations = [
                                        1 => 'Kota, Rajasthan',
                                        2 => 'Pune, Maharashtra',
                                        3 => 'Mumbai, Maharashtra',
                                        4 => 'Bengaluru, Karnataka',
                                        5 => 'New Delhi',
                                        6 => 'Lucknow, UP'
                                    ];
                                @endphp

                                @for($i = 1; $i <= 6; $i++)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded-4 p-3 bg-light-subtle h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge bg-dark text-white font-monospace">Showcase Slot #{{ $i }}</span>
                                                    @if(isset($settings["showcase_{$i}_image"]))
                                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-check me-1"></i> Custom Image Set</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-image me-1"></i> Default Placeholder</span>
                                                    @endif
                                                </div>

                                                @if(isset($settings["showcase_{$i}_image"]))
                                                    <div class="mb-3 rounded-3 overflow-hidden border bg-white" style="height: 140px;">
                                                        <img src="{{ $settings["showcase_{$i}_image"] }}" class="w-100 h-100" style="object-fit: cover;" alt="Showcase {{ $i }}">
                                                    </div>
                                                @endif

                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Incident Title</label>
                                                    <input type="text" name="showcase_{{ $i }}_title" class="form-control form-control-sm" value="{{ $settings["showcase_{$i}_title"] ?? $defaultTitles[$i] }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Location Tag</label>
                                                    <input type="text" name="showcase_{{ $i }}_location" class="form-control form-control-sm" value="{{ $settings["showcase_{$i}_location"] ?? $defaultLocations[$i] }}">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="form-label small fw-bold text-primary mb-1"><i class="fa-solid fa-camera me-1"></i> Upload Evidence Photo</label>
                                                <input type="file" name="showcase_{{ $i }}_image" class="form-control form-control-sm" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <button type="submit" class="btn btn-warning w-100 py-2.5 rounded-3 fw-bold text-dark shadow-sm">
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i> Update Hackathon Showcase Incidents &amp; Media
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Add Hazard Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.settings.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Water Leakage" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">FontAwesome Icon Class</label>
                            <input type="text" name="icon" class="form-control" placeholder="fa-droplet" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="newIsActive" checked>
                            <label class="form-check-label fw-semibold text-dark ms-2" for="newIsActive">Set Active Immediately</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow" x-show="editingCategory">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form :action="'{{ url('admin/settings/categories') }}/' + (editingCategory ? editingCategory.id : '')" method="POST">
                    @csrf
                    <div class="modal-body" x-if="editingCategory">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Category Name</label>
                            <input type="text" name="name" class="form-control" :value="editingCategory ? editingCategory.name : ''" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">FontAwesome Icon Class</label>
                            <input type="text" name="icon" class="form-control" :value="editingCategory ? editingCategory.icon : ''" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Description</label>
                            <textarea name="description" class="form-control" rows="2" x-text="editingCategory ? editingCategory.description : ''"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" :checked="editingCategory && editingCategory.is_active">
                            <label class="form-check-label fw-semibold text-dark ms-2" for="editIsActive">Active Status</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
