<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_id',
        'customer_id',
        'video_id',
        'duration_hours',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'duration_hours' => 'integer',
        ];
    }

    // ============ SCOPES ============
    
    // Permission yang masih aktif dan belum expired
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    // Permission yang sudah expired
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->orWhere('is_active', false);
    }

    // ============ RELATIONSHIPS ============
    
    // Access request yang terkait
    public function access()
    {
        return $this->belongsTo(Access::class, 'access_id');
    }

    // Customer pemilik permission
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Video yang di-permission
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    // ============ HELPER METHODS ============
    
    // Check apakah permission masih valid
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at > now();
    }

    // Check apakah sudah expired
    public function isExpired(): bool
    {
        return !$this->is_active || $this->expires_at <= now();
    }

    // Sisa waktu dalam jam (float)
    public function getRemainingHours(): float
    {
        if ($this->isExpired()) {
            return 0;
        }

        return round(now()->diffInMinutes($this->expires_at) / 60, 1);
    }

    // Sisa waktu dalam format human readable
    public function getRemainingTimeAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $diff = now()->diff($this->expires_at);
        
        if ($diff->h > 0) {
            return $diff->h . ' jam ' . $diff->i . ' menit';
        } elseif ($diff->i > 0) {
            return $diff->i . ' menit';
        } else {
            return $diff->s . ' detik';
        }
    }

    // Deactivate permission
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    // Auto-check dan deactivate jika expired
    public static function checkAndDeactivateExpired()
    {
        return self::where('is_active', true)
            ->where('expires_at', '<=', now())
            ->update(['is_active' => false]);
    }
}