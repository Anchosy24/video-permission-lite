@extends('layouts.app')

@section('title', $video ? 'Edit Video' : 'Tambah Video')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-{{ $video ? 'pencil-square' : 'plus-circle' }}"></i> 
            {{ $video ? 'Edit Video' : 'Tambah Video' }}
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.videos.index') }}">Videos</a></li>
                <li class="breadcrumb-item active">{{ $video ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Form Video</h5>
            </div>
            <div class="card-body">
                <form action="{{ $video ? route('admin.videos.update', $video->id) : route('admin.videos.store') }}" 
                      method="POST" 
                      id="videoForm">
                    @csrf
                    @if($video)
                        @method('PUT')
                    @endif

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Judul Video <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $video->title ?? '') }}" 
                               placeholder="Masukkan judul video"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Masukkan deskripsi video (opsional)">{{ old('description', $video->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Video URL -->
                    <div class="mb-3">
                        <label for="video_url" class="form-label">
                            URL Video <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" 
                                   class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" 
                                   name="video_url" 
                                   value="{{ old('video_url', $video->video_url ?? '') }}" 
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   required>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Support: YouTube, Vimeo, atau URL video lainnya
                        </small>
                    </div>

                    <!-- Duration -->
                    <div class="mb-3">
                        <label for="duration" class="form-label">
                            Durasi Video (dalam menit) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="number" 
                                   class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" 
                                   name="duration" 
                                   value="{{ old('duration', $video->duration ?? '') }}" 
                                   placeholder="Contoh: 120"
                                   min="0"
                                   required>
                            <span class="input-group-text">menit</span>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted" id="durationText"></small>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $video->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <span id="statusText">
                                    {{ old('is_active', $video->is_active ?? true) ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </label>
                        </div>
                        <small class="text-muted">Video yang non-aktif tidak dapat direquest oleh customer</small>
                    </div>

                    <!-- Preview Card -->
                    <div class="card bg-light mb-4" id="previewCard" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-eye"></i> Preview Video</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="bg-primary bg-opacity-10 text-primary text-center p-4 rounded">
                                        <i class="bi bi-play-circle" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h5 id="previewTitle">-</h5>
                                    <p class="text-muted small" id="previewDescription">-</p>
                                    <div>
                                        <span class="badge bg-info" id="previewDuration">
                                            <i class="bi bi-clock"></i> 0 menit
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ $video ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Duration Calculator
    $('#duration').on('input', function() {
        const minutes = parseInt($(this).val()) || 0;
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        
        let text = '';
        if (hours > 0) {
            text = `${hours} jam ${remainingMinutes} menit`;
        } else {
            text = `${remainingMinutes} menit`;
        }
        
        $('#durationText').html(`<i class="bi bi-info-circle"></i> ${text}`);
        updatePreview();
    });

    // Status Toggle Text
    $('#is_active').on('change', function() {
        const statusText = $(this).is(':checked') ? 'Aktif' : 'Non-Aktif';
        $('#statusText').text(statusText);
    });

    // Live Preview
    function updatePreview() {
        const title = $('#title').val() || 'Judul Video';
        const description = $('#description').val() || 'Tidak ada deskripsi';
        const duration = parseInt($('#duration').val()) || 0;
        
        $('#previewTitle').text(title);
        $('#previewDescription').text(description.substring(0, 100) + (description.length > 100 ? '...' : ''));
        
        const hours = Math.floor(duration / 60);
        const minutes = duration % 60;
        let durationText = '';
        if (hours > 0) {
            durationText = `${hours} jam ${minutes} menit`;
        } else {
            durationText = `${minutes} menit`;
        }
        $('#previewDuration').html(`<i class="bi bi-clock"></i> ${durationText}`);
        
        // Show preview card if any field is filled
        if (title !== 'Judul Video' || description !== 'Tidak ada deskripsi' || duration > 0) {
            $('#previewCard').slideDown();
        }
    }

    $('#title, #description, #duration').on('input', updatePreview);

    // Initialize preview if editing
    @if($video)
        updatePreview();
    @endif

    // Form Validation
    $('#videoForm').on('submit', function(e) {
        const videoUrl = $('#video_url').val();
        
        // Basic URL validation
        if (videoUrl && !videoUrl.match(/^https?:\/\/.+/)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'URL Tidak Valid',
                text: 'URL harus dimulai dengan http:// atau https://',
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endpush