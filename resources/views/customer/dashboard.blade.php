@extends('layouts.app')

@section('title', 'Customer Dashboard')

@push('styles')
<style>
    .video-card {
        transition: all 0.3s;
        height: 100%;
    }
    
    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
    }
    
    .video-thumbnail {
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .video-thumbnail::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }
    
    .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3rem;
        color: white;
        opacity: 0.9;
        transition: all 0.3s;
    }
    
    .video-card:hover .play-icon {
        font-size: 3.5rem;
        opacity: 1;
    }
    
    .stats-card {
        transition: transform 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-house-door"></i> Dashboard Customer
        </h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}! Pilih video yang ingin Anda tonton.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Videos</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total_videos'] }}</h3>
                    </div>
                    <div class="text-primary" style="font-size: 2rem;">
                        <i class="bi bi-camera-video"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">My Requests</p>
                        <h3 class="fw-bold mb-0">{{ $stats['my_requests'] }}</h3>
                    </div>
                    <div class="text-info" style="font-size: 2rem;">
                        <i class="bi bi-send"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Pending</p>
                        <h3 class="fw-bold mb-0">{{ $stats['pending_requests'] }}</h3>
                    </div>
                    <div class="text-warning" style="font-size: 2rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Active Access</p>
                        <h3 class="fw-bold mb-0">{{ $stats['active_permissions'] }}</h3>
                    </div>
                    <div class="text-success" style="font-size: 2rem;">
                        <i class="bi bi-unlock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('customer.my-access') }}" class="btn btn-success me-2">
            <i class="bi bi-unlock-fill"></i> My Active Access
        </a>
        <a href="{{ route('customer.history') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history"></i> Request History
        </a>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('customer.dashboard') }}" method="GET">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Cari video..." 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if(request('search'))
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Videos Grid -->
@if($videos->count() > 0)
    <div class="row g-4">
        @foreach($videos as $video)
            <div class="col-lg-4 col-md-6">
                <div class="card video-card shadow-sm">
                    <!-- Video Thumbnail -->
                    <div class="video-thumbnail">
                        <i class="bi bi-play-circle-fill play-icon"></i>
                        <div class="position-absolute bottom-0 start-0 m-2">
                            <span class="badge bg-dark bg-opacity-75">
                                <i class="bi bi-clock"></i> {{ $video->formatted_duration }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($video->title, 50) }}</h5>
                        <p class="card-text text-muted small">
                            {{ Str::limit($video->description, 100) ?: 'Tidak ada deskripsi' }}
                        </p>

                        <!-- Status Badges -->
                        <div class="mb-3">
                            @if($video->user_permission)
                                <!-- User has active permission -->
                                <span class="badge bg-success mb-2">
                                    <i class="bi bi-check-circle-fill"></i> Akses Aktif
                                </span>
                                <br>
                                <small class="text-success">
                                    <i class="bi bi-clock-fill"></i> 
                                    Berlaku hingga: {{ $video->user_permission->expires_at->format('d M Y H:i') }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    Sisa waktu: {{ $video->user_permission->remaining_time }}
                                </small>
                            @elseif($video->user_access_request)
                                <!-- User has request -->
                                @if($video->user_access_request->isPending())
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Request Pending
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        Request: {{ $video->user_access_request->created_at->diffForHumans() }}
                                    </small>
                                @elseif($video->user_access_request->isApproved())
                                    @if($video->user_access_request->permission && $video->user_access_request->permission->isExpired())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Akses Expired
                                        </span>
                                    @endif
                                @elseif($video->user_access_request->isRejected())
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Request Ditolak
                                    </span>
                                    @if($video->user_access_request->reason)
                                        <br>
                                        <small class="text-danger">
                                            Alasan: {{ Str::limit($video->user_access_request->reason, 50) }}
                                        </small>
                                    @endif
                                @endif
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-lock"></i> Belum Request
                                </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        @if($video->user_permission)
                            <!-- Can watch -->
                            <a href="{{ route('customer.videos.play', $video->id) }}" 
                               class="btn btn-success w-100">
                                <i class="bi bi-play-fill"></i> Tonton Sekarang
                            </a>
                        @elseif($video->user_access_request && $video->user_access_request->isPending())
                            <!-- Pending request -->
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="bi bi-hourglass-split"></i> Menunggu Persetujuan
                            </button>
                        @else
                            <!-- Can request -->
                            <form action="{{ route('customer.videos.request', $video->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Request akses untuk video ini?')">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-send"></i> Request Akses
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="card-footer bg-white text-muted small">
                        <i class="bi bi-calendar"></i> Ditambahkan {{ $video->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($videos->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ $videos->firstItem() }} - {{ $videos->lastItem() }} dari {{ $videos->total() }} video
            </div>
            <div>
                {{ $videos->links() }}
            </div>
        </div>
    @endif
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-camera-video text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">
                @if(request('search'))
                    Tidak ada video yang sesuai dengan pencarian "{{ request('search') }}"
                @else
                    Belum ada video tersedia
                @endif
            </p>
            @if(request('search'))
                <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Semua Video
                </a>
            @endif
        </div>
    </div>
@endif
@endsection