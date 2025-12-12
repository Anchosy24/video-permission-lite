@extends('layouts.app')

@section('title', 'Video Player - ' . $video->title)

@push('styles')
<style>
    .player-container {
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
    }
    
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        overflow: hidden;
    }
    
    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    
    .access-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
    }
    
    .timer-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .warning-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-primary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h2 class="fw-bold">
            <i class="bi bi-play-circle"></i> {{ $video->title }}
        </h2>
    </div>
</div>

<div class="row g-4">
    <!-- Video Player -->
    <div class="col-lg-8">
        <div class="player-container mb-4">
            <div class="video-wrapper">
                @php
                    // Parse YouTube URL
                    $videoId = null;
                    if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $video->video_url, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/youtu\.be\/([^?]+)/', $video->video_url, $matches)) {
                        $videoId = $matches[1];
                    }
                    
                    // Parse Vimeo URL
                    $vimeoId = null;
                    if (preg_match('/vimeo\.com\/(\d+)/', $video->video_url, $matches)) {
                        $vimeoId = $matches[1];
                    }
                @endphp
                
                @if($videoId)
                    <!-- YouTube Video -->
                    <iframe 
                        src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1&rel=0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                @elseif($vimeoId)
                    <!-- Vimeo Video -->
                    <iframe 
                        src="https://player.vimeo.com/video/{{ $vimeoId }}?autoplay=1" 
                        allow="autoplay; fullscreen; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                @else
                    <!-- Direct Video URL -->
                    <video controls autoplay style="width: 100%; height: 100%;">
                        <source src="{{ $video->video_url }}" type="video/mp4">
                        Browser Anda tidak mendukung video player.
                    </video>
                @endif
            </div>
        </div>

        <!-- Video Info -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">{{ $video->title }}</h4>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-primary me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <small class="text-muted d-block">Durasi Video</small>
                                <strong>{{ $video->formatted_duration }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar text-primary me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <small class="text-muted d-block">Ditambahkan</small>
                                <strong>{{ $video->created_at->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                @if($video->description)
                    <hr>
                    <h6 class="mb-2">Deskripsi</h6>
                    <p class="text-muted">{{ $video->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Access Information -->
    <div class="col-lg-4">
        <div class="access-info mb-4">
            <h5 class="mb-3">
                <i class="bi bi-shield-check"></i> Informasi Akses
            </h5>
            
            <div class="timer-card mb-3">
                <div class="text-center">
                    <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                    <h6 class="mt-2 mb-1">Sisa Waktu Akses</h6>
                    <h3 class="fw-bold mb-0" id="remainingTime">{{ $permission->remaining_time }}</h3>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="timer-card text-center">
                        <small class="d-block mb-1">Durasi Total</small>
                        <strong>{{ $permission->duration_hours }} Jam</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="timer-card text-center">
                        <small class="d-block mb-1">Sisa Jam</small>
                        <strong id="remainingHours">{{ number_format($permission->getRemainingHours(), 1) }} Jam</strong>
                    </div>
                </div>
            </div>

            <hr class="border-light">

            <div class="mb-2">
                <small class="d-block mb-1">
                    <i class="bi bi-calendar-check"></i> Akses Dimulai
                </small>
                <strong>{{ $permission->created_at->format('d M Y H:i') }}</strong>
            </div>

            <div class="mb-3">
                <small class="d-block mb-1">
                    <i class="bi bi-calendar-x"></i> Akses Berakhir
                </small>
                <strong id="expiresAt">{{ $permission->expires_at->format('d M Y H:i') }}</strong>
            </div>

            @php
                $hoursRemaining = $permission->getRemainingHours();
            @endphp

            @if($hoursRemaining <= 1)
                <div class="alert alert-warning warning-pulse mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Perhatian!</strong><br>
                    Akses Anda akan segera berakhir!
                </div>
            @else
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle-fill"></i>
                    <strong>Akses Aktif</strong><br>
                    Nikmati video Anda!
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="bi bi-gear"></i> Aksi
                </h6>
                
                <a href="{{ route('customer.my-access') }}" 
                   class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-unlock"></i> Lihat Semua Akses
                </a>
                
                <a href="{{ route('customer.history') }}" 
                   class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-clock-history"></i> Riwayat Request
                </a>
                
                <a href="{{ route('customer.dashboard') }}" 
                   class="btn btn-outline-info w-100">
                    <i class="bi bi-collection-play"></i> Video Lainnya
                </a>
            </div>
        </div>

        <!-- Warning About Expiry -->
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i>
            <small>
                <strong>Catatan:</strong> Setelah akses berakhir, Anda dapat melakukan request ulang untuk menonton video ini kembali.
            </small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Countdown Timer
    function updateCountdown() {
        const expiresAt = new Date('{{ $permission->expires_at->format('Y-m-d H:i:s') }}').getTime();
        
        const interval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                clearInterval(interval);
                
                // Show expiry message
                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Berakhir',
                    text: 'Waktu akses Anda telah habis. Silakan request ulang untuk menonton video ini.',
                    confirmButtonText: 'Kembali ke Dashboard',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('customer.dashboard') }}';
                    }
                });
                
                return;
            }

            // Calculate time units
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update display
            let timeString = '';
            if (hours > 0) {
                timeString = hours + ' jam ' + minutes + ' menit';
            } else if (minutes > 0) {
                timeString = minutes + ' menit ' + seconds + ' detik';
            } else {
                timeString = seconds + ' detik';
            }

            document.getElementById('remainingTime').textContent = timeString;
            
            // Update remaining hours
            const remainingHours = (distance / (1000 * 60 * 60)).toFixed(1);
            document.getElementById('remainingHours').textContent = remainingHours + ' Jam';

            // Show warning when less than 5 minutes
            if (distance < 5 * 60 * 1000 && distance > 4 * 60 * 1000) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Akses Anda akan berakhir dalam 5 menit!',
                    timer: 5000,
                    showConfirmButton: false
                });
            }

            // Show critical warning when less than 1 minute
            if (distance < 60 * 1000 && distance > 59 * 1000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Hampir Berakhir!',
                    text: 'Akses Anda akan berakhir dalam kurang dari 1 menit!',
                    timer: 5000,
                    showConfirmButton: false
                });
            }
        }, 1000);
    }

    // Start countdown on page load
    updateCountdown();

    // Prevent accidental page close
    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = '';
        return 'Apakah Anda yakin ingin meninggalkan halaman ini?';
    });
</script>
@endpush