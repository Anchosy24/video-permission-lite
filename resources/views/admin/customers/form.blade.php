@extends('layouts.app')

@section('title', $customer ? 'Edit Customer' : 'Tambah Customer')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2 class="fw-bold">
            <i class="bi bi-{{ $customer ? 'pencil-square' : 'person-plus' }}"></i> 
            {{ $customer ? 'Edit Customer' : 'Tambah Customer' }}
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active">{{ $customer ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Form Customer</h5>
            </div>
            <div class="card-body">
                <form action="{{ $customer ? route('admin.customers.update', $customer->id) : route('admin.customers.store') }}" 
                      method="POST" 
                      id="customerForm">
                    @csrf
                    @if($customer)
                        @method('PUT')
                    @endif

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $customer->name ?? '') }}" 
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $customer->email ?? '') }}" 
                               placeholder="contoh@email.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password 
                            @if(!$customer)
                                <span class="text-danger">*</span>
                            @else
                                <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>
                            @endif
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Minimal 8 karakter"
                                   {{ !$customer ? 'required' : '' }}>
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            Konfirmasi Password
                            @if(!$customer)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Ulangi password"
                                   {{ !$customer ? 'required' : '' }}>
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePasswordConfirm">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted" id="passwordMatch"></small>
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
                                   {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <span id="statusText">
                                    {{ old('is_active', $customer->is_active ?? true) ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </label>
                        </div>
                        <small class="text-muted">Customer yang non-aktif tidak dapat login</small>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ $customer ? 'Update' : 'Simpan' }}
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
    // Toggle Password Visibility
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        icon.toggleClass('bi-eye-slash bi-eye');
    });

    $('#togglePasswordConfirm').on('click', function() {
        const passwordInput = $('#password_confirmation');
        const icon = $(this).find('i');
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        icon.toggleClass('bi-eye-slash bi-eye');
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

    // Status Toggle Text
    $('#is_active').on('change', function() {
        const statusText = $(this).is(':checked') ? 'Aktif' : 'Non-Aktif';
        $('#statusText').text(statusText);
    });

    // Form Validation
    $('#customerForm').on('submit', function(e) {
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        const isEdit = {{ $customer ? 'true' : 'false' }};

        // Validasi password hanya jika diisi
        if (password || confirmPassword) {
            if (password.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Terlalu Pendek',
                    text: 'Password minimal 8 karakter!',
                });
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Tidak Cocok',
                    text: 'Password dan konfirmasi password harus sama!',
                });
                return false;
            }
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