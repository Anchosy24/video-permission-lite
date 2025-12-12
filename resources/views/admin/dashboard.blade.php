@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .stats-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-speedometer2"></i> Dashboard Admin
        </h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}! Berikut ringkasan sistem.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Customers -->
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Customers</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total_customers'] }}</h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle-fill"></i> {{ $stats['active_customers'] }} Aktif
                        </small>
                    </div>
                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Videos -->
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Videos</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total_videos'] }}</h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle-fill"></i> {{ $stats['active_videos'] }} Aktif
                        </small>
                    </div>
                    <div class="stats-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-camera-video-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending Requests</p>
                        <h3 class="fw-bold mb-0">{{ $stats['pending_requests'] }}</h3>
                        <small class="text-warning">
                            <i class="bi bi-hourglass-split"></i> Menunggu
                        </small>
                    </div>
                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Permissions -->
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Active Permissions</p>
                        <h3 class="fw-bold mb-0">{{ $stats['active_permissions'] }}</h3>
                        <small class="text-success">
                            <i class="bi bi-unlock-fill"></i> Berlaku
                        </small>
                    </div>
                    <div class="stats-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pending Requests -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-inbox text-warning"></i> Pending Requests
                    @if($stats['pending_requests'] > 0)
                        <span class="badge bg-warning text-dark badge-pulse ms-2">{{ $stats['pending_requests'] }}</span>
                    @endif
                </h5>
                <a href="{{ route('admin.accesses.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @if($pendingRequests->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingRequests as $request)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $request->video->title }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> {{ $request->customer->name }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> {{ $request->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Tidak ada pending request</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Approvals -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-check-circle text-success"></i> Recent Approvals
                </h5>
                <a href="{{ route('admin.accesses.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-success">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @if($recentApprovals->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentApprovals as $approval)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $approval->video->title }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> {{ $approval->customer->name }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> {{ $approval->permission->duration_hours }} jam
                                        </small>
                                    </div>
                                    <span class="badge bg-success">Approved</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Belum ada approval</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Expiring Permissions -->
    @if($expiringPermissions->count() > 0)
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Permissions Expiring Soon
                    <span class="badge bg-danger ms-2">{{ $expiringPermissions->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Video</th>
                                <th>Expires At</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringPermissions as $permission)
                                <tr>
                                    <td>{{ $permission->customer->name }}</td>
                                    <td>{{ $permission->video->title }}</td>
                                    <td>{{ $permission->expires_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-hourglass"></i> {{ $permission->remaining_time }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection