@extends('layouts.app')

@section('title', 'Request History')

@push('styles')
<style>
    .history-card {
        transition: all 0.3s;
        border-left: 4px solid #dee2e6;
    }
    
    .history-card.status-pending {
        border-left-color: #ffc107;
    }
    
    .history-card.status-approved {
        border-left-color: #198754;
    }
    
    .history-card.status-rejected {
        border-left-color: #dc3545;
    }
    
    .history-card:hover {
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-clock-history"></i> Request History
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Request History</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('customer.history') }}" 
           class="text-decoration-none">
            <div class="card {{ !request('status') ? 'border-primary' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="bi bi-list-ul"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="{{ route('customer.history', ['status' => 'pending']) }}" 
           class="text-decoration-none">
            <div class="card {{ request('status') == 'pending' ? 'border-warning' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="{{ route('customer.history', ['status' => 'approved']) }}" 
           class="text-decoration-none">
            <div class="card {{ request('status') == 'approved' ? 'border-success' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Approved</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="{{ route('customer.history', ['status' => 'rejected']) }}" 
           class="text-decoration-none">
            <div class="card {{ request('status') == 'rejected' ? 'border-danger' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Rejected</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['rejected'] }}</h3>
                        </div>
                        <div class="text-danger" style="font-size: 2rem;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- History List -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Request History
            @if(request('status'))
                - <span class="text-capitalize">{{ request('status') }}</span>
            @endif
        </h5>
        @if(request('status'))
            <a href="{{ route('customer.history') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-x"></i> Clear Filter
            </a>
        @endif
    </div>
    <div class="card-body">
        @if($accessHistory->count() > 0)
            @foreach($accessHistory as $access)
                <div class="card history-card status-{{ $access->status }} mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h5 class="mb-2">
                                    <i class="bi bi-camera-video text-primary"></i>
                                    {{ $access->video->title }}
                                </h5>
                                <p class="text-muted small mb-2">
                                    {{ Str::limit($access->video->description, 150) ?: 'Tidak ada deskripsi' }}
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> 
                                        Durasi Video: {{ $access->video->formatted_duration }}
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    Request: {{ $access->created_at->format('d M Y H:i') }}
                                    ({{ $access->created_at->diffForHumans() }})
                                </small>
                            </div>

                            <div class="col-lg-3 text-center my-3 my-lg-0">
                                @if($access->isPending())
                                    <span class="badge bg-warning text-dark p-2">
                                        <i class="bi bi-hourglass-split"></i> Pending
                                    </span>
                                    <p class="text-muted small mt-2 mb-0">
                                        Menunggu persetujuan admin
                                    </p>
                                @elseif($access->isApproved())
                                    <span class="badge bg-success p-2 mb-2">
                                        <i class="bi bi-check-circle"></i> Approved
                                    </span>
                                    @if($access->permission)
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Durasi Akses</small>
                                            <strong>{{ $access->permission->duration_hours }} Jam</strong>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Berlaku hingga</small>
                                            <strong class="d-block">{{ $access->permission->expires_at->format('d M Y') }}</strong>
                                            <strong>{{ $access->permission->expires_at->format('H:i') }}</strong>
                                        </div>
                                        @if($access->permission->isValid())
                                            <div class="mt-2">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-unlock"></i> Masih Aktif
                                                </span>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-lock"></i> Sudah Expired
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <span class="badge bg-danger p-2 mb-2">
                                        <i class="bi bi-x-circle"></i> Rejected
                                    </span>
                                    @if($access->reason)
                                        <div class="alert alert-danger small mt-2">
                                            <strong>Alasan:</strong><br>
                                            {{ $access->reason }}
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="col-lg-3 text-end">
                                @if($access->isApproved() && $access->permission && $access->permission->isValid())
                                    <a href="{{ route('customer.videos.play', $access->video_id) }}" 
                                       class="btn btn-success btn-sm mb-2">
                                        <i class="bi bi-play-fill"></i> Tonton
                                    </a>
                                @endif
                                
                                @if($access->isRejected() || ($access->isApproved() && $access->permission && $access->permission->isExpired()))
                                    <form action="{{ route('customer.videos.request', $access->video_id) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Request akses ulang untuk video ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-arrow-clockwise"></i> Request Ulang
                                        </button>
                                    </form>
                                @endif

                                @if($access->isPending())
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="bi bi-hourglass"></i> Menunggu
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            @if($accessHistory->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $accessHistory->firstItem() }} - {{ $accessHistory->lastItem() }} 
                        dari {{ $accessHistory->total() }} request
                    </div>
                    <div>
                        {{ $accessHistory->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">
                    @if(request('status'))
                        Tidak ada request dengan status {{ request('status') }}
                    @else
                        Anda belum pernah melakukan request
                    @endif
                </p>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari Video & Request Akses
                </a>
            </div>
        @endif
    </div>
</div>
@endsection