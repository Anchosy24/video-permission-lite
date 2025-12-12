@extends('layouts.app')

@section('title', 'Register')

@push('styles')
<style>
    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem 0;
    }
    
    .register-card {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .register-header h1 {
        color: #fff;
        font-weight: 700;
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .register-header p {
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
    
    .btn-register {
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
        z-index: 10;
    }
    
    .password-toggle:hover {
        color: #495057;
    }
    
    .password-strength {
        height: 5px;
        background-color: #e9ecef;
        border-radius: 3px;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    
    .password-strength-bar {
        height: 100%;
        transition: all 0.3s;
        border-radius: 3px;
    }
    
    .password-strength-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1><i class="bi bi-person-plus-fill"></i></h1>
            <h1>Daftar Akun</h1>
            <p>Buat akun baru untuk akses video</p>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <h4 class="text-center mb-4 fw-bold">Form Registrasi</h4>

                <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Nama Lengkap
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               placeholder="Masukkan nama lengkap"
                               value="{{ old('name') }}" 
                               required 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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
                               required>
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
                                   placeholder="Minimal 8 karakter"
                                   required>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-fill"></i> Konfirmasi Password
                        </label>
                        <div class="position-relative">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Ulangi password"
                                   required>
                            <i class="bi bi-eye-slash password-toggle" id="togglePasswordConfirm"></i>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted" id="passwordMatch"></small>
                    </div>

                    <!-- Terms -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="terms" 
                               required>
                        <label class="form-check-label" for="terms">
                            Saya setuju dengan <a href="#" class="text-primary">syarat dan ketentuan</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="bi bi-person-check"></i> Daftar
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="divider">
                        <span>Sudah punya akun?</span>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
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
        $(this).toggleClass('bi-eye-slash bi-eye');
    });

    $('#togglePasswordConfirm').on('click', function() {
        const passwordInput = $('#password_confirmation');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $(this).toggleClass('bi-eye-slash bi-eye');
    });

    // Password Strength Checker
    $('#password').on('input', function() {
        const password = $(this).val();
        const strengthBar = $('#strengthBar');
        const strengthText = $('#strengthText');
        
        let strength = 0;
        let color = '';
        let text = '';

        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/)) strength += 12.5;
        if (password.match(/[$@#&!]+/)) strength += 12.5;

        if (strength <= 25) {
            color = '#dc3545';
            text = 'Lemah';
        } else if (strength <= 50) {
            color = '#ffc107';
            text = 'Cukup';
        } else if (strength <= 75) {
            color = '#17a2b8';
            text = 'Kuat';
        } else {
            color = '#28a745';
            text = 'Sangat Kuat';
        }

        strengthBar.css({
            'width': strength + '%',
            'background-color': color
        });

        strengthText.text(text).css('color', color);
    });

    // Password Match Checker
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        const matchText = $('#passwordMatch');

        if (confirmPassword.length === 0) {
            matchText.text('');
            return;
        }

        if (password === confirmPassword) {
            matchText.html('<i class="bi bi-check-circle-fill text-success"></i> Password cocok')
                     .removeClass('text-danger')
                     .addClass('text-success');
        } else {
            matchText.html('<i class="bi bi-x-circle-fill text-danger"></i> Password tidak cocok')
                     .removeClass('text-success')
                     .addClass('text-danger');
        }
    });

    // Form Validation
    $('#registerForm').on('submit', function(e) {
        const name = $('#name').val();
        const email = $('#email').val();
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirmation').val();
        const terms = $('#terms').is(':checked');

        if (!name || !email || !password || !passwordConfirm) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Semua field harus diisi!',
                confirmButtonColor: '#0d6efd'
            });
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Password Terlalu Pendek',
                text: 'Password minimal 8 karakter!',
                confirmButtonColor: '#0d6efd'
            });
            return false;
        }

        if (password !== passwordConfirm) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Password dan konfirmasi password harus sama!',
                confirmButtonColor: '#0d6efd'
            });
            return false;
        }

        if (!terms) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Syarat dan Ketentuan',
                text: 'Anda harus menyetujui syarat dan ketentuan!',
                confirmButtonColor: '#0d6efd'
            });
            return false;
        }

        // Show loading
        Swal.fire({
            title: 'Memproses Registrasi...',
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