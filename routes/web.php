<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\AccessController as AdminAccessController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\VideoController as CustomerVideoController;
use App\Http\Controllers\Customer\AccessController as CustomerAccessController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Redirect root berdasarkan role
    Route::get('/', function () {
        return auth()->user()->isAdmin() 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('customer.dashboard');
    })->name('home');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        
        // Dashboard - Pending Requests
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        /*
        |--------------------------------------------------------------------------
        | Customer Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });
        
        /*
        |--------------------------------------------------------------------------
        | Video Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('/', [AdminVideoController::class, 'index'])->name('index');
            Route::get('/create', [AdminVideoController::class, 'create'])->name('create');
            Route::post('/', [AdminVideoController::class, 'store'])->name('store');
            Route::get('/{video}/edit', [AdminVideoController::class, 'edit'])->name('edit');
            Route::put('/{video}', [AdminVideoController::class, 'update'])->name('update');
            Route::delete('/{video}', [AdminVideoController::class, 'destroy'])->name('destroy');
        });
        
        /*
        |--------------------------------------------------------------------------
        | Access Management (Approve/Reject)
        |--------------------------------------------------------------------------
        */
        Route::prefix('accesses')->name('accesses.')->group(function () {
            Route::get('/', [AdminAccessController::class, 'index'])->name('index');
            Route::post('/{access}/approve', [AdminAccessController::class, 'approve'])->name('approve');
            Route::post('/{access}/reject', [AdminAccessController::class, 'reject'])->name('reject');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('customer')->name('customer.')->middleware('role:customer')->group(function () {
        
        // Dashboard - Video List
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // My Access - Active Permissions
        Route::get('/my-access', [CustomerAccessController::class, 'myAccess'])->name('my-access');
        
        // Request History
        Route::get('/history', [CustomerAccessController::class, 'history'])->name('history');
        
        // Request Access to Video
        Route::post('/videos/{video}/request', [CustomerAccessController::class, 'requestAccess'])->name('videos.request');
        
        // Video Player
        Route::get('/videos/{video}/play', [CustomerVideoController::class, 'play'])->name('videos.play');
    });
});