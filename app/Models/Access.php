<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'video_id',
        'status',
        'reason',
    ];

    // ============ SCOPES ============
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ============ RELATIONSHIPS ============
    
    // Customer yang request
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Video yang di-request
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    // Permission yang terkait (jika approved)
    public function permission()
    {
        return $this->hasOne(Permission::class, 'access_id');
    }

    // ============ HELPER METHODS ============
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Approve request dan buat permission
    public function approve($durationHours)
    {
        $this->update(['status' => 'approved']);

        $durationHours = (int) $durationHours;

        return Permission::create([
            'access_id' => $this->id,
            'customer_id' => $this->customer_id,
            'video_id' => $this->video_id,
            'duration_hours' => $durationHours,
            'expires_at' => now()->addHours($durationHours),
        ]);
    }

    // Reject request
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }
}
