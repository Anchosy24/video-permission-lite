@extends('layouts.app')

@section('title', 'Manage Videos')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-camera-video-fill"></i> Manage Videos
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Videos</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.videos.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari judul atau deskripsi..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
                <a href="{{ route('admin.videos.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Video
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Videos Grid -->
<div class="row g-4">
    @if($videos->count() > 0)
        @foreach($videos as $video)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <!-- Video Thumbnail -->
                    <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="position-absolute top-50 start-50 translate-middle text-white text-center w-100">
                            <i class="bi bi-play-circle" style="font-size: 4rem;"></i>
                            <p class="mt-2 mb-0 px-3">
                                <i class="bi bi-clock"></i> {{ $video->formatted_duration }}
                            </p>
                        </div>
                        <!-- Status Badge -->
                        <div class="position-absolute top-0 end-0 m-2">
                            @if($video->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Non-Aktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($video->title, 50) }}</h5>
                        <p class="card-text text-muted small">
                            {{ Str::limit($video->description, 100) ?: 'Tidak ada deskripsi' }}
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-info">
                                    <i class="bi bi-people"></i> {{ $video->access_requests_count }} Requests
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-unlock"></i> {{ $video->active_permissions_count }} Active
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ $video->video_url }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-link-45deg"></i> URL
                            </a>
                            <a href="{{ route('admin.videos.edit', $video->id) }}" 
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="confirmDelete('delete-form-{{ $video->id }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <form id="delete-form-{{ $video->id }}" 
                              action="{{ route('admin.videos.destroy', $video->id) }}" 
                              method="POST" 
                              class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>

                    <div class="card-footer bg-white text-muted small">
                        <i class="bi bi-calendar"></i> {{ $video->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan {{ $videos->firstItem() }} - {{ $videos->lastItem() }} dari {{ $videos->total() }} data
                </div>
                <div>
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-camera-video text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Tidak ada data video</p>
                    <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Video Pertama
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection