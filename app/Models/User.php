<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ============ HELPER METHODS ============
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // ============ SCOPES ============
    
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ============ RELATIONSHIPS ============
    
    // Access requests yang dibuat customer
    public function accessRequests()
    {
        return $this->hasMany(Access::class, 'customer_id');
    }

    // Permissions yang dimiliki customer
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'customer_id');
    }

    // Active permissions (belum expired)
    public function activePermissions()
    {
        return $this->hasMany(Permission::class, 'customer_id')
            ->where('is_active', true)
            ->where('expires_at', '>', now());
    }
}
