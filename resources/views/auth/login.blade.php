@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem 0;
    }
    
    .login-card {
        max-width: 450px;
        width: 100%;
        margin: 0 auto;
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header h1 {
        color: #fff;
        font-weight: 700;
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .login-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
    }
    
    .card {
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .card-body {
        padding: 2.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
    }
    
    .btn-login {
        padding: 0.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #dee2e6;
    }
    
    .divider span {
        padding: 0 1rem;
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .password-toggle:hover {
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1><i class="bi bi-play-circle-fill"></i></h1>
            <h1>Mediatama</h1>
            <p>Video Access Management System</p>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <h4 class="text-center mb-4 fw-bold">Masuk ke Akun Anda</h4>

                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="contoh@email.com"
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <div class="position-relative">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password"
                                   required>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat Saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="divider">
                        <span>Belum punya akun?</span>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <small class="text-white">
                &copy; {{ date('Y') }} Mediatama. All rights reserved.
            </small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle Password Visibility
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        
        // Toggle icon
        $(this).toggleClass('bi-eye-slash bi-eye');
    });

    // Form Validation & Loading
    $('#loginForm').on('submit', function(e) {
        const email = $('#email').val();
        const password = $('#password').val();

        if (!email || !password) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Email dan Password harus diisi!',
                confirmButtonColor: '#0d6efd'
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endpush