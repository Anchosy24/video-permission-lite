@extends('layouts.app')

@section('title', 'Manage Access Requests')

@push('styles')
<style>
    .request-card {
        transition: all 0.3s;
    }
    
    .request-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .approval-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-clipboard-check"></i> Manage Access Requests
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Access Requests</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Status Badges -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.accesses.index', ['status' => 'pending']) }}" 
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
    <div class="col-md-4">
        <a href="{{ route('admin.accesses.index', ['status' => 'approved']) }}" 
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
    <div class="col-md-4">
        <a href="{{ route('admin.accesses.index', ['status' => 'rejected']) }}" 
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

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.accesses.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari customer atau video..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.accesses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Access Requests List -->
@if($accesses->count() > 0)
    @foreach($accesses as $access)
        <div class="card request-card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h5 class="mb-2">
                            <i class="bi bi-camera-video text-primary"></i> 
                            {{ $access->video->title }}
                        </h5>
                        <p class="mb-1">
                            <strong>Customer:</strong> 
                            <span class="text-primary">{{ $access->customer->name }}</span>
                            <small class="text-muted">({{ $access->customer->email }})</small>
                        </p>
                        <p class="mb-1">
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Request: {{ $access->created_at->format('d M Y H:i') }}
                                ({{ $access->created_at->diffForHumans() }})
                            </small>
                        </p>
                    </div>

                    <div class="col-lg-3 text-center">
                        @if($access->status == 'pending')
                            <span class="badge bg-warning text-dark p-2">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </span>
                        @elseif($access->status == 'approved')
                            <span class="badge bg-success p-2">
                                <i class="bi bi-check-circle"></i> Approved
                            </span>
                            @if($access->permission)
                                <br>
                                <small class="text-muted d-block mt-2">
                                    Durasi: {{ $access->permission->duration_hours }} jam
                                </small>
                                <small class="text-muted d-block">
                                    Berlaku hingga: {{ $access->permission->expires_at->format('d M Y H:i') }}
                                </small>
                                @if($access->permission->isValid())
                                    <small class="text-success d-block">
                                        <i class="bi bi-check-circle"></i> Masih Aktif
                                    </small>
                                @else
                                    <small class="text-danger d-block">
                                        <i class="bi bi-x-circle"></i> Sudah Expired
                                    </small>
                                @endif
                            @endif
                        @else
                            <span class="badge bg-danger p-2">
                                <i class="bi bi-x-circle"></i> Rejected
                            </span>
                            @if($access->reason)
                                <br>
                                <small class="text-danger d-block mt-2">
                                    Alasan: {{ $access->reason }}
                                </small>
                            @endif
                        @endif
                    </div>

                    <div class="col-lg-3 text-end">
                        @if($access->isPending())
                            <button type="button" 
                                    class="btn btn-success btn-sm" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#approve-{{ $access->id }}">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#reject-{{ $access->id }}">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        @endif
                    </div>
                </div>

                @if($access->isPending())
                    <!-- Approve Form -->
                    <div class="collapse approval-section" id="approve-{{ $access->id }}">
                        <form action="{{ route('admin.accesses.approve', $access->id) }}" method="POST">
                            @csrf
                            <h6 class="text-success mb-3">
                                <i class="bi bi-check-circle"></i> Approve Request
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Durasi Akses (dalam jam) *</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="duration_hours" 
                                           min="1" 
                                           max="168" 
                                           value="2" 
                                           required>
                                    <small class="text-muted">Maksimal 168 jam (1 minggu)</small>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Approval
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Reject Form -->
                    <div class="collapse approval-section" id="reject-{{ $access->id }}">
                        <form action="{{ route('admin.accesses.reject', $access->id) }}" method="POST">
                            @csrf
                            <h6 class="text-danger mb-3">
                                <i class="bi bi-x-circle"></i> Reject Request
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Alasan Penolakan *</label>
                                    <textarea class="form-control" 
                                              name="reason" 
                                              rows="2" 
                                              placeholder="Masukkan alasan penolakan..."
                                              required></textarea>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-x-circle"></i> Konfirmasi Penolakan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Menampilkan {{ $accesses->firstItem() }} - {{ $accesses->lastItem() }} dari {{ $accesses->total() }} data
        </div>
        <div>
            {{ $accesses->links() }}
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">
                @if(request('status'))
                    Tidak ada request dengan status {{ request('status') }}
                @else
                    Tidak ada access request
                @endif
            </p>
        </div>
    </div>
@endif
@endsection