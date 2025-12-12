<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Access;
use App\Models\Permission;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /**
     * Display customer's active permissions
     */
    public function myAccess(Request $request)
    {
        $customerId = auth()->id();

        // Get active permissions with related data
        $activePermissions = Permission::where('customer_id', $customerId)
            ->with(['video', 'access'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc')
            ->paginate(10);

        // Get expired permissions
        $expiredPermissions = Permission::where('customer_id', $customerId)
            ->with(['video', 'access'])
            ->where(function ($query) {
                $query->where('is_active', false)
                      ->orWhere('expires_at', '<=', now());
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('customer.my-access', compact('activePermissions', 'expiredPermissions'));
    }

    /**
     * Display customer's request history
     */
    public function history(Request $request)
    {
        $customerId = auth()->id();

        $query = Access::where('customer_id', $customerId)
            ->with(['video', 'permission']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $accessHistory = $query->latest()->paginate(10);

        // Statistics
        $stats = [
            'total' => Access::where('customer_id', $customerId)->count(),
            'pending' => Access::where('customer_id', $customerId)->pending()->count(),
            'approved' => Access::where('customer_id', $customerId)->approved()->count(),
            'rejected' => Access::where('customer_id', $customerId)->rejected()->count(),
        ];

        return view('customer.history', compact('accessHistory', 'stats'));
    }

    /**
     * Request access to a video
     */
    public function requestAccess(Video $video)
    {
        $customerId = auth()->id();

        // Cek apakah video aktif
        if (!$video->is_active) {
            return back()->with('error', 'Video ini tidak tersedia untuk direquest.');
        }

        // Cek apakah sudah ada pending request untuk video ini
        $pendingRequest = Access::where('customer_id', $customerId)
            ->where('video_id', $video->id)
            ->pending()
            ->first();

        if ($pendingRequest) {
            return back()->with('warning', 'Anda sudah memiliki request pending untuk video ini. Mohon tunggu konfirmasi admin.');
        }

        // Cek apakah masih punya active permission untuk video ini
        $activePermission = Permission::where('customer_id', $customerId)
            ->where('video_id', $video->id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if ($activePermission) {
            return back()->with('info', 'Anda masih memiliki akses aktif untuk video ini hingga ' . 
                $activePermission->expires_at->format('d M Y H:i'));
        }

        try {
            // Create new access request
            Access::create([
                'customer_id' => $customerId,
                'video_id' => $video->id,
                'status' => 'pending',
            ]);

            return back()->with('success', "Request akses untuk video \"{$video->title}\" berhasil dikirim. Mohon tunggu konfirmasi dari admin.");

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}