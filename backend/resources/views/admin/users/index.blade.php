@extends('layouts.admin')

@section('title', 'NagarRakshak Citizens Hub')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">User Directory</h2>
            <p class="text-secondary" style="font-size: 0.95rem;">Manage citizen user profiles, track reputation scores, and handle access control states.</p>
        </div>
    </div>

    <!-- Citizens table card -->
    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Reputation</th>
                        <th>Badge Level</th>
                        <th>Reports</th>
                        <th>Verifications</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 1rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                        <td class="text-secondary">{{ $user->email }}</td>
                        <td>
                            <span class="fw-bold text-warning" style="font-size: 0.85rem;"><i class="fa-solid fa-star me-1"></i>{{ number_format($user->reputation_score ?? 0) }} pts</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-award me-1"></i>{{ $user->badge_level ?? 'Contributor' }}
                            </span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $user->reports_submitted }}</td>
                        <td class="fw-semibold text-dark">{{ $user->reports_verified }}</td>
                        <td>
                            @if($user->role === 'Suspended')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1">{{ $user->role }}</span>
                            @elseif($user->role === 'City Admin' || $user->role === 'Moderator')
                                <span class="badge bg-primary text-white px-2.5 py-1">{{ $user->role }}</span>
                            @else
                                <span class="badge bg-light text-secondary border px-2.5 py-1">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:0.825rem;"><i class="fa-regular fa-clock me-1"></i> {{ $user->created_at ? $user->created_at->format('M d, Y') : 'Recent' }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle fw-semibold rounded-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Manage
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 12px;">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.users.show', $user->id) }}">
                                            <i class="fa-solid fa-eye text-primary me-2"></i> View Profile
                                        </a>
                                    </li>
                                    @if($user->role !== 'Suspended')
                                        <li>
                                            <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="fa-solid fa-ban me-2"></i> Suspend Access
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-success">
                                                    <i class="fa-solid fa-unlock-keyhole me-2"></i> Activate Access
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            order: [[1, 'asc']],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [0, 9] }
            ]
        });
    });
</script>
@endsection
