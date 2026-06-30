@extends('layouts.municipality')

@section('title', 'Manage Case #' . $case->id . ' - NagarRakshak')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('municipality.cases.index') }}" class="text-decoration-none text-success fw-semibold"><i class="fa-solid fa-arrow-left me-1"></i> Back to Cases</a>
    </div>

    <!-- Alert toasts -->
    @if(session('success'))
    <div class="alert alert-success border-0 bg-success-subtle text-success rounded-3 mb-4">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        <!-- Left details panel -->
        <div class="col-lg-8">
            <!-- Case Main Details Card -->
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-0">{{ $case->category }} Case</h4>
                        <p class="text-secondary small mb-0">Case ID: #{{ $case->id }} &bull; Reported {{ $case->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        @php
                            $statusColor = $case->status === 'Resolved' ? 'bg-success' : ($case->status === 'In Progress' ? 'bg-primary' : 'bg-warning text-dark');
                        @endphp
                        <span class="badge {{ $statusColor }} px-3 py-2 fs-6 rounded-1">{{ $case->status }}</span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-secondary small fw-bold text-uppercase m-0">Location coordinates</label>
                        <p class="text-dark fw-semibold"><i class="fa-solid fa-location-crosshairs me-1 text-primary"></i> {{ $case->latitude }}, {{ $case->longitude }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary small fw-bold text-uppercase m-0">Severity Level</label>
                        <p class="text-dark fw-semibold"><span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">{{ $case->severity }}</span></p>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-secondary small fw-bold text-uppercase m-0">Description</label>
                        <p class="text-dark bg-light p-3 rounded border" style="line-height: 1.6;">{{ $case->description }}</p>
                    </div>
                </div>

                <!-- Evidence Photo Gallery -->
                @php
                    $images = $case->image_urls;
                @endphp
                <div class="border-top pt-3">
                    <h6 class="fw-bold mb-3"><i class="fa-regular fa-images me-2 text-primary"></i>Evidence Photo Gallery ({{ count($images) }} file(s))</h6>
                    
                    @if(count($images) > 1)
                    <div class="alert alert-success border-0 bg-success-subtle text-success p-2 rounded-3 mb-3 small d-flex align-items-center">
                        <i class="fa-solid fa-circle-info me-2"></i> This is a <strong>merged duplicate report</strong> backed by multiple citizen contributions.
                    </div>
                    @endif

                    <div class="row row-cols-2 row-cols-md-4 g-2">
                        @forelse($images as $img)
                        <div class="col">
                            <div class="border rounded overflow-hidden shadow-sm h-100 bg-light" style="max-height: 140px;">
                                <img src="{{ $img }}" class="img-fluid w-100 h-100 object-fit-cover" alt="Evidence photo" style="height: 120px; object-fit: cover;" onclick="window.open(this.src)" role="button">
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-secondary small">No evidence photo attached.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Official and Citizen Comments Thread -->
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-comments me-2 text-secondary"></i>Official Action & Citizen Log</h5>

                <!-- Post Comment Form -->
                <form action="{{ route('municipality.cases.comment', $case->id) }}" method="POST" class="mb-4 bg-light p-3 rounded border">
                    @csrf
                    <label class="form-label small fw-bold text-secondary">Post Official Response</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select name="department" class="form-select form-select-sm" required>
                                <option value="PWD Division">PWD Division</option>
                                <option value="PHED Division">PHED Division</option>
                                <option value="DISCOM Power">DISCOM Power</option>
                                <option value="Kota Municipality Office">Municipal Office</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="content" class="form-control form-control-sm" placeholder="Type official response or update..." required>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-sm btn-success px-4" style="background-color: #10B981; border: none;"><i class="fa-regular fa-paper-plane me-1"></i> Post Response</button>
                    </div>
                </form>

                <!-- Comments list -->
                <div class="d-flex flex-column gap-3">
                    @forelse($comments as $c)
                    <div class="p-3 rounded {{ $c->is_official ? 'bg-success-subtle border border-success-subtle text-dark' : 'bg-light border text-dark' }}">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="fw-bold">{{ $c->user_name }} {!! $c->is_official ? '<span class="badge bg-success ms-1 small" style="font-size: 0.65rem;">OFFICIAL</span>' : '' !!}</span>
                            <span class="text-secondary">{{ $c->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mb-0 leading-relaxed" style="font-size: 0.92rem;">{{ $c->content }}</p>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No updates logged for this case.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right management panel -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 sticky-top" style="top: 24px;">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Action Panel</h5>
                
                <form action="{{ route('municipality.cases.status', $case->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Update Case Status</label>
                        <select name="status" id="statusSelect" class="form-select" onchange="toggleEvidenceInput()">
                            <option value="Pending" {{ $case->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $case->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $case->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <!-- Evidence file input container (hidden by default, shown when status is Resolved) -->
                    <div id="evidenceInputContainer" class="mb-3 p-3 bg-warning-subtle border border-warning-subtle rounded-3" style="display: none;">
                        <label class="form-label small fw-bold text-dark"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Resolution Evidence <span class="text-danger">*</span></label>
                        <input type="file" name="evidence_file" id="evidenceFileInput" class="form-control form-control-sm">
                        <div class="form-text small text-secondary mt-1">Upload a verification photo or PDF proving the hazard has been repaired/resolved. Max 10MB.</div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold" style="background-color: #10B981; border: none;"><i class="fa-regular fa-circle-check me-1"></i> Submit Status Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEvidenceInput() {
        var status = document.getElementById('statusSelect').value;
        var container = document.getElementById('evidenceInputContainer');
        var fileInput = document.getElementById('evidenceFileInput');

        if (status === 'Resolved') {
            container.style.display = 'block';
            fileInput.required = true;
        } else {
            container.style.display = 'none';
            fileInput.required = false;
        }
    }

    // Run on initial page load to match state
    document.addEventListener('DOMContentLoaded', function() {
        toggleEvidenceInput();
    });
</script>
@endsection
