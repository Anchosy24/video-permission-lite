<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'duration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'duration' => 'integer',
        ];
    }

    // ============ SCOPES ============
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ============ RELATIONSHIPS ============
    
    // Access requests untuk video ini
    public function accessRequests()
    {
        return $this->hasMany(Access::class, 'video_id');
    }

    // Permissions untuk video ini
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'video_id');
    }

    // Active permissions
    public function activePermissions()
    {
        return $this->hasMany(Permission::class, 'video_id')
            ->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    // ============ HELPER METHODS ============
    
    // Check apakah user punya akses aktif
    public function hasActivePermission($userId): bool
    {
        return $this->activePermissions()
            ->where('customer_id', $userId)
            ->exists();
    }

    // Get permission aktif untuk user tertentu
    public function getActivePermissionForUser($userId)
    {
        return $this->activePermissions()
            ->where('customer_id', $userId)
            ->first();
    }

    // Format durasi (menit ke jam:menit)
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        }
        return $minutes . ' menit';
    }
}
