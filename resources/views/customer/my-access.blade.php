@extends('layouts.app')

@section('title', 'My Active Access')

@push('styles')
<style>
    .access-card {
        transition: all 0.3s;
    }
    
    .access-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    
    .time-remaining {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
    
    .expired-badge {
        opacity: 0.7;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-unlock-fill"></i> My Active Access
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Active Access</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Active Permissions -->
<div class="row mb-4">
    <div class="col">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-unlock text-success"></i> Active Permissions
                    @if($activePermissions->total() > 0)
                        <span class="badge bg-success">{{ $activePermissions->total() }}</span>
                    @endif
                </h5>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Request More
                </a>
            </div>
            <div class="card-body">
                @if($activePermissions->count() > 0)
                    <div class="row g-3">
                        @foreach($activePermissions as $permission)
                            <div class="col-lg-6">
                                <div class="card access-card h-100 border-success">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="flex-grow-1">
                                                <h5 class="card-title mb-2">
                                                    <i class="bi bi-camera-video text-primary"></i>
                                                    {{ $permission->video->title }}
                                                </h5>
                                                <p class="text-muted small mb-2">
                                                    {{ Str::limit($permission->video->description, 100) ?: 'Tidak ada deskripsi' }}
                                                </p>
                                            </div>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Aktif
                                            </span>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Durasi Akses</small>
                                                <strong>{{ $permission->duration_hours }} Jam</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Durasi Video</small>
                                                <strong>{{ $permission->video->formatted_duration }}</strong>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-calendar-check"></i> Disetujui
                                                    </small>
                                                    <strong>{{ $permission->created_at->format('d M Y H:i') }}</strong>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-calendar-x"></i> Berakhir
                                                    </small>
                                                    <strong>{{ $permission->expires_at->format('d M Y H:i') }}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning time-remaining">
                                            <i class="bi bi-hourglass-split"></i>
                                            <strong>Sisa Waktu: {{ $permission->remaining_time }}</strong>
                                        </div>

                                        <a href="{{ route('customer.videos.play', $permission->video_id) }}" 
                                           class="btn btn-success w-100">
                                            <i class="bi bi-play-fill"></i> Tonton Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($activePermissions->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Menampilkan {{ $activePermissions->firstItem() }} - {{ $activePermissions->lastItem() }} 
                                dari {{ $activePermissions->total() }} akses aktif
                            </div>
                            <div>
                                {{ $activePermissions->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-unlock text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-4">Anda belum memiliki akses aktif</p>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari Video & Request Akses
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recently Expired Permissions -->
@if($expiredPermissions->count() > 0)
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history text-danger"></i> Recently Expired
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($expiredPermissions as $permission)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="bi bi-camera-video"></i>
                                        {{ $permission->video->title }}
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar-check"></i> 
                                        Disetujui: {{ $permission->created_at->format('d M Y H:i') }}
                                    </small>
                                    <small class="text-danger d-block">
                                        <i class="bi bi-calendar-x"></i> 
                                        Expired: {{ $permission->expires_at->format('d M Y H:i') }}
                                        ({{ $permission->expires_at->diffForHumans() }})
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger expired-badge mb-2">
                                        <i class="bi bi-x-circle"></i> Expired
                                    </span>
                                    <br>
                                    <form action="{{ route('customer.videos.request', $permission->video_id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="return confirm('Request akses ulang untuk video ini?')">
                                            <i class="bi bi-arrow-clockwise"></i> Request Ulang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Auto refresh untuk update sisa waktu setiap 30 detik
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endpush