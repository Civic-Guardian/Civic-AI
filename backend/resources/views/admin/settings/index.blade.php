@extends('layouts.admin')

@section('title', 'NagarRakshak Portal Settings')

@section('content')
<div class="container-fluid" x-data="{ editingCategory: null }">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-green"><i class="fa-solid fa-gear"></i> System Settings</h2>
            <p class="text-muted">Manage active hazard categories, configure radius alerts thresholds, and brand system configurations.</p>
        </div>
    </div>

    <div class="row">
        <!-- left side configurations -->
        <div class="col-lg-5 mb-4">
            <!-- Alert Radius Settings -->
            <div class="card card-custom p-4 mb-4">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-bell text-green"></i> Alert & Escalation Thresholds</h5>
                
                <form action="{{ route('admin.settings.alerts') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small">Broadcast Alert Radius (meters)</label>
                        <input type="number" name="alert_radius" class="form-control form-control-sm" value="{{ $settings['alert_radius'] ?? '500' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Critical Reports threshold (verifications required)</label>
                        <input type="number" name="critical_threshold" class="form-control form-control-sm" value="{{ $settings['critical_threshold'] ?? '10' }}" required>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_escalation" id="autoEscalate" {{ ($settings['auto_escalation'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="autoEscalate">Auto Escalate to Ward officers</label>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-toggle-on text-green"></i> Feature Toggles</h6>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="gemini_analysis_enabled" id="geminiEnabled" {{ ($settings['gemini_analysis_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="geminiEnabled">
                            <i class="fa-solid fa-brain"></i> Enable AI Analysis (Gemini)
                        </label>
                        <div class="form-text" style="font-size: 0.68rem;">When disabled, image analysis will be skipped and reports will use manual categorization only.</div>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="petition_enabled" id="petitionEnabled" {{ ($settings['petition_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="petitionEnabled">
                            <i class="fa-solid fa-file-signature"></i> Enable Petition Generation
                        </label>
                        <div class="form-text" style="font-size: 0.68rem;">When disabled, the petition draft feature will be hidden from citizens.</div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-3 py-2 fw-semibold">Save Alert Settings</button>
                </form>
            </div>

            <!-- System Settings -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-city text-green"></i> System Customization</h5>
                
                <form action="{{ route('admin.settings.system') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small">Application Name</label>
                        <input type="text" name="app_name" class="form-control form-control-sm" value="{{ $settings['app_name'] ?? 'NagarRakshak' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Municipality Contact Email</label>
                        <input type="email" name="contact_email" class="form-control form-control-sm" value="{{ $settings['contact_email'] ?? 'support@nagarrakshak.org' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Logo Path / URL</label>
                        <input type="text" name="logo_path" class="form-control form-control-sm" value="{{ $settings['logo_path'] ?? '' }}" placeholder="Default logo loaded if empty">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small"><i class="fa-solid fa-map-location-dot text-green"></i> Google Maps API Key</label>
                        <input type="text" name="google_maps_api_key" class="form-control form-control-sm" value="{{ $settings['google_maps_api_key'] ?? '' }}" placeholder="AIzaSy...">
                        <div class="form-text" style="font-size: 0.7rem;">Used application-wide for map rendering and geocoding.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small"><i class="fa-solid fa-brain text-green"></i> Google Gemini API Key</label>
                        <input type="password" name="gemini_api_key" class="form-control form-control-sm" value="{{ $settings['gemini_api_key'] ?? '' }}" placeholder="AIzaSy...">
                        <div class="form-text" style="font-size: 0.7rem;">Used for dynamic image classification and petition drafting.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small"><i class="fa-solid fa-bucket text-green"></i> GCS Bucket Name</label>
                        <input type="text" name="gcs_bucket_name" class="form-control form-control-sm" value="{{ $settings['gcs_bucket_name'] ?? '' }}" placeholder="my-gcs-bucket">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small"><i class="fa-solid fa-key text-green"></i> GCS Service Account Key (JSON)</label>
                        <textarea name="gcs_key_file" class="form-control form-control-sm" rows="4" placeholder='{"type": "service_account", ...}'>{{ $settings['gcs_key_file'] ?? '' }}</textarea>
                        <div class="form-text" style="font-size: 0.7rem;">Paste the contents of your Google Cloud Service Account credentials JSON file.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small"><i class="fa-solid fa-bell text-green"></i> Firebase Project ID</label>
                        <input type="text" name="fcm_project_id" class="form-control form-control-sm" value="{{ $settings['fcm_project_id'] ?? '' }}" placeholder="my-firebase-project">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small"><i class="fa-solid fa-key text-green"></i> Firebase Service Account Key (JSON)</label>
                        <textarea name="fcm_service_account" class="form-control form-control-sm" rows="4" placeholder='{"type": "service_account", ...}'>{{ $settings['fcm_service_account'] ?? '' }}</textarea>
                        <div class="form-text" style="font-size: 0.7rem;">Paste the contents of your Google Firebase Service Account credentials JSON file.</div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-3 py-2 fw-semibold">Save Customizations</button>
                </form>
            </div>

            <!-- Maintenance & Updates settings -->
            <div class="card card-custom p-4 mt-4">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-screwdriver-wrench text-green"></i> Maintenance & App Updates</h5>
                
                <form action="{{ route('admin.settings.maintenance') }}" method="POST">
                    @csrf
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="maintenanceMode">
                            <strong>Enable Maintenance Mode</strong>
                        </label>
                        <div class="form-text" style="font-size: 0.68rem;">When enabled, the mobile app will show an "Under Maintenance" screen and block users from accessing the app.</div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label text-muted small">Required App Version</label>
                        <input type="text" name="app_version" class="form-control form-control-sm" value="{{ $settings['app_version'] ?? '1.2.0' }}" placeholder="e.g. 1.3.0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">APK Download Link</label>
                        <input type="url" name="app_update_url" class="form-control form-control-sm" value="{{ $settings['app_update_url'] ?? '' }}" placeholder="https://example.com/downloads/nagarrakshak-v1.3.0.apk">
                        <div class="form-text" style="font-size: 0.68rem;">Internal URL to download the new version's APK archive.</div>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="app_update_mandatory" id="updateMandatory" {{ ($settings['app_update_mandatory'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="updateMandatory">
                            <strong>Make Update Mandatory</strong>
                        </label>
                        <div class="form-text" style="font-size: 0.68rem;">If checked, users running an older version will be forced to update to the required version inside the app before proceeding.</div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-3 py-2 fw-semibold">Save Maintenance Settings</button>
                </form>
            </div>
        </div>

        <!-- Category CRUD Section -->
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-tags text-green"></i> Hazard Categories</h5>
                    <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
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
                                    <td colspan="5" class="text-center text-muted py-3">No categories found.</td>
                                </tr>
                            @else
                                @foreach($categories as $category)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                    <td><i class="fa-solid {{ $category->icon }} text-green fa-lg"></i></td>
                                    <td style="font-size:0.85rem;" class="text-secondary">{{ $category->description }}</td>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Edit button triggers Alpine config -->
                                            <button class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:0.75rem;" 
                                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                    @click="editingCategory = {
                                                        id: {{ $category->id }},
                                                        name: '{{ $category->name }}',
                                                        description: '{{ $category->description }}',
                                                        icon: '{{ $category->icon }}',
                                                        is_active: {{ $category->is_active ? 'true' : 'false' }}
                                                    }">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.settings.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:0.75rem;">Delete</button>
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

    <!-- Modal: Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom p-3 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold modal-title text-dark">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.settings.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Category Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Open Sewer" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">FontAwesome Icon Class</label>
                            <input type="text" name="icon" class="form-control form-control-sm" placeholder="e.g. fa-road" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Description</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Category detail specifications..."></textarea>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="newActive" checked>
                            <label class="form-check-label text-muted small" for="newActive">Category is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success px-3">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom p-3 border-0" x-show="editingCategory !== null">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold modal-title text-dark">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Dynamic form path using Alpine binding -->
                <form :action="'{{ route('admin.settings.categories.store') }}/' + editingCategory?.id" method="POST">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Category Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" :value="editingCategory?.name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">FontAwesome Icon Class</label>
                            <input type="text" name="icon" class="form-control form-control-sm" :value="editingCategory?.icon" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Description</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" x-text="editingCategory?.description"></textarea>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editActive" :checked="editingCategory?.is_active">
                            <label class="form-check-label text-muted small" for="editActive">Category is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success px-3">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
